<?php
use Illuminate\Support\Facades\DB;
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

DB::table('mobile_home_items')->truncate();

$data = [
    [
        'type' => 'hero', 'title' => 'حملة رمضان 2026', 'description' => 'أفطر صائماً في رمضان واكسب الأجر',
        'image_path' => 'mobile/home/hero_ramadan.jpg', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now(),
    ],
    [
        'type' => 'hero', 'title' => 'كفالة الأيتام', 'description' => 'كن سبباً في سعادة طفل يتيم',
        'image_path' => 'mobile/home/hero_orphans.jpg', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now(),
    ],
    [
        'type' => 'service', 'title' => 'مشروع زاد الأيتام', 'description' => 'كفالة الأسر المحتاجة بوجبات شهرية',
        'image_path' => 'mobile/home/service_zad.jpg', 'icon' => 'bi-heart', 'price' => 500.00, 'share_price' => 50.00, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now(),
    ],
    [
        'type' => 'service', 'title' => 'مشروع بعثاء الأمل', 'description' => 'توفير العلاج الطبي للحالات الحرجة',
        'image_path' => 'mobile/home/service_hope.jpg', 'icon' => 'bi-heart-pulse', 'price' => 1000.00, 'share_price' => 100.00, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now(),
    ],
    [
        'type' => 'share', 'title' => 'الأثاث المنزلي', 'description' => 'تبرع بالأثاث الذي لا تحتاجه للأسر الفقيرة',
        'image_path' => 'mobile/home/share_furniture.jpg', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now(),
    ],
    [
        'type' => 'share', 'title' => 'الملابس الشتوية', 'description' => 'دفئ غطاء في برد الشتاء',
        'image_path' => 'mobile/home/share_clothes.jpg', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now(),
    ],
    [
        'type' => 'gallery', 'image_path' => 'mobile/home/gallery_1.jpg', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now(),
    ],
    [
        'type' => 'gallery', 'image_path' => 'mobile/home/gallery_2.jpg', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now(),
    ],
    [
        'type' => 'campaign', 'title' => 'حملة الشتاء الدافئ', 'description' => 'توفير البطانيات والملابس الثقيلة',
        'image_path' => 'mobile/home/campaign_winter.jpg', 'details' => 'نسعى للوصول لـ 1000 أسرة', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now(),
    ],
    [
        'type' => 'about_us', 'title' => 'عن مؤسسة إنسان', 'description' => 'نحن مؤسسة خيرية تسعى لتخفيف المعاناة وتقديم الدعم في شتى المجالات الإنسانية.',
        'image_path' => 'mobile/home/about_us.jpg', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now(),
    ],
    [
        'type' => 'final', 'title' => 'انضم لعائلة إنسان', 'description' => 'دعنا نصنع أثراً إيجابياً معاً',
        'sort_order' => 1, 'created_at' => now(), 'updated_at' => now(),
    ],
];

foreach ($data as $row) {
    DB::table('mobile_home_items')->insert($row);
}

echo "Mock data inserted successfully with correct encoding!\n";
