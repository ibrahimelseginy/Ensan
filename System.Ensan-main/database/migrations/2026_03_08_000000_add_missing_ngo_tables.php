<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. سجلات الدفع الإلكتروني (Online Payment Logs)
        // هذا الجدول ضروري جداً لتتبع عمليات الدفع عبر بوابات الدفع (مثل Paymob, Fawry, Stripe)
        // وحفظ حالة العملية (ناجحة، فاشلة، معلقة) وتفاصيل الرد من البوابة.
        if (!Schema::hasTable('payment_transactions')) {
            Schema::create('payment_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('donor_id')->nullable()->constrained('donors')->nullOnDelete();
                $table->foreignId('donation_id')->nullable()->constrained('donations')->nullOnDelete();
                $table->decimal('amount', 12, 2);
                $table->string('currency')->default('EGP');
                $table->string('gateway'); // e.g., paymob, fawry, stripe
                $table->string('transaction_id')->nullable()->index(); // Gateway reference ID
                $table->string('status')->default('pending'); // pending, success, failed, refunded
                $table->json('gateway_response')->nullable();
                $table->string('payment_method')->nullable(); // visa, wallet, instapay
                $table->text('error_message')->nullable();
                $table->timestamps();
            });
        }

        // 2. سجلات الإشعارات والرسائل (SMS & Email Logs)
        // لتتبع حالة إرسال الرسائل النصية والإشعارات، وتقارير الوصول (Delivered/Failed)
        if (!Schema::hasTable('notification_logs')) {
            Schema::create('notification_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->string('type'); // sms, email, push
                $table->string('provider')->nullable(); // e.g., twilio, firebase, victorylink
                $table->text('message');
                $table->string('status')->default('sent'); // sent, delivered, failed
                $table->json('provider_response')->nullable();
                $table->timestamps();
            });
        }
        
        // 3. إضافة حقل ربط الدفع لجدول التبرعات إن لم يكن موجوداً
        if (Schema::hasTable('donations') && !Schema::hasColumn('donations', 'payment_transaction_id')) {
            Schema::table('donations', function (Blueprint $table) {
                $table->unsignedBigInteger('payment_transaction_id')->nullable()->after('amount');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
        Schema::dropIfExists('notification_logs');
        
        if (Schema::hasTable('donations') && Schema::hasColumn('donations', 'payment_transaction_id')) {
            Schema::table('donations', function (Blueprint $table) {
                $table->dropColumn('payment_transaction_id');
            });
        }
    }
};
