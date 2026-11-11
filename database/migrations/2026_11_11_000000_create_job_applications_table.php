<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('job_applications')) {
            Schema::create('job_applications', function (Blueprint $table) {
                $table->id();
                $table->string('tracking_code', 40)->unique();
                $table->string('full_name', 191);
                $table->unsignedTinyInteger('age')->nullable();
                $table->string('phone', 30)->index();
                $table->string('email', 191)->nullable();
                $table->string('residence', 191)->nullable();
                $table->string('position', 191);
                $table->string('faculty', 191)->nullable();
                $table->boolean('has_volunteered')->default(false);
                $table->text('volunteer_details')->nullable();
                $table->string('cv_path', 500)->nullable();
                $table->string('status', 30)->default('new')->index();
                $table->dateTime('interview_date')->nullable();
                $table->string('interview_location', 191)->nullable();
                $table->text('hr_notes')->nullable();
                $table->text('applicant_message')->nullable();
                $table->unsignedBigInteger('reviewed_by')->nullable();
                $table->dateTime('reviewed_at')->nullable();
                $table->string('source', 30)->default('website');
                $table->timestamps();
            });
        }

        // صلاحيات قسم طلبات التوظيف
        if (Schema::hasTable('permissions')) {
            $now = now();
            foreach ([
                'job_applications.view'   => 'عرض طلبات التوظيف الواردة من الموقع',
                'job_applications.manage' => 'مراجعة طلبات التوظيف وتغيير حالتها (قبول / رفض / مقابلة)',
                'job_applications.delete' => 'حذف طلبات التوظيف',
            ] as $key => $name) {
                if (!DB::table('permissions')->where('key', $key)->exists()) {
                    DB::table('permissions')->insert([
                        'key' => $key,
                        'name' => $name,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};

