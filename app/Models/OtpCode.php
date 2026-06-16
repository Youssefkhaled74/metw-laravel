<?php

namespace App\Models;

use App\Enum\OtpPurpose;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpCode extends Model
{
    use HasFactory;

    protected $fillable =[
        'user_id',
        'code',
        'purpose',
        'is_used',
        'expires_at'
    ];
    protected $casts=[
        'purpose'=> OtpPurpose::class
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
