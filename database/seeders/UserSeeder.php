<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
       
        User::create([
            'name'     => 'Admin Bharata',
            'email'    => 'admin@bharataherbal.id',
            'password' => Hash::make('password'),
            'phone'    => '081234567890',
            'role'     => 'admin',
        ]);

        $customers = [
            ['name' => 'Budi Santoso',   'email' => 'budi@example.com',   'phone' => '081234567891'],
            ['name' => 'Siti Aminah',    'email' => 'siti@example.com',   'phone' => '081234567892'],
            ['name' => 'Agus Wijaya',    'email' => 'agus@example.com',   'phone' => '081234567893'],
            ['name' => 'Dewi Rahayu',    'email' => 'dewi@example.com',   'phone' => '081234567894'],
            ['name' => 'Eko Prasetyo',   'email' => 'eko@example.com',    'phone' => '081234567895'],
        ];

        foreach ($customers as $data) {
            User::create([...$data, 'password' => Hash::make('password'), 'role' => 'customer']);
        }
    }
}
