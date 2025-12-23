<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TahunAjaran>
 */
class TahunAjaranFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * `status` will be handled in the seeder to ensure only one is active.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $year = $this->faker->year();
        return [
            'nama_tahun' => $year . '/' . ($year + 1),
            'semester' => $this->faker->randomElement(['Ganjil', 'Genap']),
            'status' => 0, // Default to inactive
        ];
    }
}