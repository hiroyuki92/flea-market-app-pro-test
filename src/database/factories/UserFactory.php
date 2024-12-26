<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $defaultImagePath = base_path('tests/fixtures/images/default.jpg');
        $destinationPath = storage_path('app/public/profile_images/default.jpg');
        if (!file_exists(dirname($destinationPath))) {
            mkdir(dirname($destinationPath), 0755, true);
        }

        if (!file_exists($destinationPath) && file_exists($defaultImagePath)) {
        copy($defaultImagePath, $destinationPath);
        }
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'profile_image' => 'default.jpg',
            'postal_code' => $this->faker->numerify('###-####'),
            'address_line' => $this->faker->prefecture . $this->faker->city . $this->faker->streetAddress,
            'building' => $this->faker->secondaryAddress(),
            'role' => 'user',
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }

    /**
     * Define an admin state.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function admin()
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'admin', // 管理者ユーザー
                'email' => 'admin@example.com',
                'name' => '管理者ユーザー',
            ];
        });
    }

    /**
     * Define a regular user state.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function regularUser()
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'user', // 一般ユーザー
                'email' => 'user@example.com',
                'name' => '一般ユーザー',
            ];
        });
    }
}
