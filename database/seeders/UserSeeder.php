<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Admin Users
        |--------------------------------------------------------------------------
        */

        User::create([
            'name'       => 'Admin One',
            'email'      => 'admin1@studybuddy.com',
            'phone'      => '0770000001',
            'role'       => 'admin',
            'is_blocked' => false,
            'password'   => Hash::make('password'),
        ]);

        User::create([
            'name'       => 'Admin Two',
            'email'      => 'admin2@studybuddy.com',
            'phone'      => '0770000002',
            'role'       => 'admin',
            'is_blocked' => false,
            'password'   => Hash::make('password'),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Student Users
        |--------------------------------------------------------------------------
        */

        $students = [
            ['name' => 'Simon',  'email' => 'simon@test.com',  'phone' => '0771111111'],
            ['name' => 'Nethmi', 'email' => 'nethmi@test.com', 'phone' => '0772222222'],
            ['name' => 'Kamal',  'email' => 'kamal@test.com',  'phone' => '0773333333'],
            ['name' => 'Amal',   'email' => 'amal@test.com',   'phone' => '0774444444'],
            ['name' => 'Saman',  'email' => 'saman@test.com',  'phone' => '0775555555'],
        ];

        foreach ($students as $student) {
            User::create([
                'name'       => $student['name'],
                'email'      => $student['email'],
                'phone'      => $student['phone'],
                'role'       => 'student',
                'is_blocked' => false,
                'password'   => Hash::make('password'),
            ]);
        }
    }
}
