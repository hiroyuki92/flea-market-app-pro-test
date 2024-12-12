<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');  // ユーザーとのリレーション（商品を出品したユーザー）
            $table->string('name');  // 商品名
            $table->string('brand');  // ブランド名
            $table->decimal('price', 10, 2);  // 商品価格（小数点2桁）
            $table->text('description');  // 商品説明
            $table->string('image_url');  // 商品画像
            $table->string('condition');  // 商品状態
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
        Schema::dropIfExists('items');
    }
}
