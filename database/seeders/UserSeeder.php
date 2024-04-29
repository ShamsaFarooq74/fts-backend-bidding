<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
            'created_at' => now(),
            'updated_at' => now()

        ])->assignRole('Admin');

        User::create([
            'name' => 'customer',
            'email' => 'customer@gmail.com',
            'password' => bcrypt('password'),
            'created_at' => now(),
            'updated_at' => now()

        ])->assignRole('Customer');
        User::create([
            'name' => 'company',
            'email' => 'company@gmail.com',
            'password' => bcrypt('password'),
            'created_at' => now(),
            'updated_at' => now()

        ])->assignRole('Company');
    }
}
