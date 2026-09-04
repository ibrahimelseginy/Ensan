<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use App\Models\Project;
use App\Models\Campaign;
use App\Models\WebBoardMember;
use App\Models\WebPartner;
use App\Models\WebNews;
use App\Models\WebSetting;
use App\Models\WebVolunteerRequest;
use App\Models\WebContactMessage;
use App\Models\WebRoomBooking;
use App\Models\WebTestimonial;
use App\Models\WebFeature;
use App\Models\WebSector;
use App\Models\WebBranch;
use App\Models\WebVolunteerWall;
use App\Models\WebFaq;
use App\Models\WebEvent;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

final class WebsiteApiController extends Controller
{
    protected $donationSettings;

    public function __construct(\App\Features\WebsiteContent\Interfaces\DonationPageSettingsInterface $donationSettings)
    {
        $this->donationSettings = $donationSettings;
    }

    private function getStorageUrl($path): ?string
    {
        if (!$path) return null;

        // تنظيف المسار من أي URL كامل محفوظ بالخطأ
        $path = \App\Traits\UploadsImages::normalizeImagePath($path);
        if (!$path) return null;

        // Storage::disk('public')->url() يقرأ APP_URL تلقائياً
        // → local: http://127.0.0.1:8000/storage/path
        // → production: https://system.ensaneg.com/storage/path
        return Storage::disk('public')->url($path);
    }

    private function getMediaUrl($path): ?string
    {
        if (!$path) return null;

        // تنظيف المسار من أي URL كامل محفوظ بالخطأ
        $path = \App\Traits\UploadsImages::normalizeImagePath($path);
        if (!$path) return null;

        // استخدام Storage::disk('public')->url() للاتساق
        return Storage::disk('public')->url($path);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/website",
     *     tags={"Website"},
     *     summary="Get landing page data",
     *     description="Returns hero, stats, gallery, featured projects/campaigns/news, testimonials, features, sectors, partners, branches, volunteer wall, FAQs and other landing page sections.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response with landing page data"
     *     )
     * )
     * @OA\Get(
     *     path="/api/v1/website/landing",
     *     tags={"Website"},
     *     summary="Get landing page data (alias)",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response with landing page data"
     *     )
     * )
     */
    public function landingPage()
    {
        $cacheDuration = app()->environment('local') ? 0 : 3600;
        $data = Cache::remember('website_landing_page_data', $cacheDuration, function () {
            $settings = WebSetting::all()->pluck('value', 'key')->toArray();

            // Helper: safe settings get with fallback (handles both missing keys AND empty strings)
            $s = fn($key, $default = '') => (!empty($settings[$key])) ? $settings[$key] : $default;

            // Prepare stats
            $stats = [
                'donations' => $s('stats_donations', '13M+'),
                'donations_label' => $s('stats_donations_label', 'التبرعات الخيريه'),
                'projects' => $s('stats_projects', '400K'),
                'projects_count' => $s('stats_projects', '400K'),
                'projects_label' => $s('stats_projects_label', 'المشاريع'),
                'beneficiaries' => $s('stats_beneficiaries', '300K'),
                'beneficiaries_label' => $s('stats_beneficiaries_label', 'المستفيدون'),
                'governorates' => $s('stats_governorates', '45'),
                'governorates_label' => $s('stats_governorates_label', 'المحافظات'),
                'branches' => $s('stats_branches', '4'),
                'branches_label' => $s('stats_branches_label', 'فرع'),
                'volunteers' => $s('stats_volunteers', '1000+'),
                'volunteers_label' => $s('stats_volunteers_label', 'متطوع نشط'),
            ];

            // Guest House Specific Stats (Image 9)
            $guestHouseStats = [
                'beds' => [
                    'value' => $s('gh_stat1_value', $s('gh_stats_beds', '+50')),
                    'label' => $s('gh_stat1_label', $s('gh_stats_beds_label', 'سرير')),
                ],
                'patients' => [
                    'value' => $s('gh_stat2_value', $s('gh_stats_patients', '+3000')),
                    'label' => $s('gh_stat2_label', $s('gh_stats_patients_label', 'مريض سنوياً')),
                ],
                'branches' => [
                    'value' => $s('gh_stat3_value', $s('gh_stats_branches', '2')),
                    'label' => $s('gh_stat3_label', $s('gh_stats_branches_label', 'فرع')),
                ],
                'reception' => [
                    'value' => $s('gh_stat4_value', $s('gh_stats_reception', '24/7')),
                    'label' => $s('gh_stat4_label', $s('gh_stats_reception_label', 'استقبال')),
                ],
            ];

            // Prepare gallery (Mix of general and guest house slider)
            $gallery = [];
            for ($i = 1; $i <= 6; $i++) {
                $path = $settings['gallery_image_' . $i] ?? null;
                if ($path) {
                    $gallery[] = $this->getMediaUrl($path);
                }
            }

            // Add GH Slider images if general gallery is empty
            if (empty($gallery)) {
                for ($i = 1; $i <= 10; $i++) {
                    $path = $settings["gh_slider_$i"] ?? null;
                    if ($path) {
                        $gallery[] = $this->getMediaUrl($path);
                    }
                }
            }

            // Prepare News Slider
            $newsSlider = [];
            for ($i = 1; $i <= 10; $i++) {
                $path = $settings["news_slider_$i"] ?? null;
                if ($path) {
                    $newsSlider[] = $this->getMediaUrl($path);
                }
            }

            // Prepare hero
            $hero = [
                'title_primary' => $s('hero_title_primary', 'نبني جيل'),
                'title_secondary' => $s('hero_title_secondary', 'يبني حياه'),
                'description' => $s('hero_description'),
                'primary_button_text' => $s('hero_primary_button_text', 'تبرع الان'),
                'primary_button_link' => $s('hero_primary_button_link', 'http://127.0.0.1:4200/donate'),
                'image' => !empty($settings['hero_image']) ? $this->getMediaUrl($settings['hero_image']) : null,
                'stat_beneficiaries' => $s('stats_beneficiaries', '300K'),
                'stat_donations' => $s('stats_donations', '13M+'),
                'stat_projects' => $s('stats_projects', '400K'),
                'stat_governorates' => $s('stats_governorates', '45'),
                'stat_volunteers' => $s('stats_volunteers', '1000+'),
            ];

            // Campaigns Section Settings
            $campaignsSettings = [
                'title' => $s('campaigns_title', 'حملاتنا'),
                'subtitle' => $s('campaigns_subtitle', 'شاركنا الخير'),
                'labels' => [
                    'active' => $s('campaigns_active_label', 'نشطة'),
                    'upcoming' => $s('campaigns_upcoming_label', 'قادمة'),
                    'contribute_btn' => $s('campaigns_contribute_btn', 'ساهم الآن'),
                    'remind_btn' => $s('campaigns_remind_btn', 'ذكرني'),
                    'collected_label' => $s('campaigns_collected_label', 'تم جمع'),
                    'goal_label' => $s('campaigns_goal_label', 'الهدف'),
                ]
            ];

            // Featured Campaign Config (Manually Managed Card)
            $featuredCampaignConfig = [
                'title' => $s('featured_campaign_title', 'حملة الشتاء'),
                'description' => $s('featured_campaign_desc'),
                'year' => $s('campaign_featured_year', '2024'),
                'beneficiaries' => $s('featured_campaign_beneficiaries', '2,500+'),
                'progress_percentage' => (float) $s('featured_campaign_progress', '65'),
                'progress_label' => $s('featured_campaign_progress_label', 'مرحلة التحضير'),
                'status' => $s('featured_campaign_status', 'active'),
                'collected' => (float) str_replace(',', '', $s('featured_campaign_collected', '0')),
                'goal' => (float) str_replace(',', '', $s('featured_campaign_goal', '0')),
                'days_left' => (int) $s('featured_campaign_days', '0'),
                'button_text' => $s('featured_campaign_button_text', 'ساهم الآن'),
                'button_option' => $s('featured_campaign_button_option', 'contribute'),
                'btn_color' => $s('featured_campaign_btn_color', '#0d6efd'),
                'primary_color' => $s('featured_campaign_primary_color', '#ffc107'),
                'tint_color' => $s('featured_campaign_tint_color', 'rgba(255, 193, 7, 0.1)'),
                'border_color' => $s('featured_campaign_border_color', 'rgba(255, 193, 7, 0.2)'),
                'icon_color' => $s('featured_campaign_icon_color', '#ffc107'),
                'share_price' => (float) $s('featured_campaign_share_price', '0'),
                'starts_days' => (int) $s('featured_campaign_starts_days', '0'),
                'starts_hours' => (int) $s('featured_campaign_starts_hours', '0'),
                'show_countdown' => (bool) $s('featured_campaign_show_countdown', '1'),
                'show_register_stats' => (bool) $s('featured_campaign_show_register_stats', '1'),
                'show_custom_text' => $s('featured_campaign_show_custom_text', '0'),
                'custom_text' => $s('featured_campaign_custom_text'),
                'image' => !empty($settings['featured_campaign_image'])
                ? $this->getStorageUrl($settings['featured_campaign_image'])
                : null,
                'icon_image' => !empty($settings['featured_campaign_icon_image'])
                ? $this->getStorageUrl($settings['featured_campaign_icon_image'])
                : null,
                'custom_icons' => [
                    [
                        'label' => $s('featured_campaign_icon1_label'),
                        'value' => $s('featured_campaign_icon1_value'),
                        'type' => !empty($settings['featured_campaign_icon1_image']) ? 'image' : 'icon',
                        'src' => !empty($settings['featured_campaign_icon1_image']) ? $this->getStorageUrl($settings['featured_campaign_icon1_image']) : $s('featured_campaign_icon1_class'),
                    ],
                    [
                        'label' => $s('featured_campaign_icon2_label'),
                        'value' => $s('featured_campaign_icon2_value'),
                        'type' => !empty($settings['featured_campaign_icon2_image']) ? 'image' : 'icon',
                        'src' => !empty($settings['featured_campaign_icon2_image']) ? $this->getStorageUrl($settings['featured_campaign_icon2_image']) : $s('featured_campaign_icon2_class'),
                    ]
                ],
                'register_fields' => [
                    'label1' => $s('featured_campaign_register_label', 'مرحلة التعديل'),
                    'label2' => $s('featured_campaign_register_label2'),
                    'value1' => $s('featured_campaign_register_value1', 'التحضير'),
                    'value2' => $s('featured_campaign_register_value2', 'جاري الإعداد'),
                ]
            ];

            // Headquarters Data
            $headquarters = [
                'title' => $s('headquarters_title', 'مقر مؤسسة إنسان الخيرية'),
                'description' => $s('headquarters_description', 'المقر الإداري الرئيسي للمؤسسة، يختص بإدارة كافة الأنشطة والمشاريع الخيرية.'),
                'address' => $s('headquarters_address', 'تقسيم 2 ش تونس المتفرع من ش مخزن الزيت العماره اللي جنب كازيون'),
                'phone' => $s('headquarters_phone', '01006090616'),
                'working_days' => $s('headquarters_working_days', $s('contact_working_days', 'السبت - الخميس')),
                'working_hours' => $s('headquarters_working_hours', $s('contact_working_hours', '9:00 ص - 5:00 م')),
                'features' => [
                    $s('headquarters_feature1', 'المقر الإداري'),
                    $s('headquarters_feature2', 'خدمة عملاء'),
                    $s('headquarters_feature3', 'مخازن رئيسية'),
                ],
                'labels' => [
                    'directions' => $s('headquarters_directions_label', 'الاتجاهات'),
                    'call' => $s('headquarters_call_label', 'اتصل الآن'),
                ],
                'stats' => [
                    // 'branches' => $s('headquarters_stats_branches', '4'),
                    // 'branches_label' => $s('headquarters_stats_branches_label', 'فرع رئيسي'),
                    'governorates' => $s('headquarters_stats_governorates', '45'),
                    'governorates_label' => $s('headquarters_stats_governorates_label', 'محافظة'),
                    'employees' => $s('headquarters_stats_employees', '+200'),
                    'employees_label' => $s('headquarters_stats_employees_label', 'موظف'),
                    'donors' => $s('headquarters_stats_donors', '+100K'),
                    'donors_label' => $s('headquarters_stats_donors_label', 'متبرع نشط'),
                ]
            ];

            // Sponsorship Programs
            $sponsorship_programs = [
                [
                    'title' => $s('sponsorship_prog1_title', 'مشروع بعثاء الأمل'),
                    'description' => $s('sponsorship_prog1_desc', 'اكفل شخصاً من ذوي الاحتياجات الخاصة أو طفلاً من أطفال السرطان'),
                    'feature1' => $s('sponsorship_prog1_feature1', 'دعم طبي ونفسي'),
                    'feature2' => $s('sponsorship_prog1_feature2', 'تأهيل وتدريب'),
                    'price' => $s('sponsorship_prog1_price', '300'),
                    'currency' => $s('sponsorship_prog1_currency', 'ج.م / شهر')
                ],
                [
                    'title' => $s('sponsorship_prog2_title', 'مشروع زاد'),
                    'description' => $s('sponsorship_prog2_desc', 'كفالة شاملة للأسر المحتاجة تشمل الغذاء والصحة والتعليم وفك الكرب'),
                    'feature1' => $s('sponsorship_prog2_feature1', 'كفالة شهرية'),
                    'feature2' => $s('sponsorship_prog2_feature2', 'تقارير دورية'),
                    'price' => $s('sponsorship_prog2_price', '500'),
                    'currency' => $s('sponsorship_prog2_currency', 'ج.م / شهر')
                ]
            ];


            // About
            $about = [
                'story_title' => $s('about_story_title', 'رحلة عطاء بدأت بفكرة'),
                'story_content' => $s('about_story_content'),
                'vision' => $s('about_vision'),
                'mission' => $s('about_mission'),
            ];

            // Content
            $projects = Project::latest()->take(3)->get()->map(fn($p) => $this->formatProject($p));
            $campaigns = Campaign::latest()->take(3)->get()->map(fn($c) => $this->formatCampaign($c, $settings));
            $testimonials = WebTestimonial::approved()->get();
            $features = WebFeature::orderBy('sort_order')->get();
            $boardMembers = WebBoardMember::orderBy('sort_order')->get()->map(function($m) {
                return [
                    'id' => $m->id,
                    'name' => $m->name,
                    'role' => $m->role,
                    'description' => $m->description,
                    'image' => $m->image_path ? $m->getFileUrl('image_path') : null,
                ];
            });
            $sectors = WebSector::all();
            $partners = WebPartner::all();
            $branches = WebBranch::all();
            $volunteerWall = WebVolunteerWall::orderBy('rank')->get();
            $faqs = WebFaq::orderBy('sort_order')->take(4)->get();
            $news = WebNews::latest()->take(3)->get()->map(function($n) {
                return [
                    'id' => $n->id,
                    'title' => $n->title,
                    'content' => $n->content,
                    'summary' => \Str::limit(strip_tags($n->content), 200),
                    'image' => $n->image_path ? $n->getFileUrl('image_path') : null,
                    'image_path' => $n->image_path,
                    'category' => $n->category,
                    'views' => $n->views_count ?? 0,
                    'shares' => $n->shares_count ?? 0,
                    'statistic_number' => $n->statistic_number,
                    'statistic_description' => $n->statistic_description,
                    'published_at' => $n->published_at ? $n->published_at->format('Y-m-d') : $n->created_at->format('Y-m-d'),
                ];
            });
            $events = WebEvent::where('is_featured', true)->orderByDesc('event_date')->take(3)->get()
                ->map(fn($e) => $this->formatEvent($e));

            // Guest House Content
            $ghPage = \App\Models\WebPage::where('slug', 'guest-house')->first();
            $guestHouseData = [
                'title' => $s('gh_home_title', $ghPage->title ?? 'ضيافة إنسان'),
                'content' => $s('gh_home_content', $ghPage->content ?? ''),
                'image' => !empty($settings['gh_home_image']) ? $this->getStorageUrl($settings['gh_home_image']) : (($ghPage && $ghPage->image_path) ? $ghPage->getFileUrl('image_path') : null),
                'link' => '/guest-house'
            ];

            // Bottom CTA
            $bottomCta = [
                'title' => $s('cta_title', 'كن جزءاً من قصة نجاح وصلاح'),
                'text' => $s('cta_text', 'كل مساهمة منك تصنع فارقاً في حياة إنسان الخيري .'),
                'phone_number' => $s('contact_phone_link', '01006090616'),
                'phone_link' => 'tel:' . str_replace(' ', '', $s('contact_phone_link', '01006090616')),

                'stats' => [
                    [
                        'value' => (string) $s('cta_stat1_value', '50M+'),
                        'label' => (string) $s('cta_stat1_label', 'تبرعات'),
                        'icon' => 'bi-heart-fill'
                    ],
                    [
                        'value' => (string) $s('cta_stat2_value', '150K+'),
                        'label' => (string) $s('cta_stat2_label', 'ابتسامة'),
                        'icon' => 'bi-emoji-smile-fill'
                    ],
                    [
                        'value' => (string) $s('cta_stat3_value', '8+'),
                        'label' => (string) $s('cta_stat3_label', 'سنوات'),
                        'icon' => 'bi-calendar-check-fill'
                    ],
                ],
                // Keep old keys for backward compatibility
                'stat1_value' => (string) $s('cta_stat1_value', '50M+'),
                'stat1_label' => (string) $s('cta_stat1_label', 'تبرعات'),
                'stat2_value' => (string) $s('cta_stat2_value', '150K+'),
                'stat2_label' => (string) $s('cta_stat2_label', 'ابتسامة'),
                'stat3_value' => (string) $s('cta_stat3_value', '8+'),
                'stat3_label' => (string) $s('cta_stat3_label', 'سنوات'),
            ];

            // Prepare contact channels
            $contactChannels = [
                'phone' => [
                    'value' => $s('contact_phone_link', '01006090616'),
                ],
                'email' => [
                    'value' => $s('contact_email_link', 'info@ensan.org'),
                ],
                'whatsapp' => [
                    'value' => $s('contact_whatsapp_link', '+20 100 609 0616'),
                ],
            ];





            return [
            'hero' => $hero,
            'social_links' => [
                'facebook' => $s('social_facebook'),
                'twitter' => $s('social_twitter'),
                'x' => $s('social_twitter'),
                'instagram' => $s('social_instagram'),
                'youtube' => $s('social_youtube'),
                'tiktok' => $s('social_tiktok'),
                'whatsapp' => $s('contact_whatsapp_link', '+20 100 609 0616'),
            ],
            'stats' => $stats,
            'partners_stats' => [
            'donors' => $s('partners_stats_donors', '+5000'),
            'donors_label' => $s('partners_stats_donors_label', 'متبرع'),
            'volunteers' => $s('partners_stats_volunteers', '+1000'),
            'volunteers_label' => $s('partners_stats_volunteers_label', 'متطوع'),
            'institutions' => $s('partners_stats_institutions', '+10'),
            'institutions_label' => $s('partners_stats_institutions_label', 'مؤسسة شريكة'),
            'campaigns' => $s('partners_stats_campaigns', '+100'),
            'campaigns_label' => $s('partners_stats_campaigns_label', 'حملة مكتملة'),
            ],
            'gh_stats' => $guestHouseStats,
            'gallery' => $gallery,
            'about' => $about,
            'features' => $features,
            'board_members' => [
                'title' => $s('board_title', 'مجلس الأمناء'),
                'subtitle' => $s('board_subtitle', 'فريقنا القيادي والمتميز'),
                'members' => $boardMembers
            ],
            'sectors' => $sectors,
            'partners' => $partners,
            'testimonials' => $testimonials,
            'branches' => $branches,
            'volunteer_wall' => $volunteerWall,
            'featured_projects' => $projects,
            'featured_campaigns' => $campaigns,
            'featured_campaign_card' => $featuredCampaignConfig,
            'featured_news' => $news,
            'featured_events' => $events,
            'headquarters' => $headquarters,
            'sponsorship_programs' => $sponsorship_programs,
            'guest_house_content' => $guestHouseData,
            'bottom_cta' => $bottomCta,
            'campaigns_settings' => $campaignsSettings,
            'faqs' => $faqs,
            'news_slider' => $newsSlider,
            'donation_page' => $this->donationSettings->getSettings(),
            'ideal_partner' => [
                'title' => $s('ideal_partner_title', 'الشريك الأمثل لتبرعاتك'),
                'items' => [
                    [
                        'value' => $s('ideal_partner_item1_value', '100%'),
                        'label' => $s('ideal_partner_item1_label', 'شفافية'),
                        'icon' => 'bi-shield-check'
                    ],
                    [
                        'value' => $s('ideal_partner_item2_value', '200+'),
                        'label' => $s('ideal_partner_item2_label', 'موظف'),
                        'icon' => 'bi-people'
                    ],
                    [
                        'value' => $s('ideal_partner_item3_value', '200'),
                        'label' => $s('ideal_partner_item3_label', 'قرية'),
                        'icon' => 'bi-geo-alt'
                    ],
                    [
                        'value' => $s('ideal_partner_item4_value', 'معتمدة'),
                        'label' => $s('ideal_partner_item4_label', 'مؤسسة'),
                        'icon' => 'bi-patch-check'
                    ],
                ]
            ],
            'contact_channels' => $contactChannels,
            'projects_slider' => array_values(array_filter(array_map(fn($i) => !empty($settings["project_slider_$i"]) ? $this->getStorageUrl($settings["project_slider_$i"]) : null, range(1, 10)))),
            'campaigns_slider' => array_values(array_filter(array_map(fn($i) => !empty($settings["campaign_slider_$i"]) ? $this->getStorageUrl($settings["campaign_slider_$i"]) : null, range(1, 10)))),
            'honor_wall_slider' => array_values(array_filter(array_map(fn($i) => !empty($settings["honor_wall_slider_$i"]) ? $this->getStorageUrl($settings["honor_wall_slider_$i"]) : null, range(1, 10)))),
            'field_images' => array_values(array_filter(array_map(fn($i) => !empty($settings["field_image_$i"]) ? $this->getStorageUrl($settings["field_image_$i"]) : null, range(1, 4)))),
            'beneficiary_evaluations' => [
                'positive_reviews' => [
                    'value' => $s('eval_positive_reviews_value', '+500'),
                    'label' => $s('eval_positive_reviews_label', 'تقييم إيجابي'),
                    'icon' => $s('eval_positive_reviews_icon', 'bi-hand-thumbs-up-fill')
                ],
                'average_rating' => [
                    'value' => $s('eval_average_rating_value', '4.8'),
                    'label' => $s('eval_average_rating_label', 'متوسط التقييم'),
                    'icon' => $s('eval_average_rating_icon', 'bi-star-fill')
                ],
                'satisfaction_rate' => [
                    'value' => $s('eval_satisfaction_rate_value', '98%'),
                    'label' => $s('eval_satisfaction_rate_label', 'نسبة الرضا'),
                    'icon' => $s('eval_satisfaction_rate_icon', 'bi-pie-chart-fill')
                ],
                'total_beneficiaries' => [
                    'value' => $s('eval_total_beneficiaries_value', '200K+'),
                    'label' => $s('eval_total_beneficiaries_label', 'مستفيد'),
                    'icon' => $s('eval_total_beneficiaries_icon', 'bi-people-fill')
                ],
            ],
            'raw_settings' => $settings
            ];
        });

        // Fresh Notification Data (Always bypasses cache for instant updates)
        $settings = WebSetting::all()->pluck('value', 'key');
        $data['notification'] = [
            'is_active' => ($settings['notification_active'] ?? 'off') === 'on',
            'label' => $settings['notification_label'] ?? 'عاجل',
            'text' => $settings['notification_text'] ?? '',
            'link_text' => $settings['notification_link_text'] ?? '',
            'link_url' => $settings['notification_link_url'] ?? '',
        ];

        return response()->json($data);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/website/projects",
     *     tags={"Website - Projects"},
     *     summary="List website projects",
     *     @OA\Response(
     *         response=200,
     *         description="List of projects formatted for website"
     *     )
     * )
     */
    public function projects()
    {
        $settings = WebSetting::all()->pluck('value', 'key')->toArray();
        $s = fn($key, $default = '') => (!empty($settings[$key])) ? $settings[$key] : $default;

        $projects = Project::all()->map(function ($p) {
            return $this->formatProject($p);
        });

        $slider_images = [];
        for ($i = 1; $i <= 10; $i++) {
            $key = "project_slider_$i";
            if (!empty($settings[$key])) {
                $slider_images[] = $this->getStorageUrl($settings[$key]);
            }
        }

        return response()->json([
            'page_title' => $s('projects_page_title', 'مشاريعنا الخيرية'),
            'page_description' => $s('projects_page_description', 'تغطية واسعة في محافظات الدلتا وعلى مستوى الجمهورية منذ أكثر من 8 سنوات'),
            'achievements' => [
                'donations' => $s('stats_donations', '13M+'),
                'donations_label' => $s('stats_donations_label', 'التبرعات الخيريه'),
                'projects' => $s('stats_projects', '400K'),
                'projects_count' => $s('stats_projects', '400K'),
                'projects_label' => $s('stats_projects_label', 'المشاريع'),
                'beneficiaries' => $s('stats_beneficiaries', '300K'),
                'beneficiaries_label' => $s('stats_beneficiaries_label', 'المستفيدون'),
                'governorates' => $s('stats_governorates', '45'),
                'governorates_label' => $s('stats_governorates_label', 'المحافظات'),
                'branches' => $s('stats_branches', '4'),
                'volunteers' => $s('stats_volunteers', '1000+'),
            ],
            'stats' => [ // Alias for landing page consistency
                'donations' => $s('stats_donations', '13M+'),
                'donations_label' => $s('stats_donations_label', 'جنيه تبرعات'),
                'projects' => $s('stats_projects', '4'),
                'projects_count' => $s('stats_projects', '4'),
                'beneficiaries' => $s('stats_beneficiaries', '300K'),
                'beneficiaries_label' => $s('stats_beneficiaries_label', 'المستفيدون'),
                'governorates' => $s('stats_governorates', '2'),
                'governorates_label' => $s('stats_governorates_label', 'محافظة'),
            ],

            'sponsorship_programs' => [
                [
                    'id' => 'baatha',
                    'title' => $s('sponsorship_prog1_title', 'مشروع بعثاء الأمل'),
                    'description' => $s('sponsorship_prog1_desc', 'اكفل شخصاً من ذوي الاحتياجات الخاصة أو طفلاً من أطفال السرطان'),
                    'features' => [
                        ['label' => $s('sponsorship_prog1_feature1', 'دعم طبي ونفسي')],
                        ['label' => $s('sponsorship_prog1_feature2', 'تأهيل وتدريب')],
                    ],
                    'price' => $s('sponsorship_prog1_price', '300'),
                    'currency' => $s('sponsorship_prog1_currency', 'ج.م / شهر'),
                ],
                [
                    'id' => 'zad',
                    'title' => $s('sponsorship_prog2_title', 'مشروع زاد'),
                    'description' => $s('sponsorship_prog2_desc', 'كفالة شاملة للأسر المحتاجة تشمل الغذاء والصحة والتعليم وفك الكرب'),
                    'features' => [
                        ['label' => $s('sponsorship_prog2_feature1', 'كفالة شهرية')],
                        ['label' => $s('sponsorship_prog2_feature2', 'تقارير دورية')],
                    ],
                    'price' => $s('sponsorship_prog2_price', '500'),
                    'currency' => $s('sponsorship_prog2_currency', 'ج.م / شهر'),
                ]
            ],

            'slider_images' => $slider_images,
            'projects' => $projects
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/website/projects/{project}",
     *     tags={"Website - Projects"},
     *     summary="Show single website project",
     *     @OA\Parameter(
     *         name="project",
     *         in="path",
     *         required=true,
     *         description="Project ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Project details formatted for website"
     *     )
     * )
     */
    public function projectShow(Project $project)
    {
        return response()->json($this->formatProject($project));
    }

    /**
     * @OA\Get(
     *     path="/api/v1/website/campaigns",
     *     tags={"Website - Campaigns"},
     *     summary="List website campaigns",
     *     @OA\Response(
     *         response=200,
     *         description="List of campaigns formatted for website"
     *     )
     * )
     */
    public function campaigns()
    {
        $settings = WebSetting::all()->pluck('value', 'key');
        $campaigns = Campaign::all()->map(function ($c) use ($settings) {
            return $this->formatCampaign($c, $settings);
        });
        return response()->json($campaigns);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/website/campaigns/{campaign}",
     *     tags={"Website - Campaigns"},
     *     summary="Show single website campaign",
     *     @OA\Parameter(
     *         name="campaign",
     *         in="path",
     *         required=true,
     *         description="Campaign ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Campaign details formatted for website"
     *     )
     * )
     */
    public function campaignShow(Campaign $campaign)
    {
        return response()->json($this->formatCampaign($campaign));
    }

    public function getSettings()
    {
        $settings = WebSetting::all()->pluck('value', 'key');
        return response()->json($settings);
    }

    public function breakingNews()
    {
        $settings = WebSetting::all()->pluck('value', 'key');
        return response()->json([
            'is_active' => ($settings['notification_active'] ?? 'off') === 'on',
            'label' => $settings['notification_label'] ?? 'عاجل',
            'text' => $settings['notification_text'] ?? '',
            'link_text' => $settings['notification_link_text'] ?? '',
            'link_url' => $settings['notification_link_url'] ?? '',
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/website/stats",
     *     tags={"Website"},
     *     summary="Get website statistics blocks",
     *     @OA\Response(
     *         response=200,
     *         description="Statistics for achievements, projects, campaigns and guest house"
     *     )
     * )
     */
    public function getStats()
    {
        $settings = WebSetting::all()->pluck('value', 'key')->toArray();
        $s = fn($key, $default = '') => (!empty($settings[$key])) ? $settings[$key] : $default;

        return response()->json([
            'achievements' => [
                'donations' => $s('stats_donations', '13M+'),
                'donations_label' => $s('stats_donations_label', 'التبرعات الخيريه'),
                'projects' => $s('stats_projects', '400K'),
                'projects_label' => $s('stats_projects_label', 'المشاريع'),
                'governorates' => $s('stats_governorates', '45'),
                'governorates_label' => $s('stats_governorates_label', 'المحافظات'),
                'beneficiaries' => $s('stats_beneficiaries', '300K'),
                'beneficiaries_label' => $s('stats_beneficiaries_label', 'المستفيدون'),
                'volunteers' => $s('stats_volunteers', '1000+'),
                'volunteers_label' => $s('stats_volunteers_label', 'متطوع نشط'),
            ],
            'categories' => [
                'all' => $s('cat_all_title', 'الكل'),
                'zakat' => $s('cat_zakat_title', 'الزكاة والصدقات'),
                'sponsorship' => $s('cat_sponsorship_title', 'الكفالات'),
                'health' => $s('cat_health_title', 'الصحة'),
                'housing' => $s('cat_housing_title', 'الإسكان'),
            ],
            'projects' => [
                'donations' => $s('stats_donations', '13M+'),
                'donations_label' => $s('stats_donations_label', 'التبرعات الخيريه'),
                'projects' => $s('stats_projects', '400K'),
                'projects_count' => $s('stats_projects', '400K'),
                'projects_label' => $s('stats_projects_label', 'المستفيدون'),
                'governorates' => $s('stats_governorates', '45'),
                'governorates_label' => $s('stats_governorates_label', 'المحافظات'),
                'beneficiaries' => $s('stats_beneficiaries', '300K'),
                'beneficiaries_label' => $s('stats_beneficiaries_label', 'المستفيدون'),
                'branches' => $s('stats_branches', '4'),
                'volunteers' => $s('stats_volunteers', '1000+'),
            ],
            'campaigns' => [
                'beneficiaries' => $s('campaign_stats_beneficiaries', '100K+'),
                'active_campaigns' => $s('campaign_stats_active', '5+'),
                'donations' => $s('campaign_stats_donations', '1M+'),
                'governorates' => $s('campaign_stats_governorates', '2+'),
                'filters' => [
                    'all' => $s('campaign_filter_all', 'الكل'),
                    'active' => $s('campaign_filter_active', 'نشطة الآن'),
                    'upcoming' => $s('campaign_filter_upcoming', 'قادمة'),
                    'ended' => $s('campaign_filter_ended', 'منتهية'),
                ],
                'ui_labels' => [
                    'contribute_btn' => $s('campaign_ui_contribute_btn', 'ساهم الآن'),
                    'ended_btn' => $s('campaign_ui_ended_btn', 'انتهت الحملة'),
                    'remind_btn' => $s('campaign_ui_remind_btn', 'ذكرني عند البدء'),
                    'share_label' => $s('campaign_ui_share_label', 'السهم'),
                    'collected_label' => $s('campaign_ui_collected_label', 'تم جمع'),
                    'goal_label' => $s('campaign_ui_goal_label', 'الهدف'),
                    'benefited_label' => $s('campaign_ui_benefited_label', 'استفاد'),
                    'starts_title' => $s('campaign_ui_starts_title', 'تبدأ بعد'),
                ],
                'slider_images' => collect(range(1, 10))->map(function ($i) use ($settings) {
                    $key = "campaign_slider_$i";
                    return isset($settings[$key]) ? $this->getStorageUrl($settings[$key]) : null;
                })->filter()->values(),
                'featured_card' => [
                    'title' => $s('featured_campaign_title', 'حملة الشتاء'),
                    'desc' => $s('featured_campaign_desc', 'نعمل على توفير الدفء والكساء للأسر الأكثر احتياجاً في المناطق الجبلية والنائية.'),
                    'image' => !empty($settings['featured_campaign_image']) ? $this->getStorageUrl($settings['featured_campaign_image']) : null,
                    'icon' => !empty($settings['featured_campaign_icon_image']) ? $this->getStorageUrl($settings['featured_campaign_icon_image']) : null,
                    'btn_color' => $settings['featured_campaign_btn_color'] ?? '#ffc107',
                    'primary_color' => $settings['featured_campaign_primary_color'] ?? '#ffc107',
                    'tint_color' => $settings['featured_campaign_tint_color'] ?? 'rgba(255, 193, 7, 0.1)',
                    'border_color' => $settings['featured_campaign_border_color'] ?? 'rgba(255, 193, 7, 0.2)',
                    'icon_color' => $settings['featured_campaign_icon_color'] ?? '#ffc107',
                    'button_text' => $settings['featured_campaign_button_text'] ?? 'ساهم الآن',
                    'share_price' => (float) str_replace(',', '', $settings['featured_campaign_share_price'] ?? '0'),
                    'show_countdown' => ($settings['featured_campaign_show_countdown'] ?? '0') == '1',
                    'starts_days' => (int) str_replace(',', '', $settings['featured_campaign_starts_days'] ?? '0'),
                    'starts_hours' => (int) str_replace(',', '', $settings['featured_campaign_starts_hours'] ?? '0'),
                    'progress_label' => $settings['featured_campaign_progress_label'] ?? 'مرحلة التحضير',
                    'show_custom_text' => ($settings['featured_campaign_show_custom_text'] ?? '0') == '1',
                    'custom_text' => $settings['featured_campaign_custom_text'] ?? null,
                    'custom_icons' => [
                        [
                            'label' => $settings['featured_campaign_icon1_label'] ?? null,
                            'value' => $settings['featured_campaign_icon1_value'] ?? null,
                            'type' => !empty($settings['featured_campaign_icon1_image']) ? 'image' : 'icon',
                            'src' => !empty($settings['featured_campaign_icon1_image']) ? $this->getStorageUrl($settings['featured_campaign_icon1_image']) : ($settings['featured_campaign_icon1_class'] ?? 'bi-star'),
                        ],
                        [
                            'label' => $settings['featured_campaign_icon2_label'] ?? null,
                            'value' => $settings['featured_campaign_icon2_value'] ?? null,
                            'type' => !empty($settings['featured_campaign_icon2_image']) ? 'image' : 'icon',
                            'src' => !empty($settings['featured_campaign_icon2_image']) ? $this->getStorageUrl($settings['featured_campaign_icon2_image']) : ($settings['featured_campaign_icon2_class'] ?? 'bi-heart'),
                        ]
                    ],
                ],
                'featured_banner' => [
                    'title' => $settings['featured_campaign_title'] ?? 'حملة الشتاء 2024',
                    'beneficiaries' => $settings['featured_campaign_beneficiaries'] ?? '2,500+',
                    'progress' => $settings['featured_campaign_progress'] ?? '65',
                    'button_text' => $settings['featured_campaign_button_text'] ?? 'ساهم الآن',
                    'icon' => isset($settings['featured_campaign_icon']) ? $this->getStorageUrl($settings['featured_campaign_icon']) : null,
                    'status' => $settings['featured_campaign_status'] ?? 'نشطة الآن',
                ],
            ],
            'guest_house' => [
                'patients_yearly' => $settings['gh_stat2_value'] ?? $settings['gh_stats_patients'] ?? '3000+',
                'beds_count' => $settings['gh_stat1_value'] ?? $settings['gh_stats_beds'] ?? '50+',
                'reception' => $settings['gh_stat4_value'] ?? $settings['gh_stats_reception'] ?? '24/7',
                'branches' => $settings['gh_stat3_value'] ?? $settings['gh_stats_branches'] ?? '2',
                'labels' => [
                    'patients' => $settings['gh_stat2_label'] ?? 'مريض سنوياً',
                    'beds' => $settings['gh_stat1_label'] ?? 'سرير',
                    'reception' => $settings['gh_stat4_label'] ?? 'استقبال',
                    'branches' => $settings['gh_stat3_label'] ?? 'فرع',
                ]
            ],

            'coverage' => [],
            'beneficiary_evaluations' => [
                'positive_reviews' => [
                    'value' => $s('eval_positive_reviews_value', '+500'),
                    'label' => $s('eval_positive_reviews_label', 'تقييم إيجابي'),
                    'icon' => $s('eval_positive_reviews_icon', 'bi-hand-thumbs-up-fill')
                ],
                'average_rating' => [
                    'value' => $s('eval_average_rating_value', '4.8'),
                    'label' => $s('eval_average_rating_label', 'متوسط التقييم'),
                    'icon' => $s('eval_average_rating_icon', 'bi-star-fill')
                ],
                'satisfaction_rate' => [
                    'value' => $s('eval_satisfaction_rate_value', '98%'),
                    'label' => $s('eval_satisfaction_rate_label', 'نسبة الرضا'),
                    'icon' => $s('eval_satisfaction_rate_icon', 'bi-pie-chart-fill')
                ],
                'total_beneficiaries' => [
                    'value' => $s('eval_total_beneficiaries_value', '+10K'),
                    'label' => $s('eval_total_beneficiaries_label', 'مستفيد'),
                    'icon' => $s('eval_total_beneficiaries_icon', 'bi-people-fill')
                ],
            ]
        ]);
    }

    public function shareOpinion()
    {
        $settings = WebSetting::all()->pluck('value', 'key')->toArray();
        $s = fn($key, $default = '') => (!empty($settings[$key])) ? $settings[$key] : $default;

        $testimonials = WebTestimonial::where('status', 'approved')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($t) {
                return [
                    'id' => $t->id,
                    'name' => $t->name,
                    'role' => $t->role,
                    'content' => $t->content,
                    'rating' => $t->rating,
                    'image' => $t->image_path ? $t->getFileUrl('image_path') : null,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => [
                'beneficiary_evaluations' => [
                    'positive_reviews' => [
                        'value' => $s('eval_positive_reviews_value', '+500'),
                        'label' => $s('eval_positive_reviews_label', 'تقييم إيجابي'),
                        'icon' => $s('eval_positive_reviews_icon', 'bi-hand-thumbs-up-fill')
                    ],
                    'average_rating' => [
                        'value' => $s('eval_average_rating_value', '4.8'),
                        'label' => $s('eval_average_rating_label', 'متوسط التقييم'),
                        'icon' => $s('eval_average_rating_icon', 'bi-star-fill')
                    ],
                    'satisfaction_rate' => [
                        'value' => $s('eval_satisfaction_rate_value', '98%'),
                        'label' => $s('eval_satisfaction_rate_label', 'نسبة الرضا'),
                        'icon' => $s('eval_satisfaction_rate_icon', 'bi-pie-chart-fill')
                    ],
                    'total_beneficiaries' => [
                        'value' => $s('eval_total_beneficiaries_value', '+10K'),
                        'label' => $s('eval_total_beneficiaries_label', 'مستفيد'),
                        'icon' => $s('eval_total_beneficiaries_icon', 'bi-people-fill')
                    ],
                ],
                'testimonials' => $testimonials
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/website/volunteer",
     *     tags={"Website - Volunteer"},
     *     summary="Get volunteer page content",
     *     @OA\Response(
     *         response=200,
     *         description="Volunteer hero, stats and wall data"
     *     )
     * )
     */
    public function volunteerPage()
    {
        $settings = WebSetting::all()->pluck('value', 'key')->toArray();
        $s = fn($key, $default = '') => (!empty($settings[$key])) ? $settings[$key] : $default;

        $wall = \App\Models\WebVolunteerWall::orderBy('rank', 'desc')->get()->map(function ($v) {
            return [
            'name' => $v->name,
            'hours' => $v->hours,
            'rank' => $v->rank,
            'image' => $v->image_path ? $v->getFileUrl('image_path') : null,
            ];
        });

        $slider = [];
        for ($i = 1; $i <= 10; $i++) {
            if (!empty($settings["volunteer_slider_$i"])) {
                $slider[] = $this->getStorageUrl($settings["volunteer_slider_$i"]);
            }
        }

        $content = [
            'hero' => [
                'title' => $s('volunteer_title', 'تطوع معنا'),
                'subtitle' => $s('volunteer_subtitle', 'كن جزءاً من عائلة إنسان وساهم في التغيير'),
                'description' => $s('volunteer_description'),
                'image' => !empty($settings['volunteer_hero_image']) ? $this->getStorageUrl($settings['volunteer_hero_image']) : null,
            ],
            'slider' => $slider,
            'stats' => [
                'volunteers' => $s('volunteer_stats_volunteers', '500+'),
                'hours' => $s('volunteer_stats_hours', '10,000+'),
                'branches' => $s('volunteer_stats_branches', '2'),
                'projects' => $s('volunteer_stats_projects', '50+'),
            ],
            'success_partners_stats' => [
                'donors' => $s('partners_stats_donors', '+5000'),
                'donors_label' => $s('partners_stats_donors_label', 'متبرع'),
                'volunteers' => $s('partners_stats_volunteers', '+1000'),
                'volunteers_label' => $s('partners_stats_volunteers_label', 'متطوع'),
                'institutions' => $s('partners_stats_institutions', '+10'),
                'institutions_label' => $s('partners_stats_institutions_label', 'مؤسسة شريكة'),
                'completed_campaigns' => $s('partners_stats_campaigns', '+100'),
                'completed_campaigns_label' => $s('partners_stats_campaigns_label', 'حملة مكتملة'),
            ],
            'wall' => $wall
        ];

        return response()->json($content);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/website/guest-house",
     *     tags={"Website - Guest House"},
     *     summary="Get guest house page content",
     *     @OA\Response(
     *         response=200,
     *         description="Guest house hero, stats and slider"
     *     )
     * )
     */
    public function guestHousePage()
    {
        $settings = \App\Models\WebSetting::all()->pluck('value', 'key')->toArray();
        $s = fn($key, $default = '') => (!empty($settings[$key])) ? $settings[$key] : $default;
        $page = \App\Models\WebPage::where('slug', 'guest-house')->first();

        $slider = [];
        for ($i = 1; $i <= 10; $i++) {
            if (!empty($settings["gh_slider_$i"])) {
                $slider[] = $this->getStorageUrl($settings["gh_slider_$i"]);
            }
        }

        return response()->json([
            'title' => $page->title ?? 'دار الضيافة',
            'hero_subtitle' => $s('gh_hero_subtitle', 'ملاذ آمن للمرضى ومرافقيهم'),
            'hero_image' => ($page && $page->image_path) ? $page->getFileUrl('image_path') : null,
            'stats' => [
                'beds' => $s('gh_stats_beds', $s('gh_stat1_value', '50+')),
                'beds_label' => $s('gh_stats_beds_label', $s('gh_stat1_label', 'سرير')),
                'patients' => $s('gh_stats_patients', $s('gh_stat2_value', '3000+')),
                'patients_label' => $s('gh_stats_patients_label', $s('gh_stat2_label', 'مريض سنوياً')),
                'branches' => $s('gh_stats_branches', $s('gh_stat3_value', '2')),
                'branches_label' => $s('gh_stats_branches_label', $s('gh_stat3_label', 'فرع')),
                'reception' => $s('gh_stats_reception', $s('gh_stat4_value', '24/7')),
                'reception_label' => $s('gh_stats_reception_label', $s('gh_stat4_label', 'استقبال')),
            ],
            'slider' => $slider,
            'meta' => [
                'title' => $page->meta_title ?? '',
                'description' => $page->meta_description ?? '',
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/website/board-members",
     *     tags={"Website"},
     *     summary="Get board members for website",
     *     @OA\Response(
     *         response=200,
     *         description="List of board members"
     *     )
     * )
     */
    public function boardMembers()
    {
        $members = WebBoardMember::orderBy('sort_order')->get()->map(function ($m) {
            return [
            'id' => $m->id,
            'name' => $m->name,
            'role' => $m->role,
            'description' => $m->description,
            'image' => $m->image_path ? $m->getFileUrl('image_path') : null,
            ];
        });
        return response()->json($members);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/website/features",
     *     tags={"Website"},
     *     summary="Get website features",
     *     @OA\Response(
     *         response=200,
     *         description="List of website features"
     *     )
     * )
     */
    public function features()
    {
        $features = WebFeature::orderBy('sort_order')->get()->map(function ($f) {
            return [
            'id' => $f->id,
            'title' => $f->title,
            'content' => $f->content,
            'icon' => $f->icon,
            'sort_order' => $f->sort_order,
            ];
        });
        return response()->json($features);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/website/sectors",
     *     tags={"Website"},
     *     summary="Get website sectors",
     *     @OA\Response(
     *         response=200,
     *         description="List of website sectors"
     *     )
     * )
     */
    public function sectors()
    {
        $sectors = WebSector::all()->map(function ($s) {
            return [
            'id' => $s->id,
            'title' => $s->title,
            'icon' => $s->icon,
            'description' => $s->description,
            ];
        });
        return response()->json($sectors);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/website/branches",
     *     tags={"Website"},
     *     summary="Get website branches",
     *     @OA\Response(
     *         response=200,
     *         description="List of website branches"
     *     )
     * )
     */
    public function coverage()
    {
        $settings = \App\Models\WebSetting::all()->pluck('value', 'key');
        return response()->json([
            [
                'value' => $settings['headquarters_stats_branches'] ?? '1',
                'label' => $settings['headquarters_stats_branches_label'] ?? 'فرع رئيسي',
                'icon' => 'bi-geo-alt'
            ],
            [
                'value' => $settings['headquarters_stats_governorates'] ?? '3',
                'label' => $settings['headquarters_stats_governorates_label'] ?? 'محافظات',
                'icon' => 'bi-map'
            ],
            [
                'value' => $settings['headquarters_stats_employees'] ?? '+200',
                'label' => $settings['headquarters_stats_employees_label'] ?? 'موظف',
                'icon' => 'bi-people'
            ],
            [
                'value' => $settings['headquarters_stats_donors'] ?? '+10K',
                'label' => $settings['headquarters_stats_donors_label'] ?? 'متبرع',
                'icon' => 'bi-heart'
            ],
            [
                'value' => $settings['coverage_news_count'] ?? '1',
                'label' => $settings['coverage_news_label'] ?? 'خبر',
                'icon' => 'bi-newspaper'
            ],
            [
                'value' => $settings['coverage_views_count'] ?? '3',
                'label' => $settings['coverage_views_label'] ?? 'مشاهدة',
                'icon' => 'bi-tv'
            ],
        ]);
    }

    public function branches()
    {
        $settings = \App\Models\WebSetting::all()->pluck('value', 'key');

        $coverage_stats = [
            [
                'value' => $settings['headquarters_stats_branches'] ?? '1',
                'label' => $settings['headquarters_stats_branches_label'] ?? 'فرع رئيسي',
            ],
            [
                'value' => $settings['headquarters_stats_governorates'] ?? '2',
                'label' => $settings['headquarters_stats_governorates_label'] ?? 'محافظات',
            ],
            [
                'value' => $settings['headquarters_stats_employees'] ?? '+200',
                'label' => $settings['headquarters_stats_employees_label'] ?? 'موظف',
            ],
            [
                'value' => $settings['headquarters_stats_donors'] ?? '+10K',
                'label' => $settings['headquarters_stats_donors_label'] ?? 'متبرع',
            ],
        ];

        $branches = WebBranch::all()->map(function ($b) {
            return [
            'id' => $b->id,
            'name' => $b->name,
            'address' => $b->address,
            'phone' => $b->phone,
            'working_hours' => $b->working_hours,
            'email' => $b->email,
            'google_maps_url' => $b->google_maps_url,
            'is_main' => (bool)$b->is_main,
            'status_text' => $b->status_text,
            'description' => $b->description,
            ];
        });

        $headquarters = [
            'title' => $settings['headquarters_title'] ?? 'مقر مؤسسة إنسان الخيرية',
            'description' => $settings['headquarters_description'] ?? 'المقر الإداري الرئيسي للمؤسسة، يختص بإدارة كافة الأنشطة والمشاريع الخيرية.',
            'address' => $settings['headquarters_address'] ?? 'تقسيم 2 ش تونس المتفرع من ش مخزن الزيت العماره اللي جنب كازيون',
            'phone' => $settings['headquarters_phone'] ?? '01006090616',
            'working_hours' => $settings['headquarters_working_hours'] ?? 'يومياً من 9 صباحاً إلى 5 مساءً',
            'features' => [
                $settings['headquarters_feature1'] ?? 'المقر الإداري',
                $settings['headquarters_feature2'] ?? 'خدمة عملاء',
                $settings['headquarters_feature3'] ?? 'مخازن رئيسية',
            ],
            'labels' => [
                'directions' => $settings['headquarters_directions_label'] ?? 'الاتجاهات',
                'call' => $settings['headquarters_call_label'] ?? 'اتصل الآن',
            ]
        ];

        return response()->json([
            'headquarters' => $headquarters,
            'coverage_stats' => $coverage_stats,
            'branches' => $branches
        ]);
    }

    public function partners()
    {
        $partners = WebPartner::all()->map(function ($p) {
            return [
            'id' => $p->id,
            'name' => $p->name,
            'logo' => $p->logo_path ? $p->getFileUrl('logo_path') : null,
            'description' => $p->description,
            'type' => $p->type,
            'website' => $p->website_url,
            ];
        });
        return response()->json($partners);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/website/news",
     *     tags={"Website"},
     *     summary="Get website news",
     *     @OA\Response(
     *         response=200,
     *         description="List of news items"
     *     )
     * )
     */
    public function news()
    {
        $news = WebNews::orderByDesc('published_at')->get()->map(function ($n) {
            return [
                'id' => $n->id,
                'title' => $n->title,
                'content' => $n->content,
                'summary' => \Str::limit(strip_tags($n->content), 200),
                'image' => $n->image_path ? $n->getFileUrl('image_path') : null,
                'image_path' => $n->image_path,
                'category' => $n->category,
                'contact_name' => $n->contact_name,
                'contact_number' => $n->contact_number,
                'views_count' => $n->views_count ?? 0,
                'views' => $n->views_count ?? 0,
                'shares_count' => $n->shares_count ?? 0,
                'shares' => $n->shares_count ?? 0,
                'statistic_number' => $n->statistic_number,
                'statistic_description' => $n->statistic_description,
                'date' => $n->published_at
                    ? $n->published_at->format('Y-m-d')
                    : $n->created_at->format('Y-m-d'),
                'published_at' => $n->published_at,
                'created_at' => $n->created_at,
            ];
        });
        return response()->json($news);
    }

    public function newsShow(WebNews $news)
    {
        return response()->json([
            'id' => $news->id,
            'title' => $news->title,
            'content' => $news->content,
            'summary' => \Str::limit(strip_tags($news->content), 200),
            'image' => $news->image_path ? $news->getFileUrl('image_path') : null,
            'image_path' => $news->image_path,
            'category' => $news->category,
            'contact_name' => $news->contact_name,
            'contact_number' => $news->contact_number,
            'views_count' => $news->views_count ?? 0,
            'views' => $news->views_count ?? 0,
            'shares_count' => $news->shares_count ?? 0,
            'shares' => $news->shares_count ?? 0,
            'statistic_number' => $news->statistic_number,
            'statistic_description' => $news->statistic_description,
            'date' => $news->published_at
                ? $news->published_at->format('Y-m-d')
                : $news->created_at->format('Y-m-d'),
            'published_at' => $news->published_at,
            'created_at' => $news->created_at,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/website/volunteer",
     *     tags={"Website - Volunteer"},
     *     summary="Submit volunteer request",
     *     @OA\Response(
     *         response=200,
     *         description="Volunteer request submitted successfully"
     *     )
     * )
     */
    public function submitVolunteer(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'area_of_interest' => 'nullable|string',
            'message' => 'nullable|string',
            // File required, max 5MB (5120 KB), allowed types: pdf, doc, docx
            'cv' => 'required|file|mimes:pdf,doc,docx|max:5120',
            // Detailed Profile Fields
            'address' => 'nullable|string',
            'current_address' => 'nullable|string',
            'birth_date' => 'nullable|date',
            'national_id' => 'nullable|string',
            'gender' => 'nullable|string',
            'education_level' => 'nullable|string',
            'faculty' => 'nullable|string',
            'university' => 'nullable|string',
            'current_job' => 'nullable|string',
            'previous_experience' => 'nullable|string', // Changed to string as likely text input
            'skills' => 'nullable|string',
            'goal' => 'nullable|string',
            'expectations' => 'nullable|string',
            'volunteer_hours' => 'nullable|string'
        ]);

        try {
            // 1. Ensure the uploaded CV file is physically stored on the server BEFORE saving path in DB
            if ($request->hasFile('cv')) {
                // 3. Store the file using Laravel storage
                $cvPath = $request->file('cv')->store('cvs', 'public');

                if (!$cvPath) {
                    throw new \Exception('Failed to store CV file physically on the server.');
                }

                // 4. Save the returned path (e.g., cvs/hashedfilename.pdf) into the data array
                $data['cv_path'] = $cvPath;
            } else {
                throw new \Exception('CV file is missing from the request.');
            }

            // Remove 'cv' from data to avoid mass assignment issues if cv isn't in fillable safely
            unset($data['cv']);

            // Insert database record only after file has successfully been stored
            $volunteerRequest = WebVolunteerRequest::create($data);

            return response()->json(['message' => 'تم استلام طلب التطوع بنجاح، سنقوم بالتواصل معك قريباً']);

        } catch (\Exception $e) {
            // 6. Log upload errors using Laravel logs
            \Illuminate\Support\Facades\Log::error('Volunteer CV Upload Error: ' . $e->getMessage(), [
                'request' => $request->except(['cv']),
                'trace' => $e->getTraceAsString()
            ]);

            // 5. If file upload fails: Do NOT insert database record, return proper error message.
            return response()->json([
                'message' => 'حدث خطأ أثناء رفع السيرة الذاتية، يرجى المحاولة مرة أخرى.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/v1/website/contact",
     *     tags={"Website - Contact"},
     *     summary="Submit contact message",
     *     @OA\Response(
     *         response=200,
     *         description="Contact message submitted successfully"
     *     )
     * )
     */
    public function submitContact(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
            'image' => 'nullable|any_image|max:10240'
        ]);

        if ($request->hasFile('image')) {
            $data['image_path'] = app(\App\Services\ImageUploadService::class)->upload($request->file('image'), 'website/contact');
        }

        WebContactMessage::create($data);
        return response()->json(['message' => 'تم إرسال رسالتك بنجاح']);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/website/contact",
     *     tags={"Website - Contact"},
     *     summary="Get contact page data",
     *     @OA\Response(
     *         response=200,
     *         description="Contact info, social links and channels"
     *     )
     * )
     */
    public function contactPage()
    {
        $settings = \App\Models\WebSetting::all()->pluck('value', 'key')->toArray();
        $s = fn($key, $default = '') => (!empty($settings[$key])) ? $settings[$key] : $default;

        $slider = [];
        for ($i = 1; $i <= 10; $i++) {
            if (!empty($settings["contact_slider_$i"])) {
                $slider[] = $this->getStorageUrl($settings["contact_slider_$i"]);
            }
        }

        return response()->json([
            'slider' => $slider,
            'section' => [
                'title' => $s('contact_section_title', 'معلومات التواصل'),
                'subtitle' => $s('contact_section_subtitle', 'يسعدنا تواصلكم معنا بأي وسيلة تناسبكم'),
            ],
            'social_links' => [
                'facebook' => $s('social_facebook'),
                'twitter' => $s('social_twitter'),
                'x' => $s('social_twitter'),
                'instagram' => $s('social_instagram'),
                'youtube' => $s('social_youtube'),
                'tiktok' => $s('social_tiktok'),
                'whatsapp' => $s('contact_whatsapp_link', '+20 100 609 0616'),
            ],
            'channels' => [
                'phone' => [
                    'title' => $s('contact_phone_title', 'رقم الهاتف'),
                    'subtitle' => $s('contact_phone_subtitle', 'متاح 24/7'),
                    'value' => $s('contact_phone_link', '01006090616'),
                ],
                'email' => [
                    'title' => $s('contact_email_title', 'البريد الإلكتروني'),
                    'subtitle' => $s('contact_email_subtitle', 'نرد خلال 24 ساعة'),
                    'value' => $s('contact_email_link', 'info@ensan.org'),
                ],
                'whatsapp' => [
                    'title' => $s('contact_whatsapp_title', 'واتساب'),
                    'subtitle' => $s('contact_whatsapp_subtitle', 'تواصل فوري'),
                    'value' => $s('contact_whatsapp_link', '+20 100 609 0616'),
                ],
                'visit' => [
                    'title' => $s('contact_visit_title', 'زيارة المقر'),
                    'subtitle' => $s('contact_visit_subtitle', '9 ص - 5 م يومياً'),
                    'value' => $s('contact_visit_link', '#'),
                ],
            ],
            'info' => [
                'hq_address' => $s('contact_hq_address', 'تقسيم 2 ش تونس المتفرع من ش مخزن الزيت'),
                'hq_details' => $s('contact_hq_details', 'العماره اللي جنب كازيون'),
                'hotline' => $s('contact_hotline', '19000'),
                'support_email' => $s('contact_support_email', 'support@ensan.org'),
                'working_days' => $s('contact_working_days', 'السبت - الخميس'),
                'working_hours' => $s('contact_working_hours', '9:00 ص - 5:00 م'),
            ],
            'faqs' => \App\Models\WebFaq::orderBy('sort_order')->get()->map(function ($faq) {
            return [
                    'id' => $faq->id,
                    'question' => $faq->question,
                    'answer' => $faq->answer,
                ];
        }),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/website/room-booking",
     *     tags={"Website - Guest House"},
     *     summary="Submit room booking request",
     *     @OA\Response(
     *         response=200,
     *         description="Room booking request created successfully"
     *     )
     * )
     * @OA\Post(
     *     path="/api/v1/website/guest-house",
     *     tags={"Website - Guest House"},
     *     summary="Submit guest house booking request (alias)",
     *     @OA\Response(
     *         response=200,
     *         description="Room booking request created successfully"
     *     )
     * )
     */
    public function submitRoomBooking(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'national_id' => 'nullable|string',
            'address' => 'nullable|string',
            'companion_name' => 'nullable|string',
            'companion_phone' => 'nullable|string',
            'arrival_date' => 'nullable|date',
            'expected_duration' => 'nullable|string',
            'medical_center' => 'nullable|string',
            'guest_house_id' => 'nullable|exists:guest_houses,id',
            'treatment_type' => 'nullable|in:chemotherapy,radiation,other',
            'sessions_count' => 'nullable|integer|min:1|max:1000',
            // Files
            'patient_id' => 'nullable|file|any_image|max:10240',
            'companion_id' => 'nullable|file|any_image|max:10240',
            'medical_transfer' => 'nullable|file|any_image|max:10240',
            'followup_card' => 'nullable|file|any_image|max:10240',
            'medical_report' => 'nullable|file|any_image|max:10240'
        ]);

        $fileFields = [
            'patient_id' => 'patient_id_path',
            'companion_id' => 'companion_id_path',
            'medical_transfer' => 'medical_transfer_path',
            'followup_card' => 'followup_card_path',
            'medical_report' => 'medical_report_path'
        ];

        foreach ($fileFields as $input => $column) {
            if ($request->hasFile($input)) {
                $data[$column] = app(\App\Services\ImageUploadService::class)->upload($request->file($input), 'website/bookings');
            }
        }

        // Default requirements
        $data['room_type'] = $data['room_type'] ?? 'غرفة مرضى';
        $data['check_in'] = $data['arrival_date'] ?? now();
        $data['check_out'] = $data['check_in']; // Placeholder

        WebRoomBooking::create($data);
        return response()->json(['message' => 'تم استلام طلب حجز الغرفة بنجاح، سنتواصل معك للتأكيد']);
    }

    public function donationPage()
    {
        return response()->json($this->donationSettings->getSettings());
    }

    public function updateDonationPageSettings(Request $request)
    {
        $data = $request->validate([
            'donation_page_campaign_title' => 'nullable|string',
            'donation_page_campaign_link' => 'nullable|string', // New Link field
            'donation_page_hero_text' => 'nullable|string',
            'donation_page_hero_desc' => 'nullable|string',
            'donation_page_stats_donors' => 'nullable|string',
            'donation_page_stats_today_collected' => 'nullable|string',
            'donation_page_projects_title' => 'nullable|string',
            'donation_page_projects_subtitle' => 'nullable|string',
            'donation_page_categories' => 'nullable|array',
        ]);

        // Map request names to service expected names
        $mappedData = [
            'urgent_campaign_title' => $data['donation_page_campaign_title'] ?? null,
            'urgent_campaign_link' => $data['donation_page_campaign_link'] ?? null,
            'hero_title' => $data['donation_page_hero_text'] ?? null,
            'hero_description' => $data['donation_page_hero_desc'] ?? null,
            'stats_donors_count' => $data['donation_page_stats_donors'] ?? null,
            'stats_today_amount' => $data['donation_page_stats_today_collected'] ?? null,
            'projects_title' => $data['donation_page_projects_title'] ?? null,
            'projects_subtitle' => $data['donation_page_projects_subtitle'] ?? null,
            'categories' => $data['donation_page_categories'] ?? null,
        ];

        $this->donationSettings->updateSettings(array_filter($mappedData, fn($v) => !is_null($v)));

        return response()->json(['message' => 'تم تحديث إعدادات صفحة التبرع بنجاح']);
    }

    // --- Management Methods (Authenticated) ---

    /**
     * Update project website content
     */
    public function updateProjectWebsite(Request $request, Project $project)
    {
        $data = $request->validate([
            'category' => 'nullable|string',
            'website_content' => 'nullable|string',
            'sponsorship_details' => 'nullable|string',
            'image' => 'nullable|any_image|max:10240'
        ]);

        if ($request->hasFile('image')) {
            app(\App\Services\ImageUploadService::class)->delete($project->image_path);
            $data['image_path'] = app(\App\Services\ImageUploadService::class)->upload($request->file('image'), 'website/projects');
        }

        $project->update($data);
        return response()->json(['message' => 'تم تحديث محتوى المشروع', 'data' => $this->formatProject($project)]);
    }

    /**
     * Update campaign website content
     */
    public function updateCampaignWebsite(Request $request, Campaign $campaign)
    {
        $data = $request->validate([
            'category' => 'nullable|string',
            'website_content' => 'nullable|string',
            'goal_amount' => 'nullable|numeric',
            'current_amount' => 'nullable|numeric',
            'beneficiaries_count' => 'nullable|integer',
            'image' => 'nullable|any_image|max:10240'
        ]);

        if ($request->hasFile('image')) {
            app(\App\Services\ImageUploadService::class)->delete($campaign->image_path);
            $data['image_path'] = app(\App\Services\ImageUploadService::class)->upload($request->file('image'), 'website/campaigns');
        }

        $campaign->update($data);
        return response()->json(['message' => 'تم تحديث محتوى الحملة', 'data' => $this->formatCampaign($campaign)]);
    }

    // Board Members Management
    public function storeBoardMember(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'role' => 'required|string',
            'description' => 'nullable|string',
            'image' => 'nullable|any_image|max:5120',
            'sort_order' => 'integer'
        ]);

        if ($request->hasFile('image')) {
            $data['image_path'] = app(\App\Services\ImageUploadService::class)->upload($request->file('image'), 'website/board');
        }

        $member = WebBoardMember::create($data);
        return response()->json(['message' => 'تم الإضافة بنجاح', 'data' => $member]);
    }

    public function updateBoardMember(Request $request, WebBoardMember $member)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'role' => 'required|string',
            'description' => 'nullable|string',
            'image' => 'nullable|any_image|max:5120',
            'sort_order' => 'integer'
        ]);

        if ($request->hasFile('image')) {
            app(\App\Services\ImageUploadService::class)->delete($member->image_path);
            $data['image_path'] = app(\App\Services\ImageUploadService::class)->upload($request->file('image'), 'website/board');
        }

        $member->update($data);
        return response()->json(['message' => 'تم التحديث بنجاح', 'data' => $member]);
    }

    public function destroyBoardMember(WebBoardMember $member)
    {
        if ($member->image_path)
            Storage::disk('public')->delete($member->image_path);
        $member->delete();
        return response()->json(['message' => 'تم الحذف بنجاح']);
    }

    // FAQ Management
    public function getFaqs()
    {
        return response()->json(WebFaq::orderBy('sort_order')->get());
    }

    public function storeFaq(Request $request)
    {
        $data = $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string',
            'category' => 'nullable|string',
            'sort_order' => 'integer'
        ]);
        $item = WebFaq::create($data);
        return response()->json(['message' => 'تم الإضافة بنجاح', 'data' => $item]);
    }

    public function updateFaq(Request $request, WebFaq $faq)
    {
        $data = $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string',
            'category' => 'nullable|string',
            'sort_order' => 'integer'
        ]);
        $faq->update($data);
        return response()->json(['message' => 'تم التحديث بنجاح', 'data' => $faq]);
    }

    public function destroyFaq(WebFaq $faq)
    {
        $faq->delete();
        return response()->json(['message' => 'تم الحذف بنجاح']);
    }

    // Settings Management
    public function updateSettings(Request $request)
    {
        // Increase time limit for bulk image processing (slider uploads)
        set_time_limit(120);

        $data = $request->all();
        $allowedKeys = [
            // Stats
            'stats_beneficiaries', 'stats_branches', 'stats_donations', 'stats_volunteers',
            'stats_projects', 'stats_years', 'stats_smiles',
            'stats_donations_label', 'stats_projects_label', 'stats_branches_label',
            'stats_beneficiaries_label', 'stats_volunteers_label',
            // Guest House Stats
            'gh_stats_patients', 'gh_stats_beds', 'gh_stats_reception', 'gh_stats_branches',
            'gh_stat1_value', 'gh_stat1_label', 'gh_stat2_value', 'gh_stat2_label',
            'gh_stat3_value', 'gh_stat3_label', 'gh_stat4_value', 'gh_stat4_label',
            'gh_hero_subtitle',
            // Hero
            'hero_title_primary', 'hero_title_secondary', 'hero_description',
            'hero_stat_money', 'hero_stat_smiles', 'hero_stat_years',
            // About
            'about_story_title', 'about_story_content', 'about_vision', 'about_mission',
            // Notification
            'notification_active', 'notification_label', 'notification_text',
            'notification_link_text', 'notification_link_url',
            // Volunteer Page
            'volunteer_title', 'volunteer_subtitle', 'volunteer_description',
            'volunteer_stats_volunteers', 'volunteer_stats_hours', 'volunteer_stats_branches', 'volunteer_stats_projects',
            // Partners Stats
            'partners_stats_donors', 'partners_stats_volunteers',
            'partners_stats_institutions', 'partners_stats_campaigns',
            // Featured Campaign
            'featured_campaign_title', 'featured_campaign_desc', 'featured_campaign_beneficiaries',
            'featured_campaign_days', 'featured_campaign_progress', 'featured_campaign_status',
            'featured_campaign_collected', 'featured_campaign_goal',
            'featured_campaign_button_option', 'featured_campaign_button_text',
            'featured_campaign_share_price', 'featured_campaign_starts_days',
            'featured_campaign_starts_hours', 'featured_campaign_btn_color',
            'featured_campaign_show_countdown', 'featured_campaign_show_custom_text',
            'featured_campaign_custom_text', 'featured_campaign_show_register_stats',
            'featured_campaign_register_label', 'featured_campaign_register_label2',
            'featured_campaign_register_value1', 'featured_campaign_register_value2',
            'featured_campaign_progress_label',
            'featured_campaign_icon1_label', 'featured_campaign_icon1_value', 'featured_campaign_icon1_class',
            'featured_campaign_icon2_label', 'featured_campaign_icon2_value', 'featured_campaign_icon2_class',
            'featured_campaign_primary_color', 'featured_campaign_tint_color',
            'featured_campaign_border_color', 'featured_campaign_icon_color',
            'campaign_featured_year',
            // Campaigns Section
            'campaigns_title', 'campaigns_subtitle',
            'campaigns_active_label', 'campaigns_upcoming_label',
            'campaigns_contribute_btn', 'campaigns_remind_btn',
            'campaigns_collected_label', 'campaigns_goal_label',
            // Campaign Sliders
            'campaign_slider_1', 'campaign_slider_2', 'campaign_slider_3', 'campaign_slider_4', 'campaign_slider_5',
            'campaign_slider_6', 'campaign_slider_7', 'campaign_slider_8', 'campaign_slider_9', 'campaign_slider_10',
            // Contact Page
            'contact_stat1_value', 'contact_stat1_label',
            'contact_stat2_value', 'contact_stat2_label',
            'contact_stat3_value', 'contact_stat3_label',
            'contact_stat4_value', 'contact_stat4_label',
            'contact_section_title', 'contact_section_subtitle',
            'contact_phone_link',
            'contact_email_link',
            'contact_whatsapp_link',
            'contact_hq_address', 'contact_hq_details', 'contact_hotline',
            'contact_support_email', 'contact_working_days', 'contact_working_hours',
            // Social
            'social_facebook', 'social_twitter', 'social_instagram', 'social_youtube',
            // Guest House Home
            'gh_home_title', 'gh_home_content',
            // Headquarters
            'headquarters_title', 'headquarters_description', 'headquarters_address',
            'headquarters_phone', 'headquarters_working_hours',
            'headquarters_feature1', 'headquarters_feature2', 'headquarters_feature3',
            'headquarters_directions_label', 'headquarters_call_label',
            'headquarters_stats_branches', 'headquarters_stats_governorates',
            'headquarters_stats_employees', 'headquarters_stats_donors',
            'coverage_news_label', 'coverage_news_count', 'coverage_views_label', 'coverage_views_count',
            // Bottom CTA
            'cta_title', 'cta_text',
            'cta_stat1_value', 'cta_stat1_label',
            'cta_stat2_value', 'cta_stat2_label',
            'cta_stat3_value', 'cta_stat3_label',
            // Ideal Partner Section
            'partner_section_title',
            'partner_item1_title', 'partner_item1_desc', 'partner_item1_value',
            'partner_item2_title', 'partner_item2_desc', 'partner_item2_value',
            'partner_item3_title', 'partner_item3_desc', 'partner_item3_value',
            'partner_item4_title', 'partner_item4_desc', 'partner_item4_value',
            // Sponsorship Programs
            'sponsorship_section_badge', 'sponsorship_section_title', 'sponsorship_section_subtitle',
            'hope_title', 'hope_description', 'hope_pill1', 'hope_pill2',
            'hope_sponsorship_amount', 'hope_action_url', 'hope_category',
            'zad_title', 'zad_description', 'zad_pill1', 'zad_pill2',
            'zad_sponsorship_amount', 'zad_action_url', 'zad_category',
            'midrar_title', 'midrar_description', 'midrar_category', 'midrar_stats_roofs', 'midrar_stats_water',
            'kiswah_title', 'kiswah_description', 'kiswah_category', 'kiswah_stats_families', 'kiswah_stats_pieces',
            // Project Page Specifics
            'projects_page_title', 'projects_page_description',
            'stats_donations_label', 'stats_projects_label', 'stats_governorates_label', 'stats_beneficiaries_label',
            'stats_branches_label', 'stats_volunteers_label',
            // FAQ Section
            'faq_section_title', 'faq_section_subtitle',
        ];

        foreach ($data as $key => $value) {
            if (in_array($key, $allowedKeys)) {
                WebSetting::set($key, $value);
            }
        }

        // Clear Landing Page Cache
        \App\Http\Controllers\WebsiteWebController::clearLandingCache();

        // Dynamic Sliders (Up to 10 images each)
        $sliders = [
            'gallery_image' => 'website/gallery',
            'news_slider' => 'website/news/slider',
            'contact_slider' => 'website/contact/slider',
            'volunteer_slider' => 'website/volunteer/slider',
            'gh_slider' => 'website/gh/slider',
            'project_slider' => 'website/projects/slider',
            'campaign_slider' => 'website/campaigns/slider'
        ];

        foreach ($sliders as $prefix => $folder) {
            for ($i = 1; $i <= 10; $i++) {
                $key = "{$prefix}_{$i}";
                if ($request->hasFile($key)) {
                    $oldPath = WebSetting::get($key);
                    app(\App\Services\ImageUploadService::class)->delete($oldPath);
                    $path = app(\App\Services\ImageUploadService::class)->upload($request->file($key), $folder);
                    WebSetting::set($key, $path, 'slider', 'image');
                }
                elseif ($request->has($key) && $request->input($key) === null) {
                    // Manual deletion if field is present but null
                    $oldPath = WebSetting::get($key);
                    app(\App\Services\ImageUploadService::class)->delete($oldPath);
                    WebSetting::set($key, null, 'slider', 'image');
                }
            }
        }

        // Handle Featured Campaign and Home Images
        foreach (['featured_campaign_icon', 'featured_campaign_icon1_image', 'featured_campaign_icon2_image', 'featured_campaign_image', 'gh_home_image', 'hero_image'] as $key) {
            if ($request->hasFile($key)) {
                $oldPath = WebSetting::get($key);
                app(\App\Services\ImageUploadService::class)->delete($oldPath);

                $folder = ($key === 'featured_campaign_image') ? 'website/campaigns' :
                    (($key === 'gh_home_image') ? 'website/home' :
                    (($key === 'hero_image') ? 'website/hero' : 'website/campaigns/icons'));

                $path = app(\App\Services\ImageUploadService::class)->upload($request->file($key), $folder);
                WebSetting::set($key, $path);
            }
            elseif ($request->has($key) && $request->input($key) === null) {
                $oldPath = WebSetting::get($key);
                app(\App\Services\ImageUploadService::class)->delete($oldPath);
                WebSetting::set($key, null);
            }
        }

        \Illuminate\Support\Facades\Cache::forget('website_landing_page_data');
        return response()->json(['message' => 'تم تحديث الإعدادات بنجاح']);
    }

    // Testimonials Management
    public function getTestimonials()
    {
        return response()->json(WebTestimonial::orderByDesc('created_at')->get());
    }

    public function storeTestimonial(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'role' => 'nullable|string',
            'content' => 'required|string',
            'rating' => 'integer|min:1|max:5',
            'image' => 'nullable|any_image|max:2048'
        ]);
        if ($request->hasFile('image')) {
            $data['image_path'] = app(\App\Services\ImageUploadService::class)->upload($request->file('image'), 'website/testimonials');
        }
        $item = \App\Models\WebTestimonial::create($data);
        return response()->json(['message' => 'تم الإضافة بنجاح', 'data' => $item]);
    }

    public function testimonials()
    {
        $testimonials = \App\Models\WebTestimonial::approved()->latest()->get();
        return response()->json($testimonials);
    }

    public function submitTestimonial(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'role' => 'nullable|string',
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'image' => 'nullable|any_image|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $data['image_path'] = app(\App\Services\ImageUploadService::class)->upload($request->file('image'), 'website/testimonials');
        }

        $data['status'] = 'pending'; // Default status for public submissions

        WebTestimonial::create($data);
        Cache::forget('website_landing_page_data');
        return response()->json(['message' => 'تم استلام رأيك بنجاح، وسيتم مراجعته قريباً']);
    }

    public function updateTestimonial(Request $request, \App\Models\WebTestimonial $testimonial)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'role' => 'nullable|string',
            'content' => 'required|string',
            'rating' => 'integer|min:1|max:5',
            'image' => 'nullable|any_image|max:2048',
            'status' => 'nullable|in:pending,approved,rejected'
        ]);
        if ($request->hasFile('image')) {
            app(\App\Services\ImageUploadService::class)->delete($testimonial->image_path);
            $data['image_path'] = app(\App\Services\ImageUploadService::class)->upload($request->file('image'), 'website/testimonials');
        }
        $testimonial->update($data);
        return response()->json(['message' => 'تم التحديث بنجاح', 'data' => $testimonial]);
    }

    public function destroyTestimonial(\App\Models\WebTestimonial $testimonial)
    {
        app(\App\Services\ImageUploadService::class)->delete($testimonial->image_path);
        $testimonial->delete();
        return response()->json(['message' => 'تم الحذف بنجاح']);
    }

    // Features Management (Why Choose Us)
    public function getFeatures()
    {
        return response()->json(WebFeature::orderBy('sort_order')->get());
    }

    public function storeFeature(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'icon' => 'nullable|string',
            'sort_order' => 'integer'
        ]);
        $item = \App\Models\WebFeature::create($data);
        return response()->json(['message' => 'تم الإضافة بنجاح', 'data' => $item]);
    }

    public function updateFeature(Request $request, \App\Models\WebFeature $feature)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'icon' => 'nullable|string',
            'sort_order' => 'integer'
        ]);
        $feature->update($data);
        return response()->json(['message' => 'تم التحديث بنجاح', 'data' => $feature]);
    }

    public function destroyFeature(\App\Models\WebFeature $feature)
    {
        $feature->delete();
        return response()->json(['message' => 'تم الحذف بنجاح']);
    }

    // Sectors Management
    public function getSectors()
    {
        return response()->json(WebSector::orderBy('id')->get());
    }

    public function storeSector(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'icon' => 'nullable|string',
            'description' => 'nullable|string'
        ]);
        $item = \App\Models\WebSector::create($data);
        return response()->json(['message' => 'تم الإضافة بنجاح', 'data' => $item]);
    }

    public function updateSector(Request $request, \App\Models\WebSector $sector)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'icon' => 'nullable|string',
            'description' => 'nullable|string'
        ]);
        $sector->update($data);
        return response()->json(['message' => 'تم التحديث بنجاح', 'data' => $sector]);
    }

    public function destroySector(\App\Models\WebSector $sector)
    {
        $sector->delete();
        return response()->json(['message' => 'تم الحذف بنجاح']);
    }

    public function getBranches()
    {
        return $this->branches();
    }

    // Branches Management
    public function storeBranch(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|string',
            'working_hours' => 'nullable|string',
            'email' => 'nullable|email',
            'google_maps_url' => 'nullable|url',
            'is_main' => 'boolean',
            'status_text' => 'string'
        ]);
        $item = \App\Models\WebBranch::create($data);
        return response()->json(['message' => 'تم الإضافة بنجاح', 'data' => $item]);
    }

    public function updateBranch(Request $request, \App\Models\WebBranch $branch)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|string',
            'working_hours' => 'nullable|string',
            'email' => 'nullable|email',
            'google_maps_url' => 'nullable|url',
            'is_main' => 'boolean',
            'status_text' => 'string'
        ]);
        $branch->update($data);
        return response()->json(['message' => 'تم التحديث بنجاح', 'data' => $branch]);
    }

    public function destroyBranch(\App\Models\WebBranch $branch)
    {
        $branch->delete();
        return response()->json(['message' => 'تم الحذف بنجاح']);
    }

    // Volunteer Wall Management
    public function getVolunteerWall()
    {
        return response()->json(\App\Models\WebVolunteerWall::orderBy('rank', 'asc')->get());
    }

    public function storeVolunteerWall(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'hours' => 'integer',
            'rank' => 'integer',
            'image' => 'nullable|any_image|max:2048'
        ]);
        if ($request->hasFile('image')) {
            $data['image_path'] = app(\App\Services\ImageUploadService::class)->upload($request->file('image'), 'website/volunteers');
        }
        $item = \App\Models\WebVolunteerWall::create($data);
        Cache::forget('website_landing_page_data');
        return response()->json(['message' => 'تم الإضافة بنجاح', 'data' => $item]);
    }

    public function updateVolunteerWall(Request $request, \App\Models\WebVolunteerWall $item)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'hours' => 'integer',
            'rank' => 'integer',
            'image' => 'nullable|any_image|max:2048'
        ]);
        if ($request->hasFile('image')) {
            app(\App\Services\ImageUploadService::class)->delete($item->image_path);
            $data['image_path'] = app(\App\Services\ImageUploadService::class)->upload($request->file('image'), 'website/volunteers');
        }
        $item->update($data);
        Cache::forget('website_landing_page_data');
        return response()->json(['message' => 'تم التحديث بنجاح', 'data' => $item]);
    }

    public function destroyVolunteerWall(\App\Models\WebVolunteerWall $item)
    {
        app(\App\Services\ImageUploadService::class)->delete($item->image_path);
        $item->delete();
        Cache::forget('website_landing_page_data');
        return response()->json(['message' => 'تم الحذف بنجاح']);
    }

    // Partner Management
    public function storePartner(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'logo' => 'required|any_image|max:2048',
            'description' => 'nullable|string',
            'type' => 'nullable|in:gold,platinum,silver,bronze',
            'website_url' => 'nullable|url'
        ]);
        $data['logo_path'] = app(\App\Services\ImageUploadService::class)->upload($request->file('logo'), 'website/partners');
        $item = WebPartner::create($data);
        Cache::forget('website_landing_page_data');
        return response()->json(['message' => 'تم الإضافة بنجاح', 'data' => $item]);
    }

    public function updatePartner(Request $request, WebPartner $partner)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'logo' => 'nullable|any_image|max:2048',
            'description' => 'nullable|string',
            'type' => 'nullable|in:gold,platinum,silver,bronze',
            'website_url' => 'nullable|url'
        ]);
        if ($request->hasFile('logo')) {
            app(\App\Services\ImageUploadService::class)->delete($partner->logo_path);
            $data['logo_path'] = app(\App\Services\ImageUploadService::class)->upload($request->file('logo'), 'website/partners');
        }
        $partner->update($data);
        Cache::forget('website_landing_page_data');
        return response()->json(['message' => 'تم التحديث بنجاح', 'data' => $partner]);
    }

    public function destroyPartner(WebPartner $partner)
    {
        app(\App\Services\ImageUploadService::class)->delete($partner->logo_path);
        $partner->delete();
        Cache::forget('website_landing_page_data');
        return response()->json(['message' => 'تم الحذف بنجاح']);
    }

    // News Management
    public function storeNews(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'image' => 'nullable|any_image|max:5120',
            'published_at' => 'nullable|date',
            'category' => 'nullable|string',
            'views_count' => 'nullable|integer',
            'shares_count' => 'nullable|integer',
            'contact_name' => 'nullable|string',
            'contact_number' => 'nullable|string'
        ]);
        if ($request->hasFile('image')) {
            $data['image_path'] = app(\App\Services\ImageUploadService::class)->upload($request->file('image'), 'website/news');
        }
        $item = WebNews::create($data);
        return response()->json(['message' => 'تم الإضافة بنجاح', 'data' => $item]);
    }

    public function updateNews(Request $request, WebNews $news)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'image' => 'nullable|any_image|max:5120',
            'published_at' => 'nullable|date',
            'category' => 'nullable|string',
            'views_count' => 'nullable|integer',
            'shares_count' => 'nullable|integer',
            'contact_name' => 'nullable|string',
            'contact_number' => 'nullable|string'
        ]);
        if ($request->hasFile('image')) {
            app(\App\Services\ImageUploadService::class)->delete($news->image_path);
            $data['image_path'] = app(\App\Services\ImageUploadService::class)->upload($request->file('image'), 'website/news');
        }
        $news->update($data);
        return response()->json(['message' => 'تم التحديث بنجاح', 'data' => $news]);
    }

    public function destroyNews(WebNews $news)
    {
        app(\App\Services\ImageUploadService::class)->delete($news->image_path);
        $news->delete();
        return response()->json(['message' => 'تم الحذف بنجاح']);
    }

    // Helper formatting methods
    private function formatProject($p)
    {
        // Format features - ensure each item is properly structured
        $features = [];
        if ($p->features && is_array($p->features)) {
            foreach ($p->features as $f) {
                if (is_array($f)) {
                    $features[] = [
                        'text' => $f['text'] ?? '',
                        'icon' => isset($f['icon']) && $f['icon'] ? (str_starts_with($f['icon'], 'http') ? $f['icon'] : $this->getStorageUrl($f['icon'])) : null,
                    ];
                } elseif (is_string($f)) {
                    $features[] = ['text' => $f, 'icon' => null];
                }
            }
        }

        // Format stats - ensure each item is properly structured
        $stats = [];
        if ($p->stats && is_array($p->stats)) {
            foreach ($p->stats as $s) {
                if (is_array($s)) {
                    $stats[] = [
                        'value' => $s['value'] ?? '',
                        'label' => $s['label'] ?? '',
                        'icon' => isset($s['icon']) && $s['icon'] ? (str_starts_with($s['icon'], 'http') ? $s['icon'] : $this->getStorageUrl($s['icon'])) : null,
                    ];
                }
            }
        }

        return [
            'id' => $p->id,
            'name' => $p->name,
            'category' => $p->category ?? 'عام',
            'image' => $p->image_path ? $p->getFileUrl('image_path') : null,
            'icon' => $p->icon_path ? $p->getFileUrl('icon_path') : null,
            'short_description' => $p->short_description,
            'description' => $p->website_content, // Key for Angular project-details
            'content' => $p->website_content,      // Key for project-details fallback
            'sponsorship_details' => $p->sponsorship_details,
            'features' => $features,
            'stats' => $stats,
            'theme' => $p->theme_colors ?? [
                'primaryColor' => '#0d6efd',
                'lightTint' => 'rgba(13, 110, 253, 0.1)',
                'borderColor' => 'rgba(13, 110, 253, 0.2)',
                'iconColor' => '#0d6efd'
            ],
            'action' => [
                'text' => 'تبرع الان',
                'url' => 'http://127.0.0.1:4200/donate',
                'color' => $p->ui_button_color ?? '#0d6efd',
                'icon' => $p->action_icon ? (str_starts_with($p->action_icon, 'http') ? $p->action_icon : $p->getFileUrl('action_icon')) : null,
            ],
            'action_url' => 'http://127.0.0.1:4200/donate',
            'badge' => [
                'show' => (bool)$p->show_badge,
                'text' => $p->badge_text,
                'icon' => $p->badge_icon ? (str_starts_with($p->badge_icon, 'http') ? $p->badge_icon : $p->getFileUrl('badge_icon')) : null,
            ],
            'subcategory' => [
                'show' => (bool)$p->show_subcategory,
                'text' => $p->subcategory_text
            ],
            'is_visible' => (bool)$p->is_visible,
        ];
    }


    private function formatCampaign($c, $settings = null)
    {
        if (!$settings) {
            $settings = WebSetting::all()->pluck('value', 'key');
        }
        $status = $c->status ?? 'active';
        $buttonText = $settings['campaign_ui_contribute_btn'] ?? 'ساهم الآن';
        $statusLabel = $settings['campaign_filter_active'] ?? 'نشطة الآن';
        $startsIn = null;

        if ($status === 'upcoming') {
            $statusLabel = $settings['campaign_filter_upcoming'] ?? 'قادمة';
            $buttonText = $settings['campaign_ui_remind_btn'] ?? 'ذكرني عند البدء';
            if ($c->start_date) {
                // Assuming start_date is cast to date/datetime in model, or parse it
                $startsIn = \Carbon\Carbon::parse($c->start_date)->locale('ar')->diffForHumans();
            }
        }
        elseif ($status === 'ended') {
            $statusLabel = $settings['campaign_filter_ended'] ?? 'منتهية';
            $buttonText = $settings['campaign_ui_ended_btn'] ?? 'انتهت الحملة';
        }

        $currentAmount = $c->current_amount;
        // Fallback: If current_amount is 0, calculate actual sum from active donations
        if ($currentAmount <= 0) {
            $currentAmount = $c->donations()->active()->sum(\Illuminate\Support\Facades\DB::raw('COALESCE(amount, 0) + COALESCE(estimated_value, 0)'));
        }

        $beneficiariesCount = $c->beneficiaries_count;
        // Fallback: If beneficiaries_count is 0, count actual beneficiary records
        if ($beneficiariesCount <= 0) {
            $beneficiariesCount = $c->beneficiaries()->count();
        }

        return [
            'id' => $c->id,
            'name' => $c->name,
            'title' => $c->season_title ?? $c->name, // Use season_title as primary display title if exists, or name
            'category' => $c->category ?? 'موسمي',
            'image' => $c->image_path ? $c->getFileUrl('image_path') : null,
            'icon' => $c->icon_path ? $c->getFileUrl('icon_path') : null,
            'content' => $c->website_content,
            'goal' => $c->goal_amount,
            'current' => $currentAmount,
            'beneficiaries' => $beneficiariesCount,
            'share_price' => $c->share_price,
            'progress' => $c->goal_amount > 0 ? round(($currentAmount / $c->goal_amount) * 100, 2) : 0,
            'status' => $status,
            'status_label' => $statusLabel,
            'button_text' => $buttonText,
            'starts_in' => $startsIn,
            'start_date_text' => $c->start_date_text,
            'action_url' => $c->action_url,
            // Pre-formatted strings for UI based on screenshots
            'ui' => [
                'collected_format' => $c->ui_collected_override ?? (($settings['campaign_ui_collected_label'] ?? 'تم جمع') . ' ' . number_format((float) ($currentAmount ?? 0)) . ' ج.م'),
                'benefited_format' => $c->ui_beneficiaries_override ?? (($settings['campaign_ui_benefited_label'] ?? 'استفاد') . ' ' . number_format((float) ($beneficiariesCount ?? 0)) . '+ أسرة'),
                'share_format' => $c->ui_share_override ?? (($settings['campaign_ui_share_label'] ?? 'السهم') . ': ' . number_format((float) ($c->share_price ?? 0)) . ' ج.م'),
                'goal_format' => $c->ui_goal_override ?? (($settings['campaign_ui_goal_label'] ?? 'الهدف') . ': ' . number_format((float) ($c->goal_amount ?? 0)) . ' ' . ($c->goal_unit ?? 'جنيه')),
                'progress_override' => $c->ui_progress_override,
                'contribute_btn' => $c->ui_contribute_btn,
                'remind_btn' => $c->ui_remind_btn,
                'ended_btn' => $c->ui_ended_btn,
                'theme_color' => $c->ui_theme_color,
                'button_color' => $c->ui_button_color,
            ]
        ];
    }

    /**
     * @OA\Get(
     *     path="/api/v1/website/events",
     *     tags={"Website - Events"},
     *     summary="List all events",
     *     @OA\Response(response=200, description="List of events")
     * )
     */
    public function events(Request $request)
    {
        $query = WebEvent::orderByDesc('event_date');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $events = $query->get()->map(fn($e) => $this->formatEvent($e));
        return response()->json($events);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/website/events/{event}",
     *     tags={"Website - Events"},
     *     summary="Get single event details",
     *     @OA\Parameter(name="event", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Event details")
     * )
     */
    public function eventShow(WebEvent $event)
    {
        $event->increment('views_count');
        return response()->json($this->formatEvent($event));
    }

    /**
     * Admin: List events
     */
    public function adminEvents()
    {
        $events = WebEvent::orderByDesc('event_date')->get()->map(fn($e) => $this->formatEvent($e));
        return response()->json($events);
    }

    /**
     * Admin: Store event
     */
    public function storeEvent(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'location' => 'nullable|string',
            'category' => 'nullable|string',
            'event_date' => 'nullable|date',
            'event_end_date' => 'nullable|date',
            'is_featured' => 'nullable|boolean',
            'image' => 'nullable|any_image|max:5120',
        ]);

        if ($request->hasFile('image')) {
            $data['image_path'] = app(\App\Services\ImageUploadService::class)->upload($request->file('image'), 'website/events');
        }

        $event = WebEvent::create($data);
        return response()->json(['message' => 'تم إضافة الفعالية بنجاح', 'data' => $this->formatEvent($event)]);
    }

    /**
     * Admin: Update event
     */
    public function updateEvent(Request $request, WebEvent $event)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'location' => 'nullable|string',
            'category' => 'nullable|string',
            'event_date' => 'nullable|date',
            'event_end_date' => 'nullable|date',
            'is_featured' => 'nullable|boolean',
            'image' => 'nullable|any_image|max:5120',
        ]);

        if ($request->hasFile('image')) {
            app(\App\Services\ImageUploadService::class)->delete($event->image_path);
            $data['image_path'] = app(\App\Services\ImageUploadService::class)->upload($request->file('image'), 'website/events');
        }

        $event->update($data);
        return response()->json(['message' => 'تم تحديث الفعالية بنجاح', 'data' => $this->formatEvent($event)]);
    }

    /**
     * Admin: Delete event
     */
    public function destroyEvent(WebEvent $event)
    {
        app(\App\Services\ImageUploadService::class)->delete($event->image_path);
        $event->delete();
        return response()->json(['message' => 'تم حذف الفعالية بنجاح']);
    }

    /**
     * Format event for public API
     */
    private function formatEvent(WebEvent $e)
    {
        return [
            'id' => $e->id,
            'title' => $e->title,
            'content' => $e->content,
            'location' => $e->location,
            'category' => $e->category,
            'event_date' => $e->event_date ? $e->event_date->format('Y-m-d H:i') : null,
            'event_end_date' => $e->event_end_date ? $e->event_end_date->format('Y-m-d H:i') : null,
            'image' => $e->image_path ? $e->getFileUrl('image_path') : null,
            'views_count' => $e->views_count,
            'shares_count' => $e->shares_count,
            'is_featured' => (bool)$e->is_featured,
            'created_at' => $e->created_at->format('Y-m-d'),
        ];
    }

    public function roomBookingInfo()
    {
        return response()->json([
            'message' => 'This endpoint is for submitting room bookings. Use POST method to submit your request.',
            'target_url' => url('/api/v1/website/room-booking'),
            'required_fields' => ['name', 'phone', 'arrival_date', 'stay_duration'],
            'hint' => 'To view guest house details, use /api/v1/website/guest-house'
        ]);
    }

    public function volunteerInfo()
    {
        return response()->json([
            'message' => 'This endpoint is for submitting volunteer applications. Use POST method to submit your request.',
            'target_url' => url('/api/v1/website/volunteer'),
            'required_fields' => ['name', 'phone', 'email', 'interest_area'],
            'hint' => 'To view volunteer statistics, use /api/v1/website/volunteer (GET)'
        ]);
    }

    public function getContactMessages()
    {
        return response()->json(\App\Models\WebContactMessage::orderByDesc('created_at')->get());
    }

    public function markContactMessageRead(WebContactMessage $message)
    {
        $message->update(['read' => true]);
        return response()->json(['message' => 'تم تحديث حالة الرسالة', 'data' => $message]);
    }

    public function destroyContactMessage(WebContactMessage $message)
    {
        $message->delete();
        return response()->json(['message' => 'تم حذف الرسالة بنجاح']);
    }

    public function getVolunteerRequests()
    {
        return response()->json(WebVolunteerRequest::orderByDesc('created_at')->get());
    }

    public function destroyVolunteerRequest(WebVolunteerRequest $request)
    {
        foreach (['cv_path', 'id_card_path'] as $column) {
            if ($request->{$column}) {
                app(\App\Services\ImageUploadService::class)->delete($request->{$column});
            }
        }
        $request->delete();
        return response()->json(['message' => 'تم حذف طلب التطوع بنجاح']);
    }

    public function getRoomBookings()
    {
        return response()->json(WebRoomBooking::with(['guestHouse', 'beneficiary'])->orderByDesc('created_at')->get());
    }

    public function updateRoomBookingStatus(Request $request, WebRoomBooking $booking)
    {
        $data = $request->validate(['status' => ['required', 'string', 'max:50']]);
        $booking->update(['status' => $data['status']]);
        return response()->json(['message' => 'تم تحديث حالة الحجز', 'data' => $booking]);
    }

    public function destroyRoomBooking(WebRoomBooking $booking)
    {
        foreach ($booking->getImageColumns() as $column) {
            if ($booking->{$column}) {
                app(\App\Services\ImageUploadService::class)->delete($booking->{$column});
            }
        }
        $booking->delete();
        return response()->json(['message' => 'تم حذف الحجز بنجاح']);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/website/opinions",
     *     tags={"Website - Interactions"},
     *     summary="Submit an opinion/feedback",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "opinion"},
     *             @OA\Property(property="name", type="string", example="أحمد محمد"),
     *             @OA\Property(property="phone", type="string", example="01000000000"),
     *             @OA\Property(property="email", type="string", example="ahmed@example.com"),
     *             @OA\Property(property="opinion", type="string", example="رأيي في الموقع ممتاز جداً")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Opinion submitted successfully")
     * )
     */
    public function submitOpinion(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'    => 'required|string|max:255',
                'phone'   => 'nullable|string|max:20',
                'email'   => 'nullable|email|max:255',
                'opinion' => 'required|string',
                'rating'  => 'nullable|integer|min:1|max:5',
                'role'    => 'nullable|string|max:255',
                'image'   => 'nullable|any_image|max:5120',
            ]);

            // Format content to include contact info (since WebTestimonial doesn't have these columns)
            $content = $validated['opinion'];
            if (!empty($validated['phone']) || !empty($validated['email'])) {
                $content .= "\n\n--- بيانات التواصل ---\n";
                if (!empty($validated['phone'])) $content .= "الهاتف: " . $validated['phone'] . "\n";
                if (!empty($validated['email'])) $content .= "البريد: " . $validated['email'] . "\n";
            }

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = app(\App\Services\ImageUploadService::class)->upload($request->file('image'), 'website/testimonials');
            }

            $testimonial = \App\Models\WebTestimonial::create([
                'name'       => $validated['name'],
                'role'       => $validated['role'] ?? 'مستفيد/متبرع',
                'content'    => $content,
                'image_path' => $imagePath,
                'rating'     => $validated['rating'] ?? 5,
                'status'     => 'pending',
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'تم استلام رأيك بنجاح، شكراً لك!',
                'data'    => $testimonial
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'خطأ في التحقق من البيانات',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Opinion Submission Error: ' . $e->getMessage(), [
                'exception' => $e,
                'input'     => $request->all()
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'حدث خطأ في السيرفر أثناء معالجة طلبك'
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/website/opinions",
     *     tags={"Website - Interactions"},
     *     summary="Get published opinions",
     *     @OA\Response(response=200, description="List of published opinions")
     * )
     */
    public function getOpinions()
    {
        // Fetch approved testimonials and map them to match the old WebOpinion structure
        $opinions = \App\Models\WebTestimonial::approved()
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($testimonial) {
                return [
                    'id' => $testimonial->id,
                    'name' => $testimonial->name,
                    'role' => $testimonial->role,
                    'opinion' => $testimonial->content,
                    'image_url' => $testimonial->image_url,
                    'rating' => $testimonial->rating,
                    'is_published' => true,
                    'created_at' => $testimonial->created_at,
                    'updated_at' => $testimonial->updated_at,
                ];
            });

        return response()->json($opinions);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/website/subscribe",
     *     tags={"Website - Interactions"},
     *     summary="Subscribe to newsletter",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", example="user@example.com")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Subscribed successfully")
     * )
     */
    public function submitSubscription(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $subscription = \App\Models\NewsletterSubscription::firstOrCreate(
            ['email' => $validated['email']],
            ['is_active' => true]
        );

        return response()->json([
            'message' => 'تم الاشتراك في النشرة الإخبارية بنجاح، شكراً لك!',
            'data' => $subscription
        ]);
    }

    /**
     * Admin: Get all newsletter subscriptions
     */
    public function getSubscriptions()
    {
        return response()->json(\App\Models\NewsletterSubscription::orderByDesc('created_at')->get());
    }

    /**
     * Admin: Delete a newsletter subscription
     */
    public function destroySubscription(\App\Models\NewsletterSubscription $subscription)
    {
        $subscription->delete();
        return response()->json(['message' => 'تم الحذف بنجاح']);
    }

    /**
     * Website donor registration
     * Required: name (Arabic, 3+ words), phone (11 digits), country
     */
    public function publicRegister(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\x{0600}-\x{06FF}a-zA-Z\s]+$/u', // Arabic or English
                function ($attribute, $value, $fail) {
                    $words = preg_split('/\s+/u', trim($value), -1, PREG_SPLIT_NO_EMPTY);
                    if (count($words) < 3) {
                        $fail('يجب أن يتكون الاسم من 3 كلمات على الأقل.');
                    }
                },
            ],
            'phone'    => 'required|string|digits:11|unique:web_donors,phone',
            'email'    => 'nullable|email|unique:web_donors,email',
            'country'  => 'nullable|string|max:100',
            'password' => 'required|string|min:6',
        ], [
            'name.regex'        => 'يجب أن يكون الاسم بالحروف العربية أو الإنجليزية فقط.',
            'phone.digits'      => 'يجب أن يتكون رقم الهاتف من 11 رقماً.',
            'phone.unique'      => 'رقم الهاتف مسجل بالفعل، الرجاء تسجيل الدخول.',
            'password.required' => 'كلمة المرور مطلوبة.',
            'password.min'      => 'يجب ألا تقل كلمة المرور عن 6 أحرف.',
        ]);

        $user = \App\Models\WebDonor::create([
            'name'         => $request->name,
            'phone'        => $request->phone,
            'country'      => $request->country,
            'email'        => $request->email ?? ($request->phone . '@anasen.charity'),
            'password'     => \Illuminate\Support\Facades\Hash::make($request->password),
            'active'       => true,
            'otp_verified' => false, // first login will require OTP
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الحساب بنجاح. سيُطلب منك التحقق برمز OTP عند أول تسجيل دخول.',
            'user'    => [
                'id'           => $user->id,
                'name'         => $user->name,
                'phone'        => $user->phone,
                'country'      => $user->country,
                'otp_verified' => false,
            ]
        ], 201);
    }

    /**
     * Website donor login
     * • Unregistered phone  → 404 error
     * • Registered, first login (otp_verified = false) → send OTP
     * • Registered, verified before (otp_verified = true) → direct token
     */
    public function publicLogin(Request $request)
    {
        $request->validate([
            'phone'    => 'required|string|digits:11',
            'password' => 'required|string',
        ], [
            'phone.digits'      => 'يجب أن يتكون رقم الهاتف من 11 رقماً.',
            'password.required' => 'كلمة المرور مطلوبة.',
        ]);

        $user = \App\Models\WebDonor::where('phone', $request->phone)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'لا يوجد حساب بهذا الرقم. الرجاء التسجيل أولاً.',
            ], 404);
        }

        // Verify password
        if (!\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'كلمة المرور غير صحيحة.',
            ], 401);
        }

        // User is authenticated, direct token
        $token = $user->createToken('EnsanWeb')->plainTextToken;

        return response()->json([
            'success'      => true,
            'requires_otp' => false,
            'message' => 'تم تسجيل الدخول بنجاح.',
            'token'   => $token,
            'user'    => [
                'id'          => $user->id,
                'name'        => $user->name,
                'phone'       => $user->phone,
                'email'       => $user->email,
                'city'        => $user->city,
                'governorate' => $user->governorate,
                'country'     => $user->country,
            ]
        ]);
    }

    /**
     * Verify OTP sent on first login.
     * On success: marks account as verified and returns auth token.
     */
    public function publicVerifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|digits:11',
            'otp'   => 'required|string|min:4|max:6',
        ], [
            'otp.min' => 'رمز التحقق غير صالح.',
            'otp.max' => 'رمز التحقق غير صالح.',
        ]);

        $user = \App\Models\WebDonor::where('phone', $request->phone)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'رقم الهاتف غير موجود.'], 404);
        }

        if (!$user->otp_code || $user->otp_code !== $request->otp) {
            return response()->json(['success' => false, 'message' => 'رمز التحقق غير صحيح.'], 422);
        }

        if ($user->otp_expires_at && now()->isAfter($user->otp_expires_at)) {
            return response()->json(['success' => false, 'message' => 'رمز التحقق منتهي الصلاحية. الرجاء طلب رمز جديد.'], 422);
        }

        // Mark account as verified, clear OTP
        $user->otp_verified  = true;
        $user->otp_code      = null;
        $user->otp_expires_at = null;
        $user->save();

        $token = $user->createToken('EnsanWeb')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'تم التحقق بنجاح، مرحباً بك!',
            'token'   => $token,
            'user'    => [
                'id'          => $user->id,
                'name'        => $user->name,
                'phone'       => $user->phone,
                'email'       => $user->email,
                'city'        => $user->city,
                'governorate' => $user->governorate,
                'country'     => $user->country,
            ]
        ]);
    }

    /**
     * Resend OTP (rate-limited by 2-minute cooldown on otp_expires_at)
     */
    public function publicResendOtp(Request $request)
    {
        $request->validate(['phone' => 'required|string|digits:11']);

        $user = \App\Models\WebDonor::where('phone', $request->phone)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'رقم الهاتف غير موجود.'], 404);
        }

        // Throttle: don't resend if last OTP was sent less than 2 minutes ago
        if ($user->otp_expires_at && now()->diffInMinutes($user->otp_expires_at) > 8) {
            return response()->json([
                'success' => false,
                'message' => 'الرجاء الانتظار قبل طلب رمز جديد.',
            ], 429);
        }

        $otp = $this->sendOtpViaBeon($user->phone);
        $user->otp_code       = $otp ?? (string) rand(1000, 9999);
        $user->otp_expires_at = now()->addMinutes(15);
        $user->save();

        \Illuminate\Support\Facades\Log::info("Resent OTP for {$user->phone}: {$user->otp_code}");

        return response()->json([
            'success' => true,
            'message' => 'تم إعادة إرسال رمز التحقق.',
            'otp_dev' => app()->isLocal() ? $otp : null,
        ]);
    }

    /**
     * Check if website donor phone is registered
     */
    public function publicCheckPhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|digits:11',
        ], [
            'phone.digits' => 'يجب أن يتكون رقم الهاتف من 11 رقماً.',
        ]);

        $exists = \App\Models\WebDonor::where('phone', $request->phone)->exists();

        return response()->json([
            'success' => true,
            'exists' => $exists,
        ]);
    }

    /**
     * Public donor dashboard data (stats + recent activity)
     */
    public function publicDonorDashboard(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // --- Self-Healing Data Sync: Link orphan donations by phone number ---
        $donorIds = [];
        $donationColumns = [];
        try {
            $userPhone = $user->phone;
            if ($userPhone) {
                // Find all matching donors in the system
                $donorIds = \App\Models\Donor::where('phone', $userPhone)->pluck('id')->toArray();
                if (!empty($donorIds)) {
                    // Update WebDonations that are not yet linked to this web_donor_id
                    \App\Models\WebDonation::whereIn('donor_id', $donorIds)
                        ->whereNull('web_donor_id')
                        ->update(['web_donor_id' => $user->id]);

                    // Update internal Donations as well to show in history
                    $donationColumns = \Illuminate\Support\Facades\Schema::getColumnListing('donations');
                    if (in_array('web_donor_id', $donationColumns)) {
                        \App\Models\Donation::whereIn('donor_id', $donorIds)
                            ->whereNull('web_donor_id')
                            ->update(['web_donor_id' => $user->id]);
                    }
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Dashboard Data Sync Error: ' . $e->getMessage());
        }
        // --- End Sync ---

        $donationsList = collect();
        $totalAmountValue = 0;

        // 1. Get all web donations for this donor
        $webDonations = \App\Models\WebDonation::where('web_donor_id', $user->id)
            ->with(['donationable', 'proof'])
            ->latest()
            ->get();

        foreach ($webDonations as $d) {
            $status = $d->status;
            if ($status === 'verified' || $status === 'approved') {
                $totalAmountValue += (float)$d->amount;
            }
            $donationsList->push([
                'id'             => 'W-' . $d->id,
                'date'           => $d->created_at->format('d/m/Y'),
                'time'           => $d->created_at->format('H:i'),
                'amount'         => (float) $d->amount,
                'category'       => $d->donationable
                    ? ($d->donationable->title ?? $d->donationable->name ?? $d->category_label)
                    : $d->category_label,
                'payment_method' => $d->payment_method_label ?? $this->translatePaymentMethod($d->payment_method),
                'status'         => $this->translateStatus($status),
                'status_key'     => $status,
                'proof_url'      => $d->proof ? $this->getStorageUrl($d->proof->image_path) : null,
                'notes'          => $d->allocation_note,
                'created_at'     => $d->created_at,
            ]);
        }

        // 2. Get Mobile Donations (Instapay, Vodafone Cash) by Phone Number
        if ($user->phone) {
            $mobileDonations = \App\Models\MobileDonation::where('donor_phone', $user->phone)->latest()->get();
            foreach ($mobileDonations as $d) {
                $status = $d->status;
                if ($status === 'verified' || $status === 'approved') {
                    $totalAmountValue += (float)$d->donation_amount;
                }
                $donationsList->push([
                    'id'             => 'M-' . $d->id,
                    'date'           => $d->created_at->format('d/m/Y'),
                    'time'           => $d->created_at->format('H:i'),
                    'amount'         => (float) $d->donation_amount,
                    'category'       => $d->donation_for ?? 'عام',
                    'payment_method' => $this->translatePaymentMethod($d->payment_method),
                    'status'         => $this->translateStatus($status),
                    'status_key'     => $status,
                    'proof_url'      => $d->receipt_path ? $this->getStorageUrl($d->receipt_path) : null,
                    'notes'          => $d->notes,
                    'created_at'     => $d->created_at,
                ]);
            }
        }

        // 3. Get Internal CRM Donations
        if (in_array('web_donor_id', $donationColumns)) {
            $query = \App\Models\Donation::where('web_donor_id', $user->id);
            if (!empty($donorIds)) {
                $query->orWhereIn('donor_id', $donorIds);
            }
            $crmDonations = $query->latest()->get();
            foreach ($crmDonations as $d) {
                // Internal donations are usually considered verified
                $totalAmountValue += (float)$d->amount;

                $categoryName = 'عام';
                if ($d->category) {
                    $categoryName = $d->category->name ?? 'عام';
                }

                $donationsList->push([
                    'id'             => 'C-' . $d->id,
                    'date'           => $d->created_at->format('d/m/Y'),
                    'time'           => $d->created_at->format('H:i'),
                    'amount'         => (float) $d->amount,
                    'category'       => $categoryName,
                    'payment_method' => $this->translatePaymentMethod($d->payment_method),
                    'status'         => 'مقبول',
                    'status_key'     => 'verified',
                    'proof_url'      => null, // CRM might not expose proof to web dashboard
                    'notes'          => $d->notes ?? '',
                    'created_at'     => $d->created_at,
                ]);
            }
        }

        // Sort all by date descending
        $donationsList = $donationsList->sortByDesc('created_at')->values();

        $donationsCount   = $donationsList->count();
        $impactPercentage = $donationsCount > 0 ? min(100, $donationsCount * 12) : 0;

        return response()->json([
            'success' => true,
            'user'    => [
                'id'          => $user->id,
                'name'        => $user->name,
                'email'       => $user->email,
                'phone'       => $user->phone,
                'city'        => $user->city,
                'governorate' => $user->governorate,
                'photo'       => $user->profile_photo_path
                    ? $this->getStorageUrl($user->profile_photo_path)
                    : null,
            ],
            'stats' => [
                'donations_count'  => $donationsCount,
                'total_amount'     => number_format((float) $totalAmountValue) . ' ج.م',
                'impact_percentage'=> $impactPercentage,
                'level'            => 'مستوى التميز',
                'next_level_hint'  => 'أنت على بعد تبرع واحد من المستوى التالي!',
            ],
            'donations'       => $donationsList,         // full history
            'recent_activity' => $donationsList->take(10)->values(), // recent 10
        ]);
    }

    private function translatePaymentMethod($method)
    {
        $map = [
            'instapay' => 'إنستا باي',
            'vodafone_cash' => 'فودافون كاش',
            'fawry' => 'فوري',
            'credit_card' => 'بطاقة ائتمان',
            'bank_transfer' => 'تحويل بنكي',
            'cash' => 'نقدي'
        ];
        return $map[$method] ?? $method;
    }

    private function translateStatus($status)
    {
        $map = [
            'pending' => 'قيد المراجعة',
            'verified' => 'مقبول',
            'rejected' => 'مرفوض'
        ];
        return $map[$status] ?? $status;
    }

    /**
     * Public donor profile update
     */
    public function publicUpdateProfile(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\x{0600}-\x{06FF}a-zA-Z\s]+$/u', // Arabic or English
                function ($attribute, $value, $fail) {
                    $words = preg_split('/\s+/u', trim($value), -1, PREG_SPLIT_NO_EMPTY);
                    if (count($words) < 3) {
                        $fail('يجب أن يتكون الاسم من 3 كلمات على الأقل.');
                    }
                },
            ],
            'email' => 'nullable|email|unique:web_donors,email,' . $user->id,
            'city' => 'nullable|string|max:255',
            'governorate' => 'nullable|string|max:255',
        ], [
            'name.regex' => 'يجب أن يكون الاسم بالحروف العربية أو الإنجليزية فقط.',
            'name.required' => 'الاسم مطلوب.',
            'email.email' => 'البريد الإلكتروني غير صالح.',
            'email.unique' => 'البريد الإلكتروني مسجل مسبقاً لمستخدم آخر.',
        ]);

        $user->name = $request->name;
        if ($request->has('email')) {
            $user->email = $request->email;
        }

        // Only update city/governorate if the columns exist in the DB
        $columns = \Illuminate\Support\Facades\Schema::getColumnListing('web_donors');
        if (in_array('city', $columns) && $request->has('city')) {
            $user->city = $request->city;
        }
        if (in_array('governorate', $columns) && $request->has('governorate')) {
            $user->governorate = $request->governorate;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الملف الشخصي بنجاح',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'email' => $user->email,
                'city' => $user->city,
                'governorate' => $user->governorate
            ]
        ]);
    }

    private function sendOtpViaBeon(string $phone): ?string
    {
        try {
            $token = config('services.beon.token');
            if (!$token) {
                \Illuminate\Support\Facades\Log::warning('Beon OTP is not configured.');
                return null;
            }

            $formattedPhone = preg_replace('/\D/', '', $phone);
            if (str_starts_with($formattedPhone, '01') && strlen($formattedPhone) == 11) {
                $formattedPhone = '2' . $formattedPhone;
            }

            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'beon-token' => $token,
                'Content-Type' => 'application/json',
            ])->post('https://v3.api.beon.chat/api/v3/messages/otp', [
                'recipient' => $formattedPhone,
                'phoneNumber' => $formattedPhone,
                'phone' => $formattedPhone,
                'name' => 'Ensan',
                'channel' => 'sms',
                'type' => 'sms',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                \Illuminate\Support\Facades\Log::info("Beon OTP response: " . json_encode($data));
                if (isset($data['data']['otp'])) {
                    return (string) $data['data']['otp'];
                }
            } else {
                \Illuminate\Support\Facades\Log::error("Beon OTP error: " . $response->body());
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Exception in sendOtpViaBeon: " . $e->getMessage());
        }

        return null;
    }

    // ─── Complaints — Public ───────────────────────────────────────────────────

    public function submitWebsiteComplaint(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'phone'       => 'required|string|max:30',
            'email'       => 'nullable|email|max:255',
            'source_type' => 'required|in:donor,beneficiary,employee',
            'subject'     => 'required|string|max:255',
            'message'     => 'required|string',
        ], [
            'name.required'        => 'الاسم مطلوب.',
            'phone.required'       => 'رقم الهاتف مطلوب.',
            'source_type.required' => 'نوع المُبلِّغ مطلوب.',
            'source_type.in'       => 'نوع المُبلِّغ غير صحيح.',
            'subject.required'     => 'موضوع الشكوى مطلوب.',
            'message.required'     => 'نص الشكوى مطلوب.',
        ]);

        // store name/phone/email in message preamble so it's visible in the admin panel
        $preamble = "المُبلِّغ: {$data['name']} | هاتف: {$data['phone']}";
        if (!empty($data['email'])) {
            $preamble .= " | بريد: {$data['email']}";
        }

        $complaint = \App\Models\Complaint::create([
            'source_type' => $data['source_type'],
            'source_id'   => 0,
            'subject'     => $data['subject'],
            'message'     => $preamble . "\n\n" . $data['message'],
            'status'      => 'open',
        ]);

        return response()->json([
            'message'       => 'تم تسجيل شكواك بنجاح.',
            'tracking_code' => $complaint->tracking_code,
        ], 201);
    }

    public function trackWebsiteComplaint(string $code)
    {
        $complaint = \App\Models\Complaint::where('tracking_code', strtoupper($code))->first();

        if (!$complaint) {
            return response()->json(['message' => 'لم يتم العثور على شكوى بهذا الكود.'], 404);
        }

        return response()->json([
            'tracking_code' => $complaint->tracking_code,
            'subject'       => $complaint->subject,
            'source_type'   => $complaint->source_type,
            'status'        => $complaint->status,
            'resolution'    => $complaint->resolution,
            'resolved_at'   => $complaint->resolved_at?->format('Y-m-d'),
            'created_at'    => $complaint->created_at->format('Y-m-d'),
        ]);
    }

    // ─── Complaints — Admin ────────────────────────────────────────────────────

    public function adminListComplaints(Request $request)
    {
        $complaints = \App\Models\Complaint::orderByDesc('id')
            ->paginate(20)
            ->through(fn ($c) => [
                'id'            => $c->id,
                'tracking_code' => $c->tracking_code,
                'subject'       => $c->subject,
                'source_type'   => $c->source_type,
                'status'        => $c->status,
                'resolution'    => $c->resolution,
                'resolved_at'   => $c->resolved_at?->format('Y-m-d'),
                'created_at'    => $c->created_at->format('Y-m-d'),
                'message'       => $c->message,
            ]);

        return response()->json($complaints);
    }

    public function adminUpdateComplaint(Request $request, \App\Models\Complaint $complaint)
    {
        $data = $request->validate([
            'status'     => 'nullable|in:open,in_progress,closed',
            'resolution' => 'nullable|string',
        ]);

        if (isset($data['status']) && $data['status'] === 'closed' && $complaint->status !== 'closed') {
            $data['resolved_at'] = now();
        } elseif (isset($data['status']) && $data['status'] !== 'closed') {
            $data['resolved_at'] = null;
        }

        $complaint->update($data);

        return response()->json([
            'message'       => 'تم تحديث الشكوى بنجاح.',
            'tracking_code' => $complaint->tracking_code,
            'status'        => $complaint->status,
            'resolution'    => $complaint->resolution,
        ]);
    }
}

