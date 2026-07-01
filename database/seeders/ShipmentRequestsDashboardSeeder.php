<?php

namespace Database\Seeders;

use App\Enum\AddressType;
use App\Enum\ShipmentContactType;
use App\Enum\ShipmentRequestStatus;
use App\Models\Address;
use App\Models\City;
use App\Models\Governorate;
use App\Models\ShipmentContact;
use App\Models\ShipmentRequest;
use App\Models\ShipmentRequestPackage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ShipmentRequestsDashboardSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $sampleRequests = [
                [
                    'request_number' => 'SHR-00010001',
                    'customer' => [
                        'name' => 'Ahmed Hassan',
                        'username' => 'ahmed.hassan',
                        'email' => 'ahmed.hassan@example.com',
                        'phone' => '01120000001',
                        'sender_contact' => [
                            'contact_number' => 'CNT-10001-S',
                            'full_name' => 'Ahmed Hassan',
                            'primary_mobile' => '01120000001',
                            'secondary_mobile' => '01090000001',
                        ],
                        'receiver_contact' => [
                            'contact_number' => 'CNT-10001-R',
                            'full_name' => 'Mona Ibrahim',
                            'primary_mobile' => '01120000011',
                            'secondary_mobile' => null,
                        ],
                        'sender_location' => ['governorate' => ['القاهرة', 'Cairo'], 'city' => ['القاهرة', 'Cairo'], 'line' => '12 El Thawra Street', 'landmark' => 'Near City Stars'],
                        'receiver_location' => ['governorate' => ['الجيزة', 'Giza'], 'city' => ['الدقي', 'Dokki'], 'line' => '5 Tahrir Road', 'landmark' => 'Opposite the museum'],
                        'status' => ShipmentRequestStatus::SUBMITTED->value,
                        'submitted_at' => now()->subDays(2),
                        'notes' => 'Customer requested afternoon pickup and careful handling.',
                        'packages' => [
                            ['name' => 'Document Envelope', 'type' => 'document', 'qty' => 1, 'weight' => 0.40, 'length' => 32, 'width' => 24, 'height' => 3, 'value' => 850, 'notes' => 'Urgent legal documents', 'reason' => 'Handle as confidential'],
                        ],
                    ],
                ],
                [
                    'request_number' => 'SHR-00010002',
                    'customer' => [
                        'name' => 'Sara Mohamed',
                        'username' => 'sara.mohamed',
                        'email' => 'sara.mohamed@example.com',
                        'phone' => '01120000002',
                        'sender_contact' => [
                            'contact_number' => 'CNT-10002-S',
                            'full_name' => 'Sara Mohamed',
                            'primary_mobile' => '01120000002',
                            'secondary_mobile' => null,
                        ],
                        'receiver_contact' => [
                            'contact_number' => 'CNT-10002-R',
                            'full_name' => 'Youssef Ali',
                            'primary_mobile' => '01120000012',
                            'secondary_mobile' => '01090000012',
                        ],
                        'sender_location' => ['governorate' => ['الإسكندرية', 'Alexandria'], 'city' => ['سموحة', 'Smouha'], 'line' => '88 Fouad Street', 'landmark' => 'Near Carrefour'],
                        'receiver_location' => ['governorate' => ['القليوبية', 'Qalyubia'], 'city' => ['بنها', 'Banha'], 'line' => '14 Bank Street', 'landmark' => 'Beside the post office'],
                        'status' => ShipmentRequestStatus::SUBMITTED->value,
                        'submitted_at' => now()->subDays(1),
                        'notes' => 'Fragile package; please use extra padding.',
                        'packages' => [
                            ['name' => 'Glassware Box', 'type' => 'fragile', 'qty' => 2, 'weight' => 4.80, 'length' => 45, 'width' => 35, 'height' => 30, 'value' => 4200, 'notes' => 'Fragile kitchen items', 'reason' => 'Glass items inside'],
                            ['name' => 'Accessory Bag', 'type' => 'bag', 'qty' => 1, 'weight' => 1.20, 'length' => 30, 'width' => 20, 'height' => 15, 'value' => 900, 'notes' => null, 'reason' => 'Customer asked for a quick delivery'],
                        ],
                    ],
                ],
                [
                    'request_number' => 'SHR-00010003',
                    'customer' => [
                        'name' => 'Omar Adel',
                        'username' => 'omar.adel',
                        'email' => 'omar.adel@example.com',
                        'phone' => '01120000003',
                        'sender_contact' => [
                            'contact_number' => 'CNT-10003-S',
                            'full_name' => 'Omar Adel',
                            'primary_mobile' => '01120000003',
                            'secondary_mobile' => '01090000003',
                        ],
                        'receiver_contact' => [
                            'contact_number' => 'CNT-10003-R',
                            'full_name' => 'Nour Khaled',
                            'primary_mobile' => '01120000013',
                            'secondary_mobile' => null,
                        ],
                        'sender_location' => ['governorate' => ['الجيزة', 'Giza'], 'city' => ['6 أكتوبر', '6th of October City'], 'line' => '7 Industrial Zone Road', 'landmark' => 'Warehouse district'],
                        'receiver_location' => ['governorate' => ['الشرقية', 'Sharqia'], 'city' => ['الزقازيق', 'Zagazig'], 'line' => '21 Garden Street', 'landmark' => 'Near university gate'],
                        'status' => ShipmentRequestStatus::DRAFT->value,
                        'submitted_at' => null,
                        'notes' => 'Waiting for final package confirmation from the sender.',
                        'packages' => [
                            ['name' => 'Electronics Carton', 'type' => 'box', 'qty' => 1, 'weight' => 6.50, 'length' => 50, 'width' => 40, 'height' => 28, 'value' => 7600, 'notes' => 'Contains a monitor and accessories', 'reason' => null],
                        ],
                    ],
                ],
                [
                    'request_number' => 'SHR-00010004',
                    'customer' => [
                        'name' => 'Nada Samir',
                        'username' => 'nada.samir',
                        'email' => 'nada.samir@example.com',
                        'phone' => '01120000004',
                        'sender_contact' => [
                            'contact_number' => 'CNT-10004-S',
                            'full_name' => 'Nada Samir',
                            'primary_mobile' => '01120000004',
                            'secondary_mobile' => null,
                        ],
                        'receiver_contact' => [
                            'contact_number' => 'CNT-10004-R',
                            'full_name' => 'Karim Fathy',
                            'primary_mobile' => '01120000014',
                            'secondary_mobile' => null,
                        ],
                        'sender_location' => ['governorate' => ['الغربية', 'Gharbia'], 'city' => ['طنطا', 'Tanta'], 'line' => '44 School Road', 'landmark' => 'Next to the hospital'],
                        'receiver_location' => ['governorate' => ['الدقهلية', 'Dakahlia'], 'city' => ['المنصورة', 'Mansoura'], 'line' => '9 Nile Corniche', 'landmark' => 'Near the bridge'],
                        'status' => ShipmentRequestStatus::SUBMITTED->value,
                        'submitted_at' => now()->subHours(10),
                        'notes' => 'High-value shipment, requires signature on delivery.',
                        'packages' => [
                            ['name' => 'Valuable Parcel', 'type' => 'box', 'qty' => 1, 'weight' => 2.30, 'length' => 28, 'width' => 20, 'height' => 14, 'value' => 15000, 'notes' => 'Signature required', 'reason' => 'Customer explicitly requested insured handling'],
                        ],
                    ],
                ],
            ];

            foreach ($sampleRequests as $sampleRequest) {
                $customerData = $sampleRequest['customer'];
                $user = User::updateOrCreate(
                    ['email' => $customerData['email']],
                    [
                        'username' => $customerData['username'],
                        'phone' => $customerData['phone'],
                        'country_code' => '+20',
                        'password' => bcrypt('password'),
                        'notifications_enabled' => true,
                        'email_verified_at' => now(),
                        'phone_verified_at' => now(),
                        'mobile_primary_verified_at' => now(),
                        'default_shipment_lang' => 'ar',
                        'remember_token' => Str::random(60),
                    ]
                );

                $senderContact = ShipmentContact::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'contact_number' => $customerData['sender_contact']['contact_number'],
                    ],
                    array_merge(
                        ['type' => ShipmentContactType::SENDER->value, 'user_id' => $user->id],
                        $customerData['sender_contact']
                    )
                );

                $receiverContact = ShipmentContact::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'contact_number' => $customerData['receiver_contact']['contact_number'],
                    ],
                    array_merge(
                        ['type' => ShipmentContactType::RECEIVER->value, 'user_id' => $user->id],
                        $customerData['receiver_contact']
                    )
                );

                $senderLocation = $this->resolveLocation($customerData['sender_location']);
                $receiverLocation = $this->resolveLocation($customerData['receiver_location']);

                $this->syncPrimaryAddress($senderContact, $senderLocation, $customerData['sender_location'], AddressType::SHIPMENT_SENDER->value);
                $this->syncPrimaryAddress($receiverContact, $receiverLocation, $customerData['receiver_location'], AddressType::SHIPMENT_RECEIVER->value);

                $shipmentRequest = ShipmentRequest::updateOrCreate(
                    ['request_number' => $sampleRequest['request_number']],
                    [
                        'user_id' => $user->id,
                        'sender_contact_id' => $senderContact->id,
                        'receiver_contact_id' => $receiverContact->id,
                        'status' => $sampleRequest['customer']['status'],
                        'notes' => $sampleRequest['customer']['notes'],
                        'submitted_at' => $sampleRequest['customer']['submitted_at'],
                        'metadata' => [
                            'seeded' => true,
                            'source' => 'ShipmentRequestsDashboardSeeder',
                        ],
                    ]
                );

                foreach ($sampleRequest['customer']['packages'] as $packageData) {
                    ShipmentRequestPackage::updateOrCreate(
                        [
                            'shipment_request_id' => $shipmentRequest->id,
                            'package_name' => $packageData['name'],
                        ],
                        [
                            'package_type' => $packageData['type'],
                            'quantity' => $packageData['qty'],
                            'weight' => $packageData['weight'],
                            'length' => $packageData['length'],
                            'width' => $packageData['width'],
                            'height' => $packageData['height'],
                            'declared_value' => $packageData['value'],
                            'notes' => $packageData['notes'],
                            'metadata' => [
                                'reason' => $packageData['reason'],
                                'seeded' => true,
                            ],
                        ]
                    );
                }
            }
        });
    }

    protected function resolveLocation(array $location): array
    {
        $governorate = $this->findGovernorate($location['governorate'] ?? []);
        $city = $this->findCity($location['city'] ?? [], $governorate?->id);

        return [
            'governorate_id' => $governorate?->id,
            'city_id' => $city?->id,
        ];
    }

    protected function findGovernorate(array $names): ?Governorate
    {
        foreach (Arr::wrap($names) as $name) {
            $governorate = Governorate::withoutGlobalScope('active')
                ->where('name_ar', $name)
                ->first();

            if ($governorate) {
                return $governorate;
            }
        }

        return Governorate::query()->first();
    }

    protected function findCity(array $names, ?int $governorateId = null): ?City
    {
        foreach (Arr::wrap($names) as $name) {
            $cityQuery = City::withoutGlobalScope('active')
                ->where(function ($query) use ($name) {
                    $query->where('name_ar', $name)
                        ->orWhere('name_en', $name);
                });

            if ($governorateId) {
                $cityQuery->where('governorate_id', $governorateId);
            }

            $city = $cityQuery->first();

            if ($city) {
                return $city;
            }
        }

        if ($governorateId) {
            return City::withoutGlobalScope('active')->where('governorate_id', $governorateId)->first();
        }

        return City::withoutGlobalScope('active')->first();
    }

    protected function syncPrimaryAddress(ShipmentContact $contact, array $location, array $rawLocation, string $type): void
    {
        Address::updateOrCreate(
            [
                'addressable_type' => ShipmentContact::class,
                'addressable_id' => $contact->id,
                'label' => 'primary',
            ],
            [
                'type' => $type,
                'contact_name' => $contact->full_name,
                'contact_phone' => $contact->primary_mobile,
                'governorate_id' => $location['governorate_id'] ?? null,
                'city_id' => $location['city_id'] ?? null,
                'address_line_1' => $rawLocation['line'] ?? null,
                'landmark' => $rawLocation['landmark'] ?? null,
                'is_primary' => true,
                'is_active' => true,
                'metadata' => [
                    'seeded' => true,
                ],
            ]
        );

        $contact->foundationAddresses()
            ->where('label', '!=', 'primary')
            ->update(['is_primary' => false]);
    }
}
