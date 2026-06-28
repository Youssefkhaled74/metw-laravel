<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepresentativeServiceGovernorate extends Model
{
    use HasFactory;

    protected $fillable = [
        'representative_id',
        'governorate_id',
    ];

    public function representative()
    {
        return $this->belongsTo(Representative::class);
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }
}
