<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RepresentativeMediaFileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $path = $this->directory && $this->filename
            ? trim($this->directory, '/') . '/' . $this->filename
            : null;

        return [
            'id' => $this->id,
            'collection_name' => $this->collection_name,
            'disk' => $this->disk,
            'directory' => $this->directory,
            'filename' => $this->filename,
            'original_name' => $this->original_name,
            'extension' => $this->extension,
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'title' => $this->title,
            'alt_text' => $this->alt_text,
            'sort_order' => $this->sort_order,
            'is_primary' => $this->is_primary,
            'metadata' => $this->metadata,
            'url' => $this->url ?: ($path ? asset($path) : null),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
