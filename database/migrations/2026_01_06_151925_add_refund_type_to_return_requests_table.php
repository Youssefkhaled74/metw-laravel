<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('return_requests', function (Blueprint $table) {
            $table->enum('refund_type', ['wallet', 'cash'])
                ->default('cash')
                ->after('refund_amount');
        });
    }

    public function down(): void
    {
        Schema::table('return_requests', function (Blueprint $table) {
            $table->dropColumn('refund_type');
        });
    }
};
