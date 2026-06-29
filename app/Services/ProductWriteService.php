<?php

namespace App\Services;

use App\Enum\ProductMediaType;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ProductWriteService
{
    public function create(array $validatedData, Request $request): Product
    {
        return DB::transaction(function () use ($validatedData, $request) {
            $product = Product::create($this->prepareProductAttributes($validatedData, true));

            $this->syncRelations($product, $validatedData, $request, true);

            return $product->load($this->relations());
        });
    }

    public function update(Product $product, array $validatedData, Request $request): Product
    {
        return DB::transaction(function () use ($product, $validatedData, $request) {
            $product->update($this->prepareProductAttributes($validatedData, false, $product));

            $this->syncRelations($product, $validatedData, $request, false);

            return $product->load($this->relations());
        });
    }

    public function relations(): array
    {
        return [
            'media',
            'variants.color',
            'variants.size',
            'variants.media',
            'relatedProducts',
            'brand',
            'category',
            'maincategory',
            'vendor',
            'translations',
            'shippingProfile',
            'returnPolicy',
            'shippingFeePolicy',
        ];
    }

    protected function prepareProductAttributes(array $validatedData, bool $isCreate, ?Product $product = null): array
    {
        $shippingProfile = $this->resolveShippingProfileData($validatedData);
        $returnPolicy = $this->resolveReturnPolicyData($validatedData);
        $shippingFeePolicy = $this->resolveShippingFeePolicyData($validatedData);

        $attributes = Arr::except($validatedData, [
            'translations',
            'media',
            'variants',
            'related_products',
            'shipping_profile',
            'return_policy',
            'shipping_fee_policy',
        ]);

        if ($isCreate) {
            $attributes['sku'] = random_int(100000, 999999);
        }

        if (isset($attributes['name'])) {
            $attributes['slug'] = str()->slug($attributes['name'] . '-' . ($product?->sku ?? $attributes['sku']));
        }

        $attributes = array_merge($attributes, $shippingProfile, $returnPolicy, $shippingFeePolicy);

        return $attributes;
    }

    protected function syncRelations(Product $product, array $validatedData, Request $request, bool $isCreate): void
    {
        $this->syncTranslations($product, $validatedData['translations'] ?? null);
        $this->syncMedia($product, $request, $isCreate);
        $this->syncVariants($product, $validatedData['variants'] ?? null, $isCreate);
        $this->syncRelatedProducts($product, $validatedData);
        $this->syncShippingProfile($product, $validatedData);
        $this->syncReturnPolicy($product, $validatedData);
        $this->syncShippingFeePolicy($product, $validatedData);
    }

    protected function syncTranslations(Product $product, ?array $translations): void
    {
        if ($translations === null) {
            return;
        }

        $product->translations()->delete();

        foreach ($translations as $locale => $data) {
            $product->translations()->create([
                'locale' => $locale,
                'name' => $data['name'],
                'slug' => str()->slug($data['name'] . '-' . $product->sku),
                'short_description' => $data['short_description'] ?? null,
                'description' => $data['description'] ?? null,
            ]);
        }
    }

    protected function syncMedia(Product $product, Request $request, bool $isCreate): void
    {
        if (! $request->has('media')) {
            return;
        }

        if (! $isCreate) {
            $product->media()->delete();
        }

        $mediaData = [];

        foreach ($request->media as $index => $media) {
            $file = $request->file("media.$index.file");

            if (! $file) {
                continue;
            }

            $url = $media['type'] === ProductMediaType::IMAGE->value
                ? uploadImage($request, "media.$index.file", 'storage/products')
                : uploadVideo($request, "media.$index.file", 'storage/products');

            $mediaData[] = [
                'type' => $media['type'],
                'url' => $url,
                'position' => $media['position'] ?? 0,
            ];
        }

        if ($mediaData !== []) {
            $product->media()->createMany($mediaData);
        }
    }

    protected function syncVariants(Product $product, ?array $variants, bool $isCreate): void
    {
        if ($variants === null) {
            return;
        }

        if (! $isCreate) {
            $product->variants()->delete();
        }

        $variantsData = collect($variants)->map(function (array $variant) {
            return array_merge($variant, [
                'sku' => random_int(100000, 999999),
            ]);
        })->toArray();

        if ($variantsData !== []) {
            $product->variants()->createMany($variantsData);
        }
    }

    protected function syncRelatedProducts(Product $product, array $validatedData): void
    {
        if (! array_key_exists('related_products', $validatedData)) {
            return;
        }

        $product->relatedProducts()->sync($validatedData['related_products'] ?? []);
    }

    protected function syncShippingProfile(Product $product, array $validatedData): void
    {
        $payload = $this->resolveShippingProfileData($validatedData);

        $product->shippingProfile()->updateOrCreate(
            ['product_id' => $product->id],
            $payload
        );
    }

    protected function syncReturnPolicy(Product $product, array $validatedData): void
    {
        $payload = $this->resolveReturnPolicyData($validatedData);

        $product->returnPolicy()->updateOrCreate(
            ['product_id' => $product->id],
            $payload
        );
    }

    protected function syncShippingFeePolicy(Product $product, array $validatedData): void
    {
        $payload = $this->resolveShippingFeePolicyData($validatedData);

        $product->shippingFeePolicy()->updateOrCreate(
            ['product_id' => $product->id],
            $payload
        );
    }

    protected function resolveShippingProfileData(array $validatedData): array
    {
        $nested = $validatedData['shipping_profile'] ?? [];

        return [
            'shipment_type' => $nested['shipment_type'] ?? ($validatedData['shipment_type'] ?? null),
            'shipment_description' => $nested['shipment_description'] ?? ($validatedData['shipment_description'] ?? null),
            'shipment_dimensions' => $nested['shipment_dimensions'] ?? ($validatedData['shipment_dimensions'] ?? null),
            'shipment_weight' => $nested['shipment_weight'] ?? ($validatedData['shipment_weight'] ?? null),
            'storage_conditions' => $nested['storage_conditions'] ?? ($validatedData['storage_conditions'] ?? null),
            'delivery_zones' => $nested['delivery_zones'] ?? ($validatedData['delivery_zones'] ?? null),
            'delivery_options' => $nested['delivery_options'] ?? ($validatedData['delivery_options'] ?? null),
            'package_length' => $nested['package_length'] ?? ($validatedData['package_length'] ?? null),
            'package_width' => $nested['package_width'] ?? ($validatedData['package_width'] ?? null),
            'package_height' => $nested['package_height'] ?? ($validatedData['package_height'] ?? null),
            'package_weight' => $nested['package_weight'] ?? ($validatedData['package_weight'] ?? null),
        ];
    }

    protected function resolveReturnPolicyData(array $validatedData): array
    {
        $nested = $validatedData['return_policy'] ?? [];

        return [
            'is_returnable' => (bool) ($nested['is_returnable'] ?? ($validatedData['is_returnable'] ?? false)),
            'return_fee' => $nested['return_fee'] ?? ($validatedData['return_fee'] ?? null),
            'return_validity' => $nested['return_validity'] ?? ($validatedData['return_validity'] ?? null),
        ];
    }

    protected function resolveShippingFeePolicyData(array $validatedData): array
    {
        $nested = $validatedData['shipping_fee_policy'] ?? [];

        return [
            'free_shipping' => (string) ($nested['free_shipping'] ?? ($validatedData['free_shipping'] ?? '0')),
            'free_shipping_min_order' => $nested['free_shipping_min_order'] ?? ($validatedData['free_shipping_min_order'] ?? null),
            'free_shipping_price' => $nested['free_shipping_price'] ?? ($validatedData['free_shipping_price'] ?? null),
        ];
    }
}
