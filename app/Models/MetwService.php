<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MetwService extends Model
{
    protected $table = 'metwwebsite_services';
    protected $fillable = ['title_ar', 'title_en', 'description_ar', 'description_en', 'icon', 'sort_order', 'is_active'];
    
    public function scopeActive($query) { return $query->where('is_active', true); }
}