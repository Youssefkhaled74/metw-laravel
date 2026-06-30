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
            $originalName = $file->getClientOriginalName();
            $mimeType = $file->getClientMimeType();
            $size = $file->getSize();

            $file->move(public_path($directory), $filename);

            $relativePath = $directory . '/' . $filename;

            $profile->mediaFiles()->create([
                'collection_name' => 'business_profile_documents',
                'disk' => 'public',
                'directory' => $directory,
                'filename' => $filename,
                'original_name' => $originalName,
                'extension' => $extension,
                'mime_type' => $mimeType,
                'size' => $size,
                'url' => asset($relativePath),
                'title' => pathinfo($originalName, PATHINFO_FILENAME),
                'sort_order' => $index,
                'is_primary' => false,
            ]);
        }
    }
}
