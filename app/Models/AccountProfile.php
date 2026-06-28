<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'profileable_type',
        'profileable_id',
        'account_number',
        'display_name',
        'first_name',
        'last_name',
        'email',
        'phone',
        'alternate_phone',
        'date_of_birth',
        'gender',
        'national_id',
        'preferred_locale',
        'bio',
        'metadata',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'metadata' => 'array',
    ];

    public function profileable()
    {
        return $this->morphTo();
    }
}
