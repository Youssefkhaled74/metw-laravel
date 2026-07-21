# تقرير التطوير

**التاريخ:** 2026-07-20
**المطور:** youssefkhaled_74
**الفرع:** phase-two-admin-uiux

---

## ملخص

جلسة التطوير اليوم شملت إضافات رئيسية عبر لوحتي تحكم المدير والبائع. تم تنفيذ سبع ميزات مختلفة عبر ثلاثة commits:

1. **سير عمل إلغاء الطلبات / طلبات الإرجاع** — دورة حياة إلغاء متكاملة تشمل enum جديد، وcontroler، وmigration، وviews، وتحديثات حالة من جهة البائع، وتكامل مع لوحة تحكم المدير.
2. **المهام العاجلة لوحة تحكم البائع** — صفحة مخصصة تجمع طلبات الشراء المعلقة وطلبات الإلغاء وطلبات الإرجاع والطلبات المشحونة التي تحتاج اهتمام البائع، مع فلترة AJAX وتحديث تلقائي.
3. **مصفوفة صلاحيات الموظفين** — محرر صلاحيات متعدد الموظفين في صفحة واحدة باستخدام `syncPermissions()` من Spatie، مع مجموعات قابلة للطي، وأعمدة ثابتة، وأزرار تبديل.
4. **المتطلبات 26–30** — خمس تحسينات أصغر: إعادة تسمية قسم المحافظ الإلكترونية، صفحة معلومات الاتصال الإدارية، رفع شعار البائع، ترتيب التصنيفات الرئيسية، وإصلاح خطأ في اسم العلامة التجارية.

**الإجمالي:** **3,661 سطر مضاف**، **63 سطر محذوف** عبر **39 ملف**.

---

## المهام المكتملة

---

### 1. سير عمل إلغاء الطلبات / طلبات الإرجاع (المدير + البائع)

#### الوصف

كان المشروع يفتقر إلى سير عمل منظم لإلغاء الطلبات. كانت طلبات الإلغاء من العملاء تحتاج إلى تدفق إدارة مخصص للمدير (قائمة، شكاوى، موافق عليها/مكتملة)، وآلية استجابة من جهة البائع، ومعالجة استرداد المبلغ عبر المحفظة الإلكترونية. سابقاً، كانت طلبات الإلغاء والإرجاع تتشارك نفس الصفحة العامة بدون أي تمييز.

#### قبل

- كانت طلبات الإلغاء والإرجاع تُدار من خلال صفحة واحدة `admin.return-requests` بدون فلترة حسب نوع الطلب.
- لا توجد views مخصصة للإلغاء، ولا routes خاصة بالإلغاء.
- لا تتبع لحالة البائع على طلبات الإلغاء.
- لا يوجد تدفق لإعادة التفعيل أو الإكمال مع الاسترداد من جهة المدير.
- لا يوجد enum `CancellationSellerStatus`.
- لم يكن في لوحة تحكم المدير widgets للشكاوى أو الطلبات المعلقة.

#### بعد

- مجموعة routes مخصصة `admin.cancellations.*` بأربع views: index (جميع الإلغاءات)، complaints (الإلغاءات بها شكاوى)، approved (بانتظار إكمال المدير)، وshow (عرض تفصيلي مع نوافذ إعادة التفعيل والاسترداد).
- enum جديد `CancellationSellerStatus` بخمس حالات: `approved`، `rejected`، `under_inspection`، `return_accepted`، `return_rejected`.
- طريقة `updateSellerStatus()` على `ReturnRequestController` للرد على طلبات الإلغاء من جهة البائع.
- يمكن للمدير إعادة تفعيل الإلغاءات المرفوضة وإكمال المواف عليها مع استرداد المبلغ للمحفظة.
- نظام إشعارات متكامل: يتلقى المشترى والبائع إشعارات عند إعادة التفعيل وتحديثات الحالة وشحن المبالغ للمحفظة.
- تحديث لوحة تحكم المدير بقسمين جديدين: "شكاوى الإلغاءات" و"الإلغاءات المواف عليها".
- تم توسيع نموذج `ReturnRequest` بـ 8 حقول جديدة و4 scopes جديدة.
- تتضمن صفحة طلبات الإرجاع الآن فلتر `request_type` (إرجاع مقابل إلغاء).

### التغييرات التقنية

- **نماذج معدلة:** `app/Models/ReturnRequest.php` — أُضيفت الحقول `seller_status`، `seller_rejection_reason`، `return_rejection_reason`، `admin_reactivation_reason`، `reactivated_at`، `inspected_at`، `admin_refund_amount`، `wallet_credited_at` إلى `$fillable`؛ تحويل `seller_status` إلى `CancellationSellerStatus`؛ أُضيفت scopes: `scopeSellerApproved`، `scopePendingAdminCompletion`، `scopeWithComplaints`؛ أُضيفت طرق مساعدة: `sellerStatusLabel()`، `sellerStatusCss()`.
- **Controlers جديدة:** `app/Http/Controllers/Dashboard/Admin/AdminCancellationController.php` — `index()`، `complaints()`، `approved()`، `show()`، `reactivate()`، `completeCancellation()`.
- **Controlers معدلة:** `app/Http/Controllers/Dashboard/Admin/AdminDashboardController.php` — أُضيف قسمان للإلغاءات في `urgentTasks()`؛ `app/Http/Controllers/Dashboard/Vendor/ReturnRequestController.php` — أُضيفت `updateSellerStatus()`، وأُضيف تحميل `order.user`.
- **Enum جديد:** `app/Enum/CancellationSellerStatus.php` — 5 حالات مع طرق `label()` و`cssClass()`.
- **Migration جديد:** `database/migrations/2026_07_20_120000_add_cancellation_workflow_columns_to_return_requests_table.php` — يُضيف 8 أعمدة إلى `return_requests` مع فحوصات `hasColumn`.
- **Blade Views جديدة:** `resources/views/dashboard/admin/cancellations/index.blade.php`، `complaints.blade.php`، `approved.blade.php`، `show.blade.php`.
- **Blade Views معدلة:** `resources/views/dashboard/admin/return-requests.blade.php` — أُضيف فلتر `request_type`.
- **Routes جديدة:** 6 routes للمدير (`cancellations.index`، `.complaints`، `.approved`، `.show`، `.reactivate`، `.complete`)، و1 route للبائع (`return-requests.seller-status`).

---

### 2. صفحة المهام العاجلة لوحة تحكم البائع

#### الوصف

لم يكن لدى البائعين عرض مركزي للإجراءات المعلقة. طلبات الشراء المعلقة وطلبات الإلغاء وطلبات الإرجاع والطلبات المشحونة كانت مبعثرة عبر صفحات مختلفة. تجمع هذه الميزة جميع العناصر العاجلة في صفحة واحدة قابلة للفلترة مع تحديث تلقائي.

#### قبل

- كان البائعون يضطرون للتنقل بين صفحات منفصلة للطلبات وطلبات الإرجاع وتتبع الشحن.
- لا يوجد عرض مجمع للإجراءات المعلقة.
- لا يوجد ترتيب حسب الأولوية.

#### بعد

- صفحة جديدة `vendor.urgent-tasks` مع فلترة AJAX حسب النوع (الكل، مشتريات، إلغاءات، إرجاعات، شحن).
- خيارات الترتيب: حسب الأولوية، الأحدث، الأقدم.
- بحث نصي كامل في أرقام الطلبات وأسماء العملاء.
- تحديث تلقائي كل 30 ثانية مع مؤشر مرئي.
- بطاقات إحصائية تُظهر العدد Critically والأعلى والإجمالي وحسب كل نوع.
- زر معالجة (قبول/رفض) على كل بطاقة مهمة مع مربع حوار للتأكيد.
- إشعارات toast للنجاح في المعالجة.
- حساب الأولوية حسب عمر المهمة (حرجة: >24 ساعة، عالية: >12 ساعة، متوسطة: >48 ساعة).

### التغييرات التقنية

- **خدمة جديدة:** `app/Services/VendorUrgentTaskService.php` — 361 سطر؛ طرق: `getAllTasks()`، `getPurchaseOrders()`، `getCancellationRequests()`، `getReturnRequests()`، `getShippedOrders()`، `getStats()`، `processTask()`، `processPurchase()`، `processCancellationRequest()`، `processReturnRequest()`، `processShipping()`، `getAgeText()`.
- **Controller جديد:** `app/Http/Controllers/Dashboard/Vendor/UrgentTasksController.php` — `index()`، `filter()` (AJAX JSON)، `stats()` (AJAX JSON)، `process()` (AJAX POST).
- **Blade Views جديدة:** `resources/views/dashboard/vendor/urgent-tasks/index.blade.php` (الصفحة الرئيسية مع JS)، `partials/stats-cards.blade.php`، `partials/filter-bar.blade.php`، `partials/task-list.blade.php`، `partials/empty-state.blade.php`، `partials/empty-state-inner.blade.php`.
- **Layout معدل:** `resources/views/layouts/vendor.blade.php` — أُضيف رابط شريط جانبي مع أيقونة `fa-bolt`.
- **Routes جديدة:** 4 routes للبائع (`urgent-tasks`، `urgent-tasks.filter`، `urgent-tasks.stats`، `urgent-tasks.process`).
- **ملفات لغة معدلة:** `lang/en/admin-dashboard.php`، `lang/ar/admin-dashboard.php` — أُضيفت مفاتيح الترجمة لمصفوفة الصلاحيات (في نفس الـ commit).

---

### 3. مصفوفة صلاحيات الموظفين

#### الوصف

كانت إدارة صلاحيات كل موظف على حدة تتطلب الانتقال إلى صفحة تعديل كل موظف بشكل منفصل. توفر هذه الميزة عرض مصفوفة في صفحة واحدة (الموظفون كأعمدة والصلاحيات كصفوف) لإدارة الصلاحيات بشكل جماعي، مستندة إلى بنية Spatie للصلاحيات.

#### قبل

- كانت الصلاحيات تُدار لموظف واحد في كل مرة عبر نموذج التعديل.
- لا يوجد نظرة عامة على أي موظف لديه أي صلاحيات.
- لا توجد إمكانية لتعديل الصلاحيات بشكل جماعي.

#### بعد

- مصفوفة في صفحة واحدة مع الموظفين كأعمدة والصلاحيات كصفوف.
- تمرير أفقي مع العمود الأول ثابت (أسماء الصلاحيات).
- الصلاحيات مجمّعة حسب الوحدة (مجموعات قابلة للطي).
- تبديل الكل: تحديد الكل، إلغاء التحديد، تبديل لكل عمود (موظف)، تبديل لكل مجموعة.
- عداد حي للصلاحيات المحددة.
- زر حفظ مع حالة التحميل.
- بحث وفلترة الموظفين.
- تصميم متجاوب مع شريط تمرير مخصص.
- جميع تغييرات الصلاحيات تمر عبر `syncPermissions()` من Spatie — الصلاحيات الموروثة من الأدوار تُدار بشكل منفصل.

### التغييرات التقنية

- **Controller جديد:** `app/Http/Controllers/Dashboard/Admin/PermissionMatrixController.php` — `index()` (يحمّل الموظفين + الصلاحيات + الصلاحيات المجمّعة + خريطة الصلاحيات)، `update()` (يُحقق المصفوفة ويُنادي `syncPermissions()` داخل transaction).
- **Blade View جديد:** `resources/views/dashboard/admin/permission-matrix/index.blade.php` — 442 سطر؛ جدول مصفوفة كامل، أعمدة ثابتة، مجموعات قابلة للطي، JavaScript للتحديد/التبديل/تبديل المجموعة/تبديل العمود، حفظ مع حالة تحميل.
- **Layout معدل:** `resources/views/layouts/admin.blade.php` — أُضيف رابط شريط جانبي مع أيقونة `fa-table-cells`.
- **Routes جديدة:** 2 routes للمدير (`permission-matrix.index` GET، `permission-matrix.update` PUT).
- **ملفات لغة معدلة:** `lang/en/admin-dashboard.php`، `lang/ar/admin-dashboard.php` — أُضيفت 11 مفتاح ترجمة للمصفوفة.

---

### 4. إعادة تسمية قسم المحافظ الإلكترونية (المتطلب 26)

#### الوصف

كان قسم الإعدادات المعروف سابقاً بـ "E-Wallet Details" (إنجليزي) و"بيانات المحافظ الإلكترونية" (عربي) يحتاج إلى إعادة تسمية إلى "Electronic Wallet Information" للوضوح. بالإضافة إلى ذلك، تُغيرت أيقونة الشريط الجانبي من `fa-address-card` إلى `fa-wallet` لتمثيل أفضل. كما تم إصلاح خطأ إملائي في اسم العلامة التجارية ("Mitozon" إلى "Metwzon").

#### قبل

- تسمية الشريط الجانبي: "E-Wallet Details" / "بيانات المحافظ الإلكترونية"
- أيقونة الشريط الجانبي: `fa-address-card`
- وصف المعاملة يحتوي على خطأ: "refunded via Mitozon Wallet"

#### بعد

- تسمية الشريط الجانبي: "Electronic Wallet Information" / "معلومات المحافظ الإلكترونية"
- أيقونة الشريط الجانبي: `fa-wallet`
- وصف المعاملة مُصحح: "refunded via Metwzon Wallet"
- تم تحديث 14 مفتاح ترجمة متعلقاً بالإنجليزي والعربي.

### التغييرات التقنية

- **ملفات لغة معدلة:** `lang/en/admin-dashboard.php` — 14 مفتاح أُعيدت تسميتها من "E-Wallet Details" إلى "Electronic Wallet"؛ `lang/ar/admin-dashboard.php` — 14 مفتاح محدّثة بعربي أكثر وضوحاً.
- **Controller معدل:** `app/Http/Controllers/Dashboard/Admin/AdminCancellationController.php` — السطر 253: "Mitozon" → "Metwzon".
- **Layout معدل:** `resources/views/layouts/admin.blade.php` — أيقونة الشريط الجانبي تُغيرت من `fa-address-card` إلى `fa-wallet`.

---

### 5. صفحة معلومات الاتصال الإدارية (المتطلب 27)

#### الوصف

كانت هناك حاجة لصفحة جديدة للقراءة فقط في لوحة تحكم المدير لعرض معلومات الاتصال للموظفين الإداريين المخولين، المحملة تلقائياً من قاعدة بيانات الموظفين. هذا يُلغي الحاجة للصيانة اليدوية لقوائم الاتصال.

#### قبل

- لا يوجد عرض مركزي لمعلومات الاتصال الإدارية.

#### بعد

- صفحة جديدة `admin.management-contact.index` تعرض بطاقات الموظفين في شبكة متجاوبة.
- كل بطاقة تُظهر: الاسم، المنصب، البريد الإلكتروني (رابط قابل للنقر)، رقم الهاتف (رابط قابل للنقر)، رقم الموظف.
- محمل تلقائياً من نموذج `Employee` — لا حاجة لإدخال يوي.
- متاح فقط للموظفين الذين لديهم صلاحية `admin.employees.index`.

### التغييرات التقنية

- **Controller جديد:** `app/Http/Controllers/Dashboard/Admin/ManagementContactController.php` — `index()` يحمّل الموظفين مع حقول الاتصال، مرتّبين حسب المنصب ثم الاسم.
- **Blade View جديد:** `resources/views/dashboard/admin/management-contact/index.blade.php` — 102 سطر؛ شبكة بطاقات متجاوبة مع تأثيرات hover.
- **Layout معدل:** `resources/views/layouts/admin.blade.php` — أُضيف رابط شريط جانبي مع أيقونة `fa-address-book`.
- **Route جديد:** `GET /admin/management-contact`.
- **ملفات لغة معدلة:** `lang/en/admin-dashboard.php`، `lang/ar/admin-dashboard.php` — أُضيف `management_contact_info` و`management_contact_subtitle`.

---

### 6. رفع شعار البائع / العلامة التجارية (المتطلب 29)

#### الوصف

احتاج البائعون إلى القدرة على رفع وإدارة شعار علامتهم التجارية من لوحة تحكمهم. يجب عرض هذا الشعار في كل مكان تُعرض فيه علامة البائع التجارية.

#### قبل

- لا يوجد واجهة لإدارة شعار البائع.
- حقل `brand_name` كان موجوداً في قاعدة البيانات لكنه لم يكن في مصفوفة `$fillable` في نموذج `Vendor`.

#### بعد

- صفحة جديدة `vendor.brand-logo` بتخطيط عمودين: عرض الشعار الحالي (مع زر حذف) ونموذج الرفع.
- الرفع يدعم JPEG، PNG، GIF، SVG (حد أقصى 2 ميجابايت).
- معاينة حية قبل الرفع من جانب العميل.
- الشعار القديم يُحذف تلقائياً من التخزين عند الاستبدال.
- حذف الشعار مع مربع حوار للتأكيد.
- حقل `brand_name` أصبح قابلاً للتعديل من نفس الصفحة.

### التغييرات التقنية

- **Controller جديد:** `app/Http/Controllers/Dashboard/Vendor/BrandLogoController.php` — `index()` (عرض النموذج)، `update()` (رفع مع تنظيف الشعار القديم)، `destroy()` (حذف الشعار).
- **Blade View جديد:** `resources/views/dashboard/vendor/brand-logo/index.blade.php` — 140 سطر؛ تخطيط عمودين، رفع ملفات مع معاينة، نموذج حذف.
- **Model معدل:** `app/Models/Vendor.php` — أُضيف `brand_name` إلى `$fillable`.
- **Layout معدل:** `resources/views/layouts/vendor.blade.php` — أُضيف رابط شريط جانبي مع أيقونة `fa-award`.
- **Routes جديدة:** 3 routes للبائع (`brand-logo.index`، `brand-logo.update`، `brand-logo.destroy`).
- **ملفات لغة معدلة:** `lang/en/vendor-dashboard.php`، `lang/ar/vendor-dashboard.php` — أُضيفت 17 مفتاح ترجمة لإدارة الشعار.

---

### 7. ترتيب التصنيفات الرئيسية حسب الموقع (المتطلب 30)

#### الوصف

احتاجت التصنيفات الرئيسية للمنتجات إلى ترتيب عرض قابل للضبط. سابقاً، كان يمكن ترتيب التصنيفات فقط حسب المعرف أو الاسم أو الـ slug أو تاريخ الإنشاء — لا أي منها يدعم الترتيب حسب متطلبات العمل.

#### قبل

- لا يوجد عمود `position` في جدول `main_categories`.
- الترتيب الافتراضي كان `id DESC`.
- لا يوجد حقل إدخال الموقع في نماذج الإنشاء/التعديل.
- خطأ موجود: `$imagePath` كان يُستخدم قبل تعريفه عند عدم رفع صورة جديدة أثناء التحديث.

#### بعد

- عمود جديد `position` صحيح عددية غير موقعة (افتراضي 0) في `main_categories`.
- الترتيب الافتراضي تغير إلى `position ASC`.
- أُضيف حقل إدخال الموقع إلى نموذجي الإنشاء والتعديل مع نص مساعد.
- عمود "ترتيب العرض" القابل للفرز في جدول العرض.
- تم إصلاح الخطأ: أُعيد هيكلة منطق تحديث الصورة ليتم تعيين `$updateData['image']` فقط عند رفع ملف جديد.

### التغييرات التقنية

- **Migration جديد:** `database/migrations/2026_07_20_130000_add_position_to_main_categories_table.php` — يُضيف عمود `position`.
- **Model معدل:** `app/Models/MainCategory.php` — أُضيف `position` إلى `$fillable`.
- **Controller معدل:** `app/Http/Controllers/Dashboard/Admin/Settings/MainCategoryController.php` — تغير الترتيب الافتراضي إلى `position ASC`؛ أُضيف `position` إلى `$allowedSorts`؛ أُضيف قاعدة التحقق في `store()` و`update()`؛ تم إصلاح خطأ `$imagePath` في طريقة `update()`.
- **Blade Views معدلة:** `resources/views/dashboard/admin/settings/main-categories/create.blade.php`، `edit.blade.php` — أُضيف حقل إدخال الموقع؛ `index.blade.php` — أُضيف عمود "ترتيب العرض" القابل للفرز.
- **ملفات لغة معدلة:** `lang/en/admin-dashboard.php`، `lang/ar/admin-dashboard.php` — أُضيف `display_order` و`display_order_help`.

---

## الملفات المعدلة

| # | الملف | نوع التغيير | الوصف |
|---|-------|-------------|-------|
| 1 | `app/Enum/CancellationSellerStatus.php` | أُنشئ | Enum جديد بـ 5 حالات لحالة البائع في سير عمل الإلغاء |
| 2 | `app/Http/Controllers/Dashboard/Admin/AdminCancellationController.php` | أُنشئ | إدارة إلغاءات المدير: قائمة، شكاوى، موافق عليها، عرض، إعادة تفعيل، إكمال |
| 3 | `app/Http/Controllers/Dashboard/Admin/AdminDashboardController.php` | عُدل | أُضيف قسمان لإلحاح الإلغاءات في لوحة التحكم |
| 4 | `app/Http/Controllers/Dashboard/Admin/ManagementContactController.php` | أُنشئ | صفحة معلومات اتصال الموظفين للقراءة فقط |
| 5 | `app/Http/Controllers/Dashboard/Admin/PermissionMatrixController.php` | أُنشئ | مصفوفة صلاحيات متعددة الموظفين مع تحديث جماعي |
| 6 | `app/Http/Controllers/Dashboard/Admin/Settings/MainCategoryController.php` | عُدل | أُضيف حقل الموقع، الترتيب الافتراضي، إصلاح خطأ |
| 7 | `app/Http/Controllers/Dashboard/Vendor/BrandLogoController.php` | أُنشئ | رفع، تحديث، حذف شعار البائع |
| 8 | `app/Http/Controllers/Dashboard/Vendor/ReturnRequestController.php` | عُدل | أُضيفت طريقة `updateSellerStatus()` للرد على الإلغاءات |
| 9 | `app/Http/Controllers/Dashboard/Vendor/UrgentTasksController.php` | أُنشئ | مهام البائع العاجلة: فهرس، فلترة AJAX، إحصائيات، معالجة |
| 10 | `app/Models/MainCategory.php` | عُدل | أُضيف `position` إلى `$fillable` |
| 11 | `app/Models/ReturnRequest.php` | عُدل | أُضيفت 8 حقول fillable، وتحويل، وscopes، وطرق مساعدة |
| 12 | `app/Models/Vendor.php` | عُدل | أُضيف `brand_name` إلى `$fillable` |
| 13 | `app/Services/VendorUrgentTaskService.php` | أُنشئ | المنطق التجاري لتجميع مهام البائع العاجلة |
| 14 | `database/migrations/2026_07_20_120000_add_cancellation_workflow_columns_to_return_requests_table.php` | أُنشئ | يُضيف 8 أعمدة إلى جدول return_requests |
| 15 | `database/migrations/2026_07_20_130000_add_position_to_main_categories_table.php` | أُنشئ | يُضيف عمود position إلى main_categories |
| 16 | `lang/ar/admin-dashboard.php` | عُدل | تُحدّث ترجمات المحافظ، تُضيف مفاتيح مصفوفة الصلاحيات + الاتصال الإداري + ترتيب العرض |
| 17 | `lang/ar/vendor-dashboard.php` | عُدل | أُضيفت 17 مفتاح ترجمة للشعار |
| 18 | `lang/en/admin-dashboard.php` | عُدل | تُحدّث ترجمات المحافظ، تُضيف مفاتيح مصفوفة الصلاحيات + الاتصال الإداري + ترتيب العرض |
| 19 | `lang/en/vendor-dashboard.php` | عُدل | أُضيفت 17 مفتاح ترجمة للشعار |
| 20 | `resources/views/dashboard/admin/cancellations/approved.blade.php` | أُنشئ | قائمة إلغاءات مواف عليها مع نافذة الاسترداد |
| 21 | `resources/views/dashboard/admin/cancellations/complaints.blade.php` | أُنشئ | قائمة شكاوى الإلغاءات مع نافذة إعادة التفعيل |
| 22 | `resources/views/dashboard/admin/cancellations/index.blade.php` | أُنشئ | قائمة جميع الإلغاءات مع بحث وفلتر |
| 23 | `resources/views/dashboard/admin/cancellations/show.blade.php` | أُنشئ | صفحة تفاصيل الإلغاء مع شريط الإجراءات |
| 24 | `resources/views/dashboard/admin/management-contact/index.blade.php` | أُنشئ | شبكة بطاقات معلومات اتصال الموظفين |
| 25 | `resources/views/dashboard/admin/permission-matrix/index.blade.php` | أُنشئ | جدول مصفوفة الصلاحيات الكامل مع التفاعل JS |
| 26 | `resources/views/dashboard/admin/return-requests.blade.php` | عُدل | أُضيف فلتر request_type |
| 27 | `resources/views/dashboard/admin/settings/main-categories/create.blade.php` | عُدل | أُضيف حقل إدخال الموقع |
| 28 | `resources/views/dashboard/admin/settings/main-categories/edit.blade.php` | عُدل | أُضيف حقل إدخال الموقع |
| 29 | `resources/views/dashboard/admin/settings/main-categories/index.blade.php` | عُدل | أُضيف عمود ترتيب العرض القابل للفرز |
| 30 | `resources/views/dashboard/vendor/brand-logo/index.blade.php` | أُنشئ | صفحة رفع/معاينة/حذف شعار البائع |
| 31 | `resources/views/dashboard/vendor/urgent-tasks/index.blade.php` | أُنشئ | الصفحة الرئيسية للمهام العاجلة مع AJAX |
| 32 | `resources/views/dashboard/vendor/urgent-tasks/partials/empty-state-inner.blade.php` | أُنشئ | حالة فارغة عندما لا توجد مهام (داخلي) |
| 33 | `resources/views/dashboard/vendor/urgent-tasks/partials/empty-state.blade.php` | أُنشئ | حالة فارغة عندما لا توجد مهام (مخفي) |
| 34 | `resources/views/dashboard/vendor/urgent-tasks/partials/filter-bar.blade.php` | أُنشئ | أزرار الفرز والفلتر حسب النوع |
| 35 | `resources/views/dashboard/vendor/urgent-tasks/partials/stats-cards.blade.php` | أُنشئ | بطاقات إحصائية (حرجة، عالية، إجمالي، شحن) |
| 36 | `resources/views/dashboard/vendor/urgent-tasks/partials/task-list.blade.php` | أُنشئ | قائمة بطاقات المهام مع أزرار المعالجة |
| 37 | `resources/views/layouts/admin.blade.php` | عُدل | أُضيفت 3 روابط شريط جانبي: مصفوفة الصلاحيات، الاتصال الإداري، تغيير أيقونة المحافظ |
| 38 | `resources/views/layouts/vendor.blade.php` | عُدل | أُضيفت 2 رابط شريط جانبي: المهام العاجلة، الشعار |
| 39 | `routes/web.php` | عُدل | أُضيفت 15 route جديدة عبر مجموعتي المدير والبائع |

---

## تغييرات قاعدة البيانات

تم إنشاء migration جديدان (بانتظار `php artisan migrate`):

### Migration 1: أعمدة سير عمل الإلغاء

**الملف:** `database/migrations/2026_07_20_120000_add_cancellation_workflow_columns_to_return_requests_table.php`

يُضيف 8 أعمدة إلى جدول `return_requests`:

| العمود | النوع | Nullable | الوصف |
|--------|-------|----------|-------|
| `seller_status` | string | نعم | حالة استجابة البائع الحالية |
| `seller_rejection_reason` | text | نعم | سبب رفض البائع |
| `return_rejection_reason` | text | نعم | سبب رفض الإرجاع |
| `admin_reactivation_reason` | text | نعم | سبب إعادة التفعيل من المدير |
| `reactivated_at` | timestamp | نعم | متى أعاد المدير تفعيل الطلب |
| `inspected_at` | timestamp | نعم | متى استُلم المنتج للفحص |
| `admin_refund_amount` | decimal(12,2) | نعم | مبلغ الاسترداد النهائي الذي حدده المدير |
| `wallet_credited_at` | timestamp | نعم | متى شُحن المبلغ للمحفظة |

جميع الأعمدة محمية بفحوصات `Schema::hasColumn()` لضمان عدم التكرار.

### Migration 2: موقع التصنيفات الرئيسية

**الملف:** `database/migrations/2026_07_20_130000_add_position_to_main_categories_table.php`

يُضيف عمود واحد إلى جدول `main_categories`:

| العمود | النوع | الافتراضي | الوصف |
|--------|-------|-----------|-------|
| `position` | unsigned integer | 0 | ترتيب العرض (الأصغر يظهر أولاً) |

---

## تغييرات API

لا توجد تغييرات على REST API. جميع النهايات الجديدة هي routes ويب تُعرض من الخادم مع تحديثات جزئية AJAX:

### Routes المدير (جديدة)

| الطريقة | URI | الاسم | الوصف |
|---------|-----|-------|-------|
| GET | `/admin/cancellations` | `admin.cancellations.index` | قائمة جميع الإلغاءات |
| GET | `/admin/cancellations/complaints` | `admin.cancellations.complaints` | قائمة الإلغاءات بها شكاوى |
| GET | `/admin/cancellations/approved` | `admin.cancellations.approved` | قائمة الإلغاءات المواف عليها من البائع |
| GET | `/admin/cancellations/{id}` | `admin.cancellations.show` | صفحة تفاصيل الإلغاء |
| POST | `/admin/cancellations/{id}/reactivate` | `admin.cancellations.reactivate` | إعادة تفعيل طلب الإلغاء المُلغى |
| POST | `/admin/cancellations/{id}/complete` | `admin.cancellations.complete` | إكمال الإلغاء مع الاسترداد |
| GET | `/admin/permission-matrix` | `admin.permission-matrix.index` | صفحة مصفوفة الصلاحيات |
| PUT | `/admin/permission-matrix` | `admin.permission-matrix.update` | تحديث جماعي للصلاحيات |
| GET | `/admin/management-contact` | `admin.management-contact.index` | صفحة معلومات الاتصال الإدارية |

### Routes البائع (جديدة)

| الطريقة | URI | الاسم | الوصف |
|---------|-----|-------|-------|
| PATCH | `/vendor/return-requests/{id}/seller-status` | `vendor.return-requests.seller-status` | تحديث حالة البائع على الإلغاء |
| GET | `/vendor/urgent-tasks` | `vendor.urgent-tasks` | صفحة المهام العاجلة |
| GET | `/vendor/urgent-tasks/filter` | `vendor.urgent-tasks.filter` | AJAX: فلترة المهام حسب النوع |
| GET | `/vendor/urgent-tasks/stats` | `vendor.urgent-tasks.stats` | AJAX: الحصول على الإحصائيات المحدثة |
| POST | `/vendor/urgent-tasks/process` | `vendor.urgent-tasks.process` | AJAX: معالجة/قبول مهمة |
| GET | `/vendor/brand-logo` | `vendor.brand-logo.index` | صفحة إدارة الشعار |
| PUT | `/vendor/brand-logo` | `vendor.brand-logo.update` | رفع/تحديث الشعار |
| DELETE | `/vendor/brand-logo` | `vendor.brand-logo.destroy` | حذف الشعار |

---

## تغييرات واجهة المستخدم

### صفحات جديدة (8)

1. **فهرس إلغاءات المدير** — جدول مع بحث، وفلتر حالة، وتفصيل. يُظهر رقم الإلغاء، ورقم الطلب، والعميل، والبائع، والحالة (كل من الرئيسية وحالة البائع)، والسبب، والتاريخ.
2. **شكاوى إلغاءات المدير** — جدول مُصفّى يُظهر الإلغاءات التي بها شكاوى نشطة من المشترين فقط. يتضمن نافذة إعادة التفعيل.
3. **إلغاءات مواف عليها للمدير** — جدول مُصفّى يُظهر الإلغاءات المواف عليها من البائع بانتظار إكمال المدير. يتضمن نافذة الاسترداد مع إدخال المبلغ وشحن المحفظة.
4. **تفاصيل إلغاء المدير** — صفحة تفاصيل كاملة مع معلومات الطلب، وحالة البائع، ومعلومات إعادة التفعيل، ومعلومات الاسترداد، جدول المنتجات، قائمة الشكاوى، معلومات العميل في الشريط الجانبي، معلومات البائع في الشريط الجانبي، وأزرار الإجراءات.
5. **مصفوفة صلاحيات المدير** — جدول أفقي مع عمود صلاحيات ثابت، ورؤوس موظفين مع المنصب وأزرار التبديل، وصفوف صلاحيات قابلة للطي ومجمّعة، تحديد/إلغاء تحديد الكل، عداد حي.
6. **الاتصال الإداري للمدير** — شبكة بطاقات تعرض معلومات اتصال الموظفين مع روابط البريد الإلكتروني والهاتف.
7. **شعار البائع** — تخطيط عمودين: اليسار يُظهر الشعار الحالي مع زر الحذف؛ اليمين يُظهر نموذج الرفع مع إدخال اسم العلامة التجارية ومعاينة حية.
8. **المهام العاجلة للبائع** — بطاقات إحصائية (حرجة، عالية، إجمالي، شحن)، شريط فلتر (	tabs الأنواع + خيارات الترتيب)، حقل بحث، بطاقات مهام مع شارات النوع/الأولوية/الحالة، أزرار المعالجة، مؤشر تحديث تلقائي.

### صفحات مُحدّثة (4)

1. **لوحة تحكم المدير** — أُضيف قسمان لإلحاح الإلغاءات: "شكاوى الإلغاءات" و"الإلغاءات المواف عليها" مع العدّادات.
2. **طلبات إرجاع المدير** — فلتر `request_type` جديد (الكل / إرجاع / إلغاء).
3. **فهرس التصنيفات الرئيسية** — عمود "ترتيب العرض" القابل للفرز جديد بين المعرف والاسم.
4. **إنشاء/تعديل التصنيفات الرئيسية** — حقل إدخال رقم الموقع جديد مع نص مساعد.

### تحديثات الشريط الجانبي (3)

1. **الشريط الجانبي للمدير** — أُضيف رابط "مصفوفة الصلاحيات" (أيقونة: `fa-table-cells`)، ورابط "معلومات الاتصال الإدارية" (أيقونة: `fa-address-book`)، تُغيرت أيقونة المحافظ من `fa-address-card` إلى `fa-wallet`.
2. **الشريط الجانبي للبائع** — أُضيف رابط "المهام العاجلة" (أيقونة: `fa-bolt`)، ورابط "الشعار" (أيقونة: `fa-award`).

---

## إعادة الهيكلة

- **`MainCategoryController::update()`** — أُعيد هيكلة بناء مصفوفة `$updateData` قبل التعامل الشرطي مع الصورة، مما يُصلح خطأً مسبقاً حيث كان `$imagePath` يُستخدم في استدعاء `update()` حتى عند عدم رفع صورة جديدة (كان سيسبب خطأ variable غير معرّف).
- **`AdminCancellationController::completeCancellation()`** — أُصلح خطأ إملائي "Mitozon" إلى "Metwzon" في وصف معاملة المحفظة.
- **Admin return-requests blade** — عُدّلت أعمدة الشبكة من `col-lg-5/4/3/3` إلى `col-lg-4/2/2/2/2` لاستيعاب فلتر request_type الجديد.

---

## إصلاح الأخطاء

1. **`$imagePath` غير معرّف في `MainCategoryController::update()`** — عند عدم رفع صورة جديدة أثناء التعديل، كان الـ controller يُشير إلى `$imagePath` قبل تعريفه، مما يسبب خطأ PHP. تم الإصلاح بنقل رفع الصورة داخل كتلة `if ($request->hasFile('image'))` وتعيين `$updateData['image']` بشكل شرطي فقط.
2. **خطأ إملائي "Mitozon"** — كان اسم العلامة التجارية يظهر كـ "Mitozon" في وصف معاملة المحفظة. تم تصحيحه إلى "Metwzon" في `AdminCancellationController.php`.

---

## تحسينات الأداء

- **VendorUrgentTaskService** — جميع استعلامات المهام مُحدودة بالبائع المُصادق عليه عبر قيود `vendor_id`، مما يمنع تسريب البيانات بين البائعين ويُمكّن الفلترة على مستوى قاعدة البيانات.
- **AdminCancellationController** — يستخدم `leftJoin` وتحميل أعمدة انتقائية لتجنب استعلامات N+1 عند سرد الإلغاءات مع بيانات الطلب والمنتج والبائع المرتبطة.
- **مصفوفة الصلاحيات** — تحمّل جميع الصلاحيات والموظفين في استعلامين فقط، وتُبني خريطة صلاحيات في الذاكرة، مما يتجنب استعلامات الصلاحيات لكل موظف.
- **إعادة استخدام إحصائيات VendorUrgentTaskService** — عندما تكون المهام مُحمّلة مسبقاً، تتقبل `getStats()` مجموعات مُحمّلة مسبقاً لتجنب الاستعلامات المكررة.

---

## سجل الـ Commits

| # | Commit | الملف | الوقت (UTC+3) | الرسالة |
|---|--------|-------|---------------|---------|
| 1 | `6b6878f` | youssefkhaled_74 | 2026-07-20 16:03:52 | `as` |
| 2 | `66e9a97` | youssefkhaled_74 | 2026-07-20 18:17:06 | `as` |
| 3 | `0645ee1` | youssefkhaled_74 | 2026-07-20 20:22:55 | `as` |

---

## روابط الـ Commits

- https://github.com/Youssefkhaled74/metw-laravel/commit/6b6878fe57b6e4c30b29048fce60a6fed60f9623
- https://github.com/Youssefkhaled74/metw-laravel/commit/66e9a972cd7b341de9fb976da3607193369a49c0
- https://github.com/Youssefkhaled74/metw-laravel/commit/0645ee14a0ec828c35283f06cf6f1bef3ca4fc93

---

## الإحصائيات

| المقياس | القيمة |
|---------|--------|
| عدد الـ Commits | 3 |
| الملفات المعدلة | 39 |
| الأسطر المضافة | 3,661 |
| الأسطر المحذوفة | 63 |
| ملفات جديدة | 22 |
| ملفات مُعدّلة | 17 |
| Controlers جديدة | 0 (نماذج مُوسيّعة) |
| Enums جديدة | 1 |
| Services جديدة | 1 |
| Migrations جديدة | 2 |
| Blade Views جديدة | 15 |
| Blade Views مُعدّلة | 7 |
| Routes جديدة | 15 |
| ملفات لغة مُعدّلة | 4 |

---

## المخاطر / ملاحظات

1. **Migrations معلّقة** — لم تُشغّل migration الجديدان بعد (MySQL/Laragon غير نشط). يجب تشغيل `php artisan migrate` قبل أن تعمل ميزات سير عمل الإلغاء وترتيب التصنيفات.
2. **التحقق من Routes** — لم تُتحقق الـ Routes باستخدام `php artisan route:list` بسبب الاعتماد على MySQL. يجب التحقق من تعريفات الـ Routes بمجرد تشغيل التطبيق.
3. **إعداد الصلاحيات** — مصفوفة صلاحيات المدير تتطلب تعيين صلاحيتي `admin.permission-matrix.index` و`admin.permission-matrix.update` للمستخدم المدير. تُولّد تلقائياً من أسماء الـ Routes لكنها تحتاج إلى الإعداد/التعيين للأدوار الموجودة.
4. **رسائل الـ Commits** — جميع الـ commits الثلاثة تحمل رسالة العنصر النائب "as". يُنصح بتعديل رسائل الـ commits لتحسين قابلية чтة السجل في المستقبل.
5. **الاختبار** — لم يتم كتابة أو تشغيل اختبارات تلقائية للميزات الجديدة. يجب أن يشمل الاختبار اليدوي:
   - سير عمل الإلغاء بالكامل (استجابة البائع → إعادة تفعيل/إكمال المدير → شحن المحفظة)
   - مهام البائع العاجلة مع بيانات طلبات حقيقية
   - حفظ واستمرارية مصفوفة الصلاحيات
   - رفع ومعاينة وحذف شعار البائع
   - ترتيب المواقع للتصنيفات الرئيسية
6. **تحقيق "ZonMeto"** — ذكر المتطلب اسم "ZonMeto" كعلامة تجارية للتحقق منها. أظهر البحث الكامل في قاعدة الكود صفر نتائج — هذا الاسم غير موجود في المشروع.

---

## الملخص النهائي

كانت جلسة اليوم منتجة للغاية، حيث قدمت **7 ميزات مختلفة** عبر **39 ملفاً** مع **3,661 سطراً** من الكود الجديد والمُعدّل. الإضافتان الأهم هما **سير عمل إلغاء الطلبات / طلبات الإرجاع** — دورة حياة متكاملة تشمل إدارة المدير، واستجابة البائع، ومعالجة الشكاوى، والاسترداد عبر المحفظة — وصفحة **المهام العاجلة لوحة تحكم البائع** — مدير مهام مجمع مُ驱动 بـ AJAX مع تحديث تلقائي. تشمل الميزات الداعمة **مصفوفة الصلاحيات** لإدارة صلاحيات الموظفين بشكل جماعي، و**رفع شعار البائع**، و**ترتيب التصنيفات الرئيسية** حسب الموقع، و**إعادة تسمية قسم المحافظ الإلكترونية**، و**صفحة معلومات الاتصال الإدارية**. هناك migration جديدان معلّقان يحتاجان إلى التسبيغ. يتبع قاعدة الكود أنماطاً معمارية متسقة (صلاحيات Spatie، نماذج/Enums موجودة، نظام Blade layout) وجميع الملفات تجتاز التحقق من صحة PHP.
