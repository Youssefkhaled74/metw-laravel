<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE products
            MODIFY piece_type ENUM('small','medium','large','xlarge')
            DEFAULT 'small'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE products
            MODIFY piece_type ENUM('small','medium','large')
            DEFAULT 'small'
        ");
    }
};
