<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_name',
        'sender_phone',
        'pickup_date',
        'pickup_time',
        'recive_name',
        'recive_phone',
    ];

    public function packages()
    {
        return $this->hasMany(Package::class);
    }


}
