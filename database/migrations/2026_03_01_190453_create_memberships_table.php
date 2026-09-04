<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->string('entity_name'); // اسم الجهة / المكان
            $table->string('entity_type')->nullable(); // نوع الجهة
            $table->string('service_provided')->nullable(); // الخدمة المقدمة
            $table->string('discount_percentage')->nullable(); // نسبة الخصم / العرض
            $table->text('discount_conditions')->nullable(); // شروط الخصم
            $table->string('beneficiary_category')->nullable(); // الفئة المستفيدة
            $table->string('discount_activation_method')->nullable(); // طريقة تفعيل الخصم
            $table->string('working_hours')->nullable(); // ساعات العمل
            $table->string('entity_address')->nullable(); // عنوان الجهة
            $table->string('entity_location')->nullable(); // موقع الجهة
            $table->string('contact_number')->nullable(); // رقم التواصل
            $table->string('contact_person_number')->nullable(); // رقم مسؤول التواصل
            $table->string('email')->nullable(); // البريد الإلكتروني
            $table->string('entity_contact_name')->nullable(); // اسم مسؤول الجهة
            $table->string('entity_source_name')->nullable(); // اسم مصدر الجهة
            $table->date('cooperation_start_date')->nullable(); // تاريخ بدء التعاون
            $table->date('cooperation_end_date')->nullable(); // تاريخ انتهاء التعاون
            $table->string('cooperation_status')->nullable(); // حالة التعاون
            $table->string('priority_level')->nullable(); // درجة الأولوية
            $table->integer('beneficiaries_count')->nullable(); // عدد المستفيدين
            $table->string('entity_rating')->nullable(); // تقييم المتعاملين للجهة
            $table->text('notes')->nullable(); // ملاحظات
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};
