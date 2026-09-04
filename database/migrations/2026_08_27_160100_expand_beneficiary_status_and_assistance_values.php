<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE beneficiaries MODIFY assistance_type VARCHAR(30) NOT NULL DEFAULT 'monthly'");
            DB::statement("ALTER TABLE beneficiaries MODIFY status VARCHAR(40) NOT NULL DEFAULT 'new'");
            return;
        }

        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->string('assistance_type', 30)->default('monthly')->change();
            $table->string('status', 40)->default('new')->change();
        });
    }

    public function down(): void
    {
        DB::table('beneficiaries')->whereIn('assistance_type', ['monthly', 'one_time'])->update(['assistance_type' => 'financial']);
        DB::table('beneficiaries')->whereIn('status', ['pending', 'rejected', 'archived_improved', 'archived_deceased'])->update(['status' => 'accepted']);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE beneficiaries MODIFY assistance_type ENUM('financial','in_kind','service') NOT NULL");
            DB::statement("ALTER TABLE beneficiaries MODIFY status ENUM('new','under_review','accepted') NOT NULL DEFAULT 'new'");
            return;
        }

        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->string('assistance_type')->change();
            $table->string('status')->default('new')->change();
        });
    }
};
