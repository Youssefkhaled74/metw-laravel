<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('return_cash_backs', function (Blueprint $table) {
            $table->text('value')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('return_cash_backs', function (Blueprint $table) {
            $table->text('value')->nullable(false)->change();
        });
    }
};

