<?php

namespace App\Http\Controllers;

use App\Enum\PageType;
use App\Models\Bannar;
use App\Models\Page;
use App\Models\WebsiteVideo;
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
        $texts = $this->loadPromotionalTexts();
        $banners = $this->loadPromotionalBanners();
        $videos = $this->loadPromotionalVideos();

        return view('website.home', [
            'site' => $this->site,
            'pageTitle' => 'ميتولوجيستيك | MetwLogistic',
            'promotionalTexts' => $texts,
            'promotionalBanners' => $banners,
            'promotionalVideos' => $videos,
            'heroCards' => [
                ['value' => '4', 'label' => 'منصات مرتبطة'],
                ['value' => '27', 'label' => 'محافظة مستهدفة'],
                ['value' => '24/7', 'label' => 'متابعة رقمية'],
            ],
        ]);
    }

    public function about(): View
    {
        $page = $this->findPage(PageType::ABOUT->value);

        return view('website.about', [
            'site' => $this->site,
            'pageTitle' => 'نحن | MetwLogistic',
            'aboutPage' => $page,
            'heading' => $page?->title_ar ?: $page?->title ?: 'نحن',
            'summary' => $page?->content_ar ?: $page?->content ?: 'ميتولوجيستيك هي منصة تعريفية وتشغيلية لخدمات الشركة وتطبيقاتها، هدفها توحيد تجربة المستخدم والبائع ومستودع الشحن والمندوب تحت هوية واضحة واحدة.',
        ]);
    }

    public function terms(): View
    {
        return $this->policyView(
            PageType::TERMS->value,
            'الشروط والأحكام',
            'شروط استخدام ميتولوجيستيك',
            'شروط استخدام الموقع واللوحات والتطبيقات المرتبطة بخدمات ميتولوجيستيك.'
        );
    }

    public function privacy(): View
    {
        return $this->policyView(
            PageType::PRIVACY->value,
            'الخصوصية',
            'سياسة الخصوصية',
            'سياسة معالجة البيانات والاتصالات المرتبطة بخدمات ميتولوجيستيك.'
        );
    }

    public function policies(): View
    {
        return $this->policyView(
            PageType::POLICY->value,
            'السياسات',
            'سياسات الاستخدام',
            'السياسات العامة لاستخدام الموقع وخدمات ميتولوجيستيك.'
        );
    }

    private function policyView(string $type, string $breadcrumb, string $fallbackHeading, string $fallbackSummary): View
    {
        $page = $this->findPage($type);

        return view('website.policies', [
            'site' => $this->site,
            'policy' => $type,
            'breadcrumb' => $breadcrumb,
            'pageTitle' => $page?->title_ar ?: $page?->title ?: $fallbackHeading,
            'heading' => $page?->title_ar ?: $page?->title ?: $fallbackHeading,
            'summary' => $page?->content_ar ?: $page?->content ?: $fallbackSummary,
            'policyBlocks' => $this->loadPolicyBlocks($type),
        ]);
    }

    private function findPage(string $type): ?Page
    {
        return Page::valid()
            ->where('type', $type)
            ->orderByDesc('active_from')
            ->orderByDesc('id')
            ->first();
    }

    private function loadPromotionalTexts(): array
    {
        $pages = Page::valid()
            ->where('type', PageType::OTHER->value)
            ->orderByDesc('active_from')
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        if ($pages->isNotEmpty()) {
            return $pages->map(function (Page $page): array {
                return [
                    'title' => $page->title_ar ?: $page->title,
                    'content' => $page->content_ar ?: $page->content,
                ];
            })->values()->all();
        }

        return [
            [
                'title' => 'واجهة عامة قوية',
                'content' => 'صفحة رئيسية، نحن، سياسات وشروط، تحميل التطبيقات، ومحتوى ثابت قابل للتطوير لاحقًا.',
            ],
            [
                'title' => 'تجربة عربية أولًا',
                'content' => 'الموقع موجّه بالعربية مع الحفاظ على هوية الشركة الإنجليزية عند الحاجة داخل الواجهة العامة.',
            ],
            [
                'title' => 'قابل للتحديث من الأدمن',
                'content' => 'يمكن تحويل هذه البطاقات إلى صفحات قابلة للإدارة من لوحة التحكم دون كسر التصميم الحالي.',
            ],
        ];
    }

    private function loadPromotionalBanners(): array
    {
        $banners = Bannar::active()
            ->latest()
            ->limit(6)
            ->get();

        if ($banners->isNotEmpty()) {
            return $banners->map(function (Bannar $banner): array {
                return [
                    'image' => $banner->image,
                    'link' => $banner->link,
                ];
            })->values()->all();
        }

        return [];
    }

    private function loadPromotionalVideos(): array
    {
        $videos = WebsiteVideo::active()
            ->orderBy('sort_order')
            ->latest('id')
            ->limit(6)
            ->get();

        if ($videos->isNotEmpty()) {
            return $videos->map(function (WebsiteVideo $video): array {
                return [
                    'title' => $video->title_ar ?: $video->title,
                    'description' => $video->description_ar ?: $video->description,
                    'video_path' => $video->video_path,
                    'thumbnail' => $video->thumbnail,
                ];
            })->values()->all();
        }

        return [];
    }

    private function loadPolicyBlocks(string $type): array
    {
        $page = $this->findPage($type);

        if ($page) {
            return [
                [
                    'title' => $page->title_ar ?: $page->title,
                    'body' => $page->content_ar ?: $page->content,
                ],
            ];
        }

        return match ($type) {
            PageType::TERMS->value => [
                ['title' => 'الشروط العامة', 'body' => 'يستخدم الموقع للتعريف بخدمات ميتولوجيستيك وتمكين الدخول إلى لوحات التحكم المختلفة.'],
                ['title' => 'الالتزامات', 'body' => 'يلتزم المستخدم بتقديم بيانات صحيحة واستخدام الخدمات وفق سياسات المنظومة.'],
                ['title' => 'التحديثات', 'body' => 'قد يتم تحديث الشروط عند إضافة خدمات أو لوحات جديدة.'],
            ],
            PageType::PRIVACY->value => [
                ['title' => 'حماية البيانات', 'body' => 'تتم معالجة بيانات الحسابات والطلبات بما يخدم تشغيل المنظومة فقط.'],
                ['title' => 'مشاركة البيانات', 'body' => 'لا تتم مشاركة البيانات إلا مع الخدمات المرتبطة بالتشغيل أو عند الحاجة القانونية.'],
                ['title' => 'الاحتفاظ', 'body' => 'تُحفظ البيانات وفق متطلبات التشغيل والامتثال داخل النظام.'],
            ],
            default => [
                ['title' => 'سياسة الاستخدام', 'body' => 'تشرح الصفحة العامة قواعد استخدام الموقع والتطبيقات واللوحات المرتبطة به.'],
                ['title' => 'سياسة الدعم', 'body' => 'يتم توجيه الطلبات والشكاوى عبر القنوات المخصصة لكل نوع حساب.'],
                ['title' => 'سياسة التحديث', 'body' => 'تُراجع السياسات دوريًا بحسب توسع الخدمات أو تعديل الإجراءات.'],
            ],
        };
    }
}
