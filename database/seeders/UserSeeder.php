<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        User::factory()->count(10)->create(['role_id' => $adminRole->id]);
        $userRole = Role::where('name', 'user')->first();
        User::factory()->count(10)->create(['role_id' => $userRole->id]);
    }
}
