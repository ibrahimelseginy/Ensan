<?php

declare(strict_types=1);
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\TravelRoute;
use App\Models\Delegate;
use App\Models\Warehouse;
use App\Models\Project;
use App\Models\Campaign;
use App\Models\GuestHouse;
use App\Models\Beneficiary;

final class TripWebController extends Controller
{
    public function index(Request $request)
    {
        $routeId = $request->input('route_id');
        $q = trim((string) $request->input('q'));
        $type = $request->input('type');
        $delegateId = $request->input('delegate_id');
        $perPage = (int) ($request->input('per_page') ?? 12);
        $printId = $request->input('print_id');
        $sort = (string) ($request->input('sort') ?? 'date_desc');
        $export = (string) ($request->input('export') ?? '');

        $routes = TravelRoute::with(['delegates'])->orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();
        $projects = Project::orderBy('name')
            ->where('name','not like','%دار الضيافة%')
            ->where('name','not like','%ضيافة%')
            ->get();
        $campaigns = Campaign::orderByDesc('season_year')->orderBy('name')->get();
        $guestHouses = GuestHouse::orderBy('name')->get();
        $beneficiaries = Beneficiary::orderBy('full_name')->get(['id','full_name']);
        $routesPayload = $routes->map(function($r){
            return [
                'id' => $r->id,
                'name' => $r->name,
                'cities' => is_array($r->cities ?? null) ? $r->cities : [],
                'delegates' => $r->delegates->map(function($d){ return ['id'=>$d->id,'name'=>$d->name,'phone'=>$d->phone]; })->values()->all(),
            ];
        })->values();

        $delegatesAll = Delegate::orderBy('name')->get();
        $delegatesAllPayload = $delegatesAll->map(function($d){ return ['id'=>$d->id,'name'=>$d->name,'phone'=>$d->phone]; })->values();

        $base = Donation::with(['donor','delegate','route'])
            ->whereNotNull('route_id')
            ->when($routeId, function($qb,$rid){ $qb->where('route_id',$rid); })
            ->when($type, function($qb,$t){ $qb->where('type',$t); })
            ->when($delegateId, function($qb,$did){ $qb->where('delegate_id',$did); })
            ->when($q !== '', function($qb) use ($q){
                $qb->where(function($w) use ($q){
                    $w->whereHas('donor', function($d) use ($q){ $d->where('name','like','%'.$q.'%'); })
                      ->orWhereHas('delegate', function($dl) use ($q){ $dl->where('name','like','%'.$q.'%'); })
                      ->orWhereHas('route', function($r) use ($q){ $r->where('name','like','%'.$q.'%'); });
                });
            });

        $totalCount = (clone $base)->count();
        $totalCash = (clone $base)->where('type','cash')->sum('amount');
        $totalKind = (clone $base)->where('type','in_kind')->sum('estimated_value');
        $stats = [
            'count' => (int) $totalCount,
            'cash' => (float) ($totalCash ?? 0),
            'in_kind' => (float) ($totalKind ?? 0),
        ];

        $ordered = (clone $base);
        if ($sort === 'date_asc') { $ordered->orderBy('received_at','asc')->orderBy('id','asc'); }
        elseif ($sort === 'date_desc') { $ordered->orderBy('received_at','desc')->orderBy('id','desc'); }
        elseif ($sort === 'amount_asc') { $ordered->orderByRaw("CASE WHEN type='cash' THEN COALESCE(amount,0) ELSE COALESCE(estimated_value,0) END ASC"); }
        elseif ($sort === 'amount_desc') { $ordered->orderByRaw("CASE WHEN type='cash' THEN COALESCE(amount,0) ELSE COALESCE(estimated_value,0) END DESC"); }
        else { $ordered->orderBy('received_at','desc')->orderBy('id','desc'); }

        if ($export === 'csv') {
            $rows = (clone $ordered)->limit(5000)->get();
            $filename = 'trips_'.now()->format('Ymd_His').'.csv';
            $headers = ['Content-Type' => 'text/csv; charset=UTF-8','Content-Disposition' => 'attachment; filename="'.$filename.'"'];
            $content = "\xEF\xBB\xBF";
            $cols = ['title','description','date','route','type','amount_or_value','currency','donor','donor_phone','delegate','delegate_phone'];
            $content .= implode(',', $cols)."\n";
            foreach ($rows as $t) {
                $title=null;$desc=null;$cur=null;
                foreach (explode("\n", (string)($t->allocation_note ?? '')) as $line) {
                    if (str_starts_with($line,'مشوار')) { $parts = explode(':', $line, 2); $title = isset($parts[1]) ? trim($parts[1]) : ''; }
                    elseif (str_starts_with($line,'وصف')) { $parts = explode(':', $line, 2); $desc = isset($parts[1]) ? trim($parts[1]) : ''; }
                    elseif (str_starts_with($line,'عملة التبرع')) { $parts = explode(':', $line, 2); $cur = isset($parts[1]) ? trim($parts[1]) : ''; }
                }
                $amtVal = $t->type==='cash' ? ($t->amount ?? 0) : ($t->estimated_value ?? 0);
                $line = [
                    str_replace(["\r","\n"],' ', (string)($title ?? '')),
                    str_replace(["\r","\n"],' ', (string)($desc ?? '')),
                    optional($t->received_at)->format('Y-m-d'),
                    str_replace(["\r","\n"],' ', (string)($t->route->name ?? '')),
                    $t->type,
                    number_format((float)$amtVal, 2, '.', ''),
                    (string) ($cur ?? ''),
                    str_replace(["\r","\n"],' ', (string)($t->donor->name ?? '')),
                    (string) ($t->donor->phone ?? ''),
                    str_replace(["\r","\n"],' ', (string)($t->delegate->name ?? '')),
                    (string) ($t->delegate->phone ?? ''),
                ];
                $content .= implode(',', array_map(function($v){ return (string) $v; }, $line))."\n";
            }
            return response($content, 200, $headers);
        }

        $trips = (clone $ordered)
            ->paginate(max(1,$perPage))->withQueryString();

        $printTrip = null;
        if (!empty($printId)) {
            $printTrip = Donation::with(['donor','delegate','route'])->find($printId);
        }

        return view('trips.index', compact('routes','routesPayload','delegatesAllPayload','trips','routeId','delegateId','q','type','perPage','stats','sort','printTrip','warehouses','projects','campaigns','guestHouses','beneficiaries'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'route_id' => 'required|exists:travel_routes,id',
            'trip_title' => 'required|string',
            'trip_description' => 'nullable|string',
            'trip_date' => 'required|date',
            'donation_type' => 'required|in:cash,in_kind',
            'amount' => 'nullable|numeric',
            'estimated_value' => 'nullable|numeric',
            'donation_currency' => 'nullable|in:EGP,USD,SAR,EUR,AED',
            'donor_name' => 'required|string',
            'donor_phone' => ['required','string','regex:/^(01[0125][0-9]{8})$/'],
            'delegate_id' => 'nullable|exists:delegates,id',
            'trip_location' => 'nullable|string',
            'trip_city' => 'nullable|string',
            'warehouse_id' => 'nullable|exists:warehouses,id|required_if:donation_type,in_kind',
            'alloc_type' => 'nullable|in:project,guest_house,campaign,sponsorship,sadaqa_jariya',
            'project_id' => 'nullable|exists:projects,id',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'guest_house_id' => 'nullable|exists:guest_houses,id',
            'sponsorship_type' => 'nullable|in:طفل,أسرة|required_if:alloc_type,sponsorship',
            'beneficiary_id' => 'nullable|exists:beneficiaries,id|required_if:alloc_type,sponsorship',
        ], [
            'donor_phone.regex' => 'رقم الهاتف يجب أن يكون رقم مصري صحيح (010, 011, 012, 015).',
        ]);

        if ($data['donation_type'] === 'cash' && empty($data['amount'])) {
            return back()->withErrors(['amount' => 'يجب إدخال المبلغ للتبرع النقدي'])->withInput();
        }
        if ($data['donation_type'] === 'in_kind' && empty($data['estimated_value'])) {
            return back()->withErrors(['estimated_value' => 'يجب إدخال القيمة للتبرع العيني'])->withInput();
        }
        if (empty($data['donation_currency'])) {
            return back()->withErrors(['donation_currency' => 'يرجى اختيار العملة'])->withInput();
        }

        $rawPhone = trim((string) ($data['donor_phone'] ?? ''));
        $map = ['٠'=>'0','١'=>'1','٢'=>'2','٣'=>'3','٤'=>'4','٥'=>'5','٦'=>'6','٧'=>'7','٨'=>'8','٩'=>'9'];
        $normalizedPhone = strtr($rawPhone, $map);
        $normalizedPhone = preg_replace('/[^\d\+]/', '', $normalizedPhone);
        $donor = Donor::query()
            ->where(function($qq) use ($data, $normalizedPhone){
                $qq->where('name', $data['donor_name']);
                if ($normalizedPhone !== '') { $qq->orWhere('phone', $normalizedPhone); }
            })
            ->first();
        if (!$donor) {
            $donor = Donor::create([
                'name' => $data['donor_name'],
                'phone' => ($normalizedPhone !== '' ? $normalizedPhone : null),
                'type' => 'individual',
                'active' => true
            ]);
        }
        if ($normalizedPhone !== '' && ($donor->phone ?? '') !== $normalizedPhone) {
            $donor->phone = $normalizedPhone;
            $donor->save();
        }

        $route = TravelRoute::findOrFail($data['route_id']);
        $fare = null; $fareCurrency = null;
        $cities = is_array($route->cities ?? null) ? $route->cities : [];
        if (!empty($data['trip_city'])) {
            foreach ($cities as $c) {
                if (($c['name'] ?? '') === $data['trip_city']) { $fare = $c['fare'] ?? null; $fareCurrency = $c['currency'] ?? null; break; }
            }
        }

        $noteParts = [];
        $noteParts[] = 'مشوار: ' . $data['trip_title'];
        if (!empty($data['trip_description'])) { $noteParts[] = 'وصف: ' . $data['trip_description']; }
        if (!empty($data['trip_location'])) { $noteParts[] = 'مكان: ' . $data['trip_location']; }
        if (!empty($data['donation_currency'])) { $noteParts[] = 'عملة التبرع: ' . $data['donation_currency']; }
        if ($data['donation_type'] === 'cash') { $noteParts[] = 'قناة الاستلام: ' . ($data['delegate_id'] ? 'مندوب' : 'المركز'); }
        $allocationNote = implode("\n", $noteParts);

        $executor = function () use ($donor, $data, $route, $allocationNote) {
            $donation = new Donation();
            $donation->donor_id = $donor->id;
            $donation->type = $data['donation_type'];
            $donation->cash_channel = null;
            $donation->amount = $data['donation_type'] === 'cash' ? (float) ($data['amount'] ?? 0) : null;
            $donation->estimated_value = $data['donation_type'] === 'in_kind' ? (float) ($data['estimated_value'] ?? 0) : null;
            $donation->delegate_id = $data['delegate_id'] ?? null;
            if ($data['donation_type'] === 'in_kind') { $donation->warehouse_id = $data['warehouse_id'] ?? null; }
            if (($data['alloc_type'] ?? '') === 'project' || ($data['alloc_type'] ?? '') === 'sadaqa_jariya') { $donation->project_id = $data['project_id'] ?? null; }
            elseif (($data['alloc_type'] ?? '') === 'campaign') { $donation->campaign_id = $data['campaign_id'] ?? null; }
            elseif (($data['alloc_type'] ?? '') === 'guest_house') { if (\Illuminate\Support\Facades\Schema::hasColumn('donations','guest_house_id')) { $donation->guest_house_id = $data['guest_house_id'] ?? null; } }
            elseif (($data['alloc_type'] ?? '') === 'sponsorship') {
               // The note is already updated in the outer scope
            }
            $donation->route_id = $route->id;
            $donation->allocation_note = $allocationNote;
            $donation->received_at = $data['trip_date'];
            $donation->save();
            return $donation;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Donation::class,
            null,
            'create',
            array_merge($data, ['donor_id' => $donor->id, 'allocation_note' => $allocationNote]),
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب إضافة المشوار/التبرع للإدارة للموافقة.');
        }

        return redirect()->route('trips.index', ['print_id' => $result->id]);
    }
}

