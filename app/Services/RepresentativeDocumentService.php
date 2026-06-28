<?php

namespace App\Services;

use App\Models\MediaFile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class RepresentativeDocumentService
{
    public function __construct(
        protected RepresentativeService $representativeService
    ) {
    }

    public function upload(User $user, array $data, Request $request)
    {
        $representative = $this->representativeService->getCurrentOrFail($user);
        $files = $request->file('documents', []);
        $collectionName = $data['collection_name'] ?? 'representative_documents';
        $titles = $data['titles'] ?? [];
        $metadata = $data['metadata'] ?? [];
        $isPrimary = (bool) ($data['is_primary'] ?? false);
        $directory = 'storage/representatives/documents/' . $representative->id;

        File::ensureDirectoryExists(public_path($directory));

        return DB::transaction(function () use (
            $representative,
            $files,
            $collectionName,
            $titles,
            $metadata,
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
                $extension = strtolower($file->getClientOriginalExtension());
                $filename = now()->format('YmdHis') . '_' . Str::uuid() . '.' . $extension;

                $file->move(public_path($directory), $filename);

                $relativePath = $directory . '/' . $filename;

                $documents->push($representative->mediaFiles()->create([
                    'collection_name' => $collectionName,
                    'disk' => 'public',
                    'directory' => $directory,
                    'filename' => $filename,
                    'original_name' => $file->getClientOriginalName(),
                    'extension' => $extension,
                    'mime_type' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                    'url' => asset($relativePath),
                    'title' => $titles[$index] ?? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                    'sort_order' => $index,
                    'is_primary' => $isPrimary && $index === 0,
                    'metadata' => $metadata,
                ]));
            }

            return $documents;
        });
    }
}
