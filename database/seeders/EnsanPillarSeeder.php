<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EnsanPillar;

class EnsanPillarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pillars = [
            [
                'title' => 'زاد',
                'slug' => 'zad',
                'description' => 'مشروع زاد يهدف إلى مكافحة الجوع وتوفير السلال الغذائية المتكاملة للأسر المتعففة، بالإضافة إلى دعم المشاريع الزراعية الصغيرة لضمان الاستدامة الغذائية.',
                'sort_order' => 1,
            ],
            [
                'title' => 'مدرار',
                'slug' => 'midrar',
                'description' => 'مشروع مدرار يركز على توفير المياه الصالحة للشرب من خلال حفر الآبار، تركيب محطات التحلية، وتوفير حلول السقيا في المناطق الأكثر احتياجاً.',
                'sort_order' => 2,
            ],
            [
                'title' => 'كسوة',
                'slug' => 'kiswah',
                'description' => 'مشروع كسوة يعتني بتوفير الملابس اللائقة للأطفال والأيتام والأسر المحتاجة في مختلف المناسم (الأعياد، الشتاء، العودة للمدارس).',
                'sort_order' => 3,
            ],
            [
                'title' => 'دار الضيافة',
                'slug' => 'dar-al-diyafa',
                'description' => 'دار الضيافة تقدم حلول الإيواء الآمن والمؤقت للمغتربين والطلاب والحالات الإنسانية التي تحتاج إلى سكن كريم وخدمات فندقية بسيطة.',
                'sort_order' => 4,
            ],
            [
                'title' => 'سُقاء الأمل',
                'slug' => 'saqaa-al-amal',
                'description' => 'سُقاء الأمل هو مشروع نوعي يمزج بين الدعم النفسي والتمكين المجتمعي، لبناء بصيص من الأمل لمستقبل أفضل من خلال التعليم والتدريب.',
                'sort_order' => 5,
            ],
        ];

        foreach ($pillars as $pillar) {
            EnsanPillar::updateOrCreate(['slug' => $pillar['slug']], $pillar);
        }
    }
}
