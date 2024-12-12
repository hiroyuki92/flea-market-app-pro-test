<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
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
    $items = [
        [
            'user_id' => 1,
            'name' => '腕時計',
            'brand' => 'アルマーニ',
            'price' => 15000,
            'description' => 'スタイリッシュなデザインのメンズ腕時計',
            'condition' => '1',
            'image_url' => 'item_images/Armani+Mens+Clock.jpg',
            'categories' => [1, 5]
        ],
        [
            'user_id' => 2,
            'name' => 'HDD',
            'brand' => '富士通',
            'price' => 5000,
            'description' => '高速で信頼性の高いハードディスク',
            'condition' => '2',
            'image_url' => 'item_images/HDD+Hard+Disk.jpg',
            'categories' => [2]
        ],
        [
            'user_id' => 3,
            'name' => '玉ねぎ3束',
            'brand' => '淡路島',
            'price' => 300,
            'description' => '新鮮な玉ねぎ3束のセット',
            'condition' => '3',
            'image_url' => 'item_images/iLoveIMG+d.jpg',
            'categories' => [10]
        ],
        [
            'user_id' => 4,
            'name' => '革靴',
            'brand' => 'ポールスミス',
            'price' => 4000,
            'description' => 'クラシックなデザインの革靴',
            'condition' => '4',
            'image_url' => 'item_images/Leather+Shoes+Product+Photo.jpg',
            'categories' => [1, 5]
        ],
        [
            'user_id' => 5,
            'name' => 'ノートPC',
            'brand' => 'Mac',
            'price' => 45000,
            'description' => '高性能なノートパソコン',
            'condition' => '1',
            'image_url' => 'item_images/Living+Room+Laptop.jpg',
            'categories' => [2]
        ],
        [
            'user_id' => 6,
            'name' => 'マイク',
            'brand' => '東芝',
            'price' => 8000,
            'description' => '高音質のレコーディング用マイク',
            'condition' => '2',
            'image_url' => 'item_images/Music+Mic+4632231.jpg',
            'categories' => [2]
        ],
        [
            'user_id' => 7,
            'name' => 'ショルダーバッグ',
            'brand' => 'COACH',
            'price' => 3500,
            'description' => 'おしゃれなショルダーバッグ',
            'condition' => '3',
            'image_url' => 'item_images/Purse+fashion+pocket.jpg',
            'categories' => [1, 4]
        ],
        [
            'user_id' => 8,
            'name' => 'タンブラー',
            'brand' => 'スターバックス',
            'price' => 500,
            'description' => '使いやすいタンブラー',
            'condition' => '4',
            'image_url' => 'item_images/Tumbler+souvenir.jpg',
            'categories' => [10]
        ],
        [
            'user_id' => 9,
            'name' => 'コーヒーミル',
            'brand' => 'ネスカフェ',
            'price' => 4000,
            'description' => '手動のコーヒーミル',
            'condition' => '1',
            'image_url' => 'item_images/Waitress+with+Coffee+Grinder.jpg',
            'categories' => [10]
        ],
        [
            'user_id' => 1,
            'name' => 'メイクセット',
            'brand' => 'KOSE',
            'price' => 2500,
            'description' => '便利なメイクアップセット',
            'condition' => '2',
            'image_url' => 'item_images/外出メイクアップセット.jpg',
            'categories' => [4, 6]
        ],
    ];

    foreach ($items as $itemData) {
        $item = Item::create([
            'user_id' => $itemData['user_id'],
            'name' => $itemData['name'],
            'brand' => $itemData['brand'],
            'price' => $itemData['price'],
            'description' => $itemData['description'],
            'condition' => $itemData['condition'],
            'image_url' => $itemData['image_url'],
        ]);
        $item->categories()->attach($itemData['categories']);
    }
}
}