<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@mmportal.local'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('admin123'),
                'role' => UserRole::Administrador,
            ],
        );
    }
}
