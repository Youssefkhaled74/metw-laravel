@extends('website.layout')

@section('content')
<section class="inner-hero">
    <div class="container">
        <span class="pill"><span class="dot"></span> نحن</span>
        <h1 style="color:var(--purple);font-size:clamp(38px,5vw,64px);margin:18px 0 12px">ميتولوجيستيك MetwLogistic</h1>
        <p class="section-sub">موقع إلكتروني للتسويق والخدمات اللوجستية، يجمع خدمات الماركت والشحن والتوصيل والمناديب في منظومة تشغيل واحدة.</p>
    </div>
</section>

<section class="section" style="padding-top:24px">
    <div class="container split">
        <div class="content-card">
            <h2>من نحن</h2>
            <p>ميتولوجيستيك هي منصة تعريفية وتشغيلية لخدمات الشركة وتطبيقاتها، هدفها توحيد تجربة المستخدم والبائع ومستودع الشحن والمندوب تحت هوية واضحة واحدة.</p>
            <p>الموقع يوفر نقطة دخول للحسابات ولوحات التحكم، ويعرض محتوى تعريفيًا ثابتًا عن التطبيقات والخدمات والسياسات، مع قابلية التوسع لاحقًا لربطه بمحتوى قابل للإدارة من لوحة الأدمن.</p>
        </div>
        <div class="visual-panel">
            <h2 style="color:#fff">مكونات المنظومة</h2>
            <div class="timeline">
                <div class="timeline-item"><span class="timeline-number">M</span><div><strong>Metwzon</strong><p>تطبيق الماركت المصري الإلكتروني.</p></div></div>
                <div class="timeline-item"><span class="timeline-number">E</span><div><strong>MetwExpress</strong><p>تطبيق الشحن الداخلي والتوصيل السريع.</p></div></div>
                <div class="timeline-item"><span class="timeline-number">G</span><div><strong>Metwgo</strong><p>تطبيق مناديب الشحن والتوصيل في مصر.</p></div></div>
            </div>
        </div>
    </div>
</section>
@endsection
