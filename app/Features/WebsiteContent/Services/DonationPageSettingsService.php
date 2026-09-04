<?php

namespace App\Features\WebsiteContent\Services;

use App\Features\WebsiteContent\Interfaces\DonationPageSettingsInterface;
use App\Models\WebSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class DonationPageSettingsService implements DonationPageSettingsInterface
{
    /**
     * Get all donation page settings
     */
    public function getSettings(): array
    {
        $settings = WebSetting::all()->pluck('value', 'key')->toArray();
        $s = fn($key, $default = '') => (!empty($settings[$key])) ? $settings[$key] : $default;
        
        // Helper for multiple keys (fallback mechanism)
        $m = function($keys, $default = '') use ($settings) {
            foreach($keys as $key) {
                if(!empty($settings[$key])) return $settings[$key];
            }
            return $default;
        };

        return [
            'banner' => [
                'urgent_campaign' => $m(['donation_page_campaign_title', 'donation_banner_urgent_campaign'], 'مساعدات الشتاء للأسر المحتاجة'),
                'urgent_link' => $s('donation_banner_urgent_link', '#'),
                'hero_text' => $m(['donation_page_hero_text', 'donation_banner_hero_text'], 'ساهم في صناعة الفرق معنا'),
                'description' => $m(['donation_page_hero_desc', 'donation_banner_description'], 'تبرعك اليوم يضيء حياة الآلاف، كل مساهمة مهما كانت صغيرة تصنع أثراً كبيراً.'),
            ],
            'stats' => [
                'donors_count' => $m(['donation_page_stats_donors', 'donation_stats_donors_count'], '3421'),
                'donors_label' => $s('donation_stats_donors_label', 'متبرع كريم يشاركنا اللحظة'),
                'today_amount' => $m(['donation_page_stats_today_collected', 'donation_stats_today_amount'], '1,247'),
                'today_label' => $s('donation_stats_today_label', 'جنيه تم جمعها اليوم'),
            ],
            'projects_section' => [
                'title' => $m(['donation_page_projects_title', 'donation_projects_title'], 'مشاريع اليوم'),
                'subtitle' => $m(['donation_page_projects_subtitle', 'donation_projects_subtitle', 'donation_page_projects_desc'], 'أثرك ينمو باستمرار'),
            ],
            'fields_section' => [
                'title' => $m(['donation_page_fields_section_title', 'donation_fields_section_title'], 'اختر المجال الذي تود دعمه'),
                'fields' => \App\Models\DonationCategory::orderBy('sort_order')->pluck('name')->toArray() ?: ['الكل', 'المشاريع', 'الحملات', 'دار الضيافة', 'الكفالة', 'صدقة جارية', 'عام']
            ]
        ];
    }

    /**
     * Update donation page settings
     */
    public function updateSettings(array $data): void
    {
        // 1. Handle Urgent Campaign Link
        if (isset($data['urgent_campaign_link'])) {
            WebSetting::set('donation_banner_urgent_link', $data['urgent_campaign_link'], 'donation_page');
        }

        // 2. Urgent Campaign Section
        if (isset($data['urgent_campaign_title'])) {
            WebSetting::set('donation_page_campaign_title', $data['urgent_campaign_title'], 'donation_page');
            WebSetting::set('donation_banner_urgent_campaign', $data['urgent_campaign_title'], 'donation_page');
        }

        // 3. Hero Section
        if (isset($data['hero_title'])) {
            WebSetting::set('donation_page_hero_text', $data['hero_title'], 'donation_page');
            WebSetting::set('donation_banner_hero_text', $data['hero_title'], 'donation_page');
        }
        if (isset($data['hero_description'])) {
            WebSetting::set('donation_page_hero_desc', $data['hero_description'], 'donation_page');
            WebSetting::set('donation_banner_description', $data['hero_description'], 'donation_page');
        }

        // 4. Stats Section
        if (isset($data['stats_donors_count'])) {
            WebSetting::set('donation_page_stats_donors', $data['stats_donors_count'], 'donation_page');
            WebSetting::set('donation_stats_donors_count', $data['stats_donors_count'], 'donation_page');
        }
        if (isset($data['stats_today_amount'])) {
            WebSetting::set('donation_page_stats_today_collected', $data['stats_today_amount'], 'donation_page');
            WebSetting::set('donation_stats_today_amount', $data['stats_today_amount'], 'donation_page');
        }

        // 5. Projects Section
        if (isset($data['projects_title'])) {
            WebSetting::set('donation_page_projects_title', $data['projects_title'], 'donation_page');
            WebSetting::set('donation_projects_title', $data['projects_title'], 'donation_page');
        }
        if (isset($data['projects_subtitle'])) {
            WebSetting::set('donation_page_projects_subtitle', $data['projects_subtitle'], 'donation_page');
            WebSetting::set('donation_projects_subtitle', $data['projects_subtitle'], 'donation_page');
        }

        // 6. Support Fields (Categories)
        // Note: These are now managed via the DonationCategory model in the unified page.

        // 7. Global Notification Sync
        if (isset($data['urgent_campaign_title'])) {
            WebSetting::set('notification_text', $data['urgent_campaign_title'], 'general');
            WebSetting::set('notification_active', 'on', 'general');
        }
        if (isset($data['urgent_campaign_link'])) {
            WebSetting::set('notification_link_url', $data['urgent_campaign_link'], 'general');
        }

        // Clear cache
        Cache::forget('website_landing_page_data');
    }
}
