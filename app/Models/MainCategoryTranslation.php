<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainCategoryTranslation extends Model
{
    use HasFactory;
        protected $fillable = [
        'name',
        'slug',
        'locale'
    ];

    public function mainCategory()
    {
        return $this->belongsTo(MainCategory::class);
    }
}
