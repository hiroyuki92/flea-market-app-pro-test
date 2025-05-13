<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\DomCrawler\Crawler;
use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\User;
use Database\Factories\ItemFactory;




class ChatTest extends TestCase
{
    use RefreshDatabase;

    protected $seller;
    protected $buyer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\CategoriesTableSeeder::class);
        $this->seller = \App\Models\User::factory()->create(['id' => 1]);
        $this->buyer = \App\Models\User::factory()->create(['id' => 2]);
        $this->seed(\Database\Seeders\ItemsTableSeeder::class);
    }

    /**
     * @test
     * ユーザーは取引チャットを確認することができる
     * マイページから取引中の商品を確認することができるかテスト
     */
    public function user_can_see_transaction_chat()
    {
        $item = Item::create([
            'user_id' => $this->seller->id,
            'name' => 'テストアイテム',
            'brand' => 'テストブランド',
            'price' => 5000,
            'description' => 'これはテスト用のアイテムです。',
            'image_url' => 'test-image.jpg',
            'condition' => 4,
            'in_transaction' => 1,  // 取引中の商品として設定
        ]);

        $purchase = Purchase::factory()->create([
            'user_id' => $this->buyer->id,
            'item_id' => $item->id,
            'completed' => false,
            'buyer_rating' => null,
            'seller_rating' => null,
        ]);

        $response = $this->actingAs($this->buyer)
            ->get(route('profile.show', ['tab' => 'transaction']));
        $response->assertSee($item->name);
    }

    /**
     * @test
     * マイページの取り引中の商品を押下することで、取引チャット画面へ遷移することができるかテスト
     */
    public function user_can_see_transaction_chat_detail()
    {
        $item = Item::create([
            'user_id' => $this->seller->id,
            'name' => 'テストアイテム',
            'brand' => 'テストブランド',
            'price' => 5000,
            'description' => 'これはテスト用のアイテムです。',
            'image_url' => 'test-image.jpg',
            'condition' => 4,
            'in_transaction' => 1,  // 取引中の商品として設定
        ]);

        $purchase = Purchase::factory()->create([
            'user_id' => $this->buyer->id,
            'item_id' => $item->id,
            'completed' => false,
            'buyer_rating' => null,
            'seller_rating' => null,
        ]);

        $response = $this->actingAs($this->buyer)
            ->get(route('profile.show', ['tab' => 'transaction']));
        $response->assertSee($item->name);
        $crawler = new Crawler($response->getContent());
        $link = $crawler->selectLink($item->name)
            ->link();
        $url = $link->getUri();

        $response = $this->get($url);

        $response->assertStatus(200);
        $response->assertSee($item->name);
        $response->assertSee($this->seller->name);
    }

    /**
     * @test
     * 取引チャット画面のサイドバーから別の取引画面に遷移するかテスト
     */
    public function user_can_see_another_transaction_chat()
    {
        $item1 = Item::create([
            'user_id' => $this->seller->id,
            'name' => 'テストアイテム1',
            'brand' => 'テストブランド1',
            'price' => 5000,
            'description' => 'これはテスト用のアイテム1です。',
            'image_url' => 'test-image1.jpg',
            'condition' => 4,
            'in_transaction' => 1,  // 取引中の商品として設定
        ]);

        $item2 = Item::create([
            'user_id' => $this->seller->id,
            'name' => 'テストアイテム2',
            'brand' => 'テストブランド2',
            'price' => 6000,
            'description' => 'これはテスト用のアイテム2です。',
            'image_url' => 'test-image2.jpg',
            'condition' => 4,
            'in_transaction' => 1,  // 取引中の商品として設定
        ]);

        $purchase1 = Purchase::factory()->create([
            'user_id' => $this->buyer->id,
            'item_id' => $item1->id,
            'completed' => false,
            'buyer_rating' => null,
            'seller_rating' => null,
        ]);

        $purchase2 = Purchase::factory()->create([
            'user_id' => $this->buyer->id,
            'item_id' => $item2->id,
            'completed' => false,
            'buyer_rating' => null,
            'seller_rating' => null,
        ]);

        $response = $this->actingAs($this->buyer)
            ->get(route('transaction.show.buyer', ['item_id' => $item1->id]));
        $response->assertStatus(200);
        $response->assertSee($item1->name);
        $response->assertSee($item2->name);

        $crawler = new Crawler($response->getContent());
        $sidebar_link = $crawler->selectLink($item2->name)->link();
        $sidebar_url = $sidebar_link->getUri();
        $expected_url = route('transaction.show.buyer', ['item_id' => $item2->id]);
        $this->assertStringContainsString($expected_url, $sidebar_url);

        // 2つ目の取引チャット画面に遷移
        $response = $this->get($sidebar_url);

        $response->assertStatus(200);
        $response->assertSee($item2->name);

    }

    /**
     * @test
     * 取引中の商品の並び順は新規メッセージが来た順に表示しているかテスト
     */
    public function user_can_see_transaction_chat_order()
    {
        $item1 = Item::create([
            'user_id' => $this->seller->id,
            'name' => 'アイテム1',
            'brand' => 'ブランド',
            'price' => 5000,
            'description' => '説明',
            'image_url' => 'image1.jpg',
            'condition' => 4,
            'in_transaction' => 1,
        ]);

        $item2 = Item::create([
            'user_id' => $this->seller->id,
            'name' => 'アイテム2',
            'brand' => 'ブランド',
            'price' => 6000,
            'description' => '説明',
            'image_url' => 'image2.jpg',
            'condition' => 4,
            'in_transaction' => 1,
        ]);

        $item3 = Item::create([
            'user_id' => $this->seller->id,
            'name' => 'アイテム3',
            'brand' => 'ブランド',
            'price' => 7000,
            'description' => '説明',
            'image_url' => 'image3.jpg',
            'condition' => 4,
            'in_transaction' => 1,
        ]);

        $purchase1 = Purchase::factory()->create([
            'user_id' => $this->buyer->id,
            'item_id' => $item1->id,
            'completed' => false,
        ]);

        $purchase2 = Purchase::factory()->create([
            'user_id' => $this->buyer->id,
            'item_id' => $item2->id,
            'completed' => false,
        ]);

        $purchase3 = Purchase::factory()->create([
            'user_id' => $this->buyer->id,
            'item_id' => $item3->id,
            'completed' => false,
        ]);

        // 各商品でのチャットを作成
        $chat1 = \App\Models\Chat::factory()->create([
            'item_id' => $item1->id,
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
        ]);

        $chat2 = \App\Models\Chat::factory()->create([
            'item_id' => $item2->id,
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
        ]);

        $chat3 = \App\Models\Chat::factory()->create([
            'item_id' => $item3->id,
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
        ]);

        // 時系列でメッセージを送信
        // 最初にitem1にメッセージ（3時間前）
        $message1 = \App\Models\Message::factory()->create([
            'chat_id' => $chat1->id,
            'sender_id' => $this->buyer->id,
            'message' => 'アイテム1へのメッセージ',
            'created_at' => Carbon::now()->subHours(3),
        ]);

        // 次にitem3にメッセージ（1時間前）
        $message3 = \App\Models\Message::factory()->create([
            'chat_id' => $chat3->id,
            'sender_id' => $this->buyer->id,
            'message' => 'アイテム3へのメッセージ',
            'created_at' => Carbon::now()->subHours(1),
        ]);

        // 最後にitem2にメッセージ（5分前）
        $message2 = \App\Models\Message::factory()->create([
            'chat_id' => $chat2->id,
            'sender_id' => $this->seller->id,
            'message' => 'アイテム2へのメッセージ',
            'created_at' => Carbon::now()->subMinutes(5),
        ]);

        $response = $this->actingAs($this->buyer)
        ->get(route('profile.show', ['tab' => 'transaction']));
        $content = $response->getContent();

        $item2_position = strpos($content, $item2->name);
        $item3_position = strpos($content, $item3->name);
        $item1_position = strpos($content, $item1->name);
        $this->assertTrue(
            $item2_position < $item3_position && $item3_position < $item1_position,
            '取引リストが最新メッセージ順で表示されていない'
        );  // item2,item3,item1の順に表示されることを確認

    }

    /**
     * @test
     * 通知マークから何件メッセージが来ているかが確認できるかテスト
     */
    public function user_can_see_unread_message_count()
    {
        $item = Item::create([
            'user_id' => $this->seller->id,
            'name' => 'テストアイテム',
            'brand' => 'ブランド',
            'price' => 5000,
            'description' => '説明',
            'image_url' => 'image.jpg',
            'condition' => 4,
            'in_transaction' => 1,
        ]);

        Purchase::factory()->create([
            'user_id' => $this->buyer->id,
            'item_id' => $item->id,
            'completed' => false,
        ]);

        $chat = \App\Models\Chat::factory()->create([
            'item_id' => $item->id,
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
        ]);

        // Sellerから4件の未読メッセージを送信
        for ($i = 1; $i <= 4; $i++) {
            \App\Models\Message::factory()->create([
                'chat_id' => $chat->id,
                'sender_id' => $this->seller->id,
                'message' => "セラーからのメッセージ{$i}",
                'is_read' => false,
            ]);
        }

        $response = $this->actingAs($this->buyer)
        ->get(route('profile.show', ['tab' => 'transaction']));

        $response->assertStatus(200);

        $response->assertSee('4');
    
        $crawler = new Crawler($response->getContent());
        $badge = $crawler->filter('.unread-badge-overlay .badge');
        $this->assertEquals('4', $badge->text());
    }


    /**
     * @test
     * ユーザーは自分の取引評価の平均を確認することができるかテスト
     */
    public function user_can_see_average_rating()
    {
        $item = Item::first();

        $purchase = Purchase::factory()->create([
            'user_id' => $this->buyer->id,
            'item_id' => $item->id,
            'completed' => false,
            'buyer_rating' => 4,
            'seller_rating' => 5,
        ]);

        $purchase2 = Purchase::factory()->create([
            'user_id' => $this->buyer->id,
            'item_id' => $item->id,
            'completed' => true,
            'buyer_rating' => 3,
            'seller_rating' => 4,
        ]);

        $purchase3 = Purchase::factory()->create([
            'user_id' => $this->buyer->id,
            'item_id' => $item->id,
            'completed' => true,
            'buyer_rating' => 5,
            'seller_rating' => 4,
        ]);

        // buyer_rating の平均を確認
        $expectedAverageRating = (4 + 3 + 5) / 3;  // 平均: 4

        $response = $this->actingAs($this->buyer)
            ->get(route('profile.show'));
        $crawler = new Crawler($response->getContent());
        $this->assertCount(4, $crawler->filter('span.star.filled'));  // 4つの満たされた星を確認
        $this->assertCount(1, $crawler->filter('span.star.empty'));  // 1つの空の星を確認
    }

    /**
     * @test
     * 購入者が取引チャットの投稿をすることができるかテスト
     */
    public function user_can_post_message()
    {
        $item = Item::first();
        $itemId = $item->id;

        $purchase = Purchase::factory()->create([
            'user_id' => $this->buyer->id,
            'item_id' => $item->id,
            'completed' => false,
            'buyer_rating' => null,
            'seller_rating' => null,
        ]);

        $chat = \App\Models\Chat::factory()->create([
            'item_id' => $item->id,
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
        ]);

        $response = $this->actingAs($this->buyer)
            ->post(route('transaction.buyerSendMessage', ['item_id' => $itemId]), [
                'message' => 'Hello, this is a test message.',
                'chat_id' => $chat->id,
            ]);

        $response->assertRedirect(route('transaction.show.buyer', ['item_id' => $itemId]));

        $this->assertDatabaseHas('messages', [
            'message' => 'Hello, this is a test message.',
            'chat_id' => $chat->id,
            'sender_id' => $this->buyer->id,
        ]);
    }

    /**
     * @test
     * 購入者が取引チャットの投稿をすることができるかテスト
     */
    public function seller_can_post_message()
    {
        $item = Item::first();
        $itemId = $item->id;

        $purchase = Purchase::factory()->create([
            'user_id' => $this->buyer->id,
            'item_id' => $item->id,
            'completed' => false,
            'buyer_rating' => null,
            'seller_rating' => null,
        ]);

        $chat = \App\Models\Chat::factory()->create([
            'item_id' => $item->id,
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
        ]);

        $response = $this->actingAs($this->seller)
            ->post(route('transaction.sellerSendMessage', ['item_id' => $itemId]), [
                'message' => 'Hello, this is a test message.',
                'chat_id' => $chat->id,
            ]);

        $response->assertRedirect(route('transaction.show', ['item_id' => $itemId]));

        $this->assertDatabaseHas('messages', [
            'message' => 'Hello, this is a test message.',
            'chat_id' => $chat->id,
            'sender_id' => $this->seller->id,
        ]);
    }

    /**
     * @test
     * ユーザーは取引チャットの編集、削除をすることができる
     * 購入者が投稿済みのメッセージを編集することができるかテスト
     */
    public function buyer_can_edit_message()
    {
        $item = Item::first();
        $itemId = $item->id;

        $purchase = Purchase::factory()->create([
            'user_id' => $this->buyer->id,
            'item_id' => $item->id,
            'completed' => false,
            'buyer_rating' => null,
            'seller_rating' => null,
        ]);

        $chat = \App\Models\Chat::factory()->create([
            'item_id' => $item->id,
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
        ]);

        $message = \App\Models\Message::factory()->create([
            'chat_id' => $chat->id,
            'sender_id' => $this->buyer->id,
            'message' => 'Original message',
        ]);

        $response = $this->actingAs($this->buyer)
            ->patch('/transaction/update', [
                'message_id' => $message->id,
                'message' => 'Edited message',
            ]);


        $response->assertRedirect(route('transaction.show.buyer', ['item_id' => $itemId]));

        $message->refresh();
        $this->assertEquals('Edited message', $message->message);
    }
    /**
     * @test
     * 出品者が投稿済みのメッセージを編集することができるかテスト
     */
    public function seller_can_edit_message()
    {
        $item = Item::first();
        $itemId = $item->id;

        $purchase = Purchase::factory()->create([
            'user_id' => $this->buyer->id,
            'item_id' => $item->id,
            'completed' => false,
            'buyer_rating' => null,
            'seller_rating' => null,
        ]);

        $chat = \App\Models\Chat::factory()->create([
            'item_id' => $item->id,
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
        ]);

        $message = \App\Models\Message::factory()->create([
            'chat_id' => $chat->id,
            'sender_id' => $this->seller->id,
            'message' => 'Original message',
        ]);

        $response = $this->actingAs($this->seller)
            ->patch('/transaction/update', [
                'message_id' => $message->id,
                'message' => 'Edited message',
            ]);


        $response->assertRedirect(route('transaction.show', ['item_id' => $itemId]));

        $message->refresh();
        $this->assertEquals('Edited message', $message->message);
    }
    /**
     * @test
     * 購入者が投稿済みのメッセージを削除することができるかテスト
     */
    public function buyer_can_delete_message()
    {
        $item = Item::first();
        $itemId = $item->id;

        $purchase = Purchase::factory()->create([
            'user_id' => $this->buyer->id,
            'item_id' => $item->id,
            'completed' => false,
            'buyer_rating' => null,
            'seller_rating' => null,
        ]);

        $chat = \App\Models\Chat::factory()->create([
            'item_id' => $item->id,
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
        ]);

        $message = \App\Models\Message::factory()->create([
            'chat_id' => $chat->id,
            'sender_id' => $this->buyer->id,
            'message' => 'Original message',
        ]);

        $response = $this->actingAs($this->buyer)
            ->delete('/transaction/delete', [
                'message_id' => $message->id,
            ]);

        $response->assertRedirect(route('transaction.show.buyer', ['item_id' => $itemId]));

        $this->assertDatabaseMissing('messages', [
            'id' => $message->id,
        ]);
    }
    /**
     * @test
     * 出品者が投稿済みのメッセージを削除することができるかテスト
     */
    public function seller_can_delete_message()
    {
        $item = Item::first();
        $itemId = $item->id;

        $purchase = Purchase::factory()->create([
            'user_id' => $this->buyer->id,
            'item_id' => $item->id,
            'completed' => false,
            'buyer_rating' => null,
            'seller_rating' => null,
        ]);

        $chat = \App\Models\Chat::factory()->create([
            'item_id' => $item->id,
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
        ]);

        $message = \App\Models\Message::factory()->create([
            'chat_id' => $chat->id,
            'sender_id' => $this->seller->id,
            'message' => 'Original message',
        ]);

        $response = $this->actingAs($this->seller)
            ->delete('/transaction/delete', [
                'message_id' => $message->id,
            ]);

        $response->assertRedirect(route('transaction.show', ['item_id' => $itemId]));

        $this->assertDatabaseMissing('messages', [
            'id' => $message->id,
        ]);
    }
}
