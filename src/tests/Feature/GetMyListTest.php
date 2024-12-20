<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Favorite;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetMyListTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $items;
    private $likedItems;
    private $notLikedItems;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\CategoriesTableSeeder::class);
        $this->user = User::factory()->create();
        $this->seed(\Database\Seeders\ItemsTableSeeder::class);

        $this->items = Item::take(5)->get();
        $this->likedItems = $this->items->take(3);
        $this->notLikedItems = $this->items->skip(3)->take(2);

        foreach ($this->likedItems as $item) {
            Favorite::create([
                'user_id' => $this->user->id,
                'item_id' => $item->id
            ]);
        }
    }

    public function test_user_can_see_only_favorited_products()
    {
        $this->actingAs($this->user);
        $response = $this->get(route('index'));
        $response->assertStatus(200);

        // いいねした商品の確認
        foreach ($this->likedItems as $item) {
            $response->assertSee($item->name);
            $response->assertSee('item-card mylist', false);
        }

    }

    public function test_mylist_shows_sold_status_for_purchased_items()
    {
        $this->actingAs($this->user);
        $soldItem = $this->likedItems->first();
        $soldItem->update(['sold_out' => true]);
        $response = $this->get(route('index'));
        $response->assertStatus(200);
        $response->assertSee($soldItem->name);
        $response->assertSee('Sold');
    }
}
