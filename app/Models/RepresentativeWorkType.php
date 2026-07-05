<?php

namespace App\Models;

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
        'work_type' => 'string',
    ];

    public function representative()
    {
        return $this->belongsTo(Representative::class);
    }

    public function option()
    {
        return $this->belongsTo(RepresentativeWorkTypeOption::class, 'work_type', 'code');
    }
}
