<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Donor;
use App\Models\Donation;
use App\Models\InventoryTransaction;
use App\Models\Beneficiary;
use App\Models\JournalEntryLine;
use App\Models\Account;

class ReportsController extends Controller
{
    public function donors()
    {
        return [
            'count' => Donor::count(),
            'recurring' => Donor::where('classification','recurring')->count(),
            'one_time' => Donor::where('classification','one_time')->count(),
        ];
    }

    public function donations()
    {
        $cash = Donation::where('type','cash')->sum('amount');
        $inKind = Donation::where('type','in_kind')->sum('estimated_value');
        $byProject = Donation::select('project_id', DB::raw('SUM(COALESCE(amount, estimated_value, 0)) as total'))
            ->groupBy('project_id')->get();
        $byCampaign = Donation::select('campaign_id', DB::raw('SUM(COALESCE(amount, estimated_value, 0)) as total'))
            ->groupBy('campaign_id')->get();
        return compact('cash','inKind','byProject','byCampaign');
    }

    public function inventory()
    {
        $byItemWarehouse = InventoryTransaction::select('item_id','warehouse_id',
            DB::raw("SUM(CASE WHEN type='in' THEN quantity WHEN type='out' THEN -quantity ELSE 0 END) as net_quantity"))
            ->groupBy('item_id','warehouse_id')->get();
        return compact('byItemWarehouse');
    }

    public function beneficiaries()
    {
        $statuses = Beneficiary::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')->get();
        return compact('statuses');
    }

    public function finance()
    {
        $lines = JournalEntryLine::join('accounts','accounts.id','=','journal_entry_lines.account_id')
            ->select('accounts.type', DB::raw('SUM(debit) as total_debit'), DB::raw('SUM(credit) as total_credit'))
            ->groupBy('accounts.type')->get();
        return compact('lines');
    }
}
