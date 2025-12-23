<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mapel>
 */
class MapelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_mapel' => $this->faker->randomElement(['Al-Quran', 'Hadits', 'Aqidah', 'Akhlak', 'Fiqih', 'Sejarah Kebudayaan Islam', 'Bahasa Arab', 'Bahasa Inggris']),
            'nama_mapel_ar' => 'القرآن الكريم', // Anda bisa mengganti ini dengan data yang lebih relevan
            'kategori' => $this->faker->randomElement(['tulis', 'non-tulis']),
            'kkm' => $this->faker->numberBetween(50, 70),
        ];
    }
}