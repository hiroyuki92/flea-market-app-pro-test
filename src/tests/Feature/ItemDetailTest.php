<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Favorite;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['id' => 1]);
        User::factory()->create(['id' => 2]);
        $this->seed(\Database\Seeders\CategoriesTableSeeder::class);
        $this->seed(\Database\Seeders\ItemsTableSeeder::class);
        $items = Item::take(3)->get();
        $this->likedItems = $items;
        foreach ($this->likedItems as $item) {
            Favorite::create([
                'user_id' => $this->user->id,
                'item_id' => $item->id,
            ]);
        }
        $this->commentedItems = $items;
        foreach ($this->commentedItems as $item) {
            Comment::create([
                'user_id' => $this->user->id,
                'item_id' => $item->id,
                'content' => 'This is a test comment for item '.$item->id,
            ]);
        }
    }

    public function test_can_retrieve_product_details()
    {
        $item = Item::first();
        $response = $this->get(route('item.show', ['item_id' => $item->id]));
        $response->assertStatus(200);

        $response->assertSee($item->name);
        $response->assertSee($item->brand);
        $formattedPrice = '¥'.number_format($item->price).' (税込)';
        $response->assertSee($formattedPrice);
        $response->assertSee($item->description);
        $response->assertSee($item->image_url);
        $response->assertSee($item->condition);
        $response->assertSee((string) $item->favorites->count());
        $response->assertSee((string) $item->comments->count());
        foreach ($item->categories as $category) {
            $response->assertSee($category->name); // カテゴリ名
        }
        foreach ($item->comments as $comment) {
            $response->assertSee($comment->content);
            $response->assertSee($comment->user->name);
        }
    }

    public function test_can_display_multiple_selected_categories()
    {
        $item = Item::withCount('categories')
            ->having('categories_count', '>', 1)
            ->with('categories')
            ->first();

        // 商品が見つからない場合の処理
        if (! $item) {
            $item = Item::with('categories')->first();
            $newCategories = Category::whereNotIn('id', $item->categories->pluck('id'))
                ->take(2)
                ->get();
            $item->categories()->attach($newCategories->pluck('id'));
            $item->load('categories');
        }
        $response = $this->get(route('item.show', ['item_id' => $item->id]));
        $response->assertStatus(200);
        $response->assertViewHas('item');

        foreach ($item->categories as $category) {
            $response->assertSee($category->name);
        }

        $expectedCategoryCount = $item->categories->count();
        $this->assertEquals($expectedCategoryCount, $item->categories()->count(),
            'Category count mismatch');
    }
}
