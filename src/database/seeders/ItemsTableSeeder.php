<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userIds = User::pluck('id')->toArray();
        $faker = app(Faker::class);
        if (! Storage::disk('public')->exists('item_images')) {
            Storage::disk('public')->makeDirectory('item_images');
        }

        // シーダー用の画像をコピー
        $seederImages = glob(database_path('seeders/images/*'));
        foreach ($seederImages as $image) {
            $fileName = mb_substr($image, strrpos($image, '/') + 1);
            Storage::disk('public')->put(
                "item_images/{$fileName}",
                file_get_contents($image)
            );
        }
        $items = [
            [
                'name' => '腕時計',
                'brand' => 'アルマーニ',
                'price' => 15000,
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'condition' => '1',
                'image_url' => 'Armani+Mens+Clock.jpg',
                'categories' => [1, 5],
            ],
            [
                'name' => 'HDD',
                'brand' => '富士通',
                'price' => 5000,
                'description' => '高速で信頼性の高いハードディスク',
                'condition' => '2',
                'image_url' => 'HDD+Hard+Disk.jpg',
                'categories' => [2],
            ],
            [
                'name' => '玉ねぎ3束',
                'brand' => '淡路島',
                'price' => 300,
                'description' => '新鮮な玉ねぎ3束のセット',
                'condition' => '3',
                'image_url' => 'iLoveIMG+d.jpg',
                'categories' => [10],
            ],
            [
                'name' => '革靴',
                'brand' => 'ポールスミス',
                'price' => 4000,
                'description' => 'クラシックなデザインの革靴',
                'condition' => '4',
                'image_url' => 'Leather+Shoes+Product+Photo.jpg',
                'categories' => [1, 5],
            ],
            [
                'name' => 'ノートPC',
                'brand' => 'Mac',
                'price' => 45000,
                'description' => '高性能なノートパソコン',
                'condition' => '1',
                'image_url' => 'Living+Room+Laptop.jpg',
                'categories' => [2],
            ],
            [
                'name' => 'マイク',
                'brand' => '東芝',
                'price' => 8000,
                'description' => '高音質のレコーディング用マイク',
                'condition' => '2',
                'image_url' => 'Music+Mic+4632231.jpg',
                'categories' => [2],
            ],
            [
                'name' => 'ショルダーバッグ',
                'brand' => 'COACH',
                'price' => 3500,
                'description' => 'おしゃれなショルダーバッグ',
                'condition' => '3',
                'image_url' => 'Purse+fashion+pocket.jpg',
                'categories' => [1, 4],
            ],
            [
                'name' => 'タンブラー',
                'brand' => 'スターバックス',
                'price' => 500,
                'description' => '使いやすいタンブラー',
                'condition' => '4',
                'image_url' => 'Tumbler+souvenir.jpg',
                'categories' => [10],
            ],
            [
                'name' => 'コーヒーミル',
                'brand' => 'ネスカフェ',
                'price' => 4000,
                'description' => '手動のコーヒーミル',
                'condition' => '1',
                'image_url' => 'Waitress+with+Coffee+Grinder.jpg',
                'categories' => [10],
            ],
            [
                'name' => 'メイクセット',
                'brand' => 'KOSE',
                'price' => 2500,
                'description' => '便利なメイクアップセット',
                'condition' => '2',
                'image_url' => '外出メイクアップセット.jpg',
                'categories' => [4, 6],
            ],
        ];

        for ($i = 0; $i < 5; $i++) {
            $itemData = $items[$i];
            $item = Item::create([
                'user_id' => 1, // user_id=1に固定
                'name' => $itemData['name'],
                'brand' => $itemData['brand'],
                'price' => $itemData['price'],
                'description' => $itemData['description'],
                'condition' => $itemData['condition'],
                'image_url' => $itemData['image_url'],
                'sold_out' => false,
            ]);
            $item->categories()->attach($itemData['categories']);
        }

        for ($i = 5; $i < count($items); $i++) {
            $itemData = $items[$i];
            $item = Item::create([
                'user_id' => 2, // user_id=2に固定
                'name' => $itemData['name'],
                'brand' => $itemData['brand'],
                'price' => $itemData['price'],
                'description' => $itemData['description'],
                'condition' => $itemData['condition'],
                'image_url' => $itemData['image_url'],
                'sold_out' => false,
            ]);
            $item->categories()->attach($itemData['categories']);
        }
    }
}
