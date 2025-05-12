<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    private $anotherUser;

    private $item;

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
        $this->anotherUser = User::factory()->create(['id' => 2]);
        $this->seed(\Database\Seeders\ItemsTableSeeder::class);
        $this->item = Item::where('user_id', '!=', $this->user->id)->first();
    }

    public function test_selected_payment_method_is_registered_in_database()
    {
        $this->actingAs($this->user);
        $response = $this->post(route('purchase.store', $this->item->id), $this->validPurchaseData);
        $response->assertStatus(302);
        $this->assertDatabaseHas('purchases', [
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
            'payment_method' => $this->validPurchaseData['payment_method'],
        ]);
    }
}
