<?php

namespace App\Models;

use App\Models\Concerns\GeneratesPrefixedNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    use HasFactory, SoftDeletes, GeneratesPrefixedNumber;

    protected $fillable = [
        'product_number',
        'vendor_id',
        'category_id',
        'category_id_2',
        'brand_id',
        'name',
        'slug',
        'sku',
        'stock',
        'short_description',
        'description',
        'price',
        'is_active',
        'view_count',
        'sold_count',
        'rating_count',
        'rating',
        'main_category_id',
        'main_category_id_2',
        'features',
        'product_info',
        'usage_description',
        'parts_description',
        'material_description',
        'dimensions',
        'weight',
        'volume',
        'available_sizes',
        'available_colors',
        'origin_country',
        'manufacturer',
        'model',
        'expiry_period',
        'discount_percentage',
        'discounted_price',
        'discount_start',
        'discount_end',
        'free_shipping',
        'free_shipping_min_order',
        'shipment_type',
        'shipment_description',
        'shipment_dimensions',
        'shipment_weight',
        'storage_conditions',
        'delivery_zones',
        'delivery_options',
        'subcategories_level1',
        'subcategory_level2',
        'auto_discount_end_date',
        'free_shipping_price',
        'package_length',
        'package_width',
        'package_height',
        'package_weight',
        'is_returnable',
        'return_fee',
        'return_validity',
        'branch_id',
        'has_deposit',
        'deposit_percentage',
        'piece_type',
        'pieces_per_package',
        'requires_delivery_otp',
    ];


    protected $casts = [
        'is_active' => 'boolean',
        'view_count' => 'integer',
        'sold_count' => 'integer',
        'rating_count' => 'integer',
        'rating' => 'float',
        'discount_percentage' => 'float',
        'discount_start' => 'date',
        'discount_end' => 'date',
        'available_colors' => 'array',
        'available_sizes'  => 'array',
        'storage_conditions' => 'array',
        'features' => 'array',
        'product_info' => 'array',
        'delivery_options' => 'array',
        'auto_discount_end_date' => 'date',
        'delivery_zones' => 'array',
        'has_deposit' => 'boolean',
        'deposit_percentage' => 'float',
        'requires_delivery_otp' => 'boolean',
    ];
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function translations()
    {
        return $this->hasMany(ProductTranslation::class);
    }
    public function translate($locale = null)
    {
        return $this->translation($locale);
    }
    public function translation($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->translations->where('locale', $locale)->first();
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function maincategory()
    {
        return $this->belongsTo(MainCategory::class, 'main_category_id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function media()
    {
        return $this->hasMany(ProductMedia::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function sizes()
    {
        return $this->belongsToMany(ProductSize::class);
    }

    public function colors()
    {
        return $this->belongsToMany(ProductColor::class);
    }

    public function branch()
    {
        return $this->belongsTo(VendorBranch::class, 'branch_id');
    }

    public function relatedProducts()
    {
        return $this->belongsToMany(Product::class, 'related_products', 'product_id', 'related_product_id');
    }
    public function cartitmes()
    {
        return $this->hasMany(CartItem::class);
    }
    public function ecommerceOrderItems()
    {
        return $this->hasMany(EcommerceOrderItem::class);
    }
    public function favourite()
    {
        return $this->morphMany(Favourite::class, 'favouriteable');
    }
    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function recentViews()
    {
        return $this->hasMany(RecentView::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    public function images()
    {
        return $this->hasMany(ProductMedia::class)->where('type', \App\Enum\ProductMediaType::IMAGE->value);
    }

    public function mediaFiles()
    {
        return $this->morphMany(MediaFile::class, 'mediable');
    }

    public function videos()
    {
        return $this->hasMany(ProductMedia::class)->where('type', \App\Enum\ProductMediaType::VIDEO->value);
    }

    public function getHasVariantsAttribute(): bool
    {
        return $this->variants()->exists();
    }




    public function scopeSearch($query, $search)
    {
        $query->where(function ($q) use ($search) {

            // 🔹 Search in product translations
            $q->whereHas('translations', function ($tq) use ($search) {
                $tq->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('short_description', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%");
            })

            // 🔹 Search in main product fields
            ->orWhere('product_info', 'like', "%{$search}%")
            ->orWhere('features', 'like', "%{$search}%")
            ->orWhere('usage_description', 'like', "%{$search}%")
            ->orWhere('parts_description', 'like', "%{$search}%")
            ->orWhere('material_description', 'like', "%{$search}%")

            // 🔹 Search in category translations
            ->orWhereHas('category.translations', function ($cq) use ($search) {
                $cq->where('name', 'like', "%{$search}%");
            })

            // 🔹 Search in main category translations
            ->orWhereHas('maincategory.translations', function ($mcq) use ($search) {
                $mcq->where('name', 'like', "%{$search}%");
            })

            // 🔹 Search in brand translations
            ->orWhereHas('brand', function ($bq) use ($search) {
                $bq->where('name_en', 'like', "%{$search}%")
                ->orWhere('name_ar', 'like', "%{$search}%");
            })

            // 🔹 Search in vendor name
            ->orWhereHas('vendor', function ($vq) use ($search) {
                $vq->where('name', 'like', "%{$search}%");
            });
        });
    }




    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeBySecondaryCategory($query, $categoryId)
    {
        return $query->where('category_id_2', $categoryId);
    }

    public function scopeByMainCategory($query, $categoryId2)
    {
        return $query->where('main_category_id', $categoryId2);
    }

    public function scopeByBrand($query, $brandId)
    {
        return $query->where('brand_id', $brandId);
    }

    public function scopeByVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    public function scopeByPrice($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('price', [$minPrice, $maxPrice]);
    }

    public function myFavourite()
    {
        return $this->morphOne(Favourite::class, 'favouriteable')
            ->where('user_id', auth()->id());
    }

    // Filter scopes
    public function scopeSimilarToPurchases($query, $userId)
    {
        // Get categories of products the user has purchased
        $purchasedCategories = EcommerceOrderItem::whereHas('order', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->with('product.category')
            ->get()
            ->pluck('product.category_id')
            ->filter()
            ->unique();

        if ($purchasedCategories->isNotEmpty()) {
            return $query->whereIn('category_id', $purchasedCategories)
                ->where('is_active', true);
        }

        return $query->where('is_active', true);
    }

    public function scopeRecommendedByBuyers($query, $userId)
    {
        // Get products that are highly rated and have good reviews
        return $query->where('is_active', true)
            ->where('rating', '>=', 4.0)
            ->where('rating_count', '>=', 5)
            ->orderBy('rating', 'desc')
            ->orderBy('rating_count', 'desc');
    }

    public function scopePreviousPurchases($query, $userId)
    {
        // Get products the user has previously purchased
        $purchasedProductIds = EcommerceOrderItem::whereHas('order', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->pluck('product_id')->unique();

        return $query->whereIn('id', $purchasedProductIds)
            ->where('is_active', true);
    }

    public function scopeRecommendedDeals($query, $userId = null)
    {
        // Get products with discounts or special offers
        // This could be enhanced based on your discount/promo system
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->where('price', '<=', 100) // Example: products under $100
                    ->orWhere('sold_count', '>=', 10); // Popular products
            })
            ->orderBy('sold_count', 'desc')
            ->orderBy('rating', 'desc');
    }

    public function scopeTopRated($query)
    {
        return $query->where('is_active', true)
            ->where('rating', '>=', 3.0)
            ->orderBy('rating', 'desc')
            ->orderBy('rating_count', 'desc');
    }

    public function scopeNewArrivals($query)
    {
        return $query->where('is_active', true)
            ->where('created_at', '>=', now()->subDays(30)) // Products created in last 30 days
            ->orderBy('created_at', 'desc');
    }

    public function scopeRecentViews($query, $userId)
    {
        return $query->whereHas('recentViews', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->with(['recentViews' => function ($q) use ($userId) {
            $q->where('user_id', $userId)
                ->orderBy('updated_at', 'desc');
        }]);
    }

    public function scopeSimilarToRecentViews($query, $userId)
    {
        // هات الـ categories اللي اليوزر شافها
        $viewedCategories = RecentView::where('user_id', $userId)
            ->with('product.category')
            ->get()
            ->pluck('product.category_id')
            ->filter()
            ->unique();

        if ($viewedCategories->isNotEmpty()) {
            return $query->whereIn('category_id', $viewedCategories)
                ->where('is_active', true);
        }

        // fallback: رجّع top rated
        // return $query->topRated();
    }

    public function getIsSaleAttribute(): bool
    {
        // لازم يكون عنده خصم موجب
        if ($this->discount_percentage <= 0) {
            return false;
        }

        $now = now();

        // لو start و end موجودين و الوقت الحالي بينهم
        if ($this->discount_start && $this->discount_end) {
            return $now->between($this->discount_start, $this->discount_end);
        }

        // لو start فقط موجود و بدأ فعلاً
        if ($this->discount_start && !$this->discount_end) {
            return $now->greaterThanOrEqualTo($this->discount_start);
        }

        // لو end فقط موجود و لسه الخصم ما انتهى
        if (!$this->discount_start && $this->discount_end) {
            return $now->lessThanOrEqualTo($this->discount_end);
        }

        // fallback: في خصم بس مفيش تواريخ
        return true;
    }

    protected static function booted()
    {
        static::assignPrefixedNumberOnCreate('product_number', 'PRD');

        static::addGlobalScope('withCurrentLocaleTranslation', function (Builder $builder) {
            $locale = app()->getLocale();
            $builder->with(['translations' => function($query) use ($locale) {
                $query->where('locale', $locale);
            }]);
        });

        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('is_active', true);
        });
    }

    public function getNameAttribute()
    {
        $locale = app()->getLocale();
        $translation = $this->translation($locale);

        // Return translated name if exists, otherwise fallback to English or original name
        if ($translation && $translation->name) {
            return $translation->name;
        }

        // Fallback to English translation
        $englishTranslation = $this->translation('en');
        if ($englishTranslation && $englishTranslation->name) {
            return $englishTranslation->name;
        }

        // Fallback to Arabic translation
        $arabicTranslation = $this->translation('ar');
        if ($arabicTranslation && $arabicTranslation->name) {
            return $arabicTranslation->name;
        }

        // Return the original name column as last resort
        return $this->attributes['name'] ?? '';
    }

    // Add similar accessor for description if needed
    public function getDescriptionAttribute()
    {
        $locale = app()->getLocale();
        $translation = $this->translation($locale);

        if ($translation && $translation->description) {
            return $translation->description;
        }

        $englishTranslation = $this->translation('en');
        if ($englishTranslation && $englishTranslation->description) {
            return $englishTranslation->description;
        }

        $arabicTranslation = $this->translation('ar');
        if ($arabicTranslation && $arabicTranslation->description) {
            return $arabicTranslation->description;
        }

        return $this->attributes['description'] ?? '';
    }

    public function getShortDescriptionAttribute()
    {
        $locale = app()->getLocale();
        $translation = $this->translation($locale);

        if ($translation && $translation->short_description) {
            return $translation->short_description;
        }

        $englishTranslation = $this->translation('en');
        if ($englishTranslation && $englishTranslation->short_description) {
            return $englishTranslation->short_description;
        }

        $arabicTranslation = $this->translation('ar');
        if ($arabicTranslation && $arabicTranslation->short_description) {
            return $arabicTranslation->short_description;
        }

        return $this->attributes['short_description'] ?? '';
    }

}
