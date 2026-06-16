<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'username'              => 'user',
                'email'                 => 'user@example.com',
                'phone'                 => '01111111111',
                'country_code'          => '+20',
                'password'              => Hash::make('User1234'),
                'image'                 => null,
                'notifications_enabled' => true,
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['phone' => $data['phone']],
                $data + ['remember_token' => Str::random(60)]
            );
        }
    }
}
