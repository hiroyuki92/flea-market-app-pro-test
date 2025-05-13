<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */

    protected $model = Item::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->words(2, true),
            'brand' => $this->faker->company(),
            'price' => $this->faker->numberBetween(1000, 100000),
            'description' => $this->faker->text(200),
            'condition' => $this->faker->numberBetween(1, 5),
            'image_url' => 'test-image.jpg',
            'sold_out' => false,
            'in_transaction' => false,
        ];
    }

    // カテゴリーを付与するステート
    public function withCategories($categories = null)
    {
        return $this->afterCreating(function (Item $item) use ($categories) {
            if ($categories === null) {
                $categories = Category::inRandomOrder()->limit(rand(1, 3))->pluck('id');
            }
            $item->categories()->attach($categories);
        });
    }
}
