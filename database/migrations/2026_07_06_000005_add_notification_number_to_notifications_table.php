<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('notifications', 'notification_number')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->string('notification_number')->nullable()->unique()->after('id');
            });
        }

        $notifications = DB::table('notifications')
            ->select('id')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        foreach ($notifications as $index => $notification) {
            DB::table('notifications')
                ->where('id', $notification->id)
                ->update([
                    'notification_number' => 'NOT-' . str_pad((string) ($index + 1), 8, '0', STR_PAD_LEFT),
                ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('notifications', 'notification_number')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->dropUnique(['notification_number']);
                $table->dropColumn('notification_number');
            });
        }
    }
};
