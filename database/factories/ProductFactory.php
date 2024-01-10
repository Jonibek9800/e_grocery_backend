<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'poster_path' => fake()->imageUrl(),
            'name' => fake()->unique()->name(),
            'price' => fake()->randomDigitNotZero() * 10,
            'description' => fake()->text(),
            "category_id" => Category::all()->random()->id,
            // 'remember_token' => Str::random(10),
        ];
    }
}
