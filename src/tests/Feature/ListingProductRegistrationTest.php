<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ListingProductRegistrationTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\CategoriesTableSeeder::class);
        $this->user = User::factory()->create();
    }

    public function test_can_register_item_with_valid_data()
    {
    $this->actingAs($this->user);
    Storage::fake('public');
    $image = new UploadedFile(
    base_path('tests/Fixtures/images/default_item_image.jpg'),
    'default_item_image.jpg',
    'image/jpeg',
    null,
    true
);
    $itemData = [
        'user_id' => $this->user->id,
        'name' => 'テスト商品',
        'brand' => 'ブランド',
        'price' => 1000,
        'description' => 'これはテスト商品です。新品の状態です。',
        'image' => $image,
        'condition' => 1,
        'category_ids' => '3,5',
    ];

    $response = $this->post(route('item.store'), $itemData);

    $response->assertRedirect();

    $files = Storage::disk('public')->files('item_images');
    $this->assertNotEmpty($files, '画像が保存されていません');

    $storedFile = $files[0];
    $fileName = basename($storedFile);

    $this->assertDatabaseHas('items', [
        'name' => 'テスト商品',
        'brand' => 'ブランド',
        'price' => 1000,
        'description' => 'これはテスト商品です。新品の状態です。',
        'condition' => 1,
        'image_url' => $fileName,
        'user_id' => $this->user->id,
    ]);

    $savedItem = Item::latest()->first();

    $this->assertDatabaseHas('category_item', [
        'item_id' => $savedItem->id,
        'category_id' => 3,
    ]);

    $this->assertDatabaseHas('category_item', [
        'item_id' => $savedItem->id,
        'category_id' => 5,
    ]);

}
}