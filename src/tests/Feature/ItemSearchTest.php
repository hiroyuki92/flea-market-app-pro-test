<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Favorite;

class ItemSearchTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\CategoriesTableSeeder::class);
        $this->user = User::factory()->create();
        $this->seed(\Database\Seeders\ItemsTableSeeder::class);
    }

    public function test_it_can_perform_partial_match_search_by_product_name()
    {
        $items = Item::take(5)->get();
        $searchItem = $items->first();
        $partialKeyword = mb_substr($searchItem->name, 1, 2);
        $response = $this->get(route('index', ['keyword' => $partialKeyword]));

        $items = $response->viewData('items');

        $response->assertStatus(200)
                ->assertViewIs('index');

        $this->assertTrue(
            $items->contains('id', $searchItem->id),
        );
    }

    public function test_search_keyword_is_retained_on_my_list_page()
    {
        $items = Item::take(5)->get();
        $this->likedItems = $items->take(3);
        foreach ($this->likedItems as $item) {
        Favorite::create([
            'user_id' => $this->user->id,
            'item_id' => $item->id
        ]);
        }

        $this->actingAs($this->user);

        $searchItem = $this->likedItems->first();
        $partialKeyword = mb_substr($searchItem->name, 1, 2);
        $response = $this->get(route('index', ['keyword' => $partialKeyword]));
        $response->assertStatus(200)
                ->assertViewIs('index');
        foreach ($this->likedItems as $item) {
            if (str_contains($item->name, $partialKeyword)) {
            $response->assertSee($item->name);
            $response->assertSee('item-card mylist', false);
            }
        }
    }
}