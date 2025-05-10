<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->withEmail('user1@example.com')->create();
        User::factory()->withEmail('user2@example.com')->create();
        User::factory()->withEmail('user3@example.com')->create();
        $this->call([
            CategoriesTableSeeder::class,
            ItemsTableSeeder::class, ]);
    }
}
