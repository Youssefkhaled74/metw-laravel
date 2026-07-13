<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnComplaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_request_id',
        'user_id',
        'complaint_reason',
        'admin_action',
        'action_reason',
    ];

    public function returnRequest()
    {
        return $this->belongsTo(OrderReturnRequest::class, 'return_request_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
