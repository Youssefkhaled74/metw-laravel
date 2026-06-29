<?php

namespace Tests\Feature;

use App\Enum\AddressType;
use App\Enum\BusinessProfileStatus;
use App\Enum\RepresentativeStatus;
use App\Models\AccountProfile;
use App\Models\Address;
use App\Models\City;
use App\Models\Country;
use App\Models\Governorate;
use App\Models\Representative;
use App\Models\ShipmentContact;
use App\Models\ShipmentRequest;
use App\Models\State;
use App\Models\TransportType;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorBusinessProfile;
use App\Models\Warehouse;
use App\Models\WarehouseBusinessProfile;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class NewModulesFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_complete_account_profile(): void
    {
        $user = User::factory()->create();
        $attributes = AccountProfile::factory()->make([
            'profileable_type' => User::class,
            'profileable_id' => $user->id,
        ])->toArray();

        $user->accountProfile()->create($attributes);

        $this->assertDatabaseHas('account_profiles', [
            'profileable_type' => User::class,
            'profileable_id' => $user->id,
            'account_number' => $attributes['account_number'],
            'display_name' => $attributes['display_name'],
        ]);
    }

    public function test_city_must_belong_to_governorate(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $firstLocation = $this->createLocation();
        $secondLocation = $this->createLocation();

        $response = $this->postJson('/api/v1/shipment/contacts', [
            'type' => 'sender',
            'contact_number' => 'CNT-10001',
            'full_name' => 'Sender One',
            'primary_mobile' => '01000000001',
            'address' => [
                'label' => 'Home',
                'governorate_id' => $firstLocation['governorate']->id,
                'city_id' => $secondLocation['city']->id,
                'zone_id' => $secondLocation['zone']->id,
                'street_name' => 'Mismatch Street',
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonStructure(['data' => ['city_id']]);
    }

    public function test_representative_can_register_as_free_courier(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $location = $this->createLocation();
        $transportType = TransportType::factory()->create(['is_active' => true]);

        $response = $this->postJson('/api/v1/representatives/register', [
            'account_type' => 'free',
            'phone' => '01000000002',
            'work_types' => ['local_delivery'],
            'governorate_ids' => [$location['governorate']->id],
            'city_ids' => [$location['city']->id],
            'vehicle' => [
                'transport_type_id' => $transportType->id,
                'registration_number' => 'REG-1001',
                'license_number' => 'LIC-1001',
                'brand' => 'Toyota',
                'model' => 'Hiace',
                'max_weight' => 100,
                'max_volume' => 2,
            ],
        ]);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.representative.account_type', 'free')
            ->assertJsonPath('data.representative.status', 'pending_review')
            ->assertJsonPath('data.representative.work_types.0', 'local_delivery');

        $representative = Representative::firstOrFail();

        $this->assertDatabaseHas('representatives', [
            'id' => $representative->id,
            'user_id' => $user->id,
            'account_type' => 'free',
            'status' => RepresentativeStatus::PENDING_REVIEW->value,
        ]);
        $this->assertDatabaseHas('representative_work_types', [
            'representative_id' => $representative->id,
            'work_type' => 'local_delivery',
        ]);
        $this->assertDatabaseHas('representative_service_governorates', [
            'representative_id' => $representative->id,
            'governorate_id' => $location['governorate']->id,
        ]);
        $this->assertDatabaseHas('representative_service_cities', [
            'representative_id' => $representative->id,
            'city_id' => $location['city']->id,
        ]);
    }

    public function test_representative_can_register_as_warehouse_courier_only_with_warehouse_id(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $location = $this->createLocation();
        $transportType = TransportType::factory()->create(['is_active' => true]);
        $warehouse = Warehouse::factory()->create([
            'country_id' => $location['country']->id,
            'state_id' => $location['state']->id,
            'city_id' => $location['city']->id,
            'zone_id' => $location['zone']->id,
        ]);

        $invalidResponse = $this->postJson('/api/v1/representatives/register', [
            'account_type' => 'warehouse',
            'phone' => '01000000003',
            'work_types' => ['inter_governorate_shipping'],
            'governorate_ids' => [$location['governorate']->id],
            'vehicle' => [
                'transport_type_id' => $transportType->id,
                'registration_number' => 'REG-2001',
            ],
        ]);

        $invalidResponse->assertStatus(422)
            ->assertJsonStructure(['errors' => ['warehouse_id']]);

        $validResponse = $this->postJson('/api/v1/representatives/register', [
            'account_type' => 'warehouse',
            'warehouse_id' => $warehouse->id,
            'phone' => '01000000003',
            'work_types' => ['inter_governorate_shipping'],
            'governorate_ids' => [$location['governorate']->id],
            'vehicle' => [
                'transport_type_id' => $transportType->id,
                'registration_number' => 'REG-2002',
            ],
        ]);

        $validResponse->assertCreated()
            ->assertJsonPath('data.representative.account_type', 'warehouse');

        $this->assertDatabaseHas('representatives', [
            'user_id' => $user->id,
            'warehouse_id' => $warehouse->id,
            'account_type' => 'warehouse',
        ]);
    }

    public function test_representative_cannot_select_invalid_work_type_combination(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $location = $this->createLocation();
        $transportType = TransportType::factory()->create(['is_active' => true]);

        $response = $this->postJson('/api/v1/representatives/register', [
            'account_type' => 'free',
            'phone' => '01000000004',
            'work_types' => ['local_delivery', 'inter_governorate_bus_driver'],
            'governorate_ids' => [$location['governorate']->id],
            'city_ids' => [$location['city']->id],
            'vehicle' => [
                'transport_type_id' => $transportType->id,
                'registration_number' => 'REG-3001',
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['work_types']);
    }

    public function test_representative_can_upload_required_documents(): void
    {
        $user = User::factory()->create();
        $representative = Representative::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $response = $this->post('/api/v1/representatives/documents', [
            'collection_name' => 'identity_documents',
            'documents' => [
                UploadedFile::fake()->image('national-id.jpg'),
                UploadedFile::fake()->create('license.pdf', 50, 'application/pdf'),
            ],
            'titles' => ['National ID', 'Driver License'],
            'is_primary' => true,
        ]);

        $response->assertCreated()
            ->assertJsonPath('success', true);

        $this->assertDatabaseCount('media_files', 2);
        $this->assertDatabaseHas('media_files', [
            'mediable_type' => Representative::class,
            'mediable_id' => $representative->id,
            'collection_name' => 'identity_documents',
            'title' => 'National ID',
            'is_primary' => true,
        ]);
    }

    public function test_user_can_create_sender_and_receiver_contacts(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $senderLocation = $this->createLocation();
        $receiverLocation = $this->createLocation();

        $senderResponse = $this->postJson('/api/v1/shipment/contacts', [
            'type' => 'sender',
            'contact_number' => 'CNT-20001',
            'full_name' => 'Sender Contact',
            'primary_mobile' => '01000000005',
            'address' => [
                'label' => 'Sender Address',
                'governorate_id' => $senderLocation['governorate']->id,
                'city_id' => $senderLocation['city']->id,
                'zone_id' => $senderLocation['zone']->id,
                'street_name' => 'Street 1',
            ],
        ]);

        $receiverResponse = $this->postJson('/api/v1/shipment/contacts', [
            'type' => 'receiver',
            'contact_number' => 'CNT-20002',
            'full_name' => 'Receiver Contact',
            'primary_mobile' => '01000000006',
            'address' => [
                'label' => 'Receiver Address',
                'governorate_id' => $receiverLocation['governorate']->id,
                'city_id' => $receiverLocation['city']->id,
                'zone_id' => $receiverLocation['zone']->id,
                'street_name' => 'Street 2',
            ],
        ]);

        $senderResponse->assertCreated()
            ->assertJsonPath('data.contact.type', 'sender');
        $receiverResponse->assertCreated()
            ->assertJsonPath('data.contact.type', 'receiver');

        $contactsResponse = $this->getJson('/api/v1/shipment/contacts');
        $contactsResponse->assertOk()
            ->assertJsonCount(2, 'data.contacts');

        $this->assertDatabaseHas('addresses', [
            'addressable_type' => ShipmentContact::class,
            'type' => AddressType::SHIPMENT_SENDER->value,
        ]);
        $this->assertDatabaseHas('addresses', [
            'addressable_type' => ShipmentContact::class,
            'type' => AddressType::SHIPMENT_RECEIVER->value,
        ]);
    }

    public function test_user_can_create_shipment_request_with_multiple_packages(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $sender = ShipmentContact::factory()->create([
            'user_id' => $user->id,
            'type' => 'sender',
        ]);
        $receiver = ShipmentContact::factory()->create([
            'user_id' => $user->id,
            'type' => 'receiver',
        ]);

        Address::factory()->create([
            'addressable_type' => ShipmentContact::class,
            'addressable_id' => $sender->id,
            'type' => AddressType::SHIPMENT_SENDER->value,
            'is_primary' => true,
        ]);
        Address::factory()->create([
            'addressable_type' => ShipmentContact::class,
            'addressable_id' => $receiver->id,
            'type' => AddressType::SHIPMENT_RECEIVER->value,
            'is_primary' => true,
        ]);

        $createResponse = $this->postJson('/api/v1/shipment/requests', [
            'sender_contact_id' => $sender->id,
            'receiver_contact_id' => $receiver->id,
            'notes' => 'Handle carefully',
        ]);

        $createResponse->assertCreated()
            ->assertJsonPath('data.shipment_request.status', 'draft');

        $requestId = $createResponse->json('data.shipment_request.id');

        $this->post('/api/v1/shipment/requests/' . $requestId . '/packages', [
            'package_name' => 'Package One',
            'package_type' => 'box',
            'quantity' => 1,
            'weight' => 2.5,
            'images' => [UploadedFile::fake()->image('package-one.jpg')],
        ])->assertCreated();

        $this->post('/api/v1/shipment/requests/' . $requestId . '/packages', [
            'package_name' => 'Package Two',
            'package_type' => 'bag',
            'quantity' => 2,
            'weight' => 5,
        ])->assertCreated();

        $submitResponse = $this->postJson('/api/v1/shipment/requests/' . $requestId . '/submit');
        $submitResponse->assertOk()
            ->assertJsonPath('data.shipment_request.status', 'submitted')
            ->assertJsonPath('data.shipment_request.packages_count', 2);

        $this->assertDatabaseHas('shipment_requests', [
            'id' => $requestId,
            'status' => 'submitted',
        ]);
        $this->assertDatabaseCount('shipment_request_packages', 2);
        $this->assertDatabaseHas('media_files', [
            'mediable_type' => \App\Models\ShipmentRequestPackage::class,
            'collection_name' => 'shipment_request_package_images',
        ]);
    }

    public function test_vendor_can_complete_business_profile(): void
    {
        $vendor = Vendor::factory()->create();

        $response = $this
            ->actingAs($vendor, 'vendor')
            ->from('/vendor/profile')
            ->patch(route('vendor.business-profile.upsert'), [
                'legal_name' => 'Vendor Legal Name',
                'commercial_name' => 'Vendor Commercial Name',
                'tax_number' => 'TAX-12345',
                'commercial_register_number' => 'CR-12345',
                'contact_name' => 'Vendor Contact',
                'contact_phone' => '01000000007',
                'documents' => [
                    UploadedFile::fake()->create('vendor-doc.pdf', 50, 'application/pdf'),
                ],
            ]);

        $response->assertRedirect('/vendor/profile');

        $profile = VendorBusinessProfile::firstOrFail();

        $this->assertDatabaseHas('vendor_business_profiles', [
            'vendor_id' => $vendor->id,
            'status' => BusinessProfileStatus::PENDING_REVIEW->value,
            'legal_name' => 'Vendor Legal Name',
        ]);
        $this->assertDatabaseHas('media_files', [
            'mediable_type' => VendorBusinessProfile::class,
            'mediable_id' => $profile->id,
            'collection_name' => 'business_profile_documents',
        ]);
    }

    public function test_warehouse_can_complete_business_profile(): void
    {
        $warehouse = Warehouse::factory()->create();

        $response = $this
            ->withoutMiddleware()
            ->from('/admin/settings/warehouses')
            ->patch(route('admin.settings.warehouses.business-profile.upsert', $warehouse), [
                'legal_name' => 'Warehouse Legal Name',
                'commercial_name' => 'Warehouse Commercial Name',
                'tax_number' => 'TAX-54321',
                'commercial_register_number' => 'CR-54321',
                'manager_name' => 'Warehouse Manager',
                'manager_phone' => '01000000008',
                'documents' => [
                    UploadedFile::fake()->create('warehouse-doc.pdf', 50, 'application/pdf'),
                ],
            ]);

        $response->assertRedirect('/admin/settings/warehouses');

        $profile = WarehouseBusinessProfile::firstOrFail();

        $this->assertDatabaseHas('warehouse_business_profiles', [
            'warehouse_id' => $warehouse->id,
            'status' => BusinessProfileStatus::PENDING_REVIEW->value,
            'legal_name' => 'Warehouse Legal Name',
        ]);
        $this->assertDatabaseHas('media_files', [
            'mediable_type' => WarehouseBusinessProfile::class,
            'mediable_id' => $profile->id,
            'collection_name' => 'business_profile_documents',
        ]);
    }

    protected function createLocation(): array
    {
        $country = Country::factory()->create();
        $state = State::factory()->create(['country_id' => $country->id]);
        $governorate = Governorate::factory()->create();
        $city = City::factory()->create([
            'state_id' => $state->id,
            'governorate_id' => $governorate->id,
        ]);
        $governorate->update(['capital_city_id' => $city->id]);
        $zone = Zone::factory()->create(['city_id' => $city->id]);

        return compact('country', 'state', 'governorate', 'city', 'zone');
    }
}
