<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Donor;
use App\Models\Donation;
use App\Models\InventoryTransaction;
use App\Models\Beneficiary;
use App\Models\JournalEntryLine;

class ReportsWebController extends Controller
{
    public function index()
    {
        $q = trim(request('q',''));
        if ($q !== '') {
            $donors = Donor::where('name','like',"%$q%")
                ->orWhere('email','like',"%$q%")
                ->orWhere('phone','like',"%$q%")
                ->limit(10)->get();
            $beneficiaries = Beneficiary::where('full_name','like',"%$q%")
                ->orWhere('phone','like',"%$q%")
                ->orWhere('national_id','like',"%$q%")
                ->orWhere('address','like',"%$q%")
                ->limit(10)->get();
            $users = \App\Models\User::where('name','like',"%$q%")
                ->orWhere('email','like',"%$q%")
                ->orWhere('phone','like',"%$q%")
                ->limit(10)->get();
            $items = \App\Models\Item::where('name','like',"%$q%")
                ->orWhere('sku','like',"%$q%")
                ->limit(10)->get();
            $warehouses = \App\Models\Warehouse::where('name','like',"%$q%")
                ->orWhere('location','like',"%$q%")
                ->limit(10)->get();
            $delegates = \App\Models\Delegate::where('name','like',"%$q%")
                ->orWhere('email','like',"%$q%")
                ->orWhere('phone','like',"%$q%")
                ->limit(10)->get();
            $routes = \App\Models\TravelRoute::where('name','like',"%$q%")
                ->orWhere('description','like',"%$q%")
                ->limit(10)->get();
            $tasks = \App\Models\Task::where('title','like',"%$q%")
                ->orWhere('description','like',"%$q%")
                ->limit(10)->get();
            $vhours = \App\Models\VolunteerHour::where('task','like',"%$q%")
                ->limit(10)->get();
            $attendance = \App\Models\VolunteerAttendance::where('notes','like',"%$q%")
                ->limit(10)->get();
            return view('reports.index', compact('q','donors','beneficiaries','users','items','warehouses','delegates','routes','tasks','vhours','attendance'));
        }
        $donorsCount = Donor::count();
        $donorsRecurring = Donor::where('classification','recurring')->count();
        $cash = Donation::where('type','cash')->sum('amount');
        $inKind = Donation::where('type','in_kind')->sum('estimated_value');
        $inventoryNet = InventoryTransaction::select(DB::raw("SUM(CASE WHEN type='in' THEN quantity WHEN type='out' THEN -quantity ELSE 0 END) as net"))->value('net');
        $beneficiariesByStatus = Beneficiary::select('status', DB::raw('COUNT(*) as count'))->groupBy('status')->get();
        $finance = JournalEntryLine::select(DB::raw('SUM(debit) as debit'), DB::raw('SUM(credit) as credit'))->first();
        return view('reports.index', compact('donorsCount','donorsRecurring','cash','inKind','inventoryNet','beneficiariesByStatus','finance'));
    }
}
