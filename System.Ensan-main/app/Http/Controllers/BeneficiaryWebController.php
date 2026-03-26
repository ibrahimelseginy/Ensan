<?php
namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\Project;
use App\Models\Campaign;
use Illuminate\Http\Request;

class BeneficiaryWebController extends Controller
{
    public function index(Request $request)
    {
        $q = (string)$request->get('q', '');
        $status = (string)$request->get('status', '');
        $atype = (string)$request->get('assistance_type', '');
        $projectId = $request->get('project_id');
        $campaignId = $request->get('campaign_id');
        $guestHouseId = $request->get('guest_house_id');
        $sort = (string)$request->get('sort', 'id');
        $dir = strtolower((string)$request->get('dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $perPage = (int)($request->get('per_page', 20));
        $dateFrom = (string)$request->get('date_from', '');
        $dateTo = (string)$request->get('date_to', '');
        $hasPhone = (string)$request->get('has_phone', '');
        $hasAttachments = (string)$request->get('has_attachments', '');
        $addressLike = (string)$request->get('address_like', '');
        if (!in_array($perPage, [20, 50, 100], true)) {
            $perPage = 20;
        }
        $sortable = ['id', 'full_name', 'created_at', 'status', 'assistance_type'];
        if (!in_array($sort, $sortable, true)) {
            $sort = 'id';
        }

        $beneficiaries = Beneficiary::query()
            ->with(['project', 'campaign'])
            ->withCount('attachments')
            ->when($q !== '', function ($qr) use ($q) {
            $qr->where(function ($w) use ($q) {
                    $w->where('full_name', 'like', '%' . $q . '%')
                        ->orWhere('code', 'like', '%' . $q . '%')
                        ->orWhere('phone', 'like', '%' . $q . '%')
                        ->orWhere('national_id', 'like', '%' . $q . '%');
                }
                );
            })
            ->when($status !== '', function ($qr) use ($status) {
            $qr->where('status', $status);
        })
            ->when($atype !== '', function ($qr) use ($atype) {
            $qr->where('assistance_type', $atype);
        })
            ->when(!empty($projectId), function ($qr) use ($projectId) {
            $qr->where('project_id', $projectId);
        })
            ->when(!empty($campaignId), function ($qr) use ($campaignId) {
            $qr->where('campaign_id', $campaignId);
        })
            ->when(!empty($guestHouseId), function ($qr) use ($guestHouseId) {
            $qr->where('guest_house_id', $guestHouseId);
        })
            ->when($dateFrom !== '' && $dateTo !== '', function ($qr) use ($dateFrom, $dateTo) {
            $qr->whereBetween('created_at', [$dateFrom, $dateTo]);
        })
            ->when($dateFrom !== '' && $dateTo === '', function ($qr) use ($dateFrom) {
            $qr->whereDate('created_at', '>=', $dateFrom);
        })
            ->when($dateTo !== '' && $dateFrom === '', function ($qr) use ($dateTo) {
            $qr->whereDate('created_at', '<=', $dateTo);
        })
            ->when($hasPhone === '1', function ($qr) {
            $qr->whereNotNull('phone')->where('phone', '<>', '');
        })
            ->when($addressLike !== '', function ($qr) use ($addressLike) {
            $qr->where('address', 'like', '%' . $addressLike . '%');
        })
            ->when($hasAttachments === '1', function ($qr) {
            $qr->having('attachments_count', '>', 0);
        })
            ->orderBy($sort, $dir)
            ->paginate($perPage)
            ->appends(['q' => $q, 'status' => $status, 'assistance_type' => $atype, 'project_id' => $projectId, 'campaign_id' => $campaignId, 'guest_house_id' => $guestHouseId, 'sort' => $sort, 'dir' => $dir, 'per_page' => $perPage, 'date_from' => $dateFrom, 'date_to' => $dateTo, 'has_phone' => $hasPhone, 'has_attachments' => $hasAttachments, 'address_like' => $addressLike]);

        // Add pending check for each beneficiary
        $beneficiaries->each(function ($b) {
            $b->pendingRequest = \App\Models\ChangeRequest::where('model_type', \App\Models\Beneficiary::class)
                ->where('model_id', $b->id)
                ->where('status', 'pending')
                ->first();
        });

        $projects = Project::query()
            ->where(function ($q) {
            $q->where('name', 'like', '%بعثاء%')
                ->orWhere('name', 'like', '%زاد%')
                ->orWhere('name', 'like', '%مدرار%')
                ->orWhere('name', 'like', '%كسو%');
        })
            ->orderBy('name')->get();
        $campaigns = Campaign::orderByDesc('season_year')->orderBy('name')->get();

        $stats = [
            'total' => Beneficiary::count(),
            'new' => Beneficiary::where('status', 'new')->count(),
            'under_review' => Beneficiary::where('status', 'under_review')->count(),
            'accepted' => Beneficiary::where('status', 'accepted')->count(),
            'rejected' => Beneficiary::where('status', 'rejected')->count(),
        ];

        // كشف التكرار
        $dupPhones = \DB::table('beneficiaries')->select('phone')->whereNotNull('phone')->where('phone', '<>', '')->groupBy('phone')->havingRaw('COUNT(*)>1')->pluck('phone')->all();
        $dupNids = \DB::table('beneficiaries')->select('national_id')->whereNotNull('national_id')->where('national_id', '<>', '')->groupBy('national_id')->havingRaw('COUNT(*)>1')->pluck('national_id')->all();

        // مؤشرات مساعدة
        $assistDist = [
            'financial' => (int)Beneficiary::where('assistance_type', 'financial')->count(),
            'in_kind' => (int)Beneficiary::where('assistance_type', 'in_kind')->count(),
            'service' => (int)Beneficiary::where('assistance_type', 'service')->count(),
        ];
        $last7 = (int)Beneficiary::whereBetween('created_at', [now()->subDays(7), now()])->count();

        return view('beneficiaries.index', compact('beneficiaries', 'projects', 'campaigns', 'q', 'status', 'atype', 'projectId', 'campaignId', 'stats', 'sort', 'dir', 'perPage', 'dateFrom', 'dateTo', 'hasPhone', 'hasAttachments', 'addressLike', 'dupPhones', 'dupNids', 'assistDist', 'last7'));
    }
    public function export(Request $request)
    {
        $q = (string)$request->get('q', '');
        $status = (string)$request->get('status', '');
        $atype = (string)$request->get('assistance_type', '');
        $projectId = $request->get('project_id');
        $campaignId = $request->get('campaign_id');
        $sort = (string)$request->get('sort', 'id');
        $dir = strtolower((string)$request->get('dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $sortable = ['id', 'full_name', 'created_at', 'status', 'assistance_type'];
        if (!in_array($sort, $sortable, true)) {
            $sort = 'id';
        }

        $rows = Beneficiary::query()
            ->with(['project', 'campaign'])
            ->when($q !== '', function ($qr) use ($q) {
            $qr->where(function ($w) use ($q) {
                    $w->where('full_name', 'like', '%' . $q . '%')
                        ->orWhere('code', 'like', '%' . $q . '%')
                        ->orWhere('phone', 'like', '%' . $q . '%')
                        ->orWhere('national_id', 'like', '%' . $q . '%');
                }
                );
            })
            ->when($status !== '', function ($qr) use ($status) {
            $qr->where('status', $status);
        })
            ->when($atype !== '', function ($qr) use ($atype) {
            $qr->where('assistance_type', $atype);
        })
            ->when(!empty($projectId), function ($qr) use ($projectId) {
            $qr->where('project_id', $projectId);
        })
            ->when(!empty($campaignId), function ($qr) use ($campaignId) {
            $qr->where('campaign_id', $campaignId);
        })
            ->orderBy($sort, $dir)
            ->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="beneficiaries.csv"'
        ];
        $callback = function () use ($rows) {
            echo "\xEF\xBB\xBF";
            $out = fopen('php://output', 'w');
            fputcsv($out, ['#', 'الاسم', 'الرقم القومي', 'الهاتف', 'الحالة', 'نوع المساعدة', 'المشروع', 'الحملة', 'تاريخ الإنشاء']);
            foreach ($rows as $b) {
                fputcsv($out, [
                    $b->id,
                    $b->full_name,
                    $b->national_id,
                    $b->phone,
                    $b->status,
                    $b->assistance_type,
                    optional($b->project)->name,
                    optional($b->campaign)->name,
                    optional($b->created_at)->format('Y-m-d')
                ]);
            }
            fclose($out);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function create()
    {
        $projects = Project::query()->where(function ($q) {
            $q->where('name', 'like', '%بعثاء%')->orWhere('name', 'like', '%زاد%')->orWhere('name', 'like', '%مدرار%')->orWhere('name', 'like', '%كسو%');
        })->orderBy('name')->get();
        $campaigns = Campaign::orderByDesc('season_year')->orderBy('name')->get();
        $guestHouses = \App\Models\GuestHouse::orderBy('name')->get();
        return view('beneficiaries.create', compact('projects', 'campaigns', 'guestHouses'));
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'nullable|string|unique:beneficiaries,code',
            'full_name' => 'required|string',
            'national_id' => 'nullable|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'assistance_type' => 'required|in:financial,in_kind,service',
            'status' => 'in:new,under_review,accepted,rejected',
            'project_id' => 'nullable|exists:projects,id',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'guest_house_id' => 'nullable|exists:guest_houses,id',
            'notes' => 'nullable|string',
            'rejection_reason' => 'nullable|string',
            'allocation_type' => 'nullable|string',
            'child_sponsorship_type' => 'nullable|string'
        ]);
        // ... validation ...
        $data['status'] = $data['status'] ?? 'new';
        if (empty($data['code'])) {
            $data['code'] = 'BEN-' . strtoupper(\Illuminate\Support\Str::random(6));
        }

        $executor = function () use ($data) {
            return Beneficiary::create($data);
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Beneficiary::class ,
            null,
            'create',
            $data,
            $executor,
            true
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب إضافة المستفيد للموافقة.');
        }

        return redirect()->route('beneficiaries.show', $result);
    }
    public function show(Beneficiary $beneficiary)
    {
        $pending = \App\Models\ChangeRequest::where('model_type', \App\Models\Beneficiary::class)
            ->where('model_id', $beneficiary->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذا المستفيد لديه طلب مراجعة حالياً');
        }

        $isDup = false;
        if (!empty($beneficiary->phone)) {
            $isDup = $isDup || (\DB::table('beneficiaries')->where('phone', $beneficiary->phone)->count() > 1);
        }
        if (!empty($beneficiary->national_id)) {
            $isDup = $isDup || (\DB::table('beneficiaries')->where('national_id', $beneficiary->national_id)->count() > 1);
        }
        $donations = \App\Models\Donation::whereRaw("allocation_note REGEXP 'beneficiary_id={$beneficiary->id}([^0-9]|$)'")
            ->with(['donor', 'project', 'campaign', 'guestHouse'])
            ->orderByDesc('received_at')
            ->get();
        return view('beneficiaries.show', compact('beneficiary', 'isDup', 'donations'));
    }
    public function edit(Beneficiary $beneficiary)
    {
        $pending = \App\Models\ChangeRequest::where('model_type', \App\Models\Beneficiary::class)
            ->where('model_id', $beneficiary->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذا المستفيد لديه طلب مراجعة حالياً');
        }

        $projects = Project::query()->where(function ($q) {
            $q->where('name', 'like', '%بعثاء%')->orWhere('name', 'like', '%زاد%')->orWhere('name', 'like', '%مدرار%')->orWhere('name', 'like', '%كسو%');
        })->orderBy('name')->get();
        $campaigns = Campaign::orderByDesc('season_year')->orderBy('name')->get();
        $guestHouses = \App\Models\GuestHouse::orderBy('name')->get();
        return view('beneficiaries.edit', compact('beneficiary', 'projects', 'campaigns', 'guestHouses'));
    }
    public function update(Request $request, Beneficiary $beneficiary)
    {
        $pending = \App\Models\ChangeRequest::where('model_type', \App\Models\Beneficiary::class)
            ->where('model_id', $beneficiary->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذا المستفيد لديه طلب مراجعة حالياً');
        }

        $data = $request->validate([
            'code' => 'nullable|string|unique:beneficiaries,code,' . $beneficiary->id,
            'full_name' => 'sometimes|string',
            'national_id' => 'nullable|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'assistance_type' => 'sometimes|in:financial,in_kind,service',
            'status' => 'in:new,under_review,accepted,rejected',
            'project_id' => 'nullable|exists:projects,id',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'guest_house_id' => 'nullable|exists:guest_houses,id',
            'notes' => 'nullable|string',
            'rejection_reason' => 'nullable|string',
            'allocation_type' => 'nullable|string',
            'child_sponsorship_type' => 'nullable|string'
        ]);
        if (isset($data['status'])) {
            $allowed = [
                'new' => ['under_review', 'rejected'],
                'under_review' => ['accepted', 'rejected'],
                'accepted' => ['rejected'],
                'rejected' => ['new', 'under_review'] // Allow reopening rejected?
            ];
            $current = $beneficiary->status;
            $next = $data['status'];
            // Simplify check: allow rejected from anywhere basically, and strict check only for accept flow
            if ($current !== $next && $next !== 'rejected' && $current !== 'rejected' && !in_array($next, $allowed[$current] ?? [], true)) {
                // For now, let's just allow it if we are explicit about it or just relax it. 
                // But sticking to the map is safer.
                if (!in_array($next, $allowed[$current] ?? [], true)) {
                    return back()->withErrors(['status' => 'انتقال حالة غير مسموح']);
                }
            }
        }

        // Auto-generate code if empty
        if (empty($data['code']) && empty($beneficiary->code)) {
            $data['code'] = 'BEN-' . strtoupper(\Illuminate\Support\Str::random(6));
        }

        $executor = function () use ($beneficiary, $data) {
            $beneficiary->update($data);
            return $beneficiary;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Beneficiary::class ,
            $beneficiary->id,
            'update',
            $data,
            $executor,
            true // Force request execution to be pending
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تعديل المستفيد للموافقة.');
        }

        return redirect()->route('beneficiaries.show', $beneficiary)->with('success', 'تم تعديل المستفيد بنجاح.');
    }
    public function destroy(Beneficiary $beneficiary)
    {
        $pending = \App\Models\ChangeRequest::where('model_type', \App\Models\Beneficiary::class)
            ->where('model_id', $beneficiary->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذا المستفيد لديه طلب مراجعة حالياً');
        }

        $executor = function () use ($beneficiary) {
            $beneficiary->delete();
            return true;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Beneficiary::class ,
            $beneficiary->id,
            'delete',
        [],
            $executor,
            true
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب حذف المستفيد للموافقة.');
        }

        return redirect()->route('beneficiaries.index')->with('success', 'تم حذف المستفيد بنجاح.');
    }
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:beneficiaries,id',
            'bulk_action' => 'required|string',
        ]);

        $ids = $request->ids;
        $action = $request->bulk_action;

        $count = 0;
        switch ($action) {
            case 'status_under_review':
                $count = Beneficiary::whereIn('id', $ids)->update(['status' => 'under_review']);
                break;
            case 'status_accepted':
                $count = Beneficiary::whereIn('id', $ids)->update(['status' => 'accepted']);
                break;
            case 'status_rejected':
                $count = Beneficiary::whereIn('id', $ids)->update(['status' => 'rejected']);
                break;
            case 'delete':
                $count = Beneficiary::whereIn('id', $ids)->delete();
                return redirect()->back()->with('success', "تم حذف $count مستفيد بنجاح.");
        }

        return redirect()->back()->with('success', "تم تحديث $count مستفيد بنجاح.");
    }
}
