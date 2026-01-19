<?php

namespace Database\Factories;

use App\Models\Spp;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Spp>
 */
class SppFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tahun' => fake()->numberBetween(2020, 2025),
            'nominal' => fake()->randomElement([150000, 200000, 250000, 300000, 350000]),
        ];
    }
}

