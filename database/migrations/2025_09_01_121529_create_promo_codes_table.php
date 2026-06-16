    <?php

    use App\Enum\DiscountType;
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
            Schema::create('promo_codes', function (Blueprint $table) {
                $table->id();
                $table->string('code');
                $table->string('discount_type')->default(DiscountType::FIXED);
                $table->decimal('discount_value', 10, 2);
                $table->date('valid_from')->nullable();
                $table->date('valid_to')->nullable();
                $table->integer('max_uses')->default(0);
                $table->integer('user_max_uses')->default(0);
                $table->integer('uses')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestampsTz();
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('promo_codes');
        }
    };
