<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MainCategory;
use App\Models\MainCategoryTranslation;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class MainCategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        MainCategoryTranslation::truncate();
        MainCategory::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $categories = [
            ['ar' => 'أزياء، شنط، أحذية، شرابات "جوارب"', 'en' => 'Fashion, Bags, Shoes, Socks'],
            ['ar' => 'أقمشة', 'en' => 'Fabrics'],
            ['ar' => 'سجاد ومفروشات ومراتب وستائر', 'en' => 'Carpets, Furnishings, Mattresses, Curtains'],
            ['ar' => 'مستلزمات ووسائل تعليم وترفيه وألعاب للأطفال', 'en' => 'Educational, Entertainment, and Toys for Kids'],
            ['ar' => 'أدوات وهدايا وإكسسوارات ومستلزمات شخصية', 'en' => 'Tools, Gifts, Accessories, Personal Items'],
            ['ar' => 'مواد النظافة والعناية بالبيت', 'en' => 'Cleaning and Home Care Products'],
            ['ar' => 'أدوات ومستلزمات منزلية', 'en' => 'Home Supplies and Tools'],
            ['ar' => 'أثاث منزلي ومكتبي', 'en' => 'Home and Office Furniture'],
            ['ar' => 'بوابات وأبواب وشبابيك', 'en' => 'Gates, Doors, and Windows'],
            ['ar' => 'إكسسوارات ولوازم الموبيليات والأبواب والستائر', 'en' => 'Furniture, Door, and Curtain Accessories'],
            ['ar' => 'لوحات وأنتيكات وقطع ديكور ومشغولات', 'en' => 'Paintings, Antiques, Decorations, and Crafts'],
            ['ar' => 'منتجات الدعاية والإعلان', 'en' => 'Advertising and Promotional Products'],
            ['ar' => 'كتب ومطبوعات وأوراق ومستندات', 'en' => 'Books, Prints, Papers, and Documents'],
            ['ar' => 'أطقم حمامات', 'en' => 'Bathroom Sets'],
            ['ar' => 'أكسسوارات ومستلزمات السباكة والحمامات وفلاتر مياه', 'en' => 'Plumbing, Bathroom Accessories, and Water Filters'],
            ['ar' => 'أكسسوارات ومستلزمات الكهرباء والإنارة', 'en' => 'Electrical and Lighting Accessories'],
            ['ar' => 'تنسيق الحدائق ومشاتل', 'en' => 'Landscaping and Nurseries'],
            ['ar' => 'سيارات جولف وبيتش وأطفال واسكوترات ودراجات وقطع الغيار', 'en' => 'Golf Carts, Beach Cars, Kids Cars, Scooters, Bikes & Spare Parts'],
            ['ar' => 'أدوات ومستلزمات الرياضة واللياقة البدنية', 'en' => 'Sports and Fitness Equipment'],
            ['ar' => 'أدوية ومنتجات للصحة والقوة والطاقة والدايت', 'en' => 'Health, Strength, Energy, and Diet Products'],
            ['ar' => 'منتجات غذائية من السوبر ماركت', 'en' => 'Supermarket Food Products'],
            ['ar' => 'وجبات غذائية من المطاعم', 'en' => 'Restaurant Meals'],
            ['ar' => 'سوائل في براميل أو جوالين أو زجاجات أو علب', 'en' => 'Liquids in Barrels, Gallons, Bottles, or Cans'],
            ['ar' => 'مستلزمات وكماليات العناية بالسيارات وقطع الغيار', 'en' => 'Car Care Accessories and Spare Parts'],
            ['ar' => 'أجهزة منزلية وإلكترونية ورياضية وطبية وتجارية وصناعية وقطع الغيار', 'en' => 'Home, Electronic, Sports, Medical, Commercial, and Industrial Devices'],
            ['ar' => 'أجهزة وأدوات ومستلزمات الصوتيات والشاشات والإلكترونيات والتصوير', 'en' => 'Audio, Display, Electronic, and Photography Equipment'],
            ['ar' => 'تجهيزات المحلات والمعارض والمخازن والشركات والمكاتب', 'en' => 'Shop, Exhibition, Warehouse, and Office Equipment'],
            ['ar' => 'ماكينات وأجهزة ومعدات وعدد وأدوات ومستلزمات صناعية أو طبية', 'en' => 'Machines, Devices, Tools, Industrial and Medical Equipment'],
            ['ar' => 'ألواح زجاج', 'en' => 'Glass Sheets'],
            ['ar' => 'ألواح خشب، أعواد خشب', 'en' => 'Wood Sheets and Sticks'],
            ['ar' => 'ألواح حديد، أعواد حديد، أسياخ حديد', 'en' => 'Iron Sheets, Rods, and Bars'],
            ['ar' => 'ألواح أو أعواد "بلاستيك، صاج، ألومينيوم، معادن أخرى"', 'en' => 'Plastic, Metal, Aluminum, or Other Material Sheets or Rods'],
            ['ar' => 'قطع أو الواح "سفنج، فوم"', 'en' => 'Foam or Sponge Sheets and Pieces'],
            ['ar' => 'سيراميك، بلاط، رخام', 'en' => 'Ceramic, Tiles, Marble'],
            ['ar' => 'أدوات ومواد ومستلزمات متنوعة', 'en' => 'Miscellaneous Tools and Materials'],
        ];

        foreach ($categories as $category) {
            $main = MainCategory::create([
                'name' => $category['en'],
                'slug' => Str::slug($category['en']),
                'image' => null,
                'is_active' => true,
            ]);

            // English translation
            MainCategoryTranslation::create([
                'main_category_id' => $main->id,
                'name' => $category['en'],
                'slug' => Str::slug($category['en']),
                'locale' => 'en',
            ]);

            // Arabic translation
            MainCategoryTranslation::create([
                'main_category_id' => $main->id,
                'name' => $category['ar'],
                'slug' => Str::slug($category['en']),
                'locale' => 'ar',
            ]);
        }
    }
}
