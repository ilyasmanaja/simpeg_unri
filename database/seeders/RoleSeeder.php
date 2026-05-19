<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
            'id_role' => 1,
            'jenis_role' => 'Pimpinan'
        ]);

        Role::create([
            'id_role' => 2,
            'jenis_role' => 'Dosen'
        ]);

        Role::create([
            'id_role' => 3,
            'jenis_role' => 'Tendik'
        ]);

        Role::create([
            'id_role' => 4,
            'jenis_role' => 'Operator'
        ]);
    }
}
