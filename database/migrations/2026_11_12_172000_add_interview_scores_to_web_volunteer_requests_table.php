<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('web_volunteer_requests')) {
            Schema::table('web_volunteer_requests', function (Blueprint $table) {
                if (!Schema::hasColumn('web_volunteer_requests', 'commitment_score')) {
                    $table->unsignedTinyInteger('commitment_score')->nullable()->after('interview_location');
                    $table->unsignedTinyInteger('teamwork_score')->nullable()->after('commitment_score');
                    $table->unsignedTinyInteger('responsibility_score')->nullable()->after('teamwork_score');
                    $table->unsignedTinyInteger('communication_score')->nullable()->after('responsibility_score');
                    $table->unsignedTinyInteger('enthusiasm_score')->nullable()->after('communication_score');
                    $table->unsignedTinyInteger('project_fit_score')->nullable()->after('enthusiasm_score');
                    $table->string('interview_decision', 50)->nullable()->after('project_fit_score');
                    $table->text('interview_conditions')->nullable()->after('interview_decision');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('web_volunteer_requests')) {
            Schema::table('web_volunteer_requests', function (Blueprint $table) {
                $table->dropColumn([
                    'commitment_score',
                    'teamwork_score',
                    'responsibility_score',
                    'communication_score',
                    'enthusiasm_score',
                    'project_fit_score',
                    'interview_decision',
                    'interview_conditions',
                ]);
            });
        }
    }
};

