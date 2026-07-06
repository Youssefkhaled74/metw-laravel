<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class WebsiteController extends Controller
{
    private array $site;

    public function __construct()
    {
        $this->site = [
            'name_ar' => 'ميتولوجيستيك',
            'name_en' => 'MetwLogistic',
            'tagline' => 'منصة مصرية للتسويق والخدمات اللوجستية',
            'description' => 'منظومة واحدة تربط تطبيق الماركت، الشحن الداخلي، المستودعات، والمناديب في تجربة تشغيل واضحة وسريعة.',
        ];
    }

    public function home(): View
    {
        return view('website.home', [
            'site' => $this->site,
            'pageTitle' => 'ميتولوجيستيك | MetwLogistic',
        ]);
    }

    public function about(): View
    {
        return view('website.about', [
            'site' => $this->site,
            'pageTitle' => 'نحن | MetwLogistic',
        ]);
    }

    public function policies(): View
    {
        return view('website.policies', [
            'site' => $this->site,
            'pageTitle' => 'السياسات والشروط | MetwLogistic',
        ]);
    }
}
