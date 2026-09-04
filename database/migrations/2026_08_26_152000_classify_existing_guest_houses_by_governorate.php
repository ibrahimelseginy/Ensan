<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('guest_houses')->whereNull('governorate')->where(function ($query) {
            $query->where('name', 'like', '%كفر الشيخ%')->orWhere('location', 'like', '%كفر الشيخ%');
        })->update(['governorate' => 'كفر الشيخ']);

        DB::table('guest_houses')->whereNull('governorate')->where(function ($query) {
            $query->where('name', 'like', '%طنطا%')->orWhere('name', 'like', '%الغربية%')
                ->orWhere('location', 'like', '%طنطا%')->orWhere('location', 'like', '%الغربية%');
        })->update(['governorate' => 'الغربية']);
    }

    public function down(): void
    {
        // Classification is retained on rollback to avoid removing user-confirmed data.
    }
};
