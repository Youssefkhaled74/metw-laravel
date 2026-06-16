<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MainCategory;
use App\Models\Category;

class LoloMarketCategorySeeder extends Seeder
{
    public function run()
    {
        // Define the data structure
        $categoriesData = [
            [
                'main_category_ar' => 'ملابس نسائية',
                'main_category_en' => 'Women\'s Clothing',
                'categories_ar' => [
                    'ملابس نسائية للخروج',
                    'ملابس نسائية للبيت',
                    'ملابس نسائية داخلية',
                    'فساتين عرائس',
                    'فساتين سواريه',
                    'ملابس محجبات'
                ],
                'categories_en' => [
                    'Women\'s Outdoor Clothing',
                    'Women\'s Home Clothing',
                    'Women\'s Underwear',
                    'Bridal Dresses',
                    'Evening Dresses',
                    'Modest Clothing'
                ]
            ],
            [
                'main_category_ar' => 'ملابس رجالي',
                'main_category_en' => 'Men\'s Clothing',
                'categories_ar' => [
                    'ملابس رجالي للخروج',
                    'ملابس رجالي للبيت',
                    'ملابس رجالي داخلية',
                    'بدل رجالي'
                ],
                'categories_en' => [
                    'Men\'s Outdoor Clothing',
                    'Men\'s Home Clothing',
                    'Men\'s Underwear',
                    'Men\'s Suits'
                ]
            ],
            [
                'main_category_ar' => 'ملابس أطفال "بنات"',
                'main_category_en' => 'Girls Clothing',
                'categories_ar' => [
                    'ملابس بنات للخروج',
                    'ملابس بنات للبيت',
                    'ملابس بنات داخلية',
                    'ملابس بنات للمدارس'
                ],
                'categories_en' => [
                    'Girls Outdoor Clothing',
                    'Girls Home Clothing',
                    'Girls Underwear',
                    'Girls School Clothing'
                ]
            ],
            [
                'main_category_ar' => 'ملابس أطفال "أولاد"',
                'main_category_en' => 'Boys Clothing',
                'categories_ar' => [
                    'ملابس أولاد للخروج',
                    'ملابس أولاد للبيت',
                    'ملابس أولاد داخلية',
                    'ملابس أولاد للمدارس'
                ],
                'categories_en' => [
                    'Boys Outdoor Clothing',
                    'Boys Home Clothing',
                    'Boys Underwear',
                    'Boys School Clothing'
                ]
            ],
            [
                'main_category_ar' => 'ملابس رياضية',
                'main_category_en' => 'Sportswear',
                'categories_ar' => [
                    'ملابس نسائية رياضية',
                    'ملابس رجالي رياضية',
                    'ملابس بنات رياضية',
                    'ملابس أولاد رياضية'
                ],
                'categories_en' => [
                    'Women\'s Sportswear',
                    'Men\'s Sportswear',
                    'Girls Sportswear',
                    'Boys Sportswear'
                ]
            ],
            [
                'main_category_ar' => 'ملابس مهنية',
                'main_category_en' => 'Professional Clothing',
                'categories_ar' => [
                    'ملابس أطباء وتمريض',
                    'ملابس محاماه',
                    'ملابس طيارين',
                    'ملابس مضيفات',
                    'ملابس مهندسين',
                    'ملابس عمال',
                    'ملابس حراس أمن',
                    'ملابس طباخ وحلواني'
                ],
                'categories_en' => [
                    'Doctor and Nurse Clothing',
                    'Lawyer Clothing',
                    'Pilot Clothing',
                    'Flight Attendant Clothing',
                    'Engineer Clothing',
                    'Worker Clothing',
                    'Security Guard Clothing',
                    'Chef and Confectioner Clothing'
                ]
            ],
            [
                'main_category_ar' => 'ملابس ومستلزمات المواليد',
                'main_category_en' => 'Baby Clothes and Supplies',
                'categories_ar' => [
                    'ملابس مواليد بنات',
                    'ملابس مواليد أولاد',
                    'مستلزمات مواليد'
                ],
                'categories_en' => [
                    'Baby Girl Clothes',
                    'Baby Boy Clothes',
                    'Baby Supplies'
                ]
            ],
            [
                'main_category_ar' => 'شنط وأحزمة ومحافظ',
                'main_category_en' => 'Bags, Belts and Wallets',
                'categories_ar' => [
                    'شنط نسائية',
                    'شنط رجالي',
                    'شنط بنات',
                    'شنط سفر',
                    'شنط مدارس',
                    'شنط لاب توب',
                    'شنط للنادي',
                    'شنط تسوق',
                    'أحزمة رجالي',
                    'أحزمة نسائية',
                    'محافظ نقود'
                ],
                'categories_en' => [
                    'Women\'s Bags',
                    'Men\'s Bags',
                    'Girls Bags',
                    'Travel Bags',
                    'School Bags',
                    'Laptop Bags',
                    'Gym Bags',
                    'Shopping Bags',
                    'Men\'s Belts',
                    'Women\'s Belts',
                    'Wallets'
                ]
            ],
            [
                'main_category_ar' => 'أحذية جلد',
                'main_category_en' => 'Leather Shoes',
                'categories_ar' => [
                    'أحذية جلد نسائية',
                    'أحذية جلد رجالي',
                    'أحذية جلد بنات',
                    'أحذية جلد أولاد'
                ],
                'categories_en' => [
                    'Women\'s Leather Shoes',
                    'Men\'s Leather Shoes',
                    'Girls Leather Shoes',
                    'Boys Leather Shoes'
                ]
            ],
            [
                'main_category_ar' => 'أحذية رياضية',
                'main_category_en' => 'Sports Shoes',
                'categories_ar' => [
                    'أحذية رياضية نسائية',
                    'أحذية رياضية رجالي',
                    'أحذية رياضية بنات',
                    'أحذية رياضية أولاد'
                ],
                'categories_en' => [
                    'Women\'s Sports Shoes',
                    'Men\'s Sports Shoes',
                    'Girls Sports Shoes',
                    'Boys Sports Shoes'
                ]
            ],
            [
                'main_category_ar' => 'شباشب وصنادل',
                'main_category_en' => 'Sandals and Slippers',
                'categories_ar' => [
                    'شباشب وصنادل نسائية',
                    'شباشب وصنادل رجالي',
                    'شباشب وصنادل بنات',
                    'شباشب وصنادل أولاد'
                ],
                'categories_en' => [
                    'Women\'s Sandals and Slippers',
                    'Men\'s Sandals and Slippers',
                    'Girls Sandals and Slippers',
                    'Boys Sandals and Slippers'
                ]
            ],
            [
                'main_category_ar' => 'شرابات "جوارب"',
                'main_category_en' => 'Socks',
                'categories_ar' => [
                    'شرابات "جوارب" نسائية',
                    'شرابات "جوارب" رجالي',
                    'شرابات "جوارب" بنات',
                    'شرابات "جوارب" أولاد'
                ],
                'categories_en' => [
                    'Women\'s Socks',
                    'Men\'s Socks',
                    'Girls Socks',
                    'Boys Socks'
                ]
            ],
            [
                'main_category_ar' => 'أدوات ومستلزمات شخصية',
                'main_category_en' => 'Personal Tools and Supplies',
                'categories_ar' => [
                    'إكسسوارات ومستلزمات نسائية',
                    'إكسسوارات ومستلزمات بنات',
                    'أدوات ومستلزمات رجالي',
                    'أدوات ومستلزمات أولاد'
                ],
                'categories_en' => [
                    'Women\'s Accessories and Supplies',
                    'Girls Accessories and Supplies',
                    'Men\'s Tools and Supplies',
                    'Boys Tools and Supplies'
                ]
            ],
            [
                'main_category_ar' => 'أقمشة ملابس',
                'main_category_en' => 'Clothing Fabrics',
                'categories_ar' => [
                    'أقمشة ملابس نسائية',
                    'أقمشة ملابس رجالي'
                ],
                'categories_en' => [
                    'Women\'s Clothing Fabrics',
                    'Men\'s Clothing Fabrics'
                ]
            ],
            [
                'main_category_ar' => 'أقمشة مفروشات وتنجيد',
                'main_category_en' => 'Upholstery and Furniture Fabrics',
                'categories_ar' => [
                    'أقمشة مفروشات',
                    'قطن تنجيد',
                    'اسفنج تنجيد',
                    'فايبر تنجيد',
                    'مستلزمات تنجيد'
                ],
                'categories_en' => [
                    'Furniture Fabrics',
                    'Upholstery Cotton',
                    'Upholstery Sponge',
                    'Upholstery Fiber',
                    'Upholstery Supplies'
                ]
            ],
            [
                'main_category_ar' => 'مفروشات ومراتب وستائر',
                'main_category_en' => 'Furnishings, Mattresses and Curtains',
                'categories_ar' => [
                    'مفروشات سراير',
                    'بطاطين',
                    'مراتب',
                    'مخدات',
                    'ستائر منزلية',
                    'ستائر مكتبية',
                    'مستلزمات ستائر'
                ],
                'categories_en' => [
                    'Bed Furnishings',
                    'Blankets',
                    'Mattresses',
                    'Pillows',
                    'Home Curtains',
                    'Office Curtains',
                    'Curtain Supplies'
                ]
            ],
            [
                'main_category_ar' => 'سجاد وفرش أرضيات',
                'main_category_en' => 'Carpets and Flooring',
                'categories_ar' => [
                    'سجاد وموكيت',
                    'دواسات أرضيات',
                    'أرضيات مشمع وجلد',
                    'أرضيات فوم',
                    'أرضيات بلاستيك',
                    'أرضيات خشب',
                    'عوازل أرضيات',
                    'نجيل صناعي'
                ],
                'categories_en' => [
                    'Carpets and Rugs',
                    'Floor Mats',
                    'Vinyl and Leather Flooring',
                    'Foam Flooring',
                    'Plastic Flooring',
                    'Wood Flooring',
                    'Floor Insulation',
                    'Artificial Grass'
                ]
            ],
            [
                'main_category_ar' => 'هدايا ومقتنيات متنوعة',
                'main_category_en' => 'Gifts and Miscellaneous Collectibles',
                'categories_ar' => [
                    'نظارت شمسية نسائية',
                    'نظارت شمسية رجالي',
                    'نظارت شمسية أطفال',
                    'نظارات طبية',
                    'هدايا ومقتنيات متنوعة',
                    'شنط وأكياس هدايا',
                    'كؤوس',
                    'دروع ونياشين',
                    'ميداليات',
                    'مجسمات',
                    'هدايا تذكارية',
                    'خردوات وأدوات متنوعة',
                    'مستلزمات دعاية',
                    'زينة للمناسبات'
                ],
                'categories_en' => [
                    'Women\'s Sunglasses',
                    'Men\'s Sunglasses',
                    'Children\'s Sunglasses',
                    'Prescription Glasses',
                    'Various Gifts and Collectibles',
                    'Gift Bags and Boxes',
                    'Cups and Mugs',
                    'Shields and Badges',
                    'Medals',
                    'Statues and Models',
                    'Souvenirs',
                    'Various Tools and Hardware',
                    'Advertising Supplies',
                    'Party Decorations'
                ]
            ],
            [
                'main_category_ar' => 'منتجات عطور وعناية بالجسم',
                'main_category_en' => 'Perfumes and Body Care Products',
                'categories_ar' => [
                    'عطور نسائية',
                    'عطور رجالي',
                    'منتجات جمال المرأة',
                    'منتجات العناية بالجسم'
                ],
                'categories_en' => [
                    'Women\'s Perfumes',
                    'Men\'s Perfumes',
                    'Women\'s Beauty Products',
                    'Body Care Products'
                ]
            ],
            [
                'main_category_ar' => 'منتجات نظافة وعناية بالمنزل',
                'main_category_en' => 'Cleaning and Home Care Products',
                'categories_ar' => [
                    'منتجات النظافة',
                    'منتجات العناية بالمنزل'
                ],
                'categories_en' => [
                    'Cleaning Products',
                    'Home Care Products'
                ]
            ],
            [
                'main_category_ar' => 'أثاث وديكورات وأدوات منزلية',
                'main_category_en' => 'Furniture, Decorations and Home Tools',
                'categories_ar' => [
                    'أثاث غرف النوم',
                    'أثاث السفرة',
                    'أثاث الصالون',
                    'أثاث الأنتريه',
                    'أثاث الركنة',
                    'مطابخ',
                    'أثاث متنوع للمنزل',
                    'أثاث حدائق',
                    'أبواب',
                    'شبابيك',
                    'أرفف',
                    'مستلزمات موبيليات وأبواب وشبابيك',
                    'مستلزمات ديكورات',
                    'تابلوهات ومرايا',
                    'مستلزمات متنوعة للمنزل'
                ],
                'categories_en' => [
                    'Bedroom Furniture',
                    'Dining Room Furniture',
                    'Living Room Furniture',
                    'Entrance Furniture',
                    'Corner Furniture',
                    'Kitchens',
                    'Various Home Furniture',
                    'Garden Furniture',
                    'Doors',
                    'Windows',
                    'Shelves',
                    'Furniture, Doors and Windows Supplies',
                    'Decoration Supplies',
                    'Paintings and Mirrors',
                    'Various Home Supplies'
                ]
            ],
            [
                'main_category_ar' => 'أجهزة ومستلزمات المطبخ',
                'main_category_en' => 'Kitchen Appliances and Supplies',
                'categories_ar' => [
                    'أجهزة تحضير طعام',
                    'مستلزمات تحضير طعام',
                    'أدوات ومستلزمات السفرة',
                    'أدوات وسلال تخزين'
                ],
                'categories_en' => [
                    'Food Preparation Appliances',
                    'Food Preparation Supplies',
                    'Dining Tools and Supplies',
                    'Storage Tools and Baskets'
                ]
            ],
            [
                'main_category_ar' => 'أجهزة المنزلية',
                'main_category_en' => 'Home Appliances',
                'categories_ar' => [
                    'ثلاجات',
                    'ديب فريزر',
                    'غسالات',
                    'بوتاجازات',
                    'أفران',
                    'ميكروويف',
                    'مكانس كهربائية',
                    'أجهزة تكييف',
                    'مراوح وشفاطات',
                    'مكواة ملابس',
                    'سخان غاز',
                    'سخان كهربائي',
                    'دفاية كهربائية'
                ],
                'categories_en' => [
                    'Refrigerators',
                    'Freezers',
                    'Washing Machines',
                    'Stoves',
                    'Ovens',
                    'Microwaves',
                    'Vacuum Cleaners',
                    'Air Conditioners',
                    'Fans and Hoods',
                    'Ironing Machines',
                    'Gas Water Heaters',
                    'Electric Water Heaters',
                    'Electric Heaters'
                ]
            ],
            [
                'main_category_ar' => 'شاشات وأجهزة إستقبال وعرض',
                'main_category_en' => 'Screens, Receivers and Display Devices',
                'categories_ar' => [
                    'شاشات تليفزيون',
                    'أجهزة ومستلزمات إستقبال مرئي',
                    'أجهزة ومستلزمات عرض مرئي'
                ],
                'categories_en' => [
                    'TV Screens',
                    'Video Reception Devices and Supplies',
                    'Video Display Devices and Supplies'
                ]
            ],
            [
                'main_category_ar' => 'موبايلات وأجهزة ذكية ومستلزماتها',
                'main_category_en' => 'Mobiles, Smart Devices and Accessories',
                'categories_ar' => [
                    'موبايلات',
                    'أجهزة لوحية ذكية',
                    'مستلزمات موبايلات وأجهزة ذكية'
                ],
                'categories_en' => [
                    'Mobile Phones',
                    'Smart Tablets',
                    'Mobile and Smart Device Accessories'
                ]
            ],
            [
                'main_category_ar' => 'أجهزة ومستلزمات الكمبيوتر',
                'main_category_en' => 'Computer Devices and Supplies',
                'categories_ar' => [
                    'لاب توب',
                    'كمبيوتر',
                    'شاشات كمبيوتر',
                    'طاولات كمبيوتر',
                    'مستلزمات كمبيوتر متنوعة'
                ],
                'categories_en' => [
                    'Laptops',
                    'Computers',
                    'Computer Screens',
                    'Computer Desks',
                    'Various Computer Supplies'
                ]
            ],
            [
                'main_category_ar' => 'ماكينات تصوير وطباعة واسكانر',
                'main_category_en' => 'Copy Machines, Printers and Scanners',
                'categories_ar' => [
                    'طابعات مستندات واسكانر',
                    'ماكينات تصوير مستندات',
                    'ماكينات طباعة خرائط',
                    'ماكينات طباعة تاريخ',
                    'ماكينات طباعة بار كود',
                    'ماكينات طباعة منتجات وأسطح',
                    'مستلزمات ماكينات طباعة وتصوير',
                    'أحبار طابعات',
                    'ماكينات حفر ليزر ومستلزماتها'
                ],
                'categories_en' => [
                    'Document Printers and Scanners',
                    'Document Copiers',
                    'Map Printers',
                    'Date Printers',
                    'Barcode Printers',
                    'Product and Surface Printers',
                    'Printing and Copying Machine Supplies',
                    'Printer Inks',
                    'Laser Engraving Machines and Supplies'
                ]
            ],
            [
                'main_category_ar' => 'كاميرات ومستلزمات تصوير',
                'main_category_en' => 'Cameras and Photography Supplies',
                'categories_ar' => [
                    'كاميرات تصوير',
                    'مستلزمات تصوير',
                    'مستلزمات إستوديوهات'
                ],
                'categories_en' => [
                    'Cameras',
                    'Photography Supplies',
                    'Studio Supplies'
                ]
            ],
            [
                'main_category_ar' => 'أجهزة ومستلزمات صوتيات',
                'main_category_en' => 'Audio Devices and Supplies',
                'categories_ar' => [
                    'ميكروفونات',
                    'مكبرات صوت',
                    'سماعات رأس',
                    'أجهزة ومستلزمات صوتيات'
                ],
                'categories_en' => [
                    'Microphones',
                    'Speakers',
                    'Headphones',
                    'Audio Devices and Supplies'
                ]
            ],
            [
                'main_category_ar' => 'ساعات',
                'main_category_en' => 'Watches',
                'categories_ar' => [
                    'ساعات نسائية',
                    'ساعات رجالي',
                    'ساعات أولاد وبنات',
                    'ساعات حائط',
                    'ساعات مكتب',
                    'ساعات مواقيت الصلاة',
                    'مستلزمات ساعات'
                ],
                'categories_en' => [
                    'Women\'s Watches',
                    'Men\'s Watches',
                    'Boys and Girls Watches',
                    'Wall Clocks',
                    'Desk Clocks',
                    'Prayer Time Clocks',
                    'Watch Supplies'
                ]
            ],
            [
                'main_category_ar' => 'أنظمة أمن وتتبع ومراقبة وسلامة',
                'main_category_en' => 'Security, Tracking, Surveillance and Safety Systems',
                'categories_ar' => [
                    'أنظمة ومستلزمات كاميرات مراقبة',
                    'أنظمة ومستلزمات إنذار سرقة',
                    'أنظمة ومستلزمات إنذار حريق',
                    'أنظمة ومستلزمات تتبع GPS',
                    'أنظمة ومستلزمات أمنية'
                ],
                'categories_en' => [
                    'Surveillance Camera Systems and Supplies',
                    'Burglar Alarm Systems and Supplies',
                    'Fire Alarm Systems and Supplies',
                    'GPS Tracking Systems and Supplies',
                    'Security Systems and Supplies'
                ]
            ],
            [
                'main_category_ar' => 'إلكترونيات متنوعة',
                'main_category_en' => 'Various Electronics',
                'categories_ar' => [
                    'أجهزة إلكترونية متنوعة',
                    'مستلزمات إلكترونية متنوعة'
                ],
                'categories_en' => [
                    'Various Electronic Devices',
                    'Various Electronic Supplies'
                ]
            ],
            [
                'main_category_ar' => 'مستلزمات مكتبية وتعليمية',
                'main_category_en' => 'Stationery and Educational Supplies',
                'categories_ar' => [
                    'أجهزة مكتبية',
                    'مستلزمات مكتبية',
                    'مستلزمات مدرسية',
                    'مستلزمات تعليمية',
                    'مستلزمات فنية',
                    'أشغال يدوية وفنية',
                    'آلات حاسبة',
                    'أجهزة علمية',
                    'برمجيات',
                    'ألعاب إلكترونية',
                    'ألعاب أطفال'
                ],
                'categories_en' => [
                    'Office Equipment',
                    'Stationery Supplies',
                    'School Supplies',
                    'Educational Supplies',
                    'Art Supplies',
                    'Handicrafts and Artworks',
                    'Calculators',
                    'Scientific Equipment',
                    'Software',
                    'Electronic Games',
                    'Children\'s Toys'
                ]
            ],
            [
                'main_category_ar' => 'كتب وقصص ومطبوعات',
                'main_category_en' => 'Books, Stories and Publications',
                'categories_ar' => [
                    'مصاحف وكتب إسلامية',
                    'كتب مدرسية',
                    'كتب تعليمية أخرى',
                    'كتب وقصص عربية',
                    'كتب وقصص أجنبية',
                    'مطبوعات متنوعة'
                ],
                'categories_en' => [
                    'Qurans and Islamic Books',
                    'Textbooks',
                    'Other Educational Books',
                    'Arabic Books and Stories',
                    'Foreign Books and Stories',
                    'Various Publications'
                ]
            ],
            [
                'main_category_ar' => 'منتجات للصحة والطاقة والرجيم',
                'main_category_en' => 'Health, Energy and Diet Products',
                'categories_ar' => [
                    'منتجات صحية',
                    'منتجات طاقة',
                    'منتجات الرجيم',
                    'مكملات غذائية',
                    'أعشاب طبية'
                ],
                'categories_en' => [
                    'Health Products',
                    'Energy Products',
                    'Diet Products',
                    'Food Supplements',
                    'Medicinal Herbs'
                ]
            ],
            [
                'main_category_ar' => 'أجهزة ومستلزمات طبية',
                'main_category_en' => 'Medical Devices and Supplies',
                'categories_ar' => [
                    'أجهزة ومعدات طبية',
                    'قطع غيار أجهزة ومعدات طبية',
                    'أدوات ومستلزمات طبية',
                    'منتجات رعاية طبية'
                ],
                'categories_en' => [
                    'Medical Devices and Equipment',
                    'Medical Device Spare Parts',
                    'Medical Tools and Supplies',
                    'Medical Care Products'
                ]
            ],
            [
                'main_category_ar' => 'أجهزة ومستلزمات رياضة ولياقة وترفيه',
                'main_category_en' => 'Sports, Fitness and Entertainment Devices and Supplies',
                'categories_ar' => [
                    'أجهزة رياضة ولياقة بدنية',
                    'مستلزمات رياضة ولياقة بدنية',
                    'مستلزمات سباحة وغوص',
                    'مستلزمات صيد سمك',
                    'مستلزمات صيد بري',
                    'مستلزمات رحلات وتخييم'
                ],
                'categories_en' => [
                    'Sports and Fitness Equipment',
                    'Sports and Fitness Supplies',
                    'Swimming and Diving Supplies',
                    'Fishing Supplies',
                    'Hunting Supplies',
                    'Camping and Trip Supplies'
                ]
            ],
            [
                'main_category_ar' => 'مستلزمات نجارة وسباكة ودهان',
                'main_category_en' => 'Carpentry, Plumbing and Painting Supplies',
                'categories_ar' => [
                    'مستلزمات نجارة',
                    'مستلزمات سباكة',
                    'فلاتر مياه',
                    'مستلزمات دهان',
                    'عدد وأدوات حرفية'
                ],
                'categories_en' => [
                    'Carpentry Supplies',
                    'Plumbing Supplies',
                    'Water Filters',
                    'Painting Supplies',
                    'Craft Tools and Equipment'
                ]
            ],
            [
                'main_category_ar' => 'أطقم حمامات وسيراميك ورخام',
                'main_category_en' => 'Bathroom Sets, Ceramics and Marble',
                'categories_ar' => [
                    'أطقم حمامات',
                    'سيراميك',
                    'رخام'
                ],
                'categories_en' => [
                    'Bathroom Sets',
                    'Ceramics',
                    'Marble'
                ]
            ],
            [
                'main_category_ar' => 'أنظمة ومستلزمات كهرباء وإنارة',
                'main_category_en' => 'Electricity and Lighting Systems and Supplies',
                'categories_ar' => [
                    'نجف ولمبات وأنظمة إنارة',
                    'مستلزمات توصيلات كهربائية',
                    'أنظمة طاقة شمسية',
                    'مولدات كهرباء',
                    'محولات كهرباء',
                    'أجهزة شحن بطاريات',
                    'بطاريات قابلة للشحن',
                    'بطاريات أخرى'
                ],
                'categories_en' => [
                    'Chandeliers, Bulbs and Lighting Systems',
                    'Electrical Wiring Supplies',
                    'Solar Power Systems',
                    'Electric Generators',
                    'Electrical Transformers',
                    'Battery Chargers',
                    'Rechargeable Batteries',
                    'Other Batteries'
                ]
            ],
            [
                'main_category_ar' => 'سيارات كهربائية وجولف وبيتش وأطفال',
                'main_category_en' => 'Electric Cars, Golf, Beach and Children\'s Cars',
                'categories_ar' => [
                    'سيارات كهربائية ومستلزماتها',
                    'سيارات جولف ومستلزماتها',
                    'بيتش باجي ومستلزماتها',
                    'سيارات اطفال ومستلزماتها'
                ],
                'categories_en' => [
                    'Electric Cars and Accessories',
                    'Golf Cars and Accessories',
                    'Beach Buggies and Accessories',
                    'Children\'s Cars and Accessories'
                ]
            ],
            [
                'main_category_ar' => 'تكاتك وموتوسيكلات ودراجات واسكوترات',
                'main_category_en' => 'Tuk-tuks, Motorcycles, Bicycles and Scooters',
                'categories_ar' => [
                    'تكاتك ومستلزماتها',
                    'تروسيكلات ومستلزماتها',
                    'موتوسيكلات ومستلزماتها',
                    'فسب ومستلزماتها',
                    'اسكوترات ومستلزماتها',
                    'دراجات ومستلزماتها'
                ],
                'categories_en' => [
                    'Tuk-tuks and Accessories',
                    'Tricycles and Accessories',
                    'Motorcycles and Accessories',
                    'Vespas and Accessories',
                    'Scooters and Accessories',
                    'Bicycles and Accessories'
                ]
            ],
            [
                'main_category_ar' => 'مستلزمات وكماليات السيارات',
                'main_category_en' => 'Car Supplies and Accessories',
                'categories_ar' => [
                    'منتجات عناية بالسيارات',
                    'كماليات وزينة السيارات',
                    'شاشات سيارات',
                    'جنوط سيارات',
                    'كاوتش سيارات',
                    'بطاريات سيارات'
                ],
                'categories_en' => [
                    'Car Care Products',
                    'Car Accessories and Decorations',
                    'Car Screens',
                    'Car Rims',
                    'Car Tires',
                    'Car Batteries'
                ]
            ],
            [
                'main_category_ar' => 'تجهيزات شركات ومكاتب',
                'main_category_en' => 'Company and Office Equipment',
                'categories_ar' => [
                    'مكاتب مديرين وموظفين',
                    'مكاتب إستقبال',
                    'كراسي مكاتب وكراسي إنتظار',
                    'طاولات إجتماعات',
                    'بارتيشن مكاتب',
                    'وحدات دواليب وأدراج مكتبية',
                    'مستلزمات وإكسسوارات مكاتب',
                    'ماكينات عد نقود',
                    'خزائن نقود',
                    'أثاث فنادق',
                    'أثاث مطاعم',
                    'أثاث مستشفيات'
                ],
                'categories_en' => [
                    'Managers and Employees Desks',
                    'Reception Desks',
                    'Office Chairs and Waiting Chairs',
                    'Meeting Tables',
                    'Office Partitions',
                    'Office Cabinets and Drawers Units',
                    'Office Supplies and Accessories',
                    'Cash Counting Machines',
                    'Cash Safes',
                    'Hotel Furniture',
                    'Restaurant Furniture',
                    'Hospital Furniture'
                ]
            ],
            [
                'main_category_ar' => 'تجهيزات محلات ومعارض ومخازن',
                'main_category_en' => 'Shops, Showrooms and Warehouses Equipment',
                'categories_ar' => [
                    'أرفف عرض وتخزين',
                    'مستلزمات عرض وتخزين',
                    'ثلاجات عرض وتخزين',
                    'أبواب محلات',
                    'مستلزمات أبواب محلات',
                    'ماكينات تسعير',
                    'مكاتب كاشير',
                    'أجهزة كاشير وملحقاتها',
                    'برامج كاشير ومخزون ومبيعات',
                    'عربات وسلال تسوق',
                    'معدات رفع بضائع',
                    'معدات وأدوات ربط وتغليف',
                    'تجهيزات كافيهات',
                    'تجهيزات كوافير وحلاقين'
                ],
                'categories_en' => [
                    'Display and Storage Shelves',
                    'Display and Storage Supplies',
                    'Display and Storage Refrigerators',
                    'Shop Doors',
                    'Shop Door Supplies',
                    'Pricing Machines',
                    'Cashier Desks',
                    'Cashier Machines and Accessories',
                    'Cashier, Inventory and Sales Software',
                    'Shopping Carts and Baskets',
                    'Goods Lifting Equipment',
                    'Binding and Packaging Equipment and Tools',
                    'Cafe Equipment',
                    'Hairdresser and Barber Equipment'
                ]
            ],
            [
                'main_category_ar' => 'ماكينات وأجهزة ومعدات',
                'main_category_en' => 'Machines, Devices and Equipment',
                'categories_ar' => [
                    'ماكينات وأجهزة ومعدات صناعية',
                    'ماكينات وأجهزة ومعدات تجارية',
                    'ماكينات وأجهزة ومعدات زراعية',
                    'ماكينات وأجهزة مكتبية',
                    'أجهزة ومعدات مطاعم ومطابخ',
                    'أجهزة ومعدات حلواني ومخابز',
                    'مغاسل ومصابغ ملابس',
                    'مغاسل سيارات',
                    'أجهزة ومعدات مطابع',
                    'أجهزة ومعدات ورش',
                    'أجهزة ومعدات معامل',
                    'ماكينات ومعدات حفر وبناء وطرق'
                ],
                'categories_en' => [
                    'Industrial Machines, Devices and Equipment',
                    'Commercial Machines, Devices and Equipment',
                    'Agricultural Machines, Devices and Equipment',
                    'Office Machines and Equipment',
                    'Restaurant and Kitchen Equipment',
                    'Confectionery and Bakery Equipment',
                    'Laundries and Dry Cleaners',
                    'Car Wash Equipment',
                    'Printing Press Equipment',
                    'Workshop Equipment',
                    'Laboratory Equipment',
                    'Excavation, Construction and Road Machinery and Equipment'
                ]
            ],
            [
                'main_category_ar' => 'قطع غيار',
                'main_category_en' => 'Spare Parts',
                'categories_ar' => [
                    'قطع غيار سيارات',
                    'قطع غيار أجهزة منزلية',
                    'قطع غيار أجهزة مكتبية',
                    'قطع غيار معدات صناعية',
                    'قطع غيار معدات تجارية',
                    'قطع غيار معدات زراعية',
                    'قطع غيار معدات حفر وبناء وطرق'
                ],
                'categories_en' => [
                    'Car Spare Parts',
                    'Home Appliance Spare Parts',
                    'Office Equipment Spare Parts',
                    'Industrial Equipment Spare Parts',
                    'Commercial Equipment Spare Parts',
                    'Agricultural Equipment Spare Parts',
                    'Excavation, Construction and Road Equipment Spare Parts'
                ]
            ],
            [
                'main_category_ar' => 'ماكينات ومستلزمات الملابس',
                'main_category_en' => 'Clothing Machines and Supplies',
                'categories_ar' => [
                    'ماكينات قص قماش',
                    'ماكينات خياطة صناعية',
                    'ماكينات خياطة منزلية',
                    'ماكينات تريكو',
                    'ماكينات تطريز',
                    'مستلزمات خياطة وتطريز وتريكو',
                    'قطع غيار خياطة وتطريز وتريكو'
                ],
                'categories_en' => [
                    'Fabric Cutting Machines',
                    'Industrial Sewing Machines',
                    'Home Sewing Machines',
                    'Knitting Machines',
                    'Embroidery Machines',
                    'Sewing, Embroidery and Knitting Supplies',
                    'Sewing, Embroidery and Knitting Spare Parts'
                ]
            ],
            [
                'main_category_ar' => 'أعلاف وأطعمة حيوانات وطيور وأسماك',
                'main_category_en' => 'Animal, Bird and Fish Feed and Food',
                'categories_ar' => [
                    'أعلاف مواشي ودواجن وأسماك',
                    'أطعمة حيوانات وطيور أليفة',
                    'أطعمة أسماك زينة',
                    'مستلزمات مواشي ودواجن وأسماك',
                    'مستلزمات حيوانات وطيور أليفة',
                    'مستلزمات أسماك زينة'
                ],
                'categories_en' => [
                    'Livestock, Poultry and Fish Feed',
                    'Pet Animal and Bird Food',
                    'Ornamental Fish Food',
                    'Livestock, Poultry and Fish Supplies',
                    'Pet Animal and Bird Supplies',
                    'Ornamental Fish Supplies'
                ]
            ],
            [
                'main_category_ar' => 'أسمدة ومواد وأدوات زراعية',
                'main_category_en' => 'Fertilizers, Materials and Agricultural Tools',
                'categories_ar' => [
                    'أسمدة ومخصبات',
                    'بذور وتقاوي',
                    'مبيدات زراعية',
                    'تربة زراعية ومحسنات',
                    'مستلزمات زراعة ومشاتل وحدائق'
                ],
                'categories_en' => [
                    'Fertilizers and Conditioners',
                    'Seeds and Saplings',
                    'Agricultural Pesticides',
                    'Agricultural Soil and Improvers',
                    'Farming, Nursery and Garden Supplies'
                ]
            ],
            [
                'main_category_ar' => 'سوبر ماركت لولو',
                'main_category_en' => 'Lulu Supermarket',
                'categories_ar' => [
                    'منتجات لحوم',
                    'منتجات دواجن',
                    'فواكه وخضروات طازجة',
                    'فواكه وخضروات مجمدة',
                    'أجبان ومنتجات ألبان',
                    'بقوليات',
                    'معلبات وأغذية مغلفة',
                    'شوكولاتة وبسكويت وسكاكر',
                    'عصائر ومشروبات وأيس كريم',
                    'عطارة',
                    'تمور ومكسرات وتسالي',
                    'حلويات ومخبوزات',
                    'خامات الحلويات والمخبوزات'
                ],
                'categories_en' => [
                    'Meat Products',
                    'Poultry Products',
                    'Fresh Fruits and Vegetables',
                    'Frozen Fruits and Vegetables',
                    'Cheese and Dairy Products',
                    'Legumes',
                    'Canned and Packaged Foods',
                    'Chocolate, Biscuits and Candies',
                    'Juices, Drinks and Ice Cream',
                    'Spices and Herbs',
                    'Dates, Nuts and Snacks',
                    'Sweets and Baked Goods',
                    'Sweets and Baked Goods Raw Materials'
                ]
            ]
        ];

        // Loop through each main category
        foreach ($categoriesData as $index => $categoryData) {
            // Create main category with default name (can be empty since translations exist)
            $mainCategory = MainCategory::create([
                'name' => $categoryData['main_category_en'], // Default English name
                'slug' => $this->generateSlug($categoryData['main_category_en']),
                'is_active' => true,
                'image' => null,
            ]);

            // Create Arabic translation
            $mainCategory->translations()->create([
                'name' => $categoryData['main_category_ar'],
                'slug' => $this->generateSlug($categoryData['main_category_ar']),
                'locale' => 'ar'
            ]);

            // Create English translation (or update the main record)
            $mainCategory->translations()->create([
                'name' => $categoryData['main_category_en'],
                'slug' => $this->generateSlug($categoryData['main_category_en']),
                'locale' => 'en'
            ]);

            // Create categories for this main category
            if (!empty($categoryData['categories_ar'])) {
                foreach ($categoryData['categories_ar'] as $key => $categoryAr) {
                    $categoryEn = $categoryData['categories_en'][$key] ?? $categoryAr;

                    $category = Category::create([
                        'name' => $categoryEn, // Default English name
                        'slug' => $this->generateSlug($categoryEn),
                        'main_category_id' => $mainCategory->id,
                        'is_active' => true,
                        'type' => 'piece', // Default type
                        'image' => null,
                    ]);

                    // Create Arabic translation for category
                    $category->translations()->create([
                        'name' => $categoryAr,
                        'slug' => $this->generateSlug($categoryAr),
                        'locale' => 'ar'
                    ]);

                    // Create English translation for category
                    $category->translations()->create([
                        'name' => $categoryEn,
                        'slug' => $this->generateSlug($categoryEn),
                        'locale' => 'en'
                    ]);
                }
            }
        }

        $this->command->info('Main categories and categories seeded successfully!');
    }

    /**
     * Generate slug from name
     */
    private function generateSlug($name)
    {
        return strtolower(
            preg_replace(
                '/[^A-Za-z0-9\-]/',
                '',
                str_replace(' ', '-', $name)
            )
        );
    }
}
