<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration {
    public function up(): void
    {
        DB::table('roles')->insert([
            ['name' => 'Administrator', 'key' => 'admin'],
            ['name' => 'Manager', 'key' => 'manager'],
            ['name' => 'Finance', 'key' => 'finance'],
        ]);

        if (!DB::table('users')->where('email','admin@ensan.local')->exists()) {
            $userId = DB::table('users')->insertGetId([
                'name' => 'System Admin',
                'email' => 'admin@ensan.local',
                'password' => Hash::make('admin123'),
                'is_employee' => true,
                'active' => true,
            ]);
            $adminRoleId = DB::table('roles')->where('key','admin')->value('id');
            DB::table('role_user')->insert(['role_id' => $adminRoleId, 'user_id' => $userId]);
        }

        $accounts = [
            ['code' => '102', 'name' => 'donation_cash', 'type' => 'asset'],
            ['code' => '120', 'name' => 'Inventory - In Kind', 'type' => 'asset'],
            ['code' => '401', 'name' => 'Donations Revenue', 'type' => 'revenue'],
            ['code' => '501', 'name' => 'Operational Expense', 'type' => 'expense'],
            ['code' => '502', 'name' => 'Aid Expense', 'type' => 'expense'],
            ['code' => '503', 'name' => 'Logistics Expense', 'type' => 'expense'],
        ];
        foreach ($accounts as $acc) {
            if (!DB::table('accounts')->where('code',$acc['code'])->exists()) {
                DB::table('accounts')->insert($acc);
            }
        }
    }
    public function down(): void
    {
        DB::table('role_user')->whereIn('role_id', function($q){$q->select('id')->from('roles')->whereIn('key',['admin','manager','finance']);})->delete();
        DB::table('roles')->whereIn('key',['admin','manager','finance'])->delete();
        DB::table('users')->where('email','admin@ensan.local')->delete();
        DB::table('accounts')->whereIn('code',['102','120','401','501','502','503'])->delete();
    }
};
