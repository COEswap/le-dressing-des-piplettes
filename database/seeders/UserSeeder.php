<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer quelques utilisateurs de test
        User::create([
            'name' => 'Marie Dupont',
            'email' => 'marie.dupont@example.com',
            'password' => Hash::make('password123'),
            'email_verified' => true,
            'is_admin' => false,
        ]);
        
        User::create([
            'name' => 'Jean Martin',
            'email' => 'jean.martin@example.com',
            'password' => Hash::make('password123'),
            'email_verified' => true,
            'is_admin' => false,
        ]);
        
        User::create([
            'name' => 'Sophie Bernard',
            'email' => 'sophie.bernard@example.com',
            'password' => Hash::make('password123'),
            'email_verified' => false,
            'is_admin' => false,
        ]);
        
        User::create([
            'name' => 'Pierre Dubois',
            'email' => 'pierre.dubois@example.com',
            'password' => Hash::make('password123'),
            'email_verified' => true,
            'is_admin' => true,
        ]);
    }
}
