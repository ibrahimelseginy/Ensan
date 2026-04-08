<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class SidebarPermissionsSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            // --- Settings ---
            ['key' => 'manage_change_requests', 'name' => 'إدارة طلبات المراجعة (الإلغاء والتعديل)'],

            // --- Suppliers ---
            ['key' => 'suppliers.view', 'name' => 'عرض الموردين'],
            ['key' => 'suppliers.create', 'name' => 'إضافة مورد'],
            ['key' => 'suppliers.edit', 'name' => 'تعديل مورد'],
            ['key' => 'suppliers.delete', 'name' => 'حذف مورد'],

            // --- Ramadan Campaigns ---
            ['key' => 'manage_ramadan', 'name' => 'إدارة حملة رمضان'],
            ['key' => 'ramadan_bags.view', 'name' => 'عرض شنط رمضان'],
            ['key' => 'ramadan_bags.create', 'name' => 'إضافة شنطة رمضان'],
            ['key' => 'ramadan_bags.edit', 'name' => 'تعديل شنطة رمضان'],
            ['key' => 'ramadan_bags.delete', 'name' => 'حذف شنطة رمضان'],
            ['key' => 'ramadan_iftars.view', 'name' => 'عرض إفطارات رمضان'],
            ['key' => 'ramadan_iftars.create', 'name' => 'إضافة إفطار رمضان'],
            ['key' => 'ramadan_iftars.edit', 'name' => 'تعديل إفطار رمضان'],
            ['key' => 'ramadan_iftars.delete', 'name' => 'حذف إفطار رمضان'],

            // --- Collaborations & Partnerships ---
            ['key' => 'manage_specialized_services', 'name' => 'إدارة التعاونات والشراكات والعضوية'],
            ['key' => 'school_collaborations.view', 'name' => 'عرض تعاونات المدارس'],
            ['key' => 'school_collaborations.create', 'name' => 'إضافة تعاون مدارس'],
            ['key' => 'school_collaborations.edit', 'name' => 'تعديل تعاون مدارس'],
            ['key' => 'school_collaborations.delete', 'name' => 'حذف تعاون مدارس'],
            
            ['key' => 'memberships.view', 'name' => 'عرض العضويات'],
            ['key' => 'memberships.create', 'name' => 'إضافة عضوية'],
            ['key' => 'memberships.edit', 'name' => 'تعديل عضوية'],
            ['key' => 'memberships.delete', 'name' => 'حذف عضوية'],

            ['key' => 'oncology_medicine_reps.view', 'name' => 'عرض مناديب أدوية الأورام'],
            ['key' => 'oncology_medicine_reps.create', 'name' => 'إضافة مندوب أدوية'],
            ['key' => 'oncology_medicine_reps.edit', 'name' => 'تعديل مندوب أدوية'],
            ['key' => 'oncology_medicine_reps.delete', 'name' => 'حذف مندوب أدوية'],

            ['key' => 'kafr_el_sheikh_brokers.view', 'name' => 'عرض سماسرة كفر الشيخ'],
            ['key' => 'kafr_el_sheikh_brokers.create', 'name' => 'إضافة سمسار'],
            ['key' => 'kafr_el_sheikh_brokers.edit', 'name' => 'تعديل سمسار'],
            ['key' => 'kafr_el_sheikh_brokers.delete', 'name' => 'حذف سمسار'],

            ['key' => 'kafr_el_sheikh_deliveries.view', 'name' => 'عرض توصيلات كفر الشيخ'],
            ['key' => 'kafr_el_sheikh_deliveries.create', 'name' => 'إضافة توصيل'],
            ['key' => 'kafr_el_sheikh_deliveries.edit', 'name' => 'تعديل توصيل'],
            ['key' => 'kafr_el_sheikh_deliveries.delete', 'name' => 'حذف توصيل'],

            ['key' => 'kafr_el_sheikh_services.view', 'name' => 'عرض خدمات كفر الشيخ'],
            ['key' => 'kafr_el_sheikh_services.create', 'name' => 'إضافة خدمة'],
            ['key' => 'kafr_el_sheikh_services.edit', 'name' => 'تعديل خدمة'],
            ['key' => 'kafr_el_sheikh_services.delete', 'name' => 'حذف خدمة'],

            ['key' => 'tanta_workers.view', 'name' => 'عرض عمال طنطا'],
            ['key' => 'tanta_workers.create', 'name' => 'إضافة عامل'],
            ['key' => 'tanta_workers.edit', 'name' => 'تعديل عامل'],
            ['key' => 'tanta_workers.delete', 'name' => 'حذف عامل'],

            // --- Website Management ---
            ['key' => 'website.settings.view_edit', 'name' => 'إدارة محتوى الصفحة الرئيسية للموقع'],
            ['key' => 'website.headquarters.view', 'name' => 'عرض المقر والفروع'],
            ['key' => 'website.headquarters.create', 'name' => 'إضافة مقر/فرع'],
            ['key' => 'website.headquarters.edit', 'name' => 'تعديل مقر/فرع'],
            ['key' => 'website.headquarters.delete', 'name' => 'حذف مقر/فرع'],
            
            ['key' => 'website.partners.view', 'name' => 'عرض جدار الشرف'],
            ['key' => 'website.partners.create', 'name' => 'إضافة شريك/شرف'],
            ['key' => 'website.partners.edit', 'name' => 'تعديل شريك/شرف'],
            ['key' => 'website.partners.delete', 'name' => 'حذف شريك/شرف'],

            ['key' => 'website.board.view', 'name' => 'عرض مجلس الأمناء'],
            ['key' => 'website.board.create', 'name' => 'إضافة عضو مجلس'],
            ['key' => 'website.board.edit', 'name' => 'تعديل عضو مجلس'],
            ['key' => 'website.board.delete', 'name' => 'حذف عضو مجلس'],

            ['key' => 'website.content.view_edit', 'name' => 'إدارة محتوى المشاريع للموقع'],
            ['key' => 'website.campaigns_content.view_edit', 'name' => 'إدارة محتوى الحملات للموقع'],
            ['key' => 'website.guest_house_content.view_edit', 'name' => 'إدارة محتوى دار الضيافة للموقع'],
            
            ['key' => 'website.news.view', 'name' => 'عرض أخبار الموقع'],
            ['key' => 'website.news.create', 'name' => 'إضافة خبر للموقع'],
            ['key' => 'website.news.edit', 'name' => 'تعديل خبر للموقع'],
            ['key' => 'website.news.delete', 'name' => 'حذف خبر للموقع'],

            ['key' => 'website.contact_messages.view', 'name' => 'عرض رسائل تواصل معنا'],
            ['key' => 'website.contact_messages.delete', 'name' => 'حذف رسائل تواصل معنا'],

            ['key' => 'website.subscriptions.view', 'name' => 'عرض النشرة الإخبارية'],
            ['key' => 'website.subscriptions.delete', 'name' => 'حذف مشترك في النشرة'],

            ['key' => 'website.volunteer_requests.view', 'name' => 'عرض طلبات التطوع (الموقع)'],
            ['key' => 'website.volunteer_requests.delete', 'name' => 'حذف طلب تطوع (الموقع)'],

            ['key' => 'website.donation_page.view_edit', 'name' => 'إدارة إعدادات صفحة التبرع'],
            ['key' => 'website.donation_settings.view_edit', 'name' => 'إدارة مجالات الدعم (الفئات)'],
            
            ['key' => 'website.accounts.view', 'name' => 'عرض حسابات المتبرعين (دخول)'],
            ['key' => 'website.accounts.delete', 'name' => 'حذف حساب متبرع'],

            ['key' => 'website.donation_accounts.view', 'name' => 'عرض حسابات تبرعات الويبسايت'],
            ['key' => 'website.donation_accounts.delete', 'name' => 'حذف حساب تبرعات ويبسايت'],

            // --- Mobile Management ---
            ['key' => 'mobile.home_content.view_edit', 'name' => 'إدارة محتوى الصفحة الرئيسية للموبايل'],
            ['key' => 'mobile.news.view', 'name' => 'عرض أخبار الموبايل'],
            ['key' => 'mobile.news.create', 'name' => 'إضافة خبر للموبايل'],
            ['key' => 'mobile.news.edit', 'name' => 'تعديل خبر للموبايل'],
            ['key' => 'mobile.news.delete', 'name' => 'حذف خبر للموبايل'],

            ['key' => 'mobile.volunteer_requests.view', 'name' => 'عرض طلبات التطوع (الموبايل)'],
            ['key' => 'mobile.volunteer_requests.delete', 'name' => 'حذف طلب تطوع (الموبايل)'],

            ['key' => 'mobile.case_applications.view', 'name' => 'عرض طلبات الحالات المستحقة (الموبايل)'],
            ['key' => 'mobile.case_applications.delete', 'name' => 'حذف طلب حالة مستحقة'],

            ['key' => 'mobile.bookings.view', 'name' => 'عرض طلبات الحجز للموبايل'],
            ['key' => 'mobile.bookings.delete', 'name' => 'حذف طلب حجز موبايل'],

            ['key' => 'mobile.donations.view', 'name' => 'عرض سجلات تبرعات الموبايل'],
            ['key' => 'mobile.donors.view', 'name' => 'عرض تسجيلات دخول الموبايل'],
            ['key' => 'mobile.notifications.view', 'name' => 'عرض إشعارات الموبايل'],
            ['key' => 'mobile.notifications.create', 'name' => 'إرسال إشعار للموبايل'],

            // --- Extra ---
            ['key' => 'revenues.view', 'name' => 'عرض تقارير الإيرادات'],
        ];

        foreach ($permissions as $p) {
            Permission::updateOrCreate(
                ['key' => $p['key']],
                ['name' => $p['name']]
            );
        }
    }
}
