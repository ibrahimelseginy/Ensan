<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('department')->nullable()->after('phone');
            $table->string('job_title')->nullable()->after('department');
            $table->decimal('salary', 12, 2)->nullable()->after('job_title');
            $table->date('join_date')->nullable()->after('salary');
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->after('id')->constrained('accounts')->nullOnDelete();
            $table->text('description')->nullable()->after('name');
        });
        
        Schema::table('journal_entries', function (Blueprint $table) {
             $table->text('description')->nullable()->after('entry_type');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['department', 'job_title', 'salary', 'join_date']);
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'description']);
        });
        
        Schema::table('journal_entries', function (Blueprint $table) {
             $table->dropColumn(['description']);
        });
    }
};
