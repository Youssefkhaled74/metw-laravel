<?php

namespace App\Http\Controllers\Api\V1\Shipment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ShipmentRequest\StoreShipmentRequestPackageRequest;
use App\Http\Requests\Api\V1\ShipmentRequest\StoreShipmentRequestRequest;
use App\Http\Resources\ShipmentRequestResource;
use App\Services\ShipmentRequestService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ShipmentRequestController extends Controller
{
    public function __construct(
        protected ShipmentRequestService $shipmentRequestService
    ) {
    }

    public function index(Request $request)
    {
        try {
            $shipmentRequests = $this->shipmentRequestService->listForUser($request->user());

            return responseJson(
                true,
                'Shipment requests fetched successfully',
                ['shipment_requests' => ShipmentRequestResource::collection($shipmentRequests)->resolve()]
            );
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function store(StoreShipmentRequestRequest $request)
    {
        try {
            $shipmentRequest = $this->shipmentRequestService->create(
                $request->user(),
                $request->validated()
            );

            return responseJson(
                true,
                'Shipment request created successfully',
                ['shipment_request' => new ShipmentRequestResource($shipmentRequest)],
                201
            );
        } catch (ValidationException $exception) {
            return responseJson(false, $exception->getMessage(), $exception->errors(), 422);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function show(Request $request, int $id)
    {
        try {
            $shipmentRequest = $this->shipmentRequestService->getForUserOrFail($request->user(), $id);

            return responseJson(
                true,
                'Shipment request fetched successfully',
                ['shipment_request' => new ShipmentRequestResource($shipmentRequest)]
            );
        } catch (ModelNotFoundException $exception) {
            return responseJson(false, 'Shipment request not found', null, 404);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function storePackage(StoreShipmentRequestPackageRequest $request, int $id)
    {
        try {
            $shipmentRequest = $this->shipmentRequestService->addPackage(
                $request->user(),
                $id,
                $request->validated(),
                $request
            );

            return responseJson(
                true,
                'Shipment request package added successfully',
                ['shipment_request' => new ShipmentRequestResource($shipmentRequest)],
                201
            );
        } catch (ModelNotFoundException $exception) {
            return responseJson(false, 'Shipment request not found', null, 404);
        } catch (ValidationException $exception) {
            return responseJson(false, $exception->getMessage(), $exception->errors(), 422);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function destroyPackage(Request $request, int $id, int $packageId)
    {
        try {
            $shipmentRequest = $this->shipmentRequestService->removePackage($request->user(), $id, $packageId);

            return responseJson(
                true,
                'Shipment request package deleted successfully',
                ['shipment_request' => new ShipmentRequestResource($shipmentRequest)]
            );
        } catch (ModelNotFoundException $exception) {
            return responseJson(false, 'Shipment request or package not found', null, 404);
        } catch (ValidationException $exception) {
            return responseJson(false, $exception->getMessage(), $exception->errors(), 422);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function submit(Request $request, int $id)
    {
        try {
            $shipmentRequest = $this->shipmentRequestService->submit($request->user(), $id);

            return responseJson(
                true,
                'Shipment request submitted successfully',
                ['shipment_request' => new ShipmentRequestResource($shipmentRequest)]
            );
        } catch (ModelNotFoundException $exception) {
            return responseJson(false, 'Shipment request not found', null, 404);
        } catch (ValidationException $exception) {
            return responseJson(false, $exception->getMessage(), $exception->errors(), 422);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }
}
