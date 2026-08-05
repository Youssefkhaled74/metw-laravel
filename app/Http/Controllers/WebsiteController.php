<?php

namespace App\Http\Controllers;

use App\Enum\PageType;
use App\Models\Bannar;
use App\Models\Config as SiteConfig;
use App\Models\MetwApp;
use App\Models\MetwContact;
use App\Models\MetwGallery;
use App\Models\MetwPolicyLink;
use App\Models\MetwService;
use App\Models\Page;
use App\Models\WebsiteVideo;
use Illuminate\View\View;
use Illuminate\Support\Facades\Route as RouteFacade;

class WebsiteController extends Controller
{
    private array $site;

    public function __construct()
    {
        $this->site = [
            'name_ar' => SiteConfig::getValue('website_name_ar', 'ميتولوجيستيك'),
            'name_en' => SiteConfig::getValue('website_name_en', 'MetwLogistic'),
            'tagline' => SiteConfig::getValue('website_tagline_ar', 'منصة مصرية للتسويق والخدمات اللوجستية'),
            'description' => SiteConfig::getValue('website_description_ar', 'منظومة واحدة تربط تطبيق الماركت، الشحن الداخلي، المستودعات، والمناديب في تجربة تشغيل واضحة وسريعة.'),
        ];
    }

    public function home(): View
    {
        $texts = $this->loadPromotionalTexts();
        $banners = $this->loadPromotionalBanners();
        $videos = $this->loadPromotionalVideos();

        return view('website.home', [
            'site' => $this->site,
            'pageTitle' => $this->site['name_en'] . ' | ' . $this->site['name_ar'],
            'heroTitle' => SiteConfig::getValue('website_home_hero_title', $this->site['name_ar'] . ' / ' . $this->site['name_en']),
            'heroSubtitle' => SiteConfig::getValue('website_home_hero_subtitle', 'منصة واحدة تربط السوق بالشحن والمستودعات والمناديب'),
            'heroDescription' => SiteConfig::getValue('website_home_hero_description', $this->site['description']),
            'promotionalTexts' => $texts,
            'promotionalBanners' => $banners,
            'promotionalVideos' => $videos,
            'heroCards' => $this->loadHeroCards(),
        ]);
    }

    public function about(): View
    {
        $page = $this->findPage(PageType::ABOUT->value);

        return view('website.about', [
            'site' => $this->site,
            'pageTitle' => $page?->translated_title ?: 'من نحن',
            'aboutPage' => $page,
            'heading' => $page?->translated_title ?: 'من نحن',
            'summary' => $page?->translated_content ?: 'ميتولوجيستيك هي منصة تعريفية وتشغيلية.',
            'aboutHtml' => $page?->translated_content,
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

    public function images(): View
    {
        $galleries = MetwGallery::active()
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get()
            ->map(function (MetwGallery $gallery): array {
                return [
                    'title' => $gallery->title_ar ?: $gallery->title_en ?: 'صورة',
                    'image' => $gallery->image_path ? asset($gallery->image_path) : null,
                ];
            })
            ->values()
            ->all();

        return view('website.images', compact('galleries'));
    }

    public function services(): View
    {
        $cards = $this->loadPromotionalTexts();

        return view('website.content', compact('cards'));
    }

    public function videos(): View
    {
        $videos = WebsiteVideo::active()
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get()
            ->map(function (WebsiteVideo $video): array {
                return [
                    'title' => $video->title_ar ?: $video->title,
                    'description' => $video->description_ar ?: $video->description,
                    'video_path' => $video->video_path,
                    'thumbnail' => $video->thumbnail,
                ];
            })
            ->values()
            ->all();

        return view('website.videos', compact('videos'));
    }

    public function showServices(): View
    {
        $services = MetwService::active()
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get()
            ->map(function (MetwService $service): array {
                return [
                    'en_title' => $service->title_en ?: $service->title_ar,
                    'ar_title' => $service->title_ar ?: $service->title_en,
                    'description' => $service->description_ar ?: $service->description_en,
                    'icon' => $service->icon,
                ];
            })
            ->values()
            ->all();

        return view('website.services', compact('services'));
    }

    public function apps(): View
    {
        $apps = MetwApp::active()
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get()
            ->map(function (MetwApp $app): array {
                return [
                    'name' => $app->name_ar ?: $app->name_en,
                    'play_url' => $app->play_store_link,
                    'store_url' => $app->app_store_link,
                    'icon' => $app->icon,
                ];
            })
            ->values()
            ->all();

        return view('website.apps', compact('apps'));
    }

    public function policiesIndex(): View
    {
        $policyItems = MetwPolicyLink::active()
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get()
            ->map(function (MetwPolicyLink $policyLink): array {
                $url = $policyLink->external_url;

                if (!$url && $policyLink->route_name && RouteFacade::has($policyLink->route_name)) {
                    $url = route($policyLink->route_name);
                }

                return [
                    'title' => $policyLink->title_ar ?: $policyLink->title_en,
                    'route' => $url ?: '#',
                ];
            })
            ->values()
            ->all();

        return view('website.policies-list', compact('policyItems'));
    }

    public function contact(): View
    {
        $contacts = MetwContact::active()
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        return view('website.contact', [
            'contacts' => $contacts,
            'contactHeading' => SiteConfig::getValue('website_contact_heading_ar', 'تواصل مع ميتو'),
            'contactJobsHeading' => SiteConfig::getValue('website_contact_jobs_heading_ar', 'وظائف ميتو'),
            'contactJobsDescription' => SiteConfig::getValue('website_contact_jobs_description_ar', 'للتقديم على وظائف ميتو، يرجى إرسال السيرة الذاتية والوثائق المطلوبة إلى البريد المحدد أدناه.'),
            'contactJobsEmail' => SiteConfig::getValue('website_contact_jobs_email', 'metwjob@gmail.com'),
            'contactAddressLabel' => SiteConfig::getValue('website_contact_address_label_ar', 'عنوان ميتو'),
            'contactAddress' => SiteConfig::getValue('website_contact_address_ar', 'جمهورية مصر العربية، محافظة الغربية، المحلة الكبرى، ميدان النافورة، 5 شارع شوقي حامد، الدور الرابع'),
        ]);
    }

    private function policyView(string $type, string $breadcrumb, string $fallbackHeading, string $fallbackSummary): View
    {
        $page = $this->findPage($type);

        return view('website.policies', [
            'site' => $this->site,
            'policy' => $type,
            'breadcrumb' => $breadcrumb,
            'pageTitle' => $page?->translated_title ?: $fallbackHeading,
            'heading' => $page?->translated_title ?: $fallbackHeading,
            'summary' => $page?->translated_content ?: $fallbackSummary,
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

    private function loadHeroCards(): array
    {
        return [
            [
                'value' => SiteConfig::getValue('website_home_stat_1_value', '4'),
                'label' => SiteConfig::getValue('website_home_stat_1_label_ar', 'منصات مرتبطة'),
            ],
            [
                'value' => SiteConfig::getValue('website_home_stat_2_value', '27'),
                'label' => SiteConfig::getValue('website_home_stat_2_label_ar', 'محافظة مستهدفة'),
            ],
            [
                'value' => SiteConfig::getValue('website_home_stat_3_value', '24/7'),
                'label' => SiteConfig::getValue('website_home_stat_3_label_ar', 'متابعة رقمية'),
            ],
        ];
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
                    'title' => $page->translated_title,
                    'content' => $page->translated_content,
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
                'title' => 'قابل للتحديث من الإدارة',
                'content' => 'يمكن تحويل هذه البطاقات إلى محتوى يديره فريق الإدارة دون كسر التصميم الحالي.',
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
                    'image' => $banner->image ? asset($banner->image) : null,
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
            ->orderByDesc('id')
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
                    'title' => $page->translated_title,
                    'body' => $page->translated_content,
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
