<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('website_videos')) {
            Schema::create('website_videos', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('title_ar')->nullable();
                $table->text('description')->nullable();
                $table->text('description_ar')->nullable();
                $table->string('video_path')->nullable();
                $table->string('thumbnail')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['is_active', 'sort_order']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('website_videos');
    }
};
