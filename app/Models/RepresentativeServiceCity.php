<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepresentativeServiceCity extends Model
{
    use HasFactory;

    protected $fillable = [
        'representative_id',
        'city_id',
    ];

    public function representative()
    {
        return $this->belongsTo(Representative::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
