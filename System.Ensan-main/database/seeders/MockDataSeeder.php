<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Project;
use App\Models\Campaign;
use App\Models\Donor;
use App\Models\Beneficiary;
use App\Models\Warehouse;
use App\Models\Item;
use App\Models\InventoryTransaction;
use App\Models\Donation;
use App\Models\WebSetting;
use App\Models\WebNews;
use App\Models\WebTestimonial;
use App\Models\WebVolunteerWall;
use App\Models\WebBranch;
use App\Models\WebSector;
use App\Models\WebFeature;
use App\Models\WebPartner;
use App\Models\WebBoardMember;
use App\Models\WebFaq;
use App\Models\WebPage;
use App\Models\WebDynamicCard;

class MockDataSeeder extends Seeder
{
    public function run()
    {
        // 1. Users & Staff
        $this->command->info('Creating Staff...');
        
        $roles = [
            'manager' => 'مدير المؤسسة',
            'hr' => 'الموارد البشرية',
            'finance' => 'المالية',
            'store_keeper' => 'أمين المخزن',
            'receptionist' => 'الاستقبال',
            'field_researcher' => 'باحث ميداني'
        ];

        foreach ($roles as $key => $name) {
            $role = Role::firstOrCreate(['key' => $key], ['name' => $name]);
            
            $user = User::firstOrCreate(
                ['email' => "{$key}@ensan.local"],
                [
                    'name' => "موظف " . $name,
                    'password' => Hash::make('password'),
                    'is_employee' => true,
                    'active' => true,
                    'salary' => rand(4000, 10000),
                    'join_date' => now()->subMonths(rand(1, 24)),
                    'contract_start_date' => now()->subMonths(rand(1, 12)),
                    'contract_end_date' => now()->addMonths(rand(6, 12)),
                ]
            );
            
            if (!$user->roles->contains($role->id)) {
                $user->roles()->attach($role->id);
            }
        }

        // 2. Warehouses & Items
        $this->command->info('Creating Warehouses & Items...');
        $warehouse = Warehouse::create([
            'name' => 'المخزن الرئيسي - القاهرة',
            'location' => 'مدينة نصر'
        ]);

        $items = [
            ['name' => 'بطانيات شتوية', 'unit' => 'قطعة'],
            ['name' => 'كرتونة مواد غذائية', 'unit' => 'كرتونة'],
            ['name' => 'شنطة مدرسية', 'unit' => 'قطعة'],
            ['name' => 'أدوية متنوعة', 'unit' => 'علبة'],
        ];

        foreach ($items as $itemData) {
            $item = Item::create(array_merge($itemData, [
                'sku' => 'SKU-' . rand(1000, 9999),
                'estimated_value' => rand(50, 500)
            ]));
            
            // Initial Stock (InventoryTransaction requires warehouse_id and item_id)
            InventoryTransaction::create([
                'warehouse_id' => $warehouse->id,
                'item_id' => $item->id,
                'type' => 'in',
                'quantity' => rand(100, 500),
                'reference' => 'OPENING-STOCK'
            ]);
        }

        // 3. Projects & Campaigns
        $this->command->info('Creating Projects & Campaigns...');
        $projects = [
            ['name' => 'كفالة اليتيم', 'description' => 'مشروع لكفالة الأيتام وتوفير حياة كريمة لهم', 'status' => 'active'],
            ['name' => 'إطعام الطعام', 'description' => 'توزيع وجبات وكراتين غذائية للمحتاجين', 'status' => 'active'],
            ['name' => 'سقيا الماء', 'description' => 'حفر الآبار وتوصيل المياه للقرى الفقيرة', 'status' => 'active'],
            ['name' => 'التعليم للجميع', 'description' => 'دعم الطلاب غير القادرين وتوفير المستلزمات', 'status' => 'active'],
        ];

        foreach ($projects as $proj) {
            $p = Project::create($proj);
            
            // Create Campaign for the project
            Campaign::create([
                'project_id' => $p->id,
                'name' => 'حملة ' . $proj['name'] . ' - رمضان',
                'target_amount' => rand(50000, 500000),
                'season_year' => (int) date('Y'),
                'start_date' => now(),
                'end_date' => now()->addMonth(),
                'status' => 'active'
            ]);
        }

        // 4. Donors
        $this->command->info('Creating Donors...');
        $donorNames = ['محمد أحمد', 'أحمد علي', 'سارة محمود', 'منى خليل', 'خالد يوسف', 'شركة النور', 'مؤسسة الخير'];
        foreach ($donorNames as $i => $name) {
            Donor::create([
                'name' => $name,
                'email' => "donor{$i}@test.com",
                'phone' => '0100000000' . $i,
                'type' => $i > 5 ? 'organization' : 'individual',
                'active' => true
            ]);
        }

        // 5. Beneficiaries
        $this->command->info('Creating Beneficiaries...');
        $beneficiaries = [
            [
                'full_name' => 'أسرة أم محمد',
                'national_id' => '29001011234567',
                'status' => 'accepted',
                'assistance_type' => 'financial',
            ],
            [
                'full_name' => 'الطفل كريم',
                'national_id' => '31505051234567',
                'status' => 'accepted',
                'assistance_type' => 'financial',
            ],
            [
                'full_name' => 'الحاجة فاطمة',
                'national_id' => '26002021234567',
                'status' => 'under_review',
                'assistance_type' => 'in_kind',
            ],
            [
                'full_name' => 'الطالب عمر',
                'national_id' => '30503031234567',
                'status' => 'new',
                'assistance_type' => 'service',
            ],
        ];

        foreach ($beneficiaries as $ben) {
            Beneficiary::create(array_merge($ben, [
                'phone' => '0110000000' . rand(0, 9),
                'address' => 'القاهرة - عنوان تجريبي',
            ]));
        }

        // 6. Recent Donations
        $this->command->info('Creating Donations...');
        $donors = Donor::all();
        $projects = Project::all();
        
        for ($i = 0; $i < 20; $i++) {
            Donation::create([
                'donor_id' => $donors->random()->id,
                'project_id' => $projects->random()->id,
                'amount' => rand(100, 5000),
                'type' => 'cash',
                'status' => 'active',
                'received_at' => now()->subDays(rand(0, 30)),
                'receipt_number' => 'REC-' . rand(1000, 9999),
                'created_by' => 1
            ]);
        }

        $this->command->info('Seeding website settings...');

        WebSetting::set('site_name', 'مؤسسة إنسان للأعمال الخيرية');
        WebSetting::set('hero_title_primary', 'معاً نصنع أثراً حقيقياً');
        WebSetting::set('hero_title_secondary', 'في حياة المحتاجين');
        WebSetting::set('hero_description', 'من خلال برامج متكاملة في الكفالة، الإطعام، السقيا، والتعليم، نعمل على صناعة تغيير مستدام في حياة الأفراد والأسر.');
        WebSetting::set('notification_label', 'جديد');
        WebSetting::set('notification_text', 'انطلاق حملة الشتاء لتوزيع البطاطين والمواد الغذائية');
        WebSetting::set('notification_link_text', 'اعرف المزيد');
        WebSetting::set('notification_link_url', '#');

        WebSetting::set('campaigns_title', 'حملاتنا الجارية تنتظر مساهمتك');
        WebSetting::set('campaigns_subtitle', 'شارك في دعم الأيتام، وإطعام المحتاجين، وسقيا الماء للقرى البعيدة.');

        WebSetting::set('cta_title', 'كن جزءاً من قصة نجاح');
        WebSetting::set('cta_text', 'كل مساهمة منك تصنع فارقاً في حياة إنسان.');
        WebSetting::set('cta_stat1_value', '50M+');
        WebSetting::set('cta_stat1_label', 'تبرعات');
        WebSetting::set('cta_stat2_value', '150K+');
        WebSetting::set('cta_stat2_label', 'مستفيد');
        WebSetting::set('cta_stat3_value', '10+');
        WebSetting::set('cta_stat3_label', 'سنوات عطاء');

        WebSetting::set('volunteer_title', 'تطوع معنا وكن جزءاً من التغيير');
        WebSetting::set('volunteer_subtitle', 'ساعات من وقتك تساوي حياة كاملة عند غيرك');
        WebSetting::set('volunteer_description', 'نستقبل المتطوعين في مجالات مختلفة: تنظيم الحملات، التوزيع الميداني، التصميم، التسويق الرقمي، والمزيد.');
        WebSetting::set('volunteer_stats_volunteers', '1200+');
        WebSetting::set('volunteer_stats_hours', '25,000+');
        WebSetting::set('volunteer_stats_branches', '3');

        WebSetting::set('partners_stats_donors', '5000+');
        WebSetting::set('partners_stats_volunteers', '1200+');
        WebSetting::set('partners_stats_campaigns', '180+');
        WebSetting::set('partners_stats_institutions', '25+');

        WebSetting::set('footer_about_text', 'مؤسسة غير ربحية تعمل على تمكين الأفراد والأسر من خلال مشاريع مستدامة في مجالات الكفالة، التعليم، الصحة، والإغاثة.');

        $this->command->info('Seeding website content models...');

        WebNews::firstOrCreate(
            ['title' => 'انطلاق حملة الشتاء 2026 لتوزيع البطاطين'],
            [
                'content' => 'أطلقت مؤسسة إنسان حملة شتاء 2026 لتوزيع بطاطين وكراتين غذائية على الأسر الأكثر احتياجاً في القرى والمناطق النائية.',
                'category' => 'حملات',
                'contact_name' => 'فريق الحملات',
                'contact_number' => '01000000001',
                'views_count' => 120,
                'shares_count' => 35,
                'published_at' => now()->subDays(5)
            ]
        );

        WebNews::firstOrCreate(
            ['title' => 'تكريم المتطوعين المتميزين في ضيافة إنسان'],
            [
                'content' => 'نظمت المؤسسة حفلاً لتكريم المتطوعين الذين تجاوزت ساعات عطائهم 300 ساعة خلال العام الماضي.',
                'category' => 'متطوعين',
                'contact_name' => 'قسم التطوع',
                'contact_number' => '01000000002',
                'views_count' => 80,
                'shares_count' => 20,
                'published_at' => now()->subDays(10)
            ]
        );

        WebTestimonial::firstOrCreate(
            ['name' => 'أحمد عبد الله', 'content' => 'تجربتي مع مؤسسة إنسان كانت مختلفة، شعرت أن تبرعي يصل لمستحقيه فعلاً.'],
            [
                'role' => 'متبرع',
                'rating' => 5,
                'status' => 'approved'
            ]
        );

        WebTestimonial::firstOrCreate(
            ['name' => 'سارة علي', 'content' => 'أكثر ما أعجبني هو التنظيم واحترام وقت المتطوعين. تجربة أفتخر بها.'],
            [
                'role' => 'متطوعة',
                'rating' => 5,
                'status' => 'approved'
            ]
        );

        WebVolunteerWall::updateOrCreate(
            ['rank' => 1],
            [
                'name' => 'محمد سامي',
                'hours' => '420',
                'image_path' => null
            ]
        );

        WebVolunteerWall::updateOrCreate(
            ['rank' => 2],
            [
                'name' => 'ريما خالد',
                'hours' => '360',
                'image_path' => null
            ]
        );

        WebVolunteerWall::updateOrCreate(
            ['rank' => 3],
            [
                'name' => 'مصطفى حسن',
                'hours' => '310',
                'image_path' => null
            ]
        );

        WebBranch::firstOrCreate(
            ['name' => 'الفرع الرئيسي - القاهرة'],
            [
                'address' => 'القاهرة، مدينة نصر، شارع مكرم عبيد',
                'phone' => '01000000003',
                'working_hours' => 'من 9 صباحاً حتى 5 مساءً',
                'email' => 'cairo@ensan.org',
                'google_maps_url' => null,
                'is_main' => true,
                'status_text' => 'يخدم محافظات القاهرة الكبرى'
            ]
        );

        WebBranch::firstOrCreate(
            ['name' => 'فرع الإسكندرية'],
            [
                'address' => 'الإسكندرية، سموحة، طريق 14 مايو',
                'phone' => '01000000004',
                'working_hours' => 'من 10 صباحاً حتى 6 مساءً',
                'email' => 'alex@ensan.org',
                'google_maps_url' => null,
                'is_main' => false,
                'status_text' => 'يخدم محافظة الإسكندرية وما حولها'
            ]
        );

        WebSector::firstOrCreate(
            ['title' => 'الكفالة'],
            [
                'icon' => 'bi-people',
                'description' => 'برامج متخصصة لكفالة الأيتام والأسر الأكثر احتياجاً.'
            ]
        );

        WebSector::firstOrCreate(
            ['title' => 'الإطعام'],
            [
                'icon' => 'bi-basket3',
                'description' => 'توزيع وجبات ساخنة وكراتين غذائية على مدار العام.'
            ]
        );

        WebSector::firstOrCreate(
            ['title' => 'السقيا'],
            [
                'icon' => 'bi-droplet',
                'description' => 'حفر الآبار وتأمين مصادر مياه آمنة للقرى المحرومة.'
            ]
        );

        WebFeature::firstOrCreate(
            ['title' => 'شفافية في التقارير'],
            [
                'content' => 'إصدار تقارير دورية عن أثر كل حملة ومصارف التبرعات.',
                'icon' => 'bi-graph-up',
                'sort_order' => 1
            ]
        );

        WebFeature::firstOrCreate(
            ['title' => 'شراكات موثوقة'],
            [
                'content' => 'نتعاون مع مؤسسات وجهات رسمية لضمان وصول الدعم لمستحقيه.',
                'icon' => 'bi-handshake',
                'sort_order' => 2
            ]
        );

        WebPartner::firstOrCreate(
            ['name' => 'شركة النور القابضة'],
            [
                'description' => 'شريك استراتيجي في حملات الكفالة والإطعام.',
                'type' => 'corporate',
                'website_url' => 'https://example.com'
            ]
        );

        WebPartner::firstOrCreate(
            ['name' => 'مؤسسة الخير التنموية'],
            [
                'description' => 'شريك في مشاريع التنمية المستدامة والتعليم.',
                'type' => 'ngo',
                'website_url' => 'https://example.org'
            ]
        );

        WebBoardMember::firstOrCreate(
            ['name' => 'د. عبد الرحمن السيد'],
            [
                'role' => 'رئيس مجلس الإدارة',
                'description' => 'خبرة طويلة في إدارة المؤسسات غير الربحية والعمل الإنساني.',
                'image_path' => null,
                'sort_order' => 1
            ]
        );

        WebBoardMember::firstOrCreate(
            ['name' => 'م. آمنة الشربيني'],
            [
                'role' => 'نائب الرئيس',
                'description' => 'متخصصة في إدارة المشاريع الاجتماعية والتنموية.',
                'image_path' => null,
                'sort_order' => 2
            ]
        );

        WebFaq::firstOrCreate(
            ['question' => 'كيف يمكنني التبرع؟'],
            [
                'answer' => 'يمكنك التبرع من خلال مقر المؤسسة، أو التحويل البنكي، أو بوابة التبرع الإلكترونية.',
                'category' => 'عام',
                'sort_order' => 1
            ]
        );

        WebFaq::firstOrCreate(
            ['question' => 'هل تصل التبرعات لمستحقيها فعلاً؟'],
            [
                'answer' => 'نلتزم بإجراءات صارمة في البحث الاجتماعي والمتابعة الميدانية لضمان وصول التبرعات لمستحقيها.',
                'category' => 'الشفافية',
                'sort_order' => 2
            ]
        );

        WebPage::firstOrCreate(
            ['title' => 'من نحن'],
            [
                'content' => '<p>مؤسسة إنسان هي مؤسسة أهلية غير هادفة للربح تعمل في مجالات الكفالة، الإغاثة، التنمية والتعليم.</p>',
                'meta_title' => 'عن مؤسسة إنسان',
                'meta_description' => 'تعرف على رسالة ورؤية وأهداف مؤسسة إنسان.',
                'meta_keywords' => 'مؤسسة إنسان, أعمال خيرية, كفالة يتيم',
                'is_published' => true,
                'sort_order' => 1
            ]
        );

        WebDynamicCard::firstOrCreate(
            ['title' => 'كفالة يتيم'],
            [
                'description' => 'ساهم بمبلغ شهري لتأمين حياة كريمة لليتيم.',
                'image' => null,
                'badge_text' => 'أولوية',
                'badge_visible' => true,
                'is_active' => true,
                'stats_data' => [
                    ['label' => 'أيتام مكفولين', 'value' => '320+'],
                    ['label' => 'مدن مستفيدة', 'value' => '12']
                ],
                'buttons_data' => [
                    ['text' => 'ساهم الآن', 'action' => '#', 'style' => 'primary', 'icon' => null]
                ],
                'sort_order' => 1
            ]
        );

        $this->command->info('Mock Data Created Successfully! 🚀');
    }
}
