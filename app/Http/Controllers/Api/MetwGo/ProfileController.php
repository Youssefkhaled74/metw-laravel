<?php

namespace App\Http\Controllers\Api\MetwGo;

use App\Enum\RepresentativeDocumentType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\MetwGo\MetwGoDocumentsRequest;
use App\Http\Requests\Api\MetwGo\MetwGoRegisterStepOneRequest;
use App\Http\Requests\Api\MetwGo\MetwGoRegisterStepTwoRequest;
use App\Http\Requests\Api\MetwGo\MetwGoServiceAreasRequest;
use App\Http\Requests\Api\MetwGo\MetwGoVehicleRequest;
use App\Models\MediaFile;
use App\Models\Representative;
use App\Models\RepresentativeVehicle;
use App\Models\RepresentativeWorkType;
use App\Models\TransportType;
use App\Services\MetwGo\MetwGoCourierService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function __construct(
        protected MetwGoCourierService $metwGoCourierService
    ) {}

    public function show(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $representative = $this->metwGoCourierService->getRepresentativeForUser($user);

            $profile = $this->metwGoCourierService->formatRepresentativeProfile($representative);

            return responseJson(true, '', $profile, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return responseJson(false, 'لم يتم العثور على حساب مندوب.', null, 404);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function update(MetwGoRegisterStepOneRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            $validated = $request->validated();
            $representative = $this->metwGoCourierService->assertRepresentativeExists($user);

            DB::transaction(function () use ($user, $representative, $validated) {
                $user->update([
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'country_code' => 'EG',
                    'username' => $validated['first_name'] . ' ' . ($validated['father_name'] ?? '') . ' ' . ($validated['last_name'] ?? ''),
                ]);

                $representative->update([
                    'first_name' => $validated['first_name'],
                    'father_name' => $validated['father_name'] ?? null,
                    'last_name' => $validated['last_name'] ?? null,
                    'phone' => $validated['phone'],
                    'second_phone' => $validated['secondary_phone'] ?? null,
                    'birth_date' => $validated['birth_date'] ?? null,
                    'gender' => $validated['gender'] ?? null,
                    'address' => $validated['address_details'] ?? null,
                ]);
            });

            return responseJson(true, 'تم تحديث الملف الشخصي', null, 200);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function updateWorkInfo(MetwGoRegisterStepTwoRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            $validated = $request->validated();
            $representative = $this->metwGoCourierService->assertRepresentativeExists($user);

            DB::transaction(function () use ($representative, $validated) {
                $courierType = $this->metwGoCourierService->mapCourierType($validated['courier_type']);

                $representative->update([
                    'account_type' => $courierType,
                    'warehouse_id' => $validated['warehouse_id'] ?? null,
                ]);

                $representative->workTypes()->delete();
                $mappedWorkTypes = $this->metwGoCourierService->mapWorkTypes($validated['work_types']);

                foreach ($mappedWorkTypes as $workType) {
                    RepresentativeWorkType::create([
                        'representative_id' => $representative->id,
                        'work_type' => $workType,
                    ]);
                }
            });

            return responseJson(true, 'تم تحديث معلومات العمل', null, 200);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function updateTransport(MetwGoVehicleRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            $validated = $request->validated();
            $representative = $this->metwGoCourierService->assertRepresentativeExists($user);

            $transportType = TransportType::findOrFail($validated['transport_type_id']);

            DB::transaction(function () use ($representative, $validated, $transportType, $request) {
                RepresentativeVehicle::updateOrCreate(
                    ['representative_id' => $representative->id],
                    [
                        'transport_type_id' => $validated['transport_type_id'],
                        'registration_number' => $validated['plate_number'] ?? null,
                        'max_weight' => $transportType->max_weight,
                        'max_volume' => $transportType->max_volume,
                    ]
                );

                if ($request->hasFile('vehicle_image')) {
                    $path = uploadImage($request, 'vehicle_image', 'storage/metwgo/vehicles');
                    if ($path) {
                        $this->metwGoCourierService->storeVehicleImage($representative, $path);
                    }
                }
            });

            return responseJson(true, 'تم تحديث معلومات المركبة', null, 200);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function updateServiceAreas(MetwGoServiceAreasRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            $validated = $request->validated();
            $representative = $this->metwGoCourierService->assertRepresentativeExists($user);

            $mappedWorkTypes = $this->metwGoCourierService->unmapWorkTypes($representative->workTypes);

            $this->metwGoCourierService->validateStepFourRules(
                $mappedWorkTypes,
                $validated['governorate_ids'],
                $validated['city_ids'] ?? []
            );

            DB::transaction(function () use ($representative, $validated) {
                $representative->governorates()->sync($validated['governorate_ids']);

                if (!empty($validated['city_ids'])) {
                    $representative->cities()->sync($validated['city_ids']);
                } else {
                    $representative->cities()->sync([]);
                }

                $representative->update([
                    'village_service' => $validated['villages_service_enabled'],
                ]);
            });

            return responseJson(true, 'تم تحديث مناطق الخدمة', null, 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return responseJson(false, $e->getMessage(), $e->errors(), 422);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function uploadDocuments(MetwGoDocumentsRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            $representative = $this->metwGoCourierService->assertRepresentativeExists($user);

            DB::transaction(function () use ($representative, $request) {
                $documentMapping = [
                    'profile_photo' => RepresentativeDocumentType::PERSONAL_PHOTO,
                    'national_id_front' => RepresentativeDocumentType::NATIONAL_ID_FRONT,
                    'national_id_back' => RepresentativeDocumentType::NATIONAL_ID_BACK,
                    'driving_license_front' => RepresentativeDocumentType::DRIVING_LICENSE_FRONT,
                    'driving_license_back' => RepresentativeDocumentType::DRIVING_LICENSE_BACK,
                    'vehicle_license_front' => RepresentativeDocumentType::VEHICLE_LICENSE_FRONT,
                    'vehicle_license_back' => RepresentativeDocumentType::VEHICLE_LICENSE_BACK,
                ];

                foreach ($documentMapping as $field => $docType) {
                    if ($request->hasFile($field)) {
                        $path = uploadImage($request, $field, 'storage/metwgo/documents');

                        if ($path) {
                            $existing = $representative->mediaFiles()
                                ->where('collection_name', 'representative_documents')
                                ->where('document_type', $docType->value)
                                ->first();

                            if ($existing) {
                                deleteImage($existing->url);
                                $existing->update(['url' => $path]);
                            } else {
                                MediaFile::create([
                                    'mediable_type' => Representative::class,
                                    'mediable_id' => $representative->id,
                                    'collection_name' => 'representative_documents',
                                    'document_type' => $docType->value,
                                    'url' => $path,
                                    'mime_type' => $request->file($field)->getMimeType(),
                                    'size' => $request->file($field)->getSize(),
                                ]);
                            }
                        }
                    }
                }
            });

            return responseJson(true, 'تم رفع المستندات', null, 200);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }
}
