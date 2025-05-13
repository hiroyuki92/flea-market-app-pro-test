<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Purchase;
use App\Models\User;
use App\Models\Item;

class PurchaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'item_id' => Item::factory(),
            'shipping_postal_code' => '123-4567',
            'shipping_address_line' => '東京都渋谷区',
            'shipping_building' => '〇〇マンション',
            'payment_method' => 'card',
            'buyer_rating' => $this->faker->numberBetween(1, 5),
            'seller_rating' => $this->faker->numberBetween(1, 5),
            'completed' => $this->faker->boolean(50),
        ];
    }
}
