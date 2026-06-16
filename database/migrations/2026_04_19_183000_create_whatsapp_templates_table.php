<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->longText('content');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $now = now();
        DB::table('whatsapp_templates')->insert([
            [
                'key' => 'pending',
                'content' => "مرحباً {customer_name} - {phone}\n\nهذه رسالة مرسلة آلياً من تطبيق الماركت المصري (ميتوزون Metwzon).\n\nرقم الطلب: {order_number}\nتاريخ الطلب: {order_date}\nوقت الطلب: {order_time}\n\nملخص طلبكم كما يلي:\nإجمالي قيمة الطلب: {total_amount} جنيه مصري\nنسبة السداد المطلوب أون لاين: %{deposit_percent}\nالمبلغ المطلوب تحويله: {paid_amount} جنيه مصري\nالمبلغ المتبقي عند الاستلام: {remaining_amount} جنيه مصري\n\nيرجى تحويل المبلغ لأحد المحافظ التالية:\n{payments_accounts}\n\nبعد التحويل يرجى إرسال صورة التحويل على الواتساب.",
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'accepted',
                'content' => "مرحباً {customer_name} - {phone}\n\nتم تأكيد طلبكم بنجاح.\n\nرقم الطلب: {order_number}\nتاريخ الطلب: {order_date}\nوقت الطلب: {order_time}\n\nجار الآن تجهيز الطلب في المخزن، وسيتم إشعاركم عند تسليمه لشركة الشحن.",
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'pickup',
                'content' => "مرحباً {customer_name} - {phone}\n\nتم تسليم الطلب لشركة الشحن.\n\nرقم الطلب: {order_number}\nتاريخ الطلب: {order_date}\nوقت الطلب: {order_time}\n\nالطلب الآن في مرحلة الاستلام من المخزن، وسيتم تحديثكم عند بدء الشحن.",
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'on_way',
                'content' => "مرحباً {customer_name} - {phone}\n\nطلبكم خرج للتوصيل.\n\nرقم الطلب: {order_number}\n\nالمندوب في الطريق إليكم، يرجى التأكد من توفر الهاتف لاستلام الاتصال.",
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'delivered',
                'content' => "مرحباً {customer_name} - {phone}\n\nتم تسليم الطلب بنجاح.\n\nرقم الطلب: {order_number}\n\nشكراً لثقتكم بنا.",
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'cancelled',
                'content' => "مرحباً {customer_name} - {phone}\n\nتم إلغاء الطلب.\n\nرقم الطلب: {order_number}\n\nفي حالة وجود استفسار يرجى التواصل معنا.",
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'returned',
                'content' => "مرحباً {customer_name} - {phone}\n\nتم إرجاع الطلب.\n\nرقم الطلب: {order_number}\n\nسيتم مراجعة حالة المرتجع وإعادة المبلغ حسب سياسة الاسترجاع.",
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_templates');
    }
};
