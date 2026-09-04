<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WebVolunteerRequest;
use Carbon\Carbon;

class VolunteerRequestsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data to avoid duplicates if re-run (optional, but good for testing)
        // WebVolunteerRequest::truncate(); // Commented out to be safe, just adding new ones.

        WebVolunteerRequest::create([
            'name' => 'أحمد محمد علي',
            'phone' => '01012345678',
            'email' => 'ahmed.ali@example.com',
            'area_of_interest' => 'التسويق الإلكتروني',
            'message' => 'متحمس جداً للمشاركة في الأنشطة الخيرية.',
            'status' => 'new',
            'address' => '15 شارع الحرية، المعادي، القاهرة',
            'current_address' => 'نفس العنوان الدائم',
            'birth_date' => '1995-05-15',
            'national_id' => '29505150101234',
            'gender' => 'ذكر',
            'education_level' => 'بكالوريوس',
            'faculty' => 'التجارة',
            'university' => 'جامعة القاهرة',
            'current_job' => 'محاسب',
            'previous_experience' => 'نعم، شاركت في حملة كسوة 2023',
            'skills' => 'إدارة الوقت، العمل الجماعي، Microsoft Office',
            'goal' => 'مساعدة المحتاجين واكتساب خبرات جديدة',
            'expectations' => 'بيئة عمل محفزة وتنظيم جيد',
            'volunteer_hours' => '5 ساعات أسبوعياً'
        ]);

        WebVolunteerRequest::create([
            'name' => 'سارة إبراهيم يوسف',
            'phone' => '01298765432',
            'email' => 'sara.ibrahim@example.com',
            'area_of_interest' => 'التنظيم والفعاليات',
            'message' => 'لدي خبرة سابقة في تنظيم المؤتمرات الطلابية.',
            'status' => 'contacted',
            'address' => 'بلوك 4، الحي السابع، مدينة نصر',
            'current_address' => 'سكن الطالبات، الجيزة',
            'birth_date' => '2001-11-20',
            'national_id' => '30111200204567',
            'gender' => 'أنثى',
            'education_level' => 'طالبة',
            'faculty' => 'الإعلام',
            'university' => 'جامعة عين شمس',
            'current_job' => 'لا يوجد',
            'previous_experience' => 'لا',
            'skills' => 'التواصل، التصوير الفوتوغرافي',
            'goal' => 'تنمية مهاراتي الاجتماعية',
            'expectations' => 'التوجيه والإرشاد',
            'volunteer_hours' => 'يومي الجمعة والسبت'
        ]);
    }
}
