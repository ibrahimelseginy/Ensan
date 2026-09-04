<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donation_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->integer('sort_order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('donation_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('image')->nullable();
            $table->boolean('status')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('donation_categories')->onDelete('cascade');
        });

        // Seed default categories
        $categories = [
            ['name' => 'المشاريع',   'slug' => 'project',    'sort_order' => 1],
            ['name' => 'الحملات',    'slug' => 'campaign',   'sort_order' => 2],
            ['name' => 'دار الضيافة','slug' => 'dar_diyafa', 'sort_order' => 3],
            ['name' => 'الكفالة',    'slug' => 'kafala',     'sort_order' => 4],
            ['name' => 'صدقة جارية', 'slug' => 'sadaqa',     'sort_order' => 5],
            ['name' => 'عام',        'slug' => 'general',    'sort_order' => 6],
        ];

        foreach ($categories as $cat) {
            DB::table('donation_categories')->insert(array_merge($cat, [
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('donation_items');
        Schema::dropIfExists('donation_categories');
    }
};
