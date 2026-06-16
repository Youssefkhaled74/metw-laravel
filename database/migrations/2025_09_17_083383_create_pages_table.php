<?php

use App\Enum\PageType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('pages')) {
            Schema::create('pages', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->string('type')->default(PageType::OTHER->value);
                $table->text('content');
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index('type');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('pages');
    }
}
