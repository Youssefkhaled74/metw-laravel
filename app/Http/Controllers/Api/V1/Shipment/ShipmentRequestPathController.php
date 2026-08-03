<?php

namespace App\Http\Controllers\Api\V1\Shipment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ShipmentRequest\PayAdvanceRequest;
use App\Http\Requests\Api\V1\ShipmentRequest\SelectRequestPathRequest;
use App\Http\Resources\AdvancePaymentResource;
use App\Http\Resources\RequestPathResource;
use App\Http\Resources\ShipmentRequestResource;
use App\Models\RequestPath;
use App\Models\ShipmentRequest;
use App\Services\CourierSystem\AdvancePaymentService;
use App\Services\CourierSystem\CourierRequestWorkflowService;
use App\Services\ShipmentRequestService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ShipmentRequestPathController extends Controller
{
    public function __construct(
        protected ShipmentRequestService $shipmentRequestService,
        protected CourierRequestWorkflowService $workflow,
        protected AdvancePaymentService $advancePaymentService
    ) {}

    public function paths(Request $request, int $id)
    {
        try {
            $shipmentRequest = $this->getOwnedRequest($request->user(), $id);

            $paths = $shipmentRequest->requestPaths()
                ->with(['assignments.representative'])
                ->get();

            return responseJson(
                true,
                'Request paths fetched successfully',
                ['request' => new ShipmentRequestResource($shipmentRequest), 'paths' => RequestPathResource::collection($paths)]
            );
        } catch (ModelNotFoundException) {
            return responseJson(false, 'Shipment request not found', null, 404);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function select(SelectRequestPathRequest $httpRequest, int $id)
    {
        try {
            $user = $httpRequest->user();
            $shipmentRequest = $this->getOwnedRequest($user, $id);

            $path = RequestPath::query()
                ->where('pathable_type', ShipmentRequest::class)
                ->where('pathable_id', $shipmentRequest->id)
                ->findOrFail($httpRequest->validated('request_path_id'));

            $updated = $this->workflow->selectPath($shipmentRequest, $path);

            return responseJson(
                true,
                'Path selected successfully',
                ['shipment_request' => new ShipmentRequestResource($updated)]
            );
        } catch (ModelNotFoundException) {
            return responseJson(false, 'Shipment request or path not found', null, 404);
        } catch (ValidationException $exception) {
            return responseJson(false, $exception->getMessage(), $exception->errors(), 422);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function pay(PayAdvanceRequest $httpRequest, int $id)
    {
        try {
            $user = $httpRequest->user();
            $shipmentRequest = $this->getOwnedRequest($user, $id);

            $payment = $this->advancePaymentService->submitForUser(
                $shipmentRequest,
                $user,
                $httpRequest->validated()
            );

            return responseJson(
                true,
                'Advance payment submitted successfully',
                ['advance_payment' => new AdvancePaymentResource($payment)],
                201
            );
        } catch (ModelNotFoundException) {
            return responseJson(false, 'Shipment request not found', null, 404);
        } catch (ValidationException $exception) {
            return responseJson(false, $exception->getMessage(), $exception->errors(), 422);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function tracking(Request $request, int $id)
    {
        try {
            $shipmentRequest = $this->getOwnedRequest($request->user(), $id);

            $shipmentRequest->load([
                'requestPaths.assignments.representative',
                'advancePayments',
                'selectedPath',
            ]);

            return responseJson(
                true,
                'Shipment request tracking fetched successfully',
                [
                    'shipment_request' => new ShipmentRequestResource($shipmentRequest),
                    'paths' => RequestPathResource::collection($shipmentRequest->requestPaths),
                    'advance_payments' => AdvancePaymentResource::collection($shipmentRequest->advancePayments),
                    'selected_path_id' => $shipmentRequest->selected_request_path_id,
                    'failure_text' => $shipmentRequest->failure_text,
                ]
            );
        } catch (ModelNotFoundException) {
            return responseJson(false, 'Shipment request not found', null, 404);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    protected function getOwnedRequest($user, int $id): ShipmentRequest
    {
        return $this->shipmentRequestService->getForUserOrFail($user, $id);
    }
}
