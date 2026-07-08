<?php

namespace Database\Seeders;

use App\Enum\RepresentativeAccountType;
use App\Enum\RepresentativeStatus;
use App\Models\City;
use App\Models\Governorate;
use App\Models\OtpCode;
use App\Models\Representative;
use App\Models\RepresentativeServiceCity;
use App\Models\RepresentativeServiceGovernorate;
use App\Models\RepresentativeVehicle;
use App\Models\RepresentativeWorkType;
use App\Models\RepresentativeWorkTypeOption;
use App\Models\TransportType;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MetwGoTestSeeder extends Seeder
{
    public function run(): void
    {
        $testPhone = '01000000000';
        $testPassword = '12345678';

        $user = User::withTrashed()->updateOrCreate(
            ['phone' => $testPhone],
            [
                'username' => 'MetwGo Test Captain',
                'email' => 'captain.test@metwgo.com',
                'country_code' => 'EG',
                'password' => $testPassword,
                'default_lang' => 'ar',
                'notifications_enabled' => true,
                'phone_verified_at' => now()->subDays(1),
                'email_verified_at' => now()->subDays(1),
            ]
        );

        if ($user->trashed()) {
            $user->restore();
        }

        $governorate = Governorate::withoutGlobalScopes()->first();
        $city = null;
        if ($governorate) {
            $city = City::withoutGlobalScopes()->where('governorate_id', $governorate->id)->first();
        }

        $transportType = TransportType::withoutGlobalScopes()->where('code', '01')->first()
            ?? TransportType::withoutGlobalScopes()->first();

        $representative = Representative::withTrashed()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'first_name' => 'MetwGo',
                'father_name' => 'Test',
                'last_name' => 'Captain',
                'account_type' => RepresentativeAccountType::FREE->value,
                'status' => RepresentativeStatus::APPROVED->value,
                'phone' => $testPhone,
                'second_phone' => '01000000001',
                'birth_date' => '1995-01-01',
                'gender' => 'male',
                'address' => 'Test Address - Cairo',
                'village_service' => true,
                'submitted_at' => now()->subDays(10),
                'reviewed_at' => now()->subDays(5),
                'approved_at' => now()->subDays(4),
                'is_active' => true,
                'notes' => 'MetwGo test courier',
                'metadata' => [
                    'availability_status' => 'offline',
                    'today_earnings' => 245.50,
                    'completed_orders_today' => 14,
                    'planned_orders_today' => 18,
                ],
            ]
        );

        if ($representative->trashed()) {
            $representative->restore();
        }

        RepresentativeWorkType::where('representative_id', $representative->id)->delete();
        $workTypeCodes = RepresentativeWorkTypeOption::activeCodes();
        $selectedWorkTypes = array_slice($workTypeCodes, 0, 2) ?: ['local_delivery', 'inter_governorate_shipping'];

        foreach ($selectedWorkTypes as $code) {
            RepresentativeWorkType::create([
                'representative_id' => $representative->id,
                'work_type' => $code,
            ]);
        }

        if ($governorate) {
            RepresentativeServiceGovernorate::updateOrCreate(
                ['representative_id' => $representative->id, 'governorate_id' => $governorate->id],
                ['representative_id' => $representative->id, 'governorate_id' => $governorate->id]
            );
        }

        if ($city) {
            RepresentativeServiceCity::updateOrCreate(
                ['representative_id' => $representative->id, 'city_id' => $city->id],
                ['representative_id' => $representative->id, 'city_id' => $city->id]
            );
        }

        if ($transportType) {
            RepresentativeVehicle::withTrashed()->updateOrCreate(
                ['representative_id' => $representative->id],
                [
                    'transport_type_id' => $transportType->id,
                    'registration_number' => 'METWGO-001',
                    'max_weight' => $transportType->max_weight,
                    'max_volume' => $transportType->max_volume,
                    'is_active' => true,
                ]
            )->restore();
        }

        Wallet::updateOrCreate(
            ['user_id' => $user->id],
            [
                'balance' => 1240.00,
                'currency' => 'EGP',
                'is_active' => true,
            ]
        );

        OtpCode::create([
            'user_id' => $user->id,
            'code' => '1111',
            'purpose' => \App\Enum\OtpPurpose::PASSWORD_RESET,
            'is_used' => false,
            'expires_at' => Carbon::now()->addMinutes(5),
        ]);

        OtpCode::create([
            'user_id' => $user->id,
            'code' => '1111',
            'purpose' => \App\Enum\OtpPurpose::REGISTER,
            'is_used' => false,
            'expires_at' => Carbon::now()->addMinutes(5),
        ]);

        $this->command->info('MetwGo test data seeded successfully!');
        $this->command->info("Phone: {$testPhone}");
        $this->command->info("Password: {$testPassword}");
        $this->command->info("Representative ID: {$representative->id}");
    }
}
