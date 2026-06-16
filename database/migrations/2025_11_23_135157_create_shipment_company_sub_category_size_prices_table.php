<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipment_company_sub_category_size_prices', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('shipment_company_category_price_id');
            $table->unsignedBigInteger('category_id');

            $table->decimal('price_small', 10, 2)->nullable();
            $table->decimal('price_medium', 10, 2)->nullable();
            $table->decimal('price_large', 10, 2)->nullable();

            $table->timestamps();

            // Short custom FK names
            $table->foreign('shipment_company_category_price_id', 'scscp_category_price_fk')
                ->references('id')
                ->on('shipment_company_category_prices')
                ->onDelete('cascade');

            $table->foreign('category_id', 'scscp_category_fk')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_company_sub_category_size_prices');
    }
};
