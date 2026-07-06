@extends('website.layout')

@section('content')
<section class="inner-hero">
    <div class="container">
        <span class="pill"><span class="dot"></span> السياسات والشروط</span>
        <h1 style="color:var(--purple);font-size:clamp(38px,5vw,64px);margin:18px 0 12px">سياسات استخدام MetwLogistic</h1>
        <p class="section-sub">صفحة Static مبدئية لعرض الشروط والسياسات لحين تزويد النصوص القانونية النهائية واعتمادها من الإدارة.</p>
    </div>
</section>

<section class="section" style="padding-top:24px">
    <div class="container">
        <div class="content-card policy-list">
            <div class="policy-item">
                <h3>شروط استخدام الموقع</h3>
                <p>يستخدم الموقع للتعريف بخدمات ميتولوجيستيك وتسهيل دخول الحسابات المختلفة إلى لوحات التحكم الخاصة بها.</p>
            </div>
            <div class="policy-item">
                <h3>سياسة البائعين وموردي خدمات الشحن</h3>
                <p>يجب على البائعين وموردي خدمات الشحن مراجعة الشروط والسياسات والموافقة عليها قبل استخدام الحساب لأول مرة.</p>
            </div>
            <div class="policy-item">
                <h3>سياسة الخصوصية</h3>
                <p>تتم معالجة بيانات الحسابات والطلبات بما يخدم تشغيل المنظومة، مع الالتزام بعدم عرض بيانات غير مطلوبة على الموقع العام.</p>
            </div>
            <div class="policy-item">
                <h3>ملاحظات مهمة</h3>
                <p>هذه النصوص مؤقتة وثابتة داخل ملف Blade، ويجب استبدالها بالنصوص النهائية عند اعتمادها.</p>
            </div>
        </div>
    </div>
</section>
@endsection
