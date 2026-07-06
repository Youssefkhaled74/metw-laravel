@extends('website.layout')

@section('content')
<section class="hero">
    <div class="container hero-grid">
        <div>
            <span class="pill"><span class="dot"></span> ميتولوجيستيك - MetwLogistic</span>
            <h1>منصة واحدة تربط <span>الماركت</span> بالشحن والمستودعات والمناديب</h1>
            <p>
                موقع إلكتروني للتسويق والخدمات اللوجستية في مصر، يجمع بين تطبيق ميتوزون للمتاجر،
                ميتوإكسبريس للشحن والتوصيل السريع، وميتوجو للمناديب في تجربة موحدة واضحة للمستخدمين والموردين.
            </p>
            <div class="hero-actions">
                <a href="#apps" class="btn btn-primary">تحميل التطبيقات</a>
                <a href="{{ route('website.about') }}" class="btn btn-ghost">اعرف أكثر</a>
                <a href="{{ route('shipment.login') }}" class="btn btn-ghost">دخول مستودع الشحن</a>
            </div>
            <div class="hero-metrics">
                <div class="metric"><strong>4</strong><span>منصات مرتبطة</span></div>
                <div class="metric"><strong>27</strong><span>محافظة مستهدفة</span></div>
                <div class="metric"><strong>24/7</strong><span>متابعة رقمية</span></div>
            </div>
        </div>

        <div class="dashboard-preview" aria-label="MetwLogistic visual preview">
            <span class="orb one"></span>
            <span class="orb two"></span>
            <div class="phone-card">
                <div class="phone-screen">
                    <div class="phone-top">
                        <strong>Metwzon</strong>
                        <div class="search-bar">ابحث عن منتج أو متجر...</div>
                    </div>
                    <div class="category-row">
                        <div class="mini-icon">🛍️</div>
                        <div class="mini-icon">🚚</div>
                        <div class="mini-icon">🏬</div>
                        <div class="mini-icon">📦</div>
                    </div>
                    <div class="shipment-card"><small>طلب شحن جديد</small><strong>من القاهرة إلى الإسكندرية</strong></div>
                    <div class="shipment-card"><small>حالة الطلب</small><strong>بانتظار موافقة مقدم الخدمة</strong></div>
                    <div class="shipment-card"><small>محفظة ميتوزون</small><strong>رصيد ومتابعة مدفوعات</strong></div>
                </div>
            </div>
            <div class="floating-card">
                <strong>لوحات معلومات ذكية</strong>
                <div class="progress"><span></span></div>
                <p style="margin:0;color:var(--muted);line-height:1.8">متابعة الحسابات، الطلبات، الشحن، الإرجاع، والإشعارات من مكان واحد.</p>
            </div>
        </div>
    </div>
</section>

<section class="section" id="services">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="pill"><span class="dot"></span> خدمات المنظومة</span>
                <h2>كل طرف في العملية له بوابة واضحة</h2>
            </div>
            <p class="section-sub">الموقع العام هو نقطة الدخول الرسمية للحسابات ولوحات التحكم، والتطبيقات هي أدوات التشغيل اليومية للمستخدمين والمناديب.</p>
        </div>
        <div class="cards">
            <article class="card">
                <div class="card-icon">🛒</div>
                <h3>تطبيق الماركت Metwzon</h3>
                <p>تجربة شراء إلكترونية للمنتجات المعروضة من المتاجر، مع عناوين محفوظة ومحفظة ومتابعة الطلبات.</p>
            </article>
            <article class="card">
                <div class="card-icon">🚛</div>
                <h3>تطبيق الشحن MetwExpress</h3>
                <p>إنشاء طلبات شحن أو توصيل سريع داخل المحافظة وبين المحافظات، مع متابعة حالة الطلب والرسوم.</p>
            </article>
            <article class="card">
                <div class="card-icon">🧭</div>
                <h3>تطبيق المناديب Metwgo</h3>
                <p>تطبيق مخصص للمناديب وسائقي الباصات لاستقبال الطلبات حسب المحافظة، المدينة، وسيلة النقل والوزن.</p>
            </article>
        </div>
    </div>
</section>

<section class="section">
    <div class="container split">
        <div class="visual-panel">
            <span class="pill" style="background:rgba(255,255,255,.12);color:#fff;border-color:rgba(255,255,255,.18)"><span class="dot"></span> دورة تشغيل مختصرة</span>
            <h2 style="color:#fff;margin-top:18px">من الطلب إلى التسليم</h2>
            <div class="timeline">
                <div class="timeline-item"><span class="timeline-number">1</span><div><strong>إنشاء الطلب</strong><p>المستخدم أو البائع يحدد المسار والبيانات المطلوبة.</p></div></div>
                <div class="timeline-item"><span class="timeline-number">2</span><div><strong>اختيار مقدم الخدمة</strong><p>النظام يرسل الطلب للأطراف المناسبة حسب المحافظة والمدينة والتصنيف.</p></div></div>
                <div class="timeline-item"><span class="timeline-number">3</span><div><strong>متابعة التنفيذ</strong><p>لوحات التحكم تعرض حالة الطلب والإشعارات والمهام العاجلة.</p></div></div>
            </div>
        </div>
        <div>
            <span class="pill"><span class="dot"></span> لماذا MetwLogistic؟</span>
            <h2>هوية واحدة بدل أسماء قديمة ومشتتة</h2>
            <p class="section-sub">تم تجهيز الواجهة العامة باسم ميتولوجيستيك بهوية واضحة، مع ربط مباشر بين الموقع ولوحات دخول الأدمن والبائع ومستودع الشحن.</p>
            <div class="cards" style="grid-template-columns:1fr; margin-top:18px">
                <div class="card"><h3>واجهة عامة قوية</h3><p>صفحة رئيسية، نحن، سياسات وشروط، تحميل التطبيقات، ومحتوى دعائي ثابت قابل للتطوير لاحقًا.</p></div>
                <div class="card"><h3>تجربة عربية أولًا</h3><p>الموقع موجه بالعربية افتراضيًا، مع استخدام هوية البرتقالي والموف الخاصة بالمشروع.</p></div>
            </div>
        </div>
    </div>
</section>

<section class="section" id="apps">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="pill"><span class="dot"></span> تحميل التطبيقات</span>
                <h2>تطبيقات المنظومة</h2>
            </div>
            <p class="section-sub">روابط التحميل هنا Static placeholders لحين تزويد روابط Google Play و App Store الفعلية.</p>
        </div>
        <div class="apps">
            <article class="app-card metwzon"><h3>Metwzon</h3><p>تطبيق ماركت مصري إلكتروني للمتاجر والمنتجات.</p><div class="store-row"><span class="store-badge">Google Play قريبًا</span><span class="store-badge">App Store قريبًا</span></div></article>
            <article class="app-card express"><h3>MetwExpress</h3><p>تطبيق الشحن الداخلي والتوصيل السريع بين المحافظات وداخل المحافظة.</p><div class="store-row"><span class="store-badge">Google Play قريبًا</span><span class="store-badge">App Store قريبًا</span></div></article>
            <article class="app-card go"><h3>Metwgo</h3><p>تطبيق المناديب لاستقبال وتنفيذ مهام الشحن والتوصيل.</p><div class="store-row"><span class="store-badge">Google Play قريبًا</span><span class="store-badge">App Store قريبًا</span></div></article>
        </div>
    </div>
</section>

<section class="section" id="media">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="pill"><span class="dot"></span> المحتوى الدعائي</span>
                <h2>نصوص وصور وفيديوهات تعريفية</h2>
            </div>
            <p class="section-sub">المحتوى الحالي ثابت داخل Blade، ويمكن لاحقًا ربطه بصفحات وبانرات الأدمن الموجودة بالفعل.</p>
        </div>
        <div class="media-grid">
            <div class="promo-image">
                <h3>حلول لوجستية للمتاجر</h3>
                <p>محتوى دعائي يشرح للبائعين كيف تتم إدارة المتجر، الفروع، المستودعات، وطلبات الشحن من خلال المنظومة.</p>
            </div>
            <div class="promo-video">
                <h3>فيديو تعريفي</h3>
                <p>مكان مخصص للفيديوهات التعريفية بالخدمة، التطبيقات، وطريقة استخدام لوحات المعلومات.</p>
                <div class="play">▶</div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container login-strip">
        <div>
            <h2>دخول الحسابات ولوحات التحكم</h2>
            <p>روابط مباشرة للأدمن، البائعين، ومستودعات/شركات الشحن من الموقع الرسمي.</p>
        </div>
        <div class="login-links">
            <a href="{{ route('admin.login') }}" class="btn">دخول الأدمن</a>
            <a href="{{ route('vendor.login') }}" class="btn">دخول البائع</a>
            <a href="{{ route('shipment.login') }}" class="btn">دخول مستودع الشحن</a>
        </div>
    </div>
</section>
@endsection
