<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            if(!Schema::hasColumn('pages', 'title_ar')) {
                $table->string('title_ar')->nullable()->after('title');
            }
            if(!Schema::hasColumn('pages', 'content_ar')) {
                $table->text('content_ar')->nullable()->after('content');
            }
            //from
            if(!Schema::hasColumn('pages', 'active_from')) {
                $table->date('active_from')->nullable()->after('is_active');
            }
            //to
            if(!Schema::hasColumn('pages', 'active_to')) {
                $table->date('active_to')->nullable()->after('active_from');
            }

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            if(Schema::hasColumn('pages', 'title_ar')) {
                $table->dropColumn('title_ar');
            }
            if(Schema::hasColumn('pages', 'content_ar')) {
                $table->dropColumn('content_ar');
            }
            if(Schema::hasColumn('pages', 'active_from')) {
                $table->dropColumn('active_from');
            }
            if(Schema::hasColumn('pages', 'active_to')) {
                $table->dropColumn('active_to');
            }

        });
    }
};
