<?php

namespace Database\Seeders;

use App\Enum\RepresentativeAccountType;
use App\Enum\RepresentativeStatus;
use App\Models\City;
use App\Models\Governorate;
use App\Models\MediaFile;
use App\Models\Representative;
use App\Models\RepresentativeServiceCity;
use App\Models\RepresentativeServiceGovernorate;
use App\Models\RepresentativeVehicle;
use App\Models\RepresentativeWorkType;
use App\Models\TransportType;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class RepresentativeSeeder extends Seeder
{
    public function run(): void
    {
        $representatives = [
            [
                'email' => 'rep.cairo.local@example.com',
                'username' => 'rep.cairo.local',
                'phone' => '0101000001',
                'second_phone' => '0101000002',
                'first_name' => 'Ahmed',
                'father_name' => 'Hassan',
                'last_name' => 'Elmasry',
                'account_type' => RepresentativeAccountType::FREE->value,
                'status' => RepresentativeStatus::PENDING_REVIEW->value,
                'warehouse_name' => null,
                'work_types' => ['local_delivery'],
                'governorates' => ['القاهرة'],
                'cities' => ['القاهرة'],
                'village_service' => true,
                'transport_code' => '07',
                'brand' => 'Toyota',
                'model' => 'Corolla',
                'plate_letters' => 'ABC',
                'plate_numbers' => '1234',
                'birth_date' => '1990-05-12',
                'gender' => 'male',
                'address' => 'Cairo - Nasr City - Main Street',
                'account_opened_at' => now()->subDays(30)->toDateString(),
                'documents' => ['personal_photo', 'national_id_front', 'national_id_back', 'vehicle_photo', 'driving_license_front', 'driving_license_back', 'vehicle_license_front', 'vehicle_license_back'],
            ],
            [
                'email' => 'rep.giza.warehouse@example.com',
                'username' => 'rep.giza.warehouse',
                'phone' => '0102000001',
                'second_phone' => '0102000002',
                'first_name' => 'Mona',
                'father_name' => 'Mahmoud',
                'last_name' => 'Sayed',
                'account_type' => RepresentativeAccountType::WAREHOUSE->value,
                'status' => RepresentativeStatus::APPROVED->value,
                'warehouse_name' => 'Cairo Main Warehouse',
                'work_types' => ['bus_driver'],
                'governorates' => ['القاهرة', 'الجيزة'],
                'cities' => [],
                'village_service' => false,
                'transport_code' => '05',
                'brand' => 'Mercedes',
                'model' => 'Sprinter',
                'plate_letters' => 'XYZ',
                'plate_numbers' => '5678',
                'birth_date' => '1987-09-20',
                'gender' => 'female',
                'address' => 'Giza - 6th of October - Near Mall of Egypt',
                'account_opened_at' => now()->subDays(25)->toDateString(),
                'documents' => ['personal_photo', 'national_id_front', 'national_id_back', 'vehicle_photo', 'driving_license_front', 'driving_license_back', 'vehicle_license_front', 'vehicle_license_back'],
            ],
            [
                'email' => 'rep.alex.inter@example.com',
                'username' => 'rep.alex.inter',
                'phone' => '0103000001',
                'second_phone' => '0103000002',
                'first_name' => 'Khaled',
                'father_name' => 'Salah',
                'last_name' => 'Omar',
                'account_type' => RepresentativeAccountType::FREE->value,
                'status' => RepresentativeStatus::REJECTED->value,
                'warehouse_name' => null,
                'work_types' => ['inter_governorate_shipping', 'local_delivery'],
                'governorates' => ['الإسكندرية', 'الجيزة'],
                'cities' => [],
                'village_service' => true,
                'transport_code' => '03',
                'brand' => 'Honda',
                'model' => 'CB150',
                'plate_letters' => 'MNO',
                'plate_numbers' => '2468',
                'birth_date' => '1992-01-14',
                'gender' => 'male',
                'address' => 'Alexandria - Smouha - Behind the hospital',
                'account_opened_at' => now()->subDays(20)->toDateString(),
                'documents' => ['personal_photo', 'national_id_front', 'national_id_back', 'vehicle_photo', 'driving_license_front', 'driving_license_back', 'vehicle_license_front', 'vehicle_license_back'],
            ],
            [
                'email' => 'rep.suspended@example.com',
                'username' => 'rep.suspended',
                'phone' => '0104000001',
                'second_phone' => '0104000002',
                'first_name' => 'Sara',
                'father_name' => 'Fouad',
                'last_name' => 'Nabil',
                'account_type' => RepresentativeAccountType::FREE->value,
                'status' => RepresentativeStatus::SUSPENDED->value,
                'warehouse_name' => null,
                'work_types' => ['inter_governorate_shipping'],
                'governorates' => ['الجيزة', 'القليوبية'],
                'cities' => [],
                'village_service' => false,
                'transport_code' => '10',
                'brand' => 'Mitsubishi',
                'model' => 'Canter',
                'plate_letters' => 'GHI',
                'plate_numbers' => '9090',
                'birth_date' => '1995-11-03',
                'gender' => 'female',
                'address' => 'Qalyubia - Kharadah - Transport Street',
                'account_opened_at' => now()->subDays(15)->toDateString(),
                'documents' => ['personal_photo', 'national_id_front', 'national_id_back', 'vehicle_photo', 'driving_license_front', 'driving_license_back', 'vehicle_license_front', 'vehicle_license_back'],
            ],
        ];

        foreach ($representatives as $item) {
            $user = User::withTrashed()->updateOrCreate(
                ['email' => $item['email']],
                [
                    'username' => $item['username'],
                    'phone' => $item['phone'],
                    'country_code' => '+20',
                    'password' => 'password',
                    'default_lang' => 'ar',
                    'default_shipment_lang' => 'ar',
                    'notifications_enabled' => true,
                    'enable_shipment_notifications' => true,
                    'email_verified_at' => now()->subDays(30),
                    'phone_verified_at' => now()->subDays(30),
                    'image' => null,
                ]
            );

            if ($user->trashed()) {
                $user->restore();
            }

            $warehouse = null;
            if (! empty($item['warehouse_name'])) {
                $warehouse = Warehouse::withoutGlobalScopes()->where('name', $item['warehouse_name'])->first();
            }

            $representative = Representative::withTrashed()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'warehouse_id' => $warehouse?->id,
                    'first_name' => $item['first_name'],
                    'father_name' => $item['father_name'],
                    'last_name' => $item['last_name'],
                    'account_opened_at' => $item['account_opened_at'],
                    'account_type' => $item['account_type'],
                    'status' => $item['status'],
                    'phone' => $item['phone'],
                    'second_phone' => $item['second_phone'],
                    'birth_date' => $item['birth_date'],
                    'gender' => $item['gender'],
                    'address' => $item['address'],
                    'village_service' => $item['village_service'],
                    'notes' => 'Seeded for dashboard testing.',
                    'rejection_reason' => $item['status'] === RepresentativeStatus::REJECTED->value ? 'Seeded rejection reason for dashboard testing.' : null,
                    'submitted_at' => now()->subDays(10),
                    'reviewed_at' => in_array($item['status'], [
                        RepresentativeStatus::PENDING_REVIEW->value,
                        RepresentativeStatus::APPROVED->value,
                        RepresentativeStatus::REJECTED->value,
                        RepresentativeStatus::SUSPENDED->value,
                    ], true) ? now()->subDays(5) : null,
                    'approved_at' => $item['status'] === RepresentativeStatus::APPROVED->value ? now()->subDays(4) : null,
                    'suspended_at' => $item['status'] === RepresentativeStatus::SUSPENDED->value ? now()->subDays(2) : null,
                    'is_active' => ! in_array($item['status'], [
                        RepresentativeStatus::SUSPENDED->value,
                        RepresentativeStatus::REJECTED->value,
                    ], true),
                    'metadata' => ['seeded' => true],
                ]
            );

            if ($representative->trashed()) {
                $representative->restore();
            }

            RepresentativeWorkType::where('representative_id', $representative->id)->delete();
            foreach ($item['work_types'] as $workType) {
                RepresentativeWorkType::create([
                    'representative_id' => $representative->id,
                    'work_type' => $workType,
                ]);
            }

            RepresentativeServiceGovernorate::where('representative_id', $representative->id)->delete();
            foreach ($item['governorates'] as $governorateName) {
                $governorate = $this->findGovernorate($governorateName);

                if ($governorate) {
                    RepresentativeServiceGovernorate::create([
                        'representative_id' => $representative->id,
                        'governorate_id' => $governorate->id,
                    ]);
                }
            }

            RepresentativeServiceCity::where('representative_id', $representative->id)->delete();
            foreach ($item['cities'] as $cityName) {
                $city = $this->findCity($cityName);

                if ($city) {
                    RepresentativeServiceCity::create([
                        'representative_id' => $representative->id,
                        'city_id' => $city->id,
                    ]);
                }
            }

            $transportType = TransportType::withoutGlobalScopes()->where('code', $item['transport_code'])->first()
                ?? TransportType::withoutGlobalScopes()->first();

            if ($transportType) {
                $vehicle = RepresentativeVehicle::withTrashed()->updateOrCreate(
                    ['representative_id' => $representative->id],
                    [
                        'transport_type_id' => $transportType->id,
                        'registration_plate_letters' => $item['plate_letters'],
                        'registration_plate_numbers' => $item['plate_numbers'],
                        'registration_number' => 'REG-' . $representative->id,
                        'license_number' => 'LIC-' . str_pad((string) $representative->id, 5, '0', STR_PAD_LEFT),
                        'brand' => $item['brand'],
                        'model' => $item['model'],
                        'color' => 'White',
                        'manufacture_year' => (int) now()->year - 3,
                        'max_weight' => $transportType->max_weight,
                        'max_volume' => $transportType->max_volume,
                        'is_active' => true,
                        'notes' => 'Seeded vehicle for dashboard testing.',
                        'metadata' => ['seeded' => true],
                    ]
                );

                if ($vehicle->trashed()) {
                    $vehicle->restore();
                }
            }

            $this->seedDocuments($representative, $item['documents']);
        }
    }

    private function findGovernorate(string $value): ?Governorate
    {
        return Governorate::withoutGlobalScopes()
            ->where(function ($query) use ($value) {
                $query->where('name_ar', $value);
            })
            ->first();
    }

    private function findCity(string $value): ?City
    {
        return City::withoutGlobalScopes()
            ->where(function ($query) use ($value) {
                $query->where('name_ar', $value)->orWhere('name_en', $value);
            })
            ->first();
    }

    private function seedDocuments(Representative $representative, array $documentTypes): void
    {
        $directory = 'storage/representatives/documents/' . $representative->id;
        File::ensureDirectoryExists(public_path($directory));

        foreach ($documentTypes as $index => $documentType) {
            $filename = Str::slug($documentType) . '.svg';
            $relativePath = $directory . '/' . $filename;
            File::put(public_path($relativePath), $this->placeholderSvg($representative->account_number, $documentType));

            $mediaFile = MediaFile::withTrashed()->updateOrCreate(
                [
                    'mediable_type' => Representative::class,
                    'mediable_id' => $representative->id,
                    'collection_name' => 'representative_documents',
                    'document_type' => $documentType,
                ],
                [
                    'disk' => 'public',
                    'directory' => $directory,
                    'filename' => $filename,
                    'original_name' => Str::title(str_replace('_', ' ', $documentType)) . '.svg',
                    'extension' => 'svg',
                    'mime_type' => 'image/svg+xml',
                    'size' => File::size(public_path($relativePath)),
                    'url' => asset($relativePath),
                    'title' => Str::title(str_replace('_', ' ', $documentType)),
                    'sort_order' => $index,
                    'is_primary' => $index === 0,
                    'metadata' => ['seeded' => true],
                ]
            );

            if ($mediaFile->trashed()) {
                $mediaFile->restore();
            }
        }
    }

    private function placeholderSvg(string $accountNumber, string $documentType): string
    {
        $label = strtoupper(str_replace('_', ' ', $documentType));

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="900" height="600" viewBox="0 0 900 600">
    <rect width="900" height="600" rx="32" fill="#f8f4ff"/>
    <rect x="50" y="50" width="800" height="500" rx="28" fill="#ffffff" stroke="#7c3aed" stroke-width="6" stroke-dasharray="16 12"/>
    <text x="450" y="220" text-anchor="middle" font-size="42" fill="#7c3aed" font-family="Arial, sans-serif">{$label}</text>
    <text x="450" y="300" text-anchor="middle" font-size="30" fill="#6b7280" font-family="Arial, sans-serif">{$accountNumber}</text>
    <text x="450" y="360" text-anchor="middle" font-size="24" fill="#9ca3af" font-family="Arial, sans-serif">Seeded document preview</text>
</svg>
SVG;
    }
}
