<?php

namespace App\Http\Controllers\Api\V1\Representative;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Representative\UploadRepresentativeDocumentsRequest;
use App\Http\Resources\RepresentativeMediaFileResource;
use App\Services\RepresentativeDocumentService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RepresentativeDocumentController extends Controller
{
    public function __construct(
        protected RepresentativeDocumentService $representativeDocumentService
    ) {
    }

    public function store(UploadRepresentativeDocumentsRequest $request)
    {
        try {
            $documents = $this->representativeDocumentService->upload(
                $request->user(),
                $request->validated(),
                $request
            );

            return responseJson(
                true,
                'Representative documents uploaded successfully',
                ['documents' => RepresentativeMediaFileResource::collection($documents)->resolve()],
                201
            );
        } catch (ModelNotFoundException $exception) {
            return responseJson(false, 'Representative profile not found', null, 404);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }
}
