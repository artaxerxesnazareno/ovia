<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Criar usuário admin
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@exemplo.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'birth_date' => '1990-01-01',
            'age' => 36,
            'gender' => 'male',
            'phone' => '+244 123 456 789',
            'favorite_activity' => 'Gosto de acompanhar o crescimento dos estudantes.',
            'email_verified_at' => now(),
        ]);

        // Criar usuários normais
        User::factory(10)->create([
            'role' => 'user',
        ]);
    }
}
