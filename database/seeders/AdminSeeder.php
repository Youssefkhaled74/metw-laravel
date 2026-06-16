<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admins = [
            [
                'username'  => 'lascoadmin',
                'email'     => 'admin@lasco.test',
                'phone'     => '201000000001',
                'is_active' => true,
                'password'  => Hash::make('Admin1234'),
                'photo'     => null,
                'country_code'=>'+20'
            ],
            [
                'username'  => 'admin',
                'email'     => 'admin2@lasco.test',
                'phone'     => '201000000002',
                'is_active' => true,
                // Plain password requested: 123456789
                'password'  => Hash::make('123456789'),
                'photo'     => null,
                'country_code'=>'+20'
            ],
        ];

        foreach ($admins as $data) {
            Admin::updateOrCreate(
                ['email' => $data['email']],
                $data + ['remember_token' => Str::random(60)]
            );
        }
    }
}
