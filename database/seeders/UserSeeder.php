<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = [
            [               
                'employee_id'   => '1',
                'name'          => 'admin',
                'email'         => 'admin@gmail.com',
                'password'      => 'admin123',
            ]
        ];
        foreach ($user as $user) {
            \App\Models\User::create($user);
        }
    }
}
