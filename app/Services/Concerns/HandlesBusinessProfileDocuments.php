<?php

namespace App\Services\Concerns;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

trait HandlesBusinessProfileDocuments
{
    protected function syncDocuments($profile, Request $request, string $directoryRoot): void
    {
        $files = $request->file('documents', []);

        if (empty($files)) {
            return;
        }

        $directory = 'storage/' . trim($directoryRoot, '/') . '/' . $profile->id;
        File::ensureDirectoryExists(public_path($directory));

        foreach ($files as $index => $file) {
            $extension = strtolower($file->getClientOriginalExtension());
            $filename = now()->format('YmdHis') . '_' . Str::uuid() . '.' . $extension;

            $file->move(public_path($directory), $filename);

            $relativePath = $directory . '/' . $filename;

            $profile->mediaFiles()->create([
                'collection_name' => 'business_profile_documents',
                'disk' => 'public',
                'directory' => $directory,
                'filename' => $filename,
                'original_name' => $file->getClientOriginalName(),
                'extension' => $extension,
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'url' => asset($relativePath),
                'title' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                'sort_order' => $index,
                'is_primary' => false,
            ]);
        }
    }
}
