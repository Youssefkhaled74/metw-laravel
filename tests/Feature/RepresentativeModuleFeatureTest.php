<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Governorate;
use App\Models\Representative;
use App\Models\RepresentativeWorkTypeOption;
use App\Models\TransportType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RepresentativeModuleFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_representative_can_complete_profile_and_upload_documents(): void
    {
        File::ensureDirectoryExists(public_path('storage/representatives/documents'));

        $user = User::factory()->create([
            'email' => 'rep@example.com',
            'phone' => '01012345678',
        ]);

        RepresentativeWorkTypeOption::create([
            'code' => 'local_delivery',
            'name_en' => 'Local Delivery',
            'name_ar' => 'توصيل محلي',
            'description' => null,
            'sort_order' => 1,
            'is_exclusive' => false,
            'is_active' => true,
            'metadata' => null,
        ]);

        $governorate = Governorate::factory()->create();
        $city = City::factory()->create(['governorate_id' => $governorate->id]);
        $transportType = TransportType::factory()->create([
            'code' => '99',
            'requires_driving_license' => true,
            'requires_vehicle_license' => true,
            'category_1_available' => true,
            'category_2_available' => false,
            'category_3_available' => false,
        ]);

        Representative::factory()->create([
            'user_id' => $user->id,
            'phone' => '01012345678',
            'second_phone' => '01087654321',
            'first_name' => 'Ahmed',
            'father_name' => 'Mohamed',
            'last_name' => 'Ali',
            'birth_date' => '1990-01-01',
            'gender' => 'male',
            'address' => 'Cairo',
            'village_service' => true,
            'account_type' => 'free',
        ]);

        Sanctum::actingAs($user);

        $completeResponse = $this->postJson('/api/v1/representatives/complete', [
            'account_type' => 'free',
            'first_name' => 'Ahmed',
            'father_name' => 'Mohamed',
            'last_name' => 'Ali',
            'email' => 'rep@example.com',
            'phone' => '01012345678',
            'second_phone' => '01087654321',
            'birth_date' => '1990-01-01',
            'gender' => 'male',
            'address' => 'Cairo, Egypt',
            'village_service' => true,
            'work_types' => ['local_delivery'],
            'governorate_ids' => [$governorate->id],
            'city_ids' => [$city->id],
            'vehicle' => [
                'transport_type_id' => $transportType->id,
                'registration_plate_letters' => 'ABC',
                'registration_plate_numbers' => '1234',
                'brand' => 'Toyota',
                'model' => 'Hilux',
            ],
        ]);

        $completeResponse
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.representative.account_type', 'free')
            ->assertJsonPath('data.representative.vehicle.transport_type.code', '99');

        $uploadResponse = $this->post('/api/v1/representatives/documents', [
            'document_types' => [
                'personal_photo',
                'national_id_front',
                'national_id_back',
                'vehicle_photo',
                'driving_license_front',
                'driving_license_back',
                'vehicle_license_front',
                'vehicle_license_back',
            ],
            'documents' => [
                UploadedFile::fake()->image('personal.jpg'),
                UploadedFile::fake()->image('id-front.jpg'),
                UploadedFile::fake()->image('id-back.jpg'),
                UploadedFile::fake()->image('vehicle.jpg'),
                UploadedFile::fake()->image('dl-front.jpg'),
                UploadedFile::fake()->image('dl-back.jpg'),
                UploadedFile::fake()->image('vl-front.jpg'),
                UploadedFile::fake()->image('vl-back.jpg'),
            ],
        ]);

        $uploadResponse
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(8, 'data.documents');

        $representative = Representative::with(['mediaFiles', 'vehicle.transportType'])->where('user_id', $user->id)->firstOrFail();

        $this->assertSame('pending_review', $representative->status->value ?? $representative->status);
        $this->assertCount(8, $representative->mediaFiles->where('collection_name', 'representative_documents'));
        $this->assertSame('vehicle_license_back', $representative->mediaFiles->last()->document_type);
    }

    public function test_representative_transport_types_endpoint_exposes_category_flags(): void
    {
        TransportType::factory()->create([
            'code' => '55',
            'category_1_available' => true,
            'category_2_available' => true,
            'category_3_available' => false,
        ]);

        $response = $this->getJson('/api/v1/representatives/transport-types');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.transport_types.0.code', '55')
            ->assertJsonPath('data.transport_types.0.category_1_available', true);
    }
}
