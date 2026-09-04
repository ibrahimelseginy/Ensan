<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('projects')) { return; }
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects','manager_user_id')) {
                $table->unsignedBigInteger('manager_user_id')->nullable()->after('description');
            }
            if (!Schema::hasColumn('projects','deputy_user_id')) {
                $table->unsignedBigInteger('deputy_user_id')->nullable()->after('manager_user_id');
            }
            if (!Schema::hasColumn('projects','manager_photo_url')) {
                $table->string('manager_photo_url', 255)->nullable()->after('deputy_user_id');
            }
            if (!Schema::hasColumn('projects','deputy_photo_url')) {
                $table->string('deputy_photo_url', 255)->nullable()->after('manager_photo_url');
            }
        });
    }
    public function down(): void
    {
        if (!Schema::hasTable('projects')) { return; }
        Schema::table('projects', function (Blueprint $table) {
            foreach (['manager_user_id','deputy_user_id','manager_photo_url','deputy_photo_url'] as $col) {
                if (Schema::hasColumn('projects',$col)) { $table->dropColumn($col); }
            }
        });
    }
};

