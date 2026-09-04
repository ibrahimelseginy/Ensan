<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            if (!Schema::hasColumn('campaigns','project_id')) {
                $table->unsignedBigInteger('project_id')->nullable()->after('id');
            }
        });
    }
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            if (Schema::hasColumn('campaigns','project_id')) {
                $table->dropColumn('project_id');
            }
        });
    }
};
