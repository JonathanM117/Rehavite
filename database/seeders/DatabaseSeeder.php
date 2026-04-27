<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([RoleSeeder::class]);

        // Create admin user
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@rehavite.com',
            'password' => Hash::make('password'),
            'admin' => true,
        ]);

        $admin->assignRole('SuperAdmin');
    }
}
