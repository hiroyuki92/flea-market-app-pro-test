<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFavoritesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

     public function up()
{
    Schema::create('favorites', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id'); // ユーザーIDを追加
        $table->unsignedBigInteger('item_id'); // アイテムIDを追加
        $table->timestamps();

        // 外部キー制約の追加
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
    });
}

    /* public function up()
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');  // ユーザーID
            $table->foreignId('item_id')->constrained()->onDelete('cascade');  // 商品ID
            $table->timestamps();

            // user_id と item_id の組み合わせで一意制約を追加
            $table->unique(['user_id', 'item_id']);
        });
    } */

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('favorites');
    }
}
