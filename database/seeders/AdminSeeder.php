<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::updateOrCreate(
            ['email' => 'youssef@metwlogistic.com'],
            [
                'username' => 'youssef',
                'email' => 'youssef@metwlogistic.com',
                'country_code' => '+20',
                'password' => Hash::make('12345678'),
            ]
        );
    }
}
