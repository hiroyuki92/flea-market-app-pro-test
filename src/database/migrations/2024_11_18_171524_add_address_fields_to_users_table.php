<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddressFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_image')->nullable()->after('password');  // プロフィール画像
            $table->string('postal_code', 10)->nullable()->after('profile_image');  // 郵便番号
            $table->string('address_line')->nullable()->after('postal_code');  // 住所
            $table->string('building')->nullable()->after('address_line');  // 建物名
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // 追加したカラムを削除
            $table->dropColumn(['postal_code', 'address_line', 'building']);
        });
    }
}
