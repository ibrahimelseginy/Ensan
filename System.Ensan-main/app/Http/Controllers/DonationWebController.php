<?php
namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Donor;
use App\Models\Project;
use App\Models\Campaign;
use App\Models\Warehouse;
use App\Models\Delegate;
use App\Models\Beneficiary;
use App\Models\TravelRoute;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DonationWebController extends Controller
{
    public function index()
    {
        $q = (string) request('q', '');
        $projectId = request('project_id');
        $campaignId = request('campaign_id');
        $guestHouseId = request('guest_house_id');

        $cashDonations = Donation::with(['donor', 'project', 'campaign', 'warehouse'])
            ->where('type', 'cash')
            ->when($projectId, function ($qr) use ($projectId) {
                $qr->where('project_id', $projectId);
            })
            ->when($campaignId, function ($qr) use ($campaignId) {
                $qr->where('campaign_id', $campaignId);
            })
            ->when($guestHouseId, function ($qr) use ($guestHouseId) {
                $qr->where('guest_house_id', $guestHouseId);
            })
            ->when(request('channel'), function ($qr, $channel) {
                $qr->where('cash_channel', $channel);
            })
            ->when($q !== '', function ($qr) use ($q) {
                $qr->where(function ($w) use ($q) {
                    $w->whereHas('donor', function ($d) use ($q) {
                        $d->where('name', 'like', '%' . $q . '%');
                    })
                        ->orWhere('receipt_number', 'like', '%' . $q . '%')
                        ->orWhereHas('project', function ($p) use ($q) {
                            $p->where('name', 'like', '%' . $q . '%');
                        })
                        ->orWhereHas('campaign', function ($c) use ($q) {
                            $c->where('name', 'like', '%' . $q . '%');
                        });
                });
            })
            ->orderByDesc('received_at')->orderByDesc('id')->paginate(10, ['*'], 'cash_page');


        // Skip in-kind donations query if filtering by cash channel
        if (!request('channel')) {
            $inKindDonations = Donation::with(['donor', 'project', 'campaign', 'warehouse'])
                ->where('type', 'in_kind')
                ->when($projectId, function ($qr) use ($projectId) {
                    $qr->where('project_id', $projectId);
                })
                ->when($campaignId, function ($qr) use ($campaignId) {
                    $qr->where('campaign_id', $campaignId);
                })
                ->when($guestHouseId, function ($qr) use ($guestHouseId) {
                    $qr->where('guest_house_id', $guestHouseId);
                })
                ->when(request('type') === 'in_kind', function ($qr) {
                    $qr->where('type', 'in_kind');
                })
                ->when($q !== '', function ($qr) use ($q) {
                    $qr->where(function ($w) use ($q) {
                        $w->whereHas('donor', function ($d) use ($q) {
                            $d->where('name', 'like', '%' . $q . '%');
                        })
                            ->orWhereHas('project', function ($p) use ($q) {
                                $p->where('name', 'like', '%' . $q . '%');
                            })
                            ->orWhereHas('campaign', function ($c) use ($q) {
                                $c->where('name', 'like', '%' . $q . '%');
                            })
                            ->orWhereHas('warehouse', function ($wh) use ($q) {
                                $wh->where('name', 'like', '%' . $q . '%');
                            });
                    });
                })
                ->orderByDesc('received_at')->orderByDesc('id')->paginate(10, ['*'], 'inkind_page');
        } else {
            // Return empty collection when filtering by cash channel
            $inKindDonations = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        }


        // Only load statistics when not filtering (to improve performance)
        $dailyCashSummary = collect();
        $todayByChannel = [
            'cash' => ['count' => 0, 'total' => 0.0],
            'instapay' => ['count' => 0, 'total' => 0.0],
            'vodafone_cash' => ['count' => 0, 'total' => 0.0],
            'delegate' => ['count' => 0, 'total' => 0.0],
        ];
        $inKindToday = (object)['count' => 0, 'total' => 0];

        // Skip statistics queries when filtering by channel or search
        if (!request('channel') && !request('q')) {
            $dailyCashSummary = Donation::select(DB::raw("DATE(received_at) as day"), DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
                ->where('type', 'cash')
                ->where('status', '!=', 'cancelled')
                ->whereDate('received_at', '>=', now()->subDays(7))
                ->when($projectId, function ($qr) use ($projectId) {
                    $qr->where('project_id', $projectId);
                })
                ->when($campaignId, function ($qr) use ($campaignId) {
                    $qr->where('campaign_id', $campaignId);
                })
                ->when($guestHouseId, function ($qr) use ($guestHouseId) {
                    $qr->where('guest_house_id', $guestHouseId);
                })
                ->groupBy(DB::raw("DATE(received_at)"))
                ->orderByDesc(DB::raw("DATE(received_at)"))
                ->limit(7)
                ->get()
                ->map(function ($r) {
                    return ['day' => (string) $r->day, 'count' => (int) $r->count, 'total' => (float) $r->total];
                });

            $today = now()->toDateString();
            $byChannelRaw = Donation::select('cash_channel', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
                ->where('type', 'cash')
                ->where('status', '!=', 'cancelled')
                ->whereDate('received_at', $today)
                ->when($projectId, function ($qr) use ($projectId) {
                    $qr->where('project_id', $projectId);
                })
                ->when($campaignId, function ($qr) use ($campaignId) {
                    $qr->where('campaign_id', $campaignId);
                })
                ->when($guestHouseId, function ($qr) use ($guestHouseId) {
                    $qr->where('guest_house_id', $guestHouseId);
                })
                ->groupBy('cash_channel')
                ->get();
            
            foreach ($byChannelRaw as $r) {
                $key = $r->cash_channel ?: 'cash';
                if (!isset($todayByChannel[$key])) {
                    $todayByChannel[$key] = ['count' => 0, 'total' => 0.0];
                }
                $todayByChannel[$key]['count'] = (int) $r->count;
                $todayByChannel[$key]['total'] = (float) $r->total;
            }
            
            $inKindToday = Donation::select(DB::raw('COUNT(*) as count'), DB::raw('SUM(estimated_value) as total'))
                ->where('type', 'in_kind')
                ->where('status', '!=', 'cancelled')
                ->whereDate('received_at', $today)
                ->when($projectId, function ($qr) use ($projectId) {
                    $qr->where('project_id', $projectId);
                })
                ->when($campaignId, function ($qr) use ($campaignId) {
                    $qr->where('campaign_id', $campaignId);
                })
                ->when($guestHouseId, function ($qr) use ($guestHouseId) {
                    $qr->where('guest_house_id', $guestHouseId);
                })
                ->first();
        }

        return view('donations.index', compact('cashDonations', 'inKindDonations', 'dailyCashSummary', 'todayByChannel', 'inKindToday', 'q'));
    }

    public function export(Request $request)
    {
        $q = (string) $request->get('q', '');
        $projectId = $request->get('project_id');
        $campaignId = $request->get('campaign_id');
        $month = $request->get('month');
        $year = $request->get('year');
        $infoType = $request->get('type', 'cash'); // cash or in_kind

        $query = Donation::with(['donor', 'project', 'campaign', 'warehouse'])
            ->where('type', $infoType)
            ->when($projectId, function ($qr) use ($projectId) {
                $qr->where('project_id', $projectId);
            })
            ->when($campaignId, function ($qr) use ($campaignId) {
                $qr->where('campaign_id', $campaignId);
            })
            ->when($month, function ($qr) use ($month) {
                $qr->whereMonth('received_at', $month);
            })
            ->when($year, function ($qr) use ($year) {
                $qr->whereYear('received_at', $year);
            })
            ->when($q !== '', function ($qr) use ($q) {
                $qr->where(function ($w) use ($q) {
                    $w->whereHas('donor', function ($d) use ($q) {
                        $d->where('name', 'like', '%' . $q . '%');
                    })
                        ->orWhere('receipt_number', 'like', '%' . $q . '%')
                        ->orWhereHas('project', function ($p) use ($q) {
                            $p->where('name', 'like', '%' . $q . '%');
                        })
                        ->orWhereHas('campaign', function ($c) use ($q) {
                            $c->where('name', 'like', '%' . $q . '%');
                        });
                });
            })
            ->orderByDesc('received_at');

        $filename = "donations_{$infoType}_" . date('Ymd_His') . ".csv";

        return response()->streamDownload(function () use ($query, $infoType) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM for Excel

            if ($infoType === 'cash') {
                fputcsv($handle, ['ID', 'المتبرع', 'المبلغ', 'المشروع', 'الحملة', 'التاريخ', 'رقم الإيصال']);
                $query->chunk(100, function ($donations) use ($handle) {
                    foreach ($donations as $d) {
                        fputcsv($handle, [
                            $d->id,
                            $d->donor->name ?? '—',
                            $d->amount,
                            $d->project->name ?? '—',
                            $d->campaign->name ?? '—',
                            $d->received_at->format('Y-m-d'),
                            $d->receipt_number
                        ]);
                    }
                });
            } else {
                fputcsv($handle, ['ID', 'المتبرع', 'القيمة التقديرية', 'المخزن', 'المشروع', 'الحملة', 'التاريخ']);
                $query->chunk(100, function ($donations) use ($handle) {
                    foreach ($donations as $d) {
                        fputcsv($handle, [
                            $d->id,
                            $d->donor->name ?? '—',
                            $d->estimated_value,
                            $d->warehouse->name ?? '—',
                            $d->project->name ?? '—',
                            $d->campaign->name ?? '—',
                            $d->received_at->format('Y-m-d')
                        ]);
                    }
                });
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function create()
    {
        $donors = Donor::orderBy('name')->get();
        $projects = Project::orderBy('name')->get();
        $campaigns = Campaign::orderByDesc('season_year')->orderBy('name')->get();
        
        // Check if is_active column exists
        $warehousesQuery = Warehouse::query();
        if (\Schema::hasColumn('warehouses', 'is_active')) {
            $warehousesQuery->where('is_active', true);
        }
        $warehouses = $warehousesQuery->orderBy('name')->get();
        
        // Check if treasuries table exists
        $treasuries = collect();
        if (\Schema::hasTable('treasuries')) {
            $treasuriesQuery = \App\Models\Treasury::query();
            if (\Schema::hasColumn('treasuries', 'is_active')) {
                $treasuriesQuery->where('is_active', true);
            }
            $treasuries = $treasuriesQuery->orderBy('name')->get();
        }
        
        // Check if items table has is_active column
        $itemsQuery = \App\Models\Item::query();
        if (\Schema::hasColumn('items', 'is_active')) {
            $itemsQuery->where('is_active', true);
        }
        $items = $itemsQuery->orderBy('name')->get();
        
        $guestHouses = \App\Models\GuestHouse::where(function ($q) {
            $q->where('location', 'like', '%كفر%')
                ->orWhere('location', 'like', '%طنطا%')
                ->orWhere('name', 'like', '%كفر%')
                ->orWhere('name', 'like', '%طنطا%');
        })->orderBy('name')->get();
        $ghKafr = $guestHouses->first(function ($gh) {
            return (strpos($gh->location, 'كفر') !== false) || (strpos($gh->name, 'كفر') !== false);
        });
        $ghTanta = $guestHouses->first(function ($gh) {
            return (strpos($gh->location, 'طنطا') !== false) || (strpos($gh->name, 'طنطا') !== false);
        });
        $delegates = Delegate::orderBy('name')->get();
        $routes = TravelRoute::orderBy('name')->get();
        $beneficiaries = Beneficiary::select('id', 'full_name')->orderBy('full_name')->get();
        
        return view('donations.create', compact(
            'donors', 
            'projects', 
            'campaigns', 
            'warehouses', 
            'treasuries',
            'items',
            'delegates', 
            'routes', 
            'beneficiaries', 
            'guestHouses', 
            'ghKafr', 
            'ghTanta'
        ));
    }

    public function store(Request $request)
    {
        // Simplify logic: either we have a donor_id OR new donor details
        $data = $request->validate([
            'donor_id' => 'nullable|exists:donors,id|required_without:new_donor_name',
            
            // New donor fields are only validated if donor_id is empty
            'new_donor_name' => 'nullable|required_without:donor_id|string|min:3|unique:donors,name|regex:/^[\p{L}\s\.]+$/u',
            'new_donor_phone' => 'nullable|required_with:new_donor_name|string|unique:donors,phone|regex:/^(01[0125][0-9]{8})$/',
            'new_donor_address' => 'nullable|string',
            'new_donor_classification' => 'nullable|in:one_time,recurring',
            'new_donor_cycle' => 'nullable|in:monthly,yearly',
            
            'type' => 'required|in:cash,in_kind',
            'cash_channel' => 'required_if:type,cash|in:cash,instapay,vodafone_cash,delegate',
            'amount' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string',
            'receipt_number' => 'required_if:type,cash|nullable|string|max:64|unique:donations,receipt_number',
            'estimated_value' => 'nullable|numeric',
            'project_id' => 'nullable|exists:projects,id',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'guest_house_id' => 'nullable|exists:guest_houses,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'treasury_id' => 'required_if:type,cash|exists:treasuries,id',
            'delegate_id' => 'nullable|exists:delegates,id',
            'route_id' => 'nullable|exists:travel_routes,id',
            'allocation_note' => 'nullable|string',
            'allocation_type' => 'nullable|string',
            'sponsorship_kind' => 'nullable|string',
            'beneficiary_id' => 'nullable|exists:beneficiaries,id',
            'received_at' => 'nullable|date'
        ], [
            'donor_id.required_without' => 'يرجى اختيار متبرع مسجل أو إدخال بيانات متبرع جديد.',
            'new_donor_name.required_without' => 'اسم المتبرع الجديد مطلوب عند عدم اختيار متبرع مسجل.',
            'new_donor_name.unique' => 'اسم المتبرع هذا مسجل مسبقاً، يرجى اختياره من القائمة.',
            'new_donor_name.min' => 'اسم المتبرع يجب أن يكون 3 أحرف على الأقل.',
            'new_donor_name.regex' => 'اسم المتبرع يجب أن يحتوي على أحرف فقط.',
            'new_donor_phone.unique' => 'رقم الهاتف هذا مسجل مسبقاً لمتبرع آخر.',
            'new_donor_phone.regex' => 'رقم الهاتف يجب أن يكون رقم مصري صحيح (010, 011, 012, 015).',
            'receipt_number.required_if' => 'رقم الإيصال مطلوب للتبرعات النقدية.',
            'receipt_number.unique' => 'رقم الإيصال هذا مسجل مسبقاً لتبرع آخر.',
            'treasury_id.required_if' => 'يرجى اختيار الخزينة للتبرع النقدي.',
        ]);

        if (empty($data['received_at'])) {
            $data['received_at'] = now();
        }

        if (empty($data['donor_id']) && !empty($data['new_donor_name'])) {
            $newDonor = Donor::create([
                'name' => $data['new_donor_name'],
                'type' => 'individual',
                'phone' => $data['new_donor_phone'] ?? null,
                'address' => $data['new_donor_address'] ?? null,
                'classification' => $data['new_donor_classification'] ?? 'one_time',
                'recurring_cycle' => $data['new_donor_cycle'] ?? null,
                'active' => true
            ]);
            $data['donor_id'] = $newDonor->id;
        }
        unset($data['new_donor_name'], $data['new_donor_phone'], $data['new_donor_address'], $data['new_donor_classification'], $data['new_donor_cycle']);
        if ($data['type'] === 'cash') {
            if (!isset($data['amount'])) {
                return back()->withErrors(['amount' => 'مطلوب مبلغ للتبرع النقدي']);
            }
            if ((float) $data['amount'] <= 0) {
                return back()->withErrors(['amount' => 'المبلغ يجب أن يكون أكبر من صفر']);
            }
            if (!isset($data['receipt_number']) || trim((string) $data['receipt_number']) === '') {
                return back()->withErrors(['receipt_number' => 'رقم الإيصال مطلوب للتبرعات النقدية'])->withInput();
            }
        } else {
            if (!isset($data['warehouse_id'])) {
                return back()->withErrors(['warehouse_id' => 'مطلوب تحديد المخزن للتبرع العيني']);
            }
            if (!isset($data['estimated_value'])) {
                return back()->withErrors(['estimated_value' => 'مطلوب قيمة تقديرية للتبرع العيني']);
            }
            if ((float) $data['estimated_value'] <= 0) {
                return back()->withErrors(['estimated_value' => 'القيمة التقديرية يجب أن تكون أكبر من صفر']);
            }
        }
        // Fix: Handle allocation types server-side to prevent validation errors if note is overwritten
        $allocType = $request->input('allocation_type');
        $currentNote = $data['allocation_note'] ?? '';

        if ($allocType === 'sadaqa_jariya') {
            if (!str_contains($currentNote, 'sponsorship=')) {
                $data['allocation_note'] = trim("sponsorship=sadaqa_jariya\n" . $currentNote);
            }
        } elseif ($allocType === 'sponsorship') {
            if (!str_contains($currentNote, 'sponsorship=')) {
                $sType = $request->input('sponsorship_type', 'طفل');
                $benId = $request->input('beneficiary_id');
                $magic = "sponsorship={$sType}" . ($benId ? ";beneficiary_id={$benId}" : "");
                $data['allocation_note'] = trim($magic . "\n" . $currentNote);
            }
        }

        // Handle sponsorship_kind from form
        if (!empty($data['sponsorship_kind'])) {
            $sKind = $data['sponsorship_kind'];
            $benId = $data['beneficiary_id'] ?? null;
            
            // Only add if not already present (to avoid double adding if logic changes)
            if (!str_contains($currentNote, 'sponsorship=')) {
                 $note = "sponsorship=" . $sKind;
                 if ($benId && str_starts_with($sKind, 'kafalat_')) {
                     $note .= ";beneficiary_id=" . $benId;
                 }
                 $data['allocation_note'] = trim($note . "\n" . $currentNote);
                 // Update $note specific variable for next check
                 $currentNote = $data['allocation_note'];
            }
        }

        $note = (string) ($data['allocation_note'] ?? '');
        if (empty($data['project_id']) && empty($data['campaign_id']) && empty($data['guest_house_id']) && !str_contains($note, 'sponsorship=')) {
            return back()->withInput()->withErrors(['project_id' => 'يجب اختيار مشروع أو دار ضيافة أو حملة أو تحديد صدقة جارية أو كفالة للتبرع']);
        }
        if (!\Illuminate\Support\Facades\Schema::hasColumn('donations', 'guest_house_id')) {
            unset($data['guest_house_id']);
        }
        // ... validation code above ...

        // Use ChangeRequestService
        $executor = function () use ($data) {
            // نحتفظ بالقناة كما هي (delegate/hq/cash/instapay/vodafone_cash)
            $donation = Donation::create($data);
            
            // Process cash donations through treasury
            if ($donation->type === 'cash' && !empty($data['treasury_id'])) {
                $treasuryService = new \App\Services\TreasuryIntegrationService();
                $treasuryService->processDonationToTreasury($donation, $data['treasury_id']);
            } else {
                // For in-kind donations, use the old service
                \App\Services\DonationService::postCreate($donation);
            }
            
            return $donation;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Donation::class,
            null,
            'create',
            $data,
            $executor
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب إضافة التبرع للموافقة.');
        }

        if (!empty($data['donor_id'])) {
            return redirect()->route('donors.index', ['selected_donor_id' => $data['donor_id']])->with('success', 'تم إضافة التبرع بنجاح.');
        }

        return redirect()->route('donations.index')->with('success', 'تم إضافة التبرع بنجاح.');
    }

    public function show(Donation $donation)
    {
        $donation->load(['donor', 'project', 'campaign', 'warehouse', 'delegate', 'route']);
        return view('donations.show', compact('donation'));
    }

    public function edit(Donation $donation)
    {
        $donors = Donor::orderBy('name')->get();
        $projects = Project::orderBy('name')->get();
        $campaigns = Campaign::orderByDesc('season_year')->orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();
        $delegates = Delegate::orderBy('name')->get();
        $routes = TravelRoute::orderBy('name')->get();
        $guestHouses = \App\Models\GuestHouse::where(function ($q) {
            $q->where('location', 'like', '%كفر%')
                ->orWhere('location', 'like', '%طنطا%')
                ->orWhere('name', 'like', '%كفر%')
                ->orWhere('name', 'like', '%طنطا%');
        })->orderBy('name')->get();
        $ghKafr = $guestHouses->first(function ($gh) {
            return (strpos($gh->location, 'كفر') !== false) || (strpos($gh->name, 'كفر') !== false);
        });
        $ghTanta = $guestHouses->first(function ($gh) {
            return (strpos($gh->location, 'طنطا') !== false) || (strpos($gh->name, 'طنطا') !== false);
        });
        $beneficiaries = Beneficiary::select('id', 'full_name')->orderBy('full_name')->get();
        return view('donations.edit', compact('donation', 'donors', 'projects', 'campaigns', 'warehouses', 'delegates', 'routes', 'guestHouses', 'ghKafr', 'ghTanta', 'beneficiaries'));
    }

    public function update(Request $request, Donation $donation)
    {
        $data = $request->validate([
            'type' => 'sometimes|in:cash,in_kind',
            'cash_channel' => 'nullable|in:cash,instapay,vodafone_cash,delegate',
            'amount' => 'nullable|numeric',
            'currency' => 'nullable|string',
            'receipt_number' => 'nullable|string|max:64|unique:donations,receipt_number,' . $donation->id,
            'estimated_value' => 'nullable|numeric',
            'allocation_type' => 'nullable|in:project,campaign,guest_house,sponsorship,sadaqa_jariya',
            'project_id' => 'nullable|exists:projects,id',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'guest_house_id' => 'nullable|exists:guest_houses,id',
            'sponsorship_type' => 'nullable|in:طفل,أسرة',
            'beneficiary_id' => 'nullable|exists:beneficiaries,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'delegate_id' => 'nullable|exists:delegates,id',
            'route_id' => 'nullable|exists:travel_routes,id',
            'allocation_note' => 'nullable|string',
            'treasury_id' => 'nullable|exists:treasuries,id',
            'received_at' => 'nullable|date'
        ]);

        // Handle allocation type logic (similar to create)
        $allocType = $request->input('allocation_type');
        $currentNote = $data['allocation_note'] ?? '';

        if ($allocType === 'sadaqa_jariya') {
            if (!str_contains($currentNote, 'sponsorship=')) {
                $data['allocation_note'] = trim("sponsorship=sadaqa_jariya\n" . $currentNote);
            }
        } elseif ($allocType === 'sponsorship') {
            if (!str_contains($currentNote, 'sponsorship=')) {
                $sType = $request->input('sponsorship_type', 'طفل');
                $benId = $request->input('beneficiary_id');
                $magic = "sponsorship={$sType}" . ($benId ? ";beneficiary_id={$benId}" : "");
                $data['allocation_note'] = trim($magic . "\n" . $currentNote);
            }
        }

        // Remove allocation_type from data as it's not a database column
        unset($data['allocation_type'], $data['sponsorship_type'], $data['beneficiary_id']);

        if (!\Illuminate\Support\Facades\Schema::hasColumn('donations', 'guest_house_id')) {
            unset($data['guest_house_id']);
        }

        if ((($data['type'] ?? $donation->type) === 'cash') && isset($data['amount']) && (float) $data['amount'] <= 0) {
            return back()->withErrors(['amount' => 'المبلغ يجب أن يكون أكبر من صفر']);
        }
        if ((($data['type'] ?? $donation->type) === 'in_kind') && isset($data['estimated_value']) && (float) $data['estimated_value'] <= 0) {
            return back()->withErrors(['estimated_value' => 'القيمة التقديرية يجب أن تكون أكبر من صفر']);
        }

        $executor = function () use ($donation, $data) {
            $donation->update($data);
            return $donation;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Donation::class,
            $donation->id,
            'update',
            $data,
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تعديل التبرع للموافقة.');
        }

        return redirect()->route('donations.show', $donation)->with('success', 'تم تعديل التبرع بنجاح.');
    }


    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:donations,id'
        ]);

        \App\Models\Donation::whereIn('id', $request->ids)->delete();

        return back()->with('success', 'تم حذف التبرعات المحددة بنجاح');
    }
    public function destroy(Donation $donation)
    {
        $executor = function () use ($donation) {
            $reason = request('cancellation_reason', 'لم يتم تحديد سبب');
            
            // 1. Update Donation Status
            $donation->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancelled_by' => auth()->id(),
                'cancellation_reason' => $reason
            ]);

            // 2. Reverse Treasury Transaction if applicable
            if ($donation->type === 'cash' && $donation->treasury_id) {
                 $treasuryService = new \App\Services\TreasuryIntegrationService();
                 $treasuryService->cancelDonationTransaction($donation, $reason);
            }

            return true;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Donation::class,
            $donation->id,
            'cancel', // Changed action name to 'cancel'
            ['reason' => request('cancellation_reason')],
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب إلغاء التبرع للموافقة.');
        }

        return back()->with('success', 'تم إلغاء التبرع وعكس العمليات المالية بنجاح.');
    }
}
