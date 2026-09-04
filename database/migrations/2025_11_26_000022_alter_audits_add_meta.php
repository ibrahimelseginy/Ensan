<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('audits', function (Blueprint $table) {
            if (!Schema::hasColumn('audits','status_code')) {
                $table->integer('status_code')->nullable()->after('path');
            }
            if (!Schema::hasColumn('audits','ip')) {
                $table->string('ip', 45)->nullable()->after('status_code');
            }
            if (!Schema::hasColumn('audits','user_agent')) {
                $table->text('user_agent')->nullable()->after('ip');
            }
        });
    }
    public function down(): void
    {
        Schema::table('audits', function (Blueprint $table) {
            if (Schema::hasColumn('audits','user_agent')) { $table->dropColumn('user_agent'); }
            if (Schema::hasColumn('audits','ip')) { $table->dropColumn('ip'); }
            if (Schema::hasColumn('audits','status_code')) { $table->dropColumn('status_code'); }
        });
    }
};

