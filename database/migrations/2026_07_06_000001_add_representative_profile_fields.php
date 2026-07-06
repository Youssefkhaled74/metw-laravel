<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('representatives', function (Blueprint $table) {
            $table->string('account_number', 40)->nullable()->unique()->after('id');
            $table->string('first_name')->nullable()->after('account_number');
            $table->string('father_name')->nullable()->after('first_name');
            $table->string('last_name')->nullable()->after('father_name');
            $table->date('account_opened_at')->nullable()->after('last_name');
            $table->string('second_phone', 30)->nullable()->after('phone');
            $table->date('birth_date')->nullable()->after('second_phone');
            $table->string('gender', 20)->nullable()->after('birth_date');
            $table->text('address')->nullable()->after('gender');
            $table->boolean('village_service')->default(false)->after('address');

            $table->index('account_number');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('representatives', function (Blueprint $table) {
            $table->dropIndex(['account_number']);
            $table->dropIndex(['status']);
            $table->dropUnique(['account_number']);

            $table->dropColumn([
                'account_number',
                'first_name',
                'father_name',
                'last_name',
                'account_opened_at',
                'second_phone',
                'birth_date',
                'gender',
                'address',
                'village_service',
            ]);
        });
    }
};
