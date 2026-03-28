<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Delegate;
use App\Models\DelegateTrip;
use App\Models\TravelRoute;
use App\Models\User;
use App\Models\ChangeRequest;
use App\Services\DelegateService;
use App\Http\Requests\StoreDelegateRequest;
use App\Http\Requests\UpdateDelegateRequest;
use App\Http\Requests\StoreDelegateTripRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

final readonly class DelegateWebController extends Controller
{
    public function __construct(
        private DelegateService $delegateService
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['q', 'route_id', 'active', 'has_phone', 'sort', 'dir', 'per_page']);
        $perPage = (int) ($filters['per_page'] ?? 20);
        
        $delegates = $this->delegateService->getFilteredDelegates($filters, $perPage);
        $routes    = TravelRoute::orderBy('name')->get();

        $stats = [
            'total'    => Delegate::count(),
            'active'   => (int) Delegate::where('active', true)->count(),
            'no_route' => (int) Delegate::whereNull('route_id')->count(),
        ];

        return view('delegates.index', array_merge(compact('delegates', 'routes', 'stats'), $filters));
    }

    public function export(Request $request): StreamedResponse
    {
        return $this->delegateService->exportToCsv($request->all());
    }

    public function bulkUpdate(Request $request): RedirectResponse
    {
        $ids    = (array) $request->input('ids', []);
        $action = (string) $request->input('bulk_action', '');
        
        if (empty($ids) || !in_array($action, ['activate', 'deactivate', 'delete'], true)) {
            return back()->with('error', 'لم يتم اختيار عناصر أو إجراء');
        }

        $this->delegateService->bulkUpdate($ids, $action);
        return back()->with('success', 'تم تنفيذ الإجراء الجماعي');
    }

    public function create(): View
    {
        $routes    = TravelRoute::orderBy('name')->get();
        $employees = User::where('is_employee', true)->orderBy('name')->get();
        return view('delegates.create', compact('routes', 'employees'));
    }

    public function store(StoreDelegateRequest $request): RedirectResponse
    {
        $result = $this->delegateService->createDelegate($request->validated(), $request->file('profile_photo'));

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تفعيل المندوب للإدارة.');
        }

        return redirect()->route('delegates.show', $result);
    }

    public function show(Delegate $delegate): View|RedirectResponse
    {
        if ($this->hasPendingRequest($delegate)) {
            return redirect()->route('change-requests.index')->with('info', 'هذا المندوب لديه طلب مراجعة حالياً');
        }

        $delegate->load('route');
        $trips = $delegate->trips()->orderBy('date', 'desc')->paginate(20);
        
        $stats = [
            'total_cost'   => (float)$delegate->trips()->sum('cost'),
            'pending_cost' => (float)$delegate->trips()->where('status', 'pending')->sum('cost'),
            'paid_cost'    => (float)$delegate->trips()->where('status', 'paid')->sum('cost'),
            'count'        => (int)$delegate->trips()->count()
        ];
        
        return view('delegates.show', compact('delegate', 'trips', 'stats'));
    }

    public function edit(Delegate $delegate): View|RedirectResponse
    {
        if ($this->hasPendingRequest($delegate)) {
            return redirect()->route('change-requests.index')->with('info', 'هذا المندوب لديه طلب مراجعة حالياً');
        }

        $routes    = TravelRoute::orderBy('name')->get();
        $employees = User::where('is_employee', true)->orderBy('name')->get();
        
        return view('delegates.edit', compact('delegate', 'routes', 'employees'));
    }

    public function update(UpdateDelegateRequest $request, Delegate $delegate): RedirectResponse
    {
        if ($this->hasPendingRequest($delegate)) {
            return redirect()->route('change-requests.index')->with('info', 'هذا المندوب لديه طلب مراجعة حالياً');
        }

        $result = $this->delegateService->updateDelegate($delegate, $request->validated(), $request->file('profile_photo'));

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تعديل المندوب للإدارة.');
        }

        return redirect()->route('delegates.show', $delegate);
    }

    public function destroy(Delegate $delegate): RedirectResponse
    {
        if ($this->hasPendingRequest($delegate)) {
            return redirect()->route('change-requests.index')->with('info', 'هذا المندوب لديه طلب مراجعة حالياً');
        }

        $result = $this->delegateService->deleteDelegate($delegate);

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب حذف المندوب للإدارة.');
        }

        return redirect()->route('delegates.index')->with('success', 'تم حذف المندوب بنجاح');
    }

    public function storeTrip(StoreDelegateTripRequest $request, Delegate $delegate): RedirectResponse
    {
        $result = $this->delegateService->storeTrip($delegate, $request->validated(), (bool)$request->create_journal_entry);

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب إضافة المشوار للإدارة.');
        }
        
        return back()->with('success', 'تم إضافة المشوار بنجاح');
    }

    public function destroyTrip(Delegate $delegate, DelegateTrip $trip): RedirectResponse
    {
        if ($trip->delegate_id !== $delegate->id) { abort(403); }

        $result = $this->delegateService->deleteTrip($delegate, $trip);

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب حذف المشوار للإدارة.');
        }

        return back()->with('success', 'تم حذف المشوار');
    }

    public function updateTripStatus(Request $request, Delegate $delegate, DelegateTrip $trip): RedirectResponse
    {
        if ($trip->delegate_id !== $delegate->id) { abort(403); }
        $request->validate(['status' => 'required|in:pending,paid']);

        $result = $this->delegateService->updateTripStatus($delegate, $trip, $request->status);

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تحديث حالة المشوار للإدارة.');
        }

        return back()->with('success', 'تم تحديث حالة المشوار');
    }

    private function hasPendingRequest(Delegate $delegate): bool
    {
        return ChangeRequest::where('model_type', Delegate::class)
            ->where('model_id', $delegate->id)
            ->where('status', 'pending')
            ->exists();
    }
}
