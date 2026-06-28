<?php

namespace App\Http\Controllers\Api\V1\Representative;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Representative\RegisterRepresentativeRequest;
use App\Http\Requests\Api\V1\Representative\UpdateRepresentativeProfileRequest;
use App\Http\Resources\RepresentativeResource;
use App\Http\Resources\TransportTypeResource;
use App\Services\RepresentativeService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RepresentativeController extends Controller
{
    public function __construct(
        protected RepresentativeService $representativeService
    ) {
    }

    public function register(RegisterRepresentativeRequest $request)
    {
        try {
            $representative = $this->representativeService->register(
                $request->user(),
                $request->validated()
            );

            return responseJson(
                true,
                'Representative profile registered successfully',
                ['representative' => new RepresentativeResource($representative)],
                201
            );
        } catch (ValidationException $exception) {
            return responseJson(false, $exception->getMessage(), $exception->errors(), 422);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function me(Request $request)
    {
        try {
            $representative = $this->representativeService->getCurrentOrFail($request->user());

            return responseJson(
                true,
                'Representative profile fetched successfully',
                ['representative' => new RepresentativeResource($representative)]
            );
        } catch (ModelNotFoundException $exception) {
            return responseJson(false, 'Representative profile not found', null, 404);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function update(UpdateRepresentativeProfileRequest $request)
    {
        try {
            $representative = $this->representativeService->update(
                $request->user(),
                $request->validated()
            );

            return responseJson(
                true,
                'Representative profile updated successfully',
                ['representative' => new RepresentativeResource($representative)]
            );
        } catch (ModelNotFoundException $exception) {
            return responseJson(false, 'Representative profile not found', null, 404);
        } catch (ValidationException $exception) {
            return responseJson(false, $exception->getMessage(), $exception->errors(), 422);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function transportTypes()
    {
        try {
            $transportTypes = $this->representativeService->getActiveTransportTypes();

            return responseJson(
                true,
                'Transport types fetched successfully',
                ['transport_types' => TransportTypeResource::collection($transportTypes)->resolve()]
            );
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }
}
