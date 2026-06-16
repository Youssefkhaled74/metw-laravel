<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnCashBack extends Model
{
    protected $fillable = [
        'return_id',
        'cash_back_method',
        'value',
    ];

    public function returnRequest()
    {
        return $this->belongsTo(ReturnRequest::class, 'return_id');
    }
}
