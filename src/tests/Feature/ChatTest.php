<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\DomCrawler\Crawler;
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
            'user_id' => $this->buyer->id,
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
