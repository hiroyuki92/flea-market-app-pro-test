<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    private $otherUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\CategoriesTableSeeder::class);
        $this->user = User::factory()->create([
            'name' => 'テストユーザー',
        ]);
        $this->otherUser = User::factory()->create([
            'name' => '出品者ユーザー',
        ]);
        $this->seed(\Database\Seeders\ItemsTableSeeder::class);
    }

    public function test_can_get_user_with_all_related_data()
    {
        $seededItemsCount = Item::where('user_id', $this->user->id)->count();

        $otherUserItems = Item::where('user_id', '!=', $this->user->id)
            ->take(2)
            ->get();
        foreach ($otherUserItems as $item) {
            Purchase::create([
                'user_id' => $this->user->id,
                'item_id' => $item->id,
                'shipping_postal_code' => '123-4567',
                'shipping_address_line' => '東京都渋谷区',
                'shipping_building' => '〇〇マンション',
                'payment_method' => 'card',
            ]);
        }

        $this->actingAs($this->user);

        $response = $this->get(route('profile.show', ['tab' => 'sell']));
        $response->assertStatus(200);

        // 出品商品数の確認
        $displayedItemsCount = $this->user->items()->count();
        $this->assertEquals(
            $seededItemsCount,
            $displayedItemsCount,
        );

        $response->assertSee('出品した商品');
        $userItems = Item::where('user_id', $this->user->id)->get();
        foreach ($userItems as $item) {
            $response->assertSee($item->name);
            $response->assertSee($item->image_url, false);
            $response->assertSee('<img', false);
        }

        // 購入商品の確認
        $response = $this->get(route('profile.show', ['tab' => 'buy']));
        $response->assertStatus(200);
        $purchasedItemsCount = $this->user->purchases()->count();
        $this->assertEquals(2, $purchasedItemsCount);

        $response->assertSee('購入した商品');
        foreach ($otherUserItems as $item) {
            $response->assertSee($item->name);
            $response->assertSee($item->image_url, false);
            $response->assertSee('<img', false);
        }

        // プロフィール情報の確認
        $response->assertSee('<img', false)
            ->assertSee($this->user->profile_image, false);
        $response->assertSee('テストユーザー');
    }
}
