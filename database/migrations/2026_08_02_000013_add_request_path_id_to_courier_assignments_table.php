<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courier_assignments', function (Blueprint $table) {
            if (! Schema::hasColumn('courier_assignments', 'request_path_id')) {
                $table->foreignId('request_path_id')
                    ->nullable()
                    ->after('leg_type')
                    ->constrained('request_paths')
                    ->nullOnDelete();

                $table->index('request_path_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('courier_assignments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('request_path_id');
        });
    }
};
