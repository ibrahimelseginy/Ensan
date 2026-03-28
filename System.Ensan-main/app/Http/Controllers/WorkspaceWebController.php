<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Workspace;
use App\Models\WorkspaceRental;
use App\Models\Expense;
use Illuminate\Http\Request;

final class WorkspaceWebController extends Controller
{
    public function index()
    {
        $q = trim((string) request()->input('q'));
        $status = request()->input('status');
        $workspaces = Workspace::when($q !== '', function ($qb) use ($q) {
            $qb->where('name', 'like', "%$q%")->orWhere('location', 'like', "%$q%");
        })
            ->when($status, function ($qb, $s) {
                $qb->where('status', $s);
            })
            ->orderBy('name')->paginate(24);

        // Add pending check for each workspace
        $workspaces->each(function($w) {
            $w->pendingRequest = \App\Models\ChangeRequest::where('model_type', \App\Models\Workspace::class)
                ->where('model_id', $w->id)
                ->where('status', 'pending')
                ->first();
        });

        $totalRevenue = WorkspaceRental::whereIn('status', ['confirmed', 'completed'])->sum('total_price');
        $totalExpenses = Expense::whereNotNull('workspace_id')->sum('amount');
        $netBalance = $totalRevenue - $totalExpenses;

        $todayRentals = WorkspaceRental::whereDate('start_time', today())
            ->orWhere(function ($query) {
                $query->whereDate('start_time', '<=', today())
                    ->whereDate('end_time', '>=', today());
            })->with('workspace')->orderBy('start_time')->get();

        return view('workspaces.index', compact('workspaces', 'q', 'status', 'totalRevenue', 'totalExpenses', 'netBalance', 'todayRentals'));
    }

    public function create()
    {
        $users = \App\Models\User::orderBy('name')->get();
        return view('workspaces.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'amenities' => 'nullable|string',
            'location' => 'nullable|string',
            'capacity' => 'nullable|integer',
            'price_per_hour' => 'nullable|numeric',
            'price_per_day' => 'nullable|numeric',
            'price_per_day' => 'nullable|numeric',
            'status' => 'in:available,maintenance,busy',
            'manager_id' => 'nullable|exists:users,id',
        ]);
        $data['status'] = $data['status'] ?? 'available';
        
        $executor = function () use ($data) {
             return Workspace::create($data);
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Workspace::class,
            null,
            'create',
            $data,
            $executor
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب إضافة المساحة للموافقة.');
        }

        return redirect()->route('workspaces.show', $result);
    }

    public function show(Workspace $workspace)
    {
        $pending = \App\Models\ChangeRequest::where('model_type', \App\Models\Workspace::class)
            ->where('model_id', $workspace->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذه المساحة لديها طلب مراجعة حالياً');
        }

        $rentals = $workspace->rentals()->orderByDesc('start_time')->paginate(20);

        // Calculate revenue (confirmed + completed rentals only)
        $totalRevenue = $workspace->rentals()->whereIn('status', ['confirmed', 'completed'])->sum('total_price');

        // Calculate expenses for this workspace
        $totalExpenses = $workspace->expenses()->sum('amount');

        // Net balance
        $netBalance = $totalRevenue - $totalExpenses;

        return view('workspaces.show', compact('workspace', 'rentals', 'totalRevenue', 'totalExpenses', 'netBalance'));
    }

    public function edit(Workspace $workspace)
    {
        $pending = \App\Models\ChangeRequest::where('model_type', \App\Models\Workspace::class)
            ->where('model_id', $workspace->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذه المساحة لديها طلب مراجعة حالياً');
        }

        $users = \App\Models\User::orderBy('name')->get();
        return view('workspaces.edit', compact('workspace', 'users'));
    }

    public function update(Request $request, Workspace $workspace)
    {
        $pending = \App\Models\ChangeRequest::where('model_type', \App\Models\Workspace::class)
            ->where('model_id', $workspace->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذه المساحة لديها طلب مراجعة حالياً');
        }

        $data = $request->validate([
            'name' => 'sometimes|string',
            'description' => 'nullable|string',
            'amenities' => 'nullable|string',
            'location' => 'nullable|string',
            'capacity' => 'nullable|integer',
            'price_per_hour' => 'nullable|numeric',
            'price_per_day' => 'nullable|numeric',

            'status' => 'in:available,maintenance,busy',
            'manager_id' => 'nullable|exists:users,id',
        ]);

        $executor = function () use ($workspace, $data) {
            $workspace->update($data);
            return $workspace;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Workspace::class,
            $workspace->id,
            'update',
            $data,
            $executor,
            true
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تعديل المساحة للموافقة.');
        }

        return redirect()->route('workspaces.show', $workspace);
    }

    public function destroy(Workspace $workspace)
    {
        $pending = \App\Models\ChangeRequest::where('model_type', \App\Models\Workspace::class)
            ->where('model_id', $workspace->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذه المساحة لديها طلب مراجعة حالياً');
        }

        $executor = function () use ($workspace) {
            $workspace->delete();
            return true;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Workspace::class,
            $workspace->id,
            'delete',
            request()->all(),
            $executor,
            true
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب حذف المساحة للموافقة.');
        }

        return redirect()->route('workspaces.index');
    }

    public function storeRental(Request $request, Workspace $workspace)
    {
        $data = $request->validate([
            'renter_name' => 'required|string',
            'renter_phone' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'total_price' => 'nullable|numeric',
            'status' => 'in:pending,confirmed,completed,cancelled',
            'notes' => 'nullable|string',
        ]);
        $workspace->rentals()->create($data);
        return redirect()->route('workspaces.show', $workspace)->with('success', 'تم إضافة الحجز بنجاح');
    }

    public function updateRentalStatus(Request $request, Workspace $workspace, WorkspaceRental $rental)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled',
        ]);
        $rental->update($data);
        return redirect()->route('workspaces.show', $workspace);
    }

    public function destroyRental(Workspace $workspace, WorkspaceRental $rental)
    {
        $rental->delete();
        return redirect()->route('workspaces.show', $workspace);
    }
}
