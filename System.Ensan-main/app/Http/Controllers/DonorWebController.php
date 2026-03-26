<?php
namespace App\Http\Controllers;

use App\Models\Donor;
use App\Models\Donation;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DonorWebController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->input('q'));
        $type = $request->input('type');
        $classification = $request->input('classification');
        $active = $request->input('active');

        $donors = Donor::query()
            ->when($q !== '', function ($q2) use ($q) {
            $q2->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%$q%")
                        ->orWhere('phone', 'like', "%$q%");
                }
                );
            })
            ->when($type, function ($q2, $t) {
            $q2->where('type', $t);
        })
            ->when($classification, function ($q2, $c) {
            $q2->where('classification', $c);
        })
            ->when(!is_null($active) && $active !== '', function ($q2, $a) {
            $q2->where('active', (bool)$a);
        })
            ->orderByDesc('id')->paginate(12)->withQueryString();

        // Add pending check for each donor
        $donors->each(function ($d) {
            $d->pendingRequest = \App\Models\ChangeRequest::where('model_type', \App\Models\Donor::class)
                ->where('model_id', $d->id)
                ->where('status', 'pending')
                ->first();
        });

        $donStats = Donation::select('donor_id', DB::raw('COUNT(*) as count'), DB::raw('SUM(COALESCE(amount, estimated_value, 0)) as total'))
            ->where('status', '!=', 'cancelled')
            ->groupBy('donor_id')->get()->keyBy('donor_id');

        $totals = [
            'all' => Donor::count(),
            'active' => Donor::where('active', true)->count(),
            'recurring' => Donor::where('classification', 'recurring')->count(),
            'one_time' => Donor::where('classification', 'one_time')->count(),
        ];

        $allDonors = Donor::orderBy('name')->get();
        $selectedDonorId = $request->input('selected_donor_id');
        $selectedDonor = null;
        $donationsHistory = collect();
        $paidThisMonth = 0.0;
        if ($selectedDonorId) {
            $selectedDonor = Donor::find($selectedDonorId);
            if ($selectedDonor) {
                $donationsHistory = Donation::with(['project', 'campaign'])
                    ->where('donor_id', $selectedDonor->id)
                    ->where('status', '!=', 'cancelled')
                    ->orderByDesc('received_at')->orderByDesc('id')
                    ->limit(10)->get();
                $paidThisMonth = Donation::where('donor_id', $selectedDonor->id)
                    ->where('type', 'cash')
                    ->where('status', '!=', 'cancelled')
                    ->when($selectedDonor->sponsorship_project_id, function ($q) use ($selectedDonor) {
                    $q->where('project_id', $selectedDonor->sponsorship_project_id);
                })
                    ->whereBetween('received_at', [now()->startOfMonth(), now()->endOfMonth()])
                    ->sum('amount');
            }
        }

        $warehouses = \App\Models\Warehouse::orderBy('name')->get();
        $projects = \App\Models\Project::query()
            ->where(function ($q) {
            $q->where('name', 'like', '%بعثاء%')
                ->orWhere('name', 'like', '%زاد%')
                ->orWhere('name', 'like', '%مدرار%')
                ->orWhere('name', 'like', '%كسو%');
        })
            ->where(function ($q) {
            $q->where('name', 'not like', '%ضياف%')
                ->where('name', 'not like', '%دار الضيا%')
                ->where('name', 'not like', '%Guest%');
        })
            ->orderBy('name')->get();
        $campaigns = \App\Models\Campaign::orderByDesc('season_year')->orderBy('name')->get();
        $guestHouses = \App\Models\GuestHouse::where(function ($q) {
            $q->where('location', 'like', '%كفر%')
                ->orWhere('location', 'like', '%طنطا%')
                ->orWhere('name', 'like', '%كفر%')
                ->orWhere('name', 'like', '%طنطا%');
        })->orderBy('name')->get();
        $beneficiaries = \App\Models\Beneficiary::orderBy('full_name')->get();
        return view('donors.index', compact('donors', 'donStats', 'totals', 'q', 'type', 'classification', 'active', 'allDonors', 'selectedDonor', 'donationsHistory', 'selectedDonorId', 'warehouses', 'paidThisMonth', 'projects', 'campaigns', 'guestHouses', 'beneficiaries'));
    }
    public function create()
    {
        $beneficiaries = \App\Models\Beneficiary::orderBy('full_name')->get();
        $projects = \App\Models\Project::query()
            ->where(function ($q) {
            $q->where('name', 'like', '%بعثاء%')
                ->orWhere('name', 'like', '%زاد%')
                ->orWhere('name', 'like', '%مدرار%')
                ->orWhere('name', 'like', '%كسو%');
        })
            ->orderBy('name')->get();
        return view('donors.create', compact('beneficiaries', 'projects'));
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:donors,name',
            'type' => 'required|in:individual,organization',
            'phone' => ['required', 'string', 'unique:donors,phone', 'regex:/^(01[0125][0-9]{8})$/'],
            'address' => 'required|string',
            'classification' => 'required|in:one_time,recurring',
            'recurring_cycle' => 'nullable|in:monthly,yearly',
            'active' => 'boolean',
            'sponsorship_type' => 'nullable|in:none,monthly_sponsor,yearly_sponsor,sadaqa_jariya',
            'sponsored_beneficiary_id' => 'nullable|exists:beneficiaries,id',
            'sponsorship_project_id' => 'nullable|exists:projects,id',
            'sponsorship_monthly_amount' => 'nullable|numeric',
            'allocation_type' => 'nullable|in:project,campaign,guest_house,sponsorship,sadaqa_jariya',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'guest_house_id' => 'nullable|exists:guest_houses,id',
        ], [
            'name.unique' => 'اسم المتبرع هذا مسجل مسبقاً، الرجاء اختيار اسم آخر أو البحث عنه.',
            'phone.unique' => 'رقم الهاتف هذا مسجل مسبقاً لمتبرع آخر.',
            'phone.regex' => 'رقم الهاتف يجب أن يكون رقم مصري صحيح (010, 011, 012, 015).',
        ]);
        // ... validation ...
        $phone = isset($data['phone']) ? trim((string)$data['phone']) : null;

        // Handle logic for allocation type if needed
        if (($data['sponsorship_type'] ?? 'none') !== 'none' && empty($data['sponsorship_project_id'])) {
            $defaultProjId = \App\Models\Project::where('name', 'بعثاء الأمل')->value('id');
            if ($defaultProjId) {
                $data['sponsorship_project_id'] = $defaultProjId;
            }
        }

        // Map quick form inputs (alloc_type, alloc_id) to model attributes if provided via quick form
        if ($request->filled('alloc_type') && empty($data['allocation_type'])) {
            $data['allocation_type'] = $request->input('alloc_type');
            $aid = $request->input('alloc_id');

            if ($data['allocation_type'] === 'project' || $data['allocation_type'] === 'sadaqa_jariya') {
                $data['sponsorship_project_id'] = $aid;
            }
            elseif ($data['allocation_type'] === 'campaign') {
                $data['campaign_id'] = $aid;
            }
            elseif ($data['allocation_type'] === 'guest_house') {
                $data['guest_house_id'] = $aid;
            }
        }

        $executor = function () use ($data) {
            return Donor::create($data);
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Donor::class ,
            null,
            'create',
            $data,
            $executor
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب إضافة المتبرع للموافقة.');
        }

        $donor = $result;

        if ($request->input('return_to') === 'donations.create') {
            $allocType = $request->input('alloc_type');
            $allocId = $request->input('alloc_id');

            $params = ['donor_id' => $donor->id];

            if ($allocType && $allocId) {
                if ($allocType === 'project') {
                    $params['project_id'] = $allocId;
                }
                elseif ($allocType === 'guest_house') {
                    $params['guest_house_id'] = $allocId;
                }
                elseif ($allocType === 'campaign') {
                    $params['campaign_id'] = $allocId;
                }
            }
            else {
                // Check internal fields if alloc_type hidden inputs failed
                if ($request->filled('guest_house_id')) {
                    $params['guest_house_id'] = $request->input('guest_house_id');
                }
                elseif ($request->filled('campaign_id')) {
                    $params['campaign_id'] = $request->input('campaign_id');
                }
            }

            // Pass sponsorship info if available
            if ($request->filled('sponsorship_type') && $request->input('sponsorship_type') !== 'none') {
                $params['sponsorship_type'] = $request->input('sponsorship_type');
                if ($request->filled('sponsored_beneficiary_id')) {
                    $params['beneficiary_id'] = $request->input('sponsored_beneficiary_id');
                }
            }

            return redirect()->route('donations.create', $params);
        }
        return redirect()->route('donors.show', $donor);
    }
    public function show(Donor $donor)
    {
        $pending = \App\Models\ChangeRequest::where('model_type', \App\Models\Donor::class)
            ->where('model_id', $donor->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذا المتبرع لديه طلب مراجعة حالياً');
        }

        $stats = Donation::select(DB::raw('COUNT(*) as count'), DB::raw('SUM(COALESCE(amount, estimated_value, 0)) as total'))
            ->where('donor_id', $donor->id)
            ->where('status', '!=', 'cancelled')
            ->first();
        $paidThisMonth = Donation::where('donor_id', $donor->id)
            ->where('type', 'cash')
            ->where('status', '!=', 'cancelled')
            ->when($donor->sponsorship_project_id, function ($q) use ($donor) {
            $q->where('project_id', $donor->sponsorship_project_id);
        })
            ->whereBetween('received_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('amount');
        $history = Donation::with(['project', 'campaign', 'warehouse'])
            ->where('donor_id', $donor->id)
            ->orderByDesc('received_at')->orderByDesc('id')
            ->get();
        return view('donors.show', compact('donor', 'stats', 'paidThisMonth', 'history'));
    }
    public function edit(Donor $donor)
    {
        $pending = \App\Models\ChangeRequest::where('model_type', \App\Models\Donor::class)
            ->where('model_id', $donor->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذا المتبرع لديه طلب مراجعة حالياً');
        }

        $beneficiaries = \App\Models\Beneficiary::orderBy('full_name')->get();
        $projects = \App\Models\Project::query()
            ->where(function ($q) {
            $q->where('name', 'like', '%بعثاء%')
                ->orWhere('name', 'like', '%زاد%')
                ->orWhere('name', 'like', '%مدرار%')
                ->orWhere('name', 'like', '%كسو%');
        })
            ->orderBy('name')->get();
        $campaigns = \App\Models\Campaign::orderByDesc('season_year')->orderBy('name')->get();
        $guestHouses = \App\Models\GuestHouse::orderBy('name')->get();
        return view('donors.edit', compact('donor', 'beneficiaries', 'projects', 'campaigns', 'guestHouses'));
    }
    public function update(Request $request, Donor $donor)
    {
        $pending = \App\Models\ChangeRequest::where('model_type', \App\Models\Donor::class)
            ->where('model_id', $donor->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذا المتبرع لديه طلب مراجعة حالياً');
        }

        $data = $request->validate([
            'name' => 'sometimes|string|unique:donors,name,' . $donor->id,
            'type' => 'sometimes|in:individual,organization',
            'phone' => ['nullable', 'string', 'unique:donors,phone,' . $donor->id, 'regex:/^(01[0125][0-9]{8})$/'],
            'address' => 'nullable|string',
            'classification' => 'sometimes|in:one_time,recurring',
            'recurring_cycle' => 'nullable|in:monthly,yearly',
            'active' => 'boolean',
            'sponsorship_type' => 'nullable|in:none,monthly_sponsor,yearly_sponsor,sadaqa_jariya',
            'sponsored_beneficiary_id' => 'nullable|exists:beneficiaries,id',
            'sponsorship_project_id' => 'nullable|exists:projects,id',
            'sponsorship_monthly_amount' => 'nullable|numeric',
            'allocation_type' => 'nullable|in:project,campaign,guest_house,sponsorship,sadaqa_jariya',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'guest_house_id' => 'nullable|exists:guest_houses,id',
        ], [
            'name.unique' => 'اسم المتبرع هذا مسجل مسبقاً لمتبرع آخر.',
            'phone.unique' => 'رقم الهاتف هذا مسجل مسبقاً لمتبرع آخر.',
            'phone.regex' => 'رقم الهاتف يجب أن يكون رقم مصري صحيح (010, 011, 012, 015).',
        ]);
        if (($data['sponsorship_type'] ?? $donor->sponsorship_type ?? 'none') !== 'none' && empty($data['sponsorship_project_id'])) {
            $defaultProjId = \App\Models\Project::where('name', 'بعثاء الأمل')->value('id');
            if ($defaultProjId) {
                $data['sponsorship_project_id'] = $defaultProjId;
            }
        }

        $executor = function () use ($donor, $data) {
            $donor->update($data);
            return $donor;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Donor::class ,
            $donor->id,
            'update',
            $data,
            $executor,
            true // Force request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تعديل المتبرع للموافقة.');
        }

        return redirect()->route('donors.show', $donor)->with('success', 'تم تعديل المتبرع بنجاح.');
    }
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:donors,id'
        ]);

        Donor::whereIn('id', $request->ids)->delete();

        return back()->with('success', 'تم حذف المتبرعين المحددين بنجاح');
    }

    public function destroy(Donor $donor)
    {
        $pending = \App\Models\ChangeRequest::where('model_type', \App\Models\Donor::class)
            ->where('model_id', $donor->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذا المتبرع لديه طلب مراجعة حالياً');
        }

        $executor = function () use ($donor) {
            $donor->delete();
            return true;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Donor::class ,
            $donor->id,
            'delete',
            request()->all(), // Capture reason if sent
            $executor,
            true
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب حذف المتبرع للموافقة.');
        }

        return redirect()->route('donors.index')->with('success', 'تم حذف المتبرع بنجاح.');
    }
}
