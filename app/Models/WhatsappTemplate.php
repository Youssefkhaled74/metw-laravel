<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'content',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public const FIXED_TEMPLATES = [
        'pending' => [
            'label' => 'Pending (قيد الانتظار)',
            'hints' => ['{customer_name}', '{phone}', '{order_number}', '{order_date}', '{order_time}', '{total_amount}', '{paid_amount}', '{remaining_amount}', '{deposit_percent}', '{payments_accounts}'],
            'content' => "مرحباً {customer_name} - {phone}\n\nهذه رسالة مرسلة آلياً من تطبيق الماركت المصري (ميتوزون Metwzon).\n\nرقم الطلب: {order_number}\nتاريخ الطلب: {order_date}\nوقت الطلب: {order_time}\n\nملخص طلبكم كما يلي:\nإجمالي قيمة الطلب: {total_amount} جنيه مصري\nنسبة السداد المطلوب أون لاين: %{deposit_percent}\nالمبلغ المطلوب تحويله: {paid_amount} جنيه مصري\nالمبلغ المتبقي عند الاستلام: {remaining_amount} جنيه مصري\n\nيرجى تحويل المبلغ لأحد المحافظ التالية:\n{payments_accounts}\n\nبعد التحويل يرجى إرسال صورة التحويل على الواتساب.",
        ],
        'accepted' => [
            'label' => 'Accepted (تم القبول)',
            'hints' => ['{customer_name}', '{phone}', '{order_number}', '{order_date}', '{order_time}'],
            'content' => "مرحباً {customer_name} - {phone}\n\nتم تأكيد طلبكم بنجاح.\n\nرقم الطلب: {order_number}\nتاريخ الطلب: {order_date}\nوقت الطلب: {order_time}\n\nجار الآن تجهيز الطلب في المخزن، وسيتم إشعاركم عند تسليمه لشركة الشحن.",
        ],
        'pickup' => [
            'label' => 'Pickup (تم الاستلام)',
            'hints' => ['{customer_name}', '{phone}', '{order_number}', '{order_date}', '{order_time}'],
            'content' => "مرحباً {customer_name} - {phone}\n\nتم تسليم الطلب لشركة الشحن.\n\nرقم الطلب: {order_number}\nتاريخ الطلب: {order_date}\nوقت الطلب: {order_time}\n\nالطلب الآن في مرحلة الاستلام من المخزن، وسيتم تحديثكم عند بدء الشحن.",
        ],
        'on_way' => [
            'label' => 'On Way (في الطريق)',
            'hints' => ['{customer_name}', '{phone}', '{order_number}', '{order_date}', '{order_time}'],
            'content' => "مرحباً {customer_name} - {phone}\n\nطلبكم خرج للتوصيل.\n\nرقم الطلب: {order_number}\n\nالمندوب في الطريق إليكم، يرجى التأكد من توفر الهاتف لاستلام الاتصال.",
        ],
        'delivered' => [
            'label' => 'Delivered (تم التسليم)',
            'hints' => ['{customer_name}', '{phone}', '{order_number}'],
            'content' => "مرحباً {customer_name} - {phone}\n\nتم تسليم الطلب بنجاح.\n\nرقم الطلب: {order_number}\n\nشكراً لثقتكم بنا.",
        ],
        'cancelled' => [
            'label' => 'Cancelled (ملغي)',
            'hints' => ['{customer_name}', '{phone}', '{order_number}'],
            'content' => "مرحباً {customer_name} - {phone}\n\nتم إلغاء الطلب.\n\nرقم الطلب: {order_number}\n\nفي حالة وجود استفسار يرجى التواصل معنا.",
        ],
        'returned' => [
            'label' => 'Returned (مرتجع)',
            'hints' => ['{customer_name}', '{phone}', '{order_number}'],
            'content' => "مرحباً {customer_name} - {phone}\n\nتم إرجاع الطلب.\n\nرقم الطلب: {order_number}\n\nسيتم مراجعة حالة المرتجع وإعادة المبلغ حسب سياسة الاسترجاع.",
        ],
    ];

    public static function fixedKeys(): array
    {
        return array_keys(self::FIXED_TEMPLATES);
    }

    public static function defaultContent(string $key): string
    {
        return self::FIXED_TEMPLATES[$key]['content'] ?? 'يتم الآن مراجعة طلبكم.';
    }

    public static function label(string $key): string
    {
        return self::FIXED_TEMPLATES[$key]['label'] ?? $key;
    }

    public static function hints(string $key): array
    {
        return self::FIXED_TEMPLATES[$key]['hints'] ?? [];
    }

    public static function ensureFixedTemplatesExist(): void
    {
        foreach (self::FIXED_TEMPLATES as $key => $template) {
            self::query()->firstOrCreate(
                ['key' => $key],
                [
                    'content' => $template['content'],
                    'is_active' => true,
                ]
            );
        }
    }
}
