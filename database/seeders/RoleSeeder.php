<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'admin', 'display_name' => 'Administrator'],
            ['name' => 'guru', 'display_name' => 'Guru'],
            ['name' => 'wali', 'display_name' => 'Wali Kelas'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}