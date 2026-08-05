<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('metwwebsite_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('label_ar')->nullable();
            $table->string('label_en')->nullable();
            $table->string('value')->nullable(); // Stores the phone, email or address
            $table->string('type')->default('phone'); // phone, email, address, whatsapp
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('metwwebsite_contacts');
    }
};
