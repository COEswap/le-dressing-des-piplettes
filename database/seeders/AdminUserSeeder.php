<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@dressingdespiplettes.com',
            'password' => Hash::make('Admin123!'),
            'email_verified' => true,
            'is_admin' => true,
        ]);
    }
}
