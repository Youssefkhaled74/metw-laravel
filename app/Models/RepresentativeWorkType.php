<?php

namespace App\Models;

use App\Enum\RepresentativeWorkType as RepresentativeWorkTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepresentativeWorkType extends Model
{
    use HasFactory;

    protected $fillable = [
        'representative_id',
        'work_type',
    ];

    protected $casts = [
        'work_type' => RepresentativeWorkTypeEnum::class,
    ];

    public function representative()
    {
        return $this->belongsTo(Representative::class);
    }
}
