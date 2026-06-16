<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MainCategory;
use App\Models\Category;
use App\Models\CategoryTranslation;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        CategoryTranslation::truncate();
        Category::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // البيانات من الجدول الذي قدمته
        $categoriesData = [
            'أزياء، شنط، أحذية، شرابات "جوارب"' => [
                'أزياء "قطعة"',
                'أزياء "طقم"',
                'أحذية "زوج"',
                'شرابات "جوارب"',
                'شنط شخصية "قطعة"',
                'شنط سفر "قطعة"',
                'شنط سفر "طقم"'
            ],
            'أقمشة' => [
                'قطعة قماش'
            ],
            'سجاد ومفروشات ومراتب وستائر' => [
                'سجادة',
                'دواسة',
                'مرتبة',
                'مخدة',
                'يطانية',
                'لحاف',
                'كوفرته',
                'مفرش "طقم"',
                'مفرش "فردي"',
                'ستارة "طقم"',
                'ستارة "فردي"',
                'سجادة صلاة',
                'فوطة'
            ],
            'مستلزمات ووسائل تعليم وترفيه وألعاب للأطفال' => [
                'طقم أو قطعة أو عبوة'
            ],
            'أدوات وهدايا وإكسسوارات ومستلزمات شخصية' => [
                'طقم أو قطعة أو عبوة'
            ],
            'مواد النظافة والعناية بالبيت' => [
                'طقم أو قطعة أو عبوة'
            ],
            'أدوات ومستلزمات منزلية' => [
                'طقم أو قطعة أو عبوة'
            ],
            'أثاث منزلي ومكتبي' => [
                'غرفة نوم',
                'غرفة سفرة',
                'طقم صالون',
                'طقم أنتريه',
                'طقم ركنة',
                'طقم دواليب مطبخ',
                'سرير',
                'دولاب',
                'تسريحة',
                'كومودينو',
                'ستاند ملابس',
                'كنبة',
                'كرسي فوتيه',
                'كرسي عادي',
                'ترابيزة',
                'طقم مكتب غرفة مدير',
                'مكتب فردي',
                'كرسي مكتب أو إنتظار',
                'وحدة أدراج أو أرفف'
            ],
            'بوابات وأبواب وشبابيك' => [
                'بوابة حديد مزدوجة',
                'بوابة حديد فردية',
                'باب حديد مزدوج',
                'باب حديد فردي',
                'باب خشب مزدوج',
                'باب خشب فردي',
                'باب ألوميتال مزدوج',
                'باب ألوميتال فردي',
                'باب أسانسير',
                'باب مروحة بلاستيك',
                'شباك حديد مزدوج',
                'شباك حديد فردي',
                'شباك ألوميتال مزدوج',
                'شباك ألوميتال فردي',
                'شباك خشب مزدوج',
                'شباك خشب فردي'
            ],
            'إكسسوارات ولوازم الموبيليات والأبواب والستائر' => [
                'طقم أو قطعة أو عبوة'
            ],
            'لوحات وأنتيكات وقطع ديكور ومشغولات' => [
                'طقم أو قطعة أو عبوة'
            ],
            'منتجات الدعاية والإعلان' => [
                'طقم أو قطعة أو عبوة',
                'كشافات',
                'صاعق حشرات',
                'لمبات وأنظمة إنارة'
            ],
            'كتب ومطبوعات وأوراق ومستندات' => [
                'دفتر أو كناب أو كشكول أو ملزمة',
                'مستند'
            ],
            'أطقم حمامات' => [
                'قاعدة حمام',
                'كابينة بانيو قدم',
                'بانيو قدم',
                'بانيو عادي',
                'بانيو سونا',
                'حوض عادي',
                'وحدة حوض بأدراج'
            ],
            'أكسسوارات ومستلزمات السباكة والحمامات وفلاتر مياه' => [
                'طقم أو قطعة أو عبوة',
                'فلتر مياه'
            ],
            'أكسسوارات ومستلزمات الكهرباء والإنارة' => [
                'تجف',
                'لمبات وكشافات وأنظمة إنارة',
                'صاعق حشرات',
                'طقم مستلزمات أو قطعة أو عبوة',
                'طقم أو قطعة أو عبوة بطاريات'
            ],
            'تنسيق الحدائق ومشاتل' => [
                'طقم أو قطعة أو عبوة'
            ],
            'سيارات جولف وبيتش وأطفال واسكوترات ودراجات وقطع الغيار' => [
                'سيارة جولف',
                'سيارة بيتش باجي',
                'سيارة أطفال كهربائية',
                'اسكوتر',
                'دراجة نارية',
                'دراجة عادية',
                'قطع غيار'
            ],
            'أدوات ومستلزمات الرياضة واللياقة البدنية' => [
                'طقم أو قطعة أو عبوة'
            ],
            'أدوية ومنتجات للصحة والقوة والطاقة والدايت' => [
                'طقم أو قطعة أو عبوة'
            ],
            'منتجات غذائية من السوبر ماركت' => [
                'طقم أو قطعة أو عبوة'
            ],
            'وجبات غذائية من المطاعم' => [
                'طقم أو قطعة أو عبوة'
            ],
            'سوائل في براميل أو جوالين أو زجاجات أو علب' => [
                'زجاجة أو علبة سوائل',
                'جالون سوائل',
                'برميل سوائل'
            ],
            'مستلزمات وكماليات العناية بالسيارات وقطع الغيار' => [
                'طقم مستلزمات أو قطعة أو عبوة',
                'قطع غيار سيارة',
                'شاشة سيارة',
                'جنط سيارة',
                'كاوتش سيارة',
                'بطارية سيارة',
                'كوريك سيارة',
                'غطاء سيارة',
                'ماتور نفخ كاوتش سيارة',
                'طفاية سيارة'
            ],
            'أجهزة منزلية وإلكترونية ورياضية وطبية وتجارية وصناعية وقطع الغيار' => [
                'أجهزة منزلية',
                'أجهزة إلكترونية',
                'أجهزة رياضية',
                'أجهزة طبية',
                'أجهزة تجارية',
                'أجهزة صناعية',
                'قطع غيار'
            ],
            'أجهزة وأدوات ومستلزمات الصوتيات والشاشات والإلكترونيات والتصوير' => [
                'شاشة',
                'طبق دش',
                'كاميرا',
                'ساعة يد',
                'ساعة مكتب أو حائط',
                'كمبيوتر',
                'لاب توب',
                'طابعة',
                'ماكينة تصوير',
                'موبايل',
                'تابلت',
                'مستلزمات وأنظمة أمن ومراقبة وتتبع',
                'طقم مستلزمات أو قطعة أو عبوة'
            ],
            'تجهيزات المحلات والمعارض والمخازن والشركات والمكاتب' => [
                // سيتم إضافة فئات فرعية لاحقاً
            ],
            'ماكينات وأجهزة ومعدات وعدد وأدوات ومستلزمات صناعية أو طبية' => [
                // سيتم إضافة فئات فرعية لاحقاً
            ],
            'ألواح زجاج' => [
                // سيتم إضافة فئات فرعية لاحقاً
            ],
            'ألواح خشب، أعواد خشب' => [
                // سيتم إضافة فئات فرعية لاحقاً
            ],
            'ألواح حديد، أعواد حديد، أسياخ حديد' => [
                // سيتم إضافة فئات فرعية لاحقاً
            ],
            'ألواح أو أعواد "بلاستيك، صاج، ألومينيوم، معادن أخرى"' => [
                // سيتم إضافة فئات فرعية لاحقاً
            ],
            'قطع أو الواح "سفنج، فوم"' => [
                // سيتم إضافة فئات فرعية لاحقاً
            ],
            'سيراميك، بلاط، رخام' => [
                // سيتم إضافة فئات فرعية لاحقاً
            ],
            'أدوات ومواد ومستلزمات متنوعة' => [
                'طقم أو قطعة أو عبوة'
            ]
        ];

        foreach ($categoriesData as $mainCategoryName => $subCategories) {
            // البحث عن التصنيف الرئيسي
            $mainCategory = MainCategory::whereHas('translations', function($query) use ($mainCategoryName) {
                $query->where('name', $mainCategoryName)->where('locale', 'ar');
            })->first();

            if ($mainCategory) {
                foreach ($subCategories as $subCategoryName) {
                    $category = Category::create([
                        'name' => $this->generateEnglishName($subCategoryName),
                        'slug' => Str::slug($this->generateEnglishName($subCategoryName)),
                        'image' => null,
                        'main_category_id' => $mainCategory->id,
                        'is_active' => true,
                    ]);

                    // الترجمة الإنجليزية
                    CategoryTranslation::create([
                        'category_id' => $category->id,
                        'name' => $this->generateEnglishName($subCategoryName),
                        'slug' => Str::slug($this->generateEnglishName($subCategoryName)),
                        'locale' => 'en',
                    ]);

                    // الترجمة العربية
                    CategoryTranslation::create([
                        'category_id' => $category->id,
                        'name' => $subCategoryName,
                        'slug' => Str::slug($this->generateEnglishName($subCategoryName)),
                        'locale' => 'ar',
                    ]);
                }
            }
        }
    }

    /**
     * توليد اسم إنجليزي من الاسم العربي
     */
    private function generateEnglishName($arabicName): string
    {
        $translations = [
            'أزياء "قطعة"' => 'Fashion Piece',
            'أزياء "طقم"' => 'Fashion Set',
            'أحذية "زوج"' => 'Shoes Pair',
            'شرابات "جوارب"' => 'Socks',
            'شنط شخصية "قطعة"' => 'Personal Bag Piece',
            'شنط سفر "قطعة"' => 'Travel Bag Piece',
            'شنط سفر "طقم"' => 'Travel Bag Set',
            'قطعة قماش' => 'Fabric Piece',
            'سجادة' => 'Carpet',
            'دواسة' => 'Doormat',
            'مرتبة' => 'Mattress',
            'مخدة' => 'Pillow',
            'يطانية' => 'Blanket',
            'لحاف' => 'Quilt',
            'كوفرته' => 'Duvet Cover',
            'مفرش "طقم"' => 'Tablecloth Set',
            'مفرش "فردي"' => 'Single Tablecloth',
            'ستارة "طقم"' => 'Curtain Set',
            'ستارة "فردي"' => 'Single Curtain',
            'سجادة صلاة' => 'Prayer Rug',
            'فوطة' => 'Towel',
            'طقم أو قطعة أو عبوة' => 'Set, Piece or Package',
            'غرفة نوم' => 'Bedroom Set',
            'غرفة سفرة' => 'Dining Room Set',
            'طقم صالون' => 'Salon Set',
            'طقم أنتريه' => 'Antre Set',
            'طقم ركنة' => 'Living Room Set',
            'طقم دواليب مطبخ' => 'Kitchen Cabinets Set',
            'سرير' => 'Bed',
            'دولاب' => 'Wardrobe',
            'تسريحة' => 'Dressing Table',
            'كومودينو' => 'Bedside Table',
            'ستاند ملابس' => 'Clothes Stand',
            'كنبة' => 'Sofa',
            'كرسي فوتيه' => 'Armchair',
            'كرسي عادي' => 'Regular Chair',
            'ترابيزة' => 'Table',
            'طقم مكتب غرفة مدير' => 'Manager Office Set',
            'مكتب فردي' => 'Single Desk',
            'كرسي مكتب أو إنتظار' => 'Office or Waiting Chair',
            'وحدة أدراج أو أرفف' => 'Drawer or Shelf Unit',
            'بوابة حديد مزدوجة' => 'Double Iron Gate',
            'بوابة حديد فردية' => 'Single Iron Gate',
            'باب حديد مزدوج' => 'Double Iron Door',
            'باب حديد فردي' => 'Single Iron Door',
            'باب خشب مزدوج' => 'Double Wood Door',
            'باب خشب فردي' => 'Single Wood Door',
            'باب ألوميتال مزدوج' => 'Double Aluminum Door',
            'باب ألوميتال فردي' => 'Single Aluminum Door',
            'باب أسانسير' => 'Elevator Door',
            'باب مروحة بلاستيك' => 'Plastic Fan Door',
            'شباك حديد مزدوج' => 'Double Iron Window',
            'شباك حديد فردي' => 'Single Iron Window',
            'شباك ألوميتال مزدوج' => 'Double Aluminum Window',
            'شباك ألوميتال فردي' => 'Single Aluminum Window',
            'شباك خشب مزدوج' => 'Double Wood Window',
            'شباك خشب فردي' => 'Single Wood Window',
            'كشافات' => 'Spotlights',
            'صاعق حشرات' => 'Insect Zapper',
            'لمبات وأنظمة إنارة' => 'Bulbs and Lighting Systems',
            'دفتر أو كناب أو كشكول أو ملزمة' => 'Notebook, Book, Copybook, or Binder',
            'مستند' => 'Document',
            'قاعدة حمام' => 'Toilet Base',
            'كابينة بانيو قدم' => 'Foot Bath Cabin',
            'بانيو قدم' => 'Foot Bath',
            'بانيو عادي' => 'Regular Bath',
            'بانيو سونا' => 'Sauna Bath',
            'حوض عادي' => 'Regular Sink',
            'وحدة حوض بأدراج' => 'Sink Unit with Drawers',
            'فلتر مياه' => 'Water Filter',
            'تجف' => 'Plug',
            'لمبات وكشافات وأنظمة إنارة' => 'Bulbs, Spotlights and Lighting Systems',
            'طقم مستلزمات أو قطعة أو عبوة' => 'Supplies Set, Piece or Package',
            'طقم أو قطعة أو عبوة بطاريات' => 'Batteries Set, Piece or Package',
            'سيارة جولف' => 'Golf Cart',
            'سيارة بيتش باجي' => 'Beach Buggy',
            'سيارة أطفال كهربائية' => 'Electric Kids Car',
            'اسكوتر' => 'Scooter',
            'دراجة نارية' => 'Motorcycle',
            'دراجة عادية' => 'Regular Bike',
            'قطع غيار' => 'Spare Parts',
            'زجاجة أو علبة سوائل' => 'Bottle or Can of Liquids',
            'جالون سوائل' => 'Gallon of Liquids',
            'برميل سوائل' => 'Barrel of Liquids',
            'قطع غيار سيارة' => 'Car Spare Parts',
            'شاشة سيارة' => 'Car Screen',
            'جنط سيارة' => 'Car Rim',
            'كاوتش سيارة' => 'Car Tire',
            'بطارية سيارة' => 'Car Battery',
            'كوريك سيارة' => 'Car Jack',
            'غطاء سيارة' => 'Car Cover',
            'ماتور نفخ كاوتش سيارة' => 'Car Tire Inflator Motor',
            'طفاية سيارة' => 'Car Fire Extinguisher',
            'أجهزة منزلية' => 'Home Appliances',
            'أجهزة إلكترونية' => 'Electronic Devices',
            'أجهزة رياضية' => 'Sports Equipment',
            'أجهزة طبية' => 'Medical Devices',
            'أجهزة تجارية' => 'Commercial Devices',
            'أجهزة صناعية' => 'Industrial Devices',
            'شاشة' => 'Screen',
            'طبق دش' => 'Satellite Dish',
            'كاميرا' => 'Camera',
            'ساعة يد' => 'Wrist Watch',
            'ساعة مكتب أو حائط' => 'Desk or Wall Clock',
            'كمبيوتر' => 'Computer',
            'لاب توب' => 'Laptop',
            'طابعة' => 'Printer',
            'ماكينة تصوير' => 'Copier Machine',
            'موبايل' => 'Mobile Phone',
            'تابلت' => 'Tablet',
            'مستلزمات وأنظمة أمن ومراقبة وتتبع' => 'Security, Surveillance and Tracking Systems'
        ];

        return $translations[$arabicName] ?? str_replace(['"', '،'], '', $arabicName);
    }
}
