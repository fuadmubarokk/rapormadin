<?php

namespace Database\Factories;

use App\Models\GuruMapelKelas;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GuruMapelKelas>
 */
class GuruMapelKelasFactory extends Factory
{
    protected $model = GuruMapelKelas::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'guru_id' => User::factory()->guru(),
            'mapel_id' => Mapel::inRandomOrder()->first()->id,
            'kelas_id' => Kelas::inRandomOrder()->first()->id,
        ];
    }
}