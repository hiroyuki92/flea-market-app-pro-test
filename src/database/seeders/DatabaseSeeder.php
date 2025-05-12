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
        User::factory()->create([
            'id' => 1,
            'email' => 'user1@example.com'
        ]);
        User::factory()->create([
            'id' => 2,
            'email' => 'user2@example.com'
        ]);
        User::factory()->create([
            'id' => 3,
            'email' => 'user3@example.com'
        ]);
        $this->call([
            CategoriesTableSeeder::class,
            ItemsTableSeeder::class, ]);
    }
}
