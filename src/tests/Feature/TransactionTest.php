<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\User;
use App\Mail\TransactionCompleted;
use Illuminate\Support\Facades\Mail;

class TransactionTest extends TestCase
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
     * ユーザーは取引をしたユーザーを評価することができる
     * 購入者は取引を完了ボタンをクリックすると取引完了モーダルからユーザーの評価をすることができるかテスト
     */

    public function buyer_can_rate_transaction()
    {
        $item = Item::first();

        $purchase = Purchase::factory()->create([
            'user_id' => $this->buyer->id,
            'item_id' => $item->id,
            'completed' => false,
            'buyer_rating' => null,
            'seller_rating' => null,
        ]);

        $response = $this->actingAs($this->buyer)
            ->post('submit-buyer-rating', [
                'purchase_id' => $purchase->id,
                'rating' => 5
            ]);

        $response->assertRedirect('/');

        $purchase->refresh();
        $this->assertEquals(5, $purchase->buyer_rating);
        $this->assertTrue($purchase->completed);
    }

    /**
     * @test
     * ユーザーは取引をしたユーザーを評価することができる
     * 出品者は、商品の購入者が取引を完了した後に、ユーザーの評価をすることができるかテスト
     */
    public function seller_can_rate_transaction()
    {
        $item = Item::first();

        $purchase = Purchase::factory()->create([
            'user_id' => $this->buyer->id,
            'item_id' => $item->id,
            'completed' => true,
            'buyer_rating' => 5,
            'seller_rating' => null,
        ]);

        $response = $this->actingAs($this->seller)
            ->post('submit-seller-rating', [
                'purchase_id' => $purchase->id,
                'rating' => 4
            ]);

        $response->assertRedirect('/');

        $purchase->refresh();
        $this->assertEquals(4, $purchase->seller_rating);
        $this->assertTrue($purchase->completed);
    }

    /**
     * @test
     * 取引機能テスト
     * 商品購入者が取引を完了すると、商品出品者宛に自動で通知メールが送信されるかテスト
     */
    public function email_sent_to_seller_when_buyer_rating_is_submitted()
    {
        $item = Item::first();

        $purchase = Purchase::factory()->create([
            'user_id' => $this->buyer->id,
            'item_id' => $item->id,
            'completed' => false,
            'buyer_rating' => null,
            'seller_rating' => null,
        ]);

        // メール送信をフェイク
        Mail::fake();

        $response = $this->actingAs($this->buyer)
            ->post('submit-buyer-rating', [
                'purchase_id' => $purchase->id,
                'rating' => 5
            ]);

        $response->assertRedirect('/');

        // データベースの更新確認
        $purchase->refresh();
        $this->assertEquals(5, $purchase->buyer_rating);
        $this->assertTrue($purchase->completed);

        // メール送信の確認
        Mail::assertSent(TransactionCompleted::class, function ($mail) use ($purchase) {
            return $mail->hasTo($purchase->item->user->email);
        });
    }
}
