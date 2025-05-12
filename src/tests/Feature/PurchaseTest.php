<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    private $anotherUser;

    private $validPurchaseData = [
        'shipping_postal_code' => '123-4567',
        'shipping_address_line' => '東京都渋谷区',
        'shipping_building' => '〇〇マンション',
        'payment_method' => 'card',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\CategoriesTableSeeder::class);
        $this->user = User::factory()->create(['id' => 1]);
        User::factory()->create(['id' => 2]);
        $this->anotherUser = User::factory()->create();
        $this->seed(\Database\Seeders\ItemsTableSeeder::class);
    }

    private function purchaseItem()
    {
        $item = Item::where('user_id', '!=', $this->user->id)->first();
        $this->assertEquals(0, $item->sold_out);

        $response = $this->post(route('purchase.store', $item->id), $this->validPurchaseData);
        $response->assertStatus(302);

        $this->assertDatabaseHas('purchases', [
            'user_id' => $this->user->id,
            'item_id' => $item->id,
        ]);

        return ['response' => $response, 'item' => $item];
    }

    public function test_user_can_complete_a_purchase()
    {
        $this->actingAs($this->user);
        $result = $this->purchaseItem();

        $this->assertEquals(1, $result['item']->fresh()->sold_out);
    }

    public function test_purchased_item_shows_sold_in_item_list()
    {
        $this->actingAs($this->user);
        $this->purchaseItem();

        $response = $this->get('/');
        $response->assertStatus(200)
            ->assertSee('Sold');
    }

    public function test_purchased_item_appears_in_profile_purchase_history()
    {
        $this->actingAs($this->user);
        $result = $this->purchaseItem();
        $item = $result['item'];
        $response = $this->get(route('profile.show', ['tab' => 'buy']));
        $response->assertStatus(200);

        $response
            ->assertSee('class="item-card purchased"', false)
            ->assertSee('class="item-image"', false)
            ->assertSee('class="item-image-picture"', false)
            ->assertSee($item->name);

        $response->assertViewHas('purchases', function ($purchases) use ($item) {
            return $purchases->contains(function ($purchase) use ($item) {
                return $purchase->item_id === $item->id;
            });
        });
    }
}
