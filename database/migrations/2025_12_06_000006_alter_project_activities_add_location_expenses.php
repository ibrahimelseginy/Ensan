<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('project_activities', function (Blueprint $table) {
            $table->string('location')->nullable()->after('type');
            $table->decimal('expenses', 12, 2)->default(0)->after('revenue');
        });
    }

    public function down()
    {
        Schema::table('project_activities', function (Blueprint $table) {
            $table->dropColumn(['location', 'expenses']);
        });
    }
};
