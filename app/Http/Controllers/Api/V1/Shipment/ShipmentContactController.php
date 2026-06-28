<?php

namespace App\Http\Controllers\Api\V1\Shipment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ShipmentContact\StoreShipmentContactRequest;
use App\Http\Requests\Api\V1\ShipmentContact\UpdateShipmentContactRequest;
use App\Http\Resources\ShipmentContactResource;
use App\Services\ShipmentContactService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class ShipmentContactController extends Controller
{
    public function __construct(
        protected ShipmentContactService $shipmentContactService
    ) {
    }

    public function index()
    {
        try {
            $contacts = $this->shipmentContactService->listForUser(auth()->user());

            return responseJson(
                true,
                'Shipment contacts fetched successfully',
                ['contacts' => ShipmentContactResource::collection($contacts)->resolve()]
            );
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function store(StoreShipmentContactRequest $request)
    {
        try {
            $contact = $this->shipmentContactService->create(
                $request->user(),
                $request->validated()
            );

            return responseJson(
                true,
                'Shipment contact created successfully',
                ['contact' => new ShipmentContactResource($contact)],
                201
            );
        } catch (ValidationException $exception) {
            return responseJson(false, $exception->getMessage(), $exception->errors(), 422);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function show(int $id)
    {
        try {
            $contact = $this->shipmentContactService->getForUserOrFail(auth()->user(), $id);

            return responseJson(
                true,
                'Shipment contact fetched successfully',
                ['contact' => new ShipmentContactResource($contact)]
            );
        } catch (ModelNotFoundException $exception) {
            return responseJson(false, 'Shipment contact not found', null, 404);
        } catch (ValidationException $exception) {
            return responseJson(false, $exception->getMessage(), $exception->errors(), 422);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function update(UpdateShipmentContactRequest $request, int $id)
    {
        try {
            $contact = $this->shipmentContactService->update(
                $request->user(),
                $id,
                $request->validated()
            );

            return responseJson(
                true,
                'Shipment contact updated successfully',
                ['contact' => new ShipmentContactResource($contact)]
            );
        } catch (ModelNotFoundException $exception) {
            return responseJson(false, 'Shipment contact not found', null, 404);
        } catch (ValidationException $exception) {
            return responseJson(false, $exception->getMessage(), $exception->errors(), 422);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->shipmentContactService->delete(auth()->user(), $id);

            return responseJson(true, 'Shipment contact deleted successfully');
        } catch (ModelNotFoundException $exception) {
            return responseJson(false, 'Shipment contact not found', null, 404);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }
}
