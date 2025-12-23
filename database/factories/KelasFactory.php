<?php

namespace Database\Factories;

use App\Models\Angkatan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Kelas>
 */
class KelasFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_kelas' => $this->faker->randomElement(['I', 'II', 'III', 'IV', 'V']) . ' ' . $this->faker->randomLetter(),
            'wali_id' => User::factory()->wali(),
            'tingkat' => $this->faker->randomElement(['Ula', 'Wustho', 'Ulya']),
            'angkatan_id' => Angkatan::factory(),
        ];
    }
}