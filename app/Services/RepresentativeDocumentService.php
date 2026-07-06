<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class RepresentativeDocumentService
{
    public function __construct(
        protected RepresentativeService $representativeService
    ) {
    }

    public function upload(User $user, array $data, Request $request): Collection
    {
        $representative = $this->representativeService->getCurrentOrFail($user);
        $files = $request->file('documents', []);
        $collectionName = $data['collection_name'] ?? 'representative_documents';
        $titles = $data['titles'] ?? [];
        $metadata = $data['metadata'] ?? [];
        $documentTypes = $data['document_types'] ?? [];
        $isPrimary = (bool) ($data['is_primary'] ?? false);
        $directory = 'storage/representatives/documents/' . $representative->id;

        File::ensureDirectoryExists(public_path($directory));

        return DB::transaction(function () use (
            $representative,
            $files,
            $collectionName,
            $titles,
            $metadata,
            $documentTypes,
            $isPrimary,
            $directory
        ) {
            if ($isPrimary) {
                $representative->mediaFiles()
                    ->where('collection_name', $collectionName)
                    ->update(['is_primary' => false]);
            }

            $documents = collect();

            foreach ($files as $index => $file) {
                $documentType = $documentTypes[$index] ?? null;
                $extension = strtolower($file->getClientOriginalExtension());
                $filename = now()->format('YmdHis') . '_' . Str::uuid() . '.' . $extension;
                $originalName = $file->getClientOriginalName();
                $mimeType = $file->getClientMimeType();
                $size = $file->getSize();

                $file->move(public_path($directory), $filename);

                $relativePath = $directory . '/' . $filename;

                $documents->push($representative->mediaFiles()->create([
                    'collection_name' => $collectionName,
                    'document_type' => $documentType,
                    'disk' => 'public',
                    'directory' => $directory,
                    'filename' => $filename,
                    'original_name' => $originalName,
                    'extension' => $extension,
                    'mime_type' => $mimeType,
                    'size' => $size,
                    'url' => asset($relativePath),
                    'title' => $titles[$index] ?? pathinfo($originalName, PATHINFO_FILENAME),
                    'sort_order' => $index,
                    'is_primary' => $isPrimary && $index === 0,
                    'metadata' => $metadata,
                ]));
            }

            $this->representativeService->recalculateStatus($representative);

            return $documents;
        });
    }
}
