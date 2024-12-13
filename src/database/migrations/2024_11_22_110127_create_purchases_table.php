<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // users テーブルとのリレーション
            $table->foreignId('item_id')->constrained()->onDelete('cascade');  // 商品ID
            $table->string('shipping_postal_code', 10);  // 郵便番号
            $table->string('shipping_address_line');  // 住所
            $table->string('shipping_building');  // 建物名
            $table->enum('payment_method', ['konbini', 'card']); // 支払い方法の選択肢を制限
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchases');
    }
}
