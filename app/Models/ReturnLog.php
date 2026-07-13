<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_request_id',
        'old_status',
        'new_status',
        'changed_by_type',
        'changed_by_id',
        'notes',
    ];

    public function returnRequest()
    {
        return $this->belongsTo(OrderReturnRequest::class, 'return_request_id');
    }
}
