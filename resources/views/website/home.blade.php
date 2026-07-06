@extends('website.layout')

@section('content')
@php
    $texts = $promotionalTexts ?? [];
    $banners = $promotionalBanners ?? [];
    $videos = $promotionalVideos ?? [];
    $heroCards = $heroCards ?? [];
@endphp

<section class="hero">
    <div class="container hero-grid">
        <div>
            <span class="pill"><span class="dot"></span> {{ $site['name_ar'] ?? 'ميتولوجيستيك' }} - {{ $site['name_en'] ?? 'MetwLogistic' }}</span>
            <h1>منصة واحدة تربط <span>الماركت</span> بالشحن والمستودعات والمناديب</h1>
            <p>
                {{ $site['description'] ?? 'منظومة واحدة تربط تطبيق الماركت، الشحن الداخلي، المستودعات، والمناديب في تجربة تشغيل واضحة وسريعة.' }}
            </p>
            <div class="hero-actions">
                <a href="#apps" class="btn btn-primary">تحميل التطبيقات</a>
                <a href="{{ route('website.about') }}" class="btn btn-ghost">نحن</a>
                <a href="{{ route('shipment.login') }}" class="btn btn-ghost">دخول مستودع الشحن</a>
            </div>
            <div class="hero-metrics">
                @foreach($heroCards as $card)
                    <div class="metric">
                        <strong>{{ $card['value'] }}</strong>
                        <span>{{ $card['label'] }}</span>
                    </div>
                @endforeach
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
                <div class="card-icon">🛍</div>
                <h3>تطبيق الماركت Metwzon</h3>
                <p>تجربة شراء إلكترونية للمنتجات المعروضة من المتاجر مع عناوين محفوظة ومتابعة الطلبات.</p>
            </article>
            <article class="card">
                <div class="card-icon">🚛</div>
                <h3>تطبيق الشحن MetwExpress</h3>
                <p>إنشاء طلبات شحن أو توصيل سريع داخل المحافظة وبين المحافظات مع متابعة الحالة والرسوم.</p>
            </article>
            <article class="card">
                <div class="card-icon">🧭</div>
                <h3>تطبيق المناديب Metwgo</h3>
                <p>تطبيق مخصص للمناديب وسائقي الباصات لاستقبال الطلبات حسب المحافظة، المدينة، وسيلة النقل والوزن.</p>
            </article>
        </div>
    </div>
</section>

<section class="section" id="website-texts">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="pill"><span class="dot"></span> محتوى الموقع</span>
                <h2>نصوص ومقالات يمكن للإدارة تحديثها</h2>
            </div>
            <p class="section-sub">يتم عرض صفحات المحتوى العامة هنا تلقائيًا من لوحة التحكم، مع الاحتفاظ بنسخة ثابتة إذا لم يضف الأدمن أي محتوى بعد.</p>
        </div>

        <div class="cards" style="grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));">
            @forelse($texts as $text)
                <article class="card">
                    <div class="card-icon">✍</div>
                    <h3>{{ $text['title'] }}</h3>
                    <p>{!! $text['content'] !!}</p>
                </article>
            @empty
                <article class="card">
                    <div class="card-icon">✍</div>
                    <h3>واجهة عامة قوية</h3>
                    <p>صفحة رئيسية، نحن، سياسات وشروط، تحميل التطبيقات، ومحتوى ثابت قابل للتطوير لاحقًا.</p>
                </article>
                <article class="card">
                    <div class="card-icon">🌐</div>
                    <h3>تجربة عربية أولًا</h3>
                    <p>الموقع موجّه بالعربية مع الحفاظ على هوية الشركة الإنجليزية عند الحاجة داخل الواجهة العامة.</p>
                </article>
                <article class="card">
                    <div class="card-icon">🧩</div>
                    <h3>قابل للتحديث من الأدمن</h3>
                    <p>يمكن تحويل هذه البطاقات إلى صفحات قابلة للإدارة من لوحة التحكم دون كسر التصميم الحالي.</p>
                </article>
            @endforelse
        </div>
    </div>
</section>

<section class="section" id="images">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="pill"><span class="dot"></span> صور الموقع الدعائية</span>
                <h2>صور وبنرات يضيفها الأدمن من لوحة التحكم</h2>
            </div>
            <p class="section-sub">تظهر الصور النشطة فقط، وإذا لم توجد صور دعائية يعرض الموقع النص الثابت الحالي كبديل آمن.</p>
        </div>

        <div class="media-grid">
            <div class="promo-image">
                <h3>صور ترويجية للموقع</h3>
                @if(count($banners))
                    <div class="cards" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); margin-top: 18px;">
                        @foreach($banners as $banner)
                            <a href="{{ $banner['link'] ?? '#' }}" class="card" style="padding:14px; display:block; color:inherit;">
                                <img src="{{ asset($banner['image'] ?: 'images/flags/logo.png') }}" alt="صورة دعائية" style="width:100%; height:160px; object-fit:cover; border-radius:18px;">
                            </a>
                        @endforeach
                    </div>
                @else
                    <p>محتوى دعائي يشرح للزوار كيف تتم إدارة المتجر، الفروع، المستودعات، والطلبات من خلال المنظومة.</p>
                @endif
            </div>

            <div class="promo-video">
                <h3>فيديو تعريفي</h3>
                <p>مكان مخصص للفيديوهات الترويجية الخاصة بالمنظومة، مع إمكانية عرض فيديو أو صورة غلاف عند توفرها.</p>
                @if(count($videos))
                    @foreach($videos as $video)
                        <div class="shipment-card" style="background: rgba(255,255,255,.08); border-color: rgba(255,255,255,.12); color: #fff;">
                            @if(!empty($video['video_path']))
                                <video controls preload="metadata" style="width:100%; max-height:240px; border-radius:18px; margin-bottom:12px;">
                                    <source src="{{ asset($video['video_path']) }}">
                                </video>
                            @elseif(!empty($video['thumbnail']))
                                <img src="{{ asset($video['thumbnail']) }}" alt="{{ $video['title'] }}" style="width:100%; max-height:240px; object-fit:cover; border-radius:18px; margin-bottom:12px;">
                            @endif
                            <strong style="color:#fff">{{ $video['title'] }}</strong>
                            @if(!empty($video['description']))
                                <small style="color:rgba(255,255,255,.78)">{{ $video['description'] }}</small>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="play">▶</div>
                @endif
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
            <p class="section-sub">روابط التحميل هنا مؤقتة كنصوص ثابتة حتى تتوفر روابط المتاجر الرسمية لاحقًا.</p>
        </div>
        <div class="apps">
            <article class="app-card metwzon">
                <h3>Metwzon</h3>
                <p>تطبيق ماركت مصري إلكتروني للمتاجر والمنتجات.</p>
                <div class="store-row"><span class="store-badge">Google Play قريبًا</span><span class="store-badge">App Store قريبًا</span></div>
            </article>
            <article class="app-card express">
                <h3>MetwExpress</h3>
                <p>تطبيق الشحن الداخلي والتوصيل السريع بين المحافظات وداخل المحافظة.</p>
                <div class="store-row"><span class="store-badge">Google Play قريبًا</span><span class="store-badge">App Store قريبًا</span></div>
            </article>
            <article class="app-card go">
                <h3>Metwgo</h3>
                <p>تطبيق المناديب لاستقبال وتنفيذ مهام الشحن والتوصيل.</p>
                <div class="store-row"><span class="store-badge">Google Play قريبًا</span><span class="store-badge">App Store قريبًا</span></div>
            </article>
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
