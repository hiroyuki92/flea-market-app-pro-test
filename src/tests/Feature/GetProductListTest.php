<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;

class GetProductListTest extends TestCase
{
    use RefreshDatabase;
    
    protected $user;
    protected $anotherUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // 共通のセットアップ処理
        $this->user = User::factory()->create();
        $this->anotherUser = User::factory()->create();
        $this->actingAs($this->user);
        $this->seed(\Database\Seeders\CategoriesTableSeeder::class);
        $this->seed(\Database\Seeders\ItemsTableSeeder::class);
    }

    public function test_can_retrieve_all_items()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        
        $items = Item::all();
        $expectedCount = 10;
        $this->assertEquals($expectedCount, $items->count(),
            "商品の総数が期待値と異なります。期待値: {$expectedCount}, 実際: {$items->count()}");
    }

    public function test_sold_item_displays_sold_status()
    {
        $item = Item::where('user_id', '!=', $this->user->id)->first();
        $this->assertNotNull($item, '商品が見つかりません');
        $item->update(['sold_out' => true]);

        $response = $this->get('/');
        $response->assertStatus(200)
            ->assertSee('Sold');
    }

    public function test_own_products_do_not_appear_in_the_list()
    {
        $items = Item::take(2)->get();
        $items[0]->update(['user_id' => $this->user->id, 'name' => 'My Item']);
        $items[1]->update(['user_id' => $this->anotherUser->id, 'name' => 'Other User Item']);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertDontSee('My Item');
        $response->assertSee('Other User Item');
    }
}

/* class GetProductListTest extends TestCase
{
    use RefreshDatabase;
    public function test_can_retrieve_all_items()
    {
        $user = User::factory()->create();
        $this->seed(\Database\Seeders\CategoriesTableSeeder::class);
        $this->actingAs($user)
        ->seed(\Database\Seeders\ItemsTableSeeder::class);
        $response = $this->get('/');
        $response->assertStatus(200);
        $items = Item::all();

        $expectedCount = 10;
        $this->assertEquals($expectedCount, $items->count(),
            "商品の総数が期待値と異なります。期待値: {$expectedCount}, 実際: {$items->count()}");
    }

    public function test_sold_item_displays_sold_status()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $seller = User::factory()->create();
        $this->seed(\Database\Seeders\CategoriesTableSeeder::class);
        $this->seed(\Database\Seeders\ItemsTableSeeder::class);
        $item = Item::where('user_id', '!=', $user->id)->first();
        $this->assertNotNull($item, '商品が見つかりません');
        $item->update(['sold_out' => true]);

        $response = $this->get('/');
        $response->assertStatus(200)
            ->assertSee('Sold');
    }

    public function test_own_products_do_not_appear_in_the_list()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $anotherUser = User::factory()->create();
        $this->seed(\Database\Seeders\CategoriesTableSeeder::class);
        $this->seed(\Database\Seeders\ItemsTableSeeder::class);

        $items = Item::take(2)->get();
        $items[0]->update(['user_id' => $user->id, 'name' => 'My Item']);
        $items[1]->update(['user_id' => $anotherUser->id, 'name' => 'Other User Item']);
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertDontSee('My Item');
        $response->assertSee('Other User Item');
    }
} */
