<?php

namespace App\Models;

use App\Models\Concerns\GeneratesPrefixedNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Employee extends Authenticatable
{
    use HasFactory, SoftDeletes, HasRoles, GeneratesPrefixedNumber;
    protected $guard_name = 'employee';

    protected $fillable = [
        'employee_number',
        'first_name',
        'last_name',
        'email',
        'phone',
        'position',
        'salary',
        'hire_date',
        'password',
    ];
        protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'hire_date' => 'date',
    ];

    protected static function booted()
    {
        static::assignPrefixedNumberOnCreate('employee_number', 'EMP');
    }

}
