<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Pastikan tabel roles dan user_roles sudah ada
        if (Schema::hasTable('roles') && Schema::hasTable('user_roles')) {

            // 1. Isi tabel roles jika kosong
            if (DB::table('roles')->count() === 0) {
                DB::table('roles')->insert([
                    ['name' => 'admin', 'display_name' => 'Administrator'],
                    ['name' => 'guru', 'display_name' => 'Guru'],
                    ['name' => 'wali', 'display_name' => 'Wali Kelas'],
                ]);
            }

            // 2. Pindahkan data dari users.role ke user_roles
            if (Schema::hasColumn('users', 'role')) {
                $users = DB::table('users')->get();

                foreach ($users as $user) {
                    if ($user->role) {
                        $role = DB::table('roles')->where('name', $user->role)->first();
                        if ($role) {
                            DB::table('user_roles')->insert([
                                'user_id' => $user->id,
                                'role_id' => $role->id,
                            ]);
                        }
                    }
                }

                // 3. Hapus kolom role lama
                Schema::table('users', function (Blueprint $table) {
                    $table->dropColumn('role');
                });
            }
        }
    }

    public function down(): void
    {
        // Tambahkan kembali kolom role jika dibutuhkan
        if (!Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['admin', 'guru', 'wali'])->default('guru');
            });
        }

        // Kembalikan data role dasar dari relasi user_roles
        $users = DB::table('users')->get();
        foreach ($users as $user) {
            $firstRole = DB::table('user_roles')
                ->join('roles', 'user_roles.role_id', '=', 'roles.id')
                ->where('user_roles.user_id', $user->id)
                ->value('roles.name');

            if ($firstRole) {
                DB::table('users')->where('id', $user->id)->update(['role' => $firstRole]);
            }
        }
    }
};
