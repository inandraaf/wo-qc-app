<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
            ],
            [
                'name' => 'Admin PPIC',
                'email' => 'ppic@example.com',
                'password' => Hash::make('password'),
                'role' => 'ppic',
            ],
            [
                'name' => 'Operator Produksi',
                'email' => 'operator@example.com',
                'password' => Hash::make('password'),
                'role' => 'operator',
            ],
            [
                'name' => 'QC Staff',
                'email' => 'qc@example.com',
                'password' => Hash::make('password'),
                'role' => 'qc',
            ],
            [
                'name' => 'Manager',
                'email' => 'manager@example.com',
                'password' => Hash::make('password'),
                'role' => 'manager',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}
