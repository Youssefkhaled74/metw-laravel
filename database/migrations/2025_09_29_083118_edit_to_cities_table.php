<?php

use App\Models\State;
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
        Schema::table('cities', function (Blueprint $table) {
            //state id
            if(!Schema::hasColumn('cities', 'state_id'  )){
                $table->dropColumn('name');
                $table->string('name_en')->after('id')->nullable();
                $table->string('name_ar')->after('name_en')->nullable();
                $table->foreignIdFor(State::class)->nullable()->constrained()->after('name_ar');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            if(Schema::hasColumn('cities', 'state_id'  )){
                $table->dropConstrainedForeignIdFor(State::class);
                $table->dropColumn('name_en');
                $table->dropColumn('name_ar');
                $table->string('name')->after('id');
            }
        });
    }
};
