<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    { // Check if the role already exists
        if (!Role::where('name', 'admin')->exists()) {
            Role::create([
                'name' => 'admin',
            ]);
        }

        // Ajoutez d'autres rôles si nécessaire
        if (!Role::where('name', 'user')->exists()) {
            Role::create([
                'name' => 'user',
            ]);
        }
    }
}
