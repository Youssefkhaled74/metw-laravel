<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MediaFile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'mediable_type',
        'mediable_id',
        'collection_name',
        'disk',
        'directory',
        'filename',
        'original_name',
        'extension',
        'mime_type',
        'size',
        'url',
        'title',
        'alt_text',
        'sort_order',
        'is_primary',
        'metadata',
    ];

    protected $casts = [
        'size' => 'integer',
        'sort_order' => 'integer',
        'is_primary' => 'boolean',
        'metadata' => 'array',
    ];

    public function mediable()
    {
        return $this->morphTo();
    }
}
