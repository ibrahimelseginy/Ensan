<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Project;
use App\Models\ChangeRequest;
use App\Services\UserService;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Arr;

final class UserWebController extends Controller
{
    public function __construct(
        private UserService $userService
    ) {}

    public function index(Request $request): View
    {
        $totalEmployees  = User::where('is_employee', true)->count();
        $activeEmployees = User::where('is_employee', true)->where('active', true)->count();

        $projectCounts = User::where('is_employee', true)
            ->whereNotNull('project_id')
            ->select('project_id', DB::raw('count(*) as total'))
            ->groupBy('project_id')
            ->pluck('total', 'project_id');

        $projectsWithStats = Project::orderBy('name')->get()->map(function ($p) use ($projectCounts) {
            $p->employees_count = $projectCounts[$p->id] ?? 0;
            return $p;
        });

        $filters = $request->only(['active', 'project_id', 'type']);
        $users   = $this->userService->getFilteredUsers($filters, 20);

        return view('users.index', compact('users', 'totalEmployees', 'activeEmployees', 'projectsWithStats'));
    }

    public function create(): View
    {
        $roles = Role::orderBy('name')->get();
        return view('users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $files = [
            'profile_photo'         => $request->file('profile_photo'),
            'contract_image'        => $request->file('contract_image'),
            'criminal_record_image' => $request->file('criminal_record_image'),
            'id_card_image'         => $request->file('id_card_image'),
        ];
        
        $user = $this->userService->createUser($request->validated(), array_filter($files));

        return redirect()->route('users.show', $user);
    }

    public function show(User $user): View
    {
        $pendingRequest = ChangeRequest::where('model_type', User::class)
            ->where('model_id', $user->id)
            ->where('status', 'pending')
            ->first();

        $isOwnProfile   = (int) request()->user()->id === (int) $user->id;
        $canManageUsers = Gate::allows('users.edit');
        $canViewUsers   = Gate::allows('users.view');
        $canDeleteUsers = Gate::allows('users.delete');

        return view('users.show', compact(
            'user',
            'pendingRequest',
            'isOwnProfile',
            'canManageUsers',
            'canViewUsers',
            'canDeleteUsers'
        ));
    }

    public function edit(User $user): View|RedirectResponse
    {
        if ($this->hasPendingRequest($user)) {
            return redirect()->route('users.show', $user)->with('info', 'يوجد طلب تعديل قيد مراجعة الأدمن حاليًا.');
        }

        $isOwnProfile   = (int) request()->user()->id === (int) $user->id;
        $canManageUsers = Gate::allows('users.edit');
        $roles = $canManageUsers ? Role::orderBy('name')->get() : collect();

        return view('users.edit', compact('user', 'roles', 'isOwnProfile', 'canManageUsers'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        if ($this->hasPendingRequest($user)) {
            return redirect()->route('users.show', $user)->with('info', 'يوجد طلب تعديل قيد مراجعة الأدمن حاليًا.');
        }

        $data = $request->validated();
        $isOwnEmployeeRequest = (int) $request->user()->id === (int) $user->id
            && !Gate::allows('users.edit');

        if ($isOwnEmployeeRequest) {
            $data = Arr::only($data, [
                'name',
                'email',
                'phone',
                'department',
                'job_title',
            ]);
        }

        $files = [
            'profile_photo'         => $request->file('profile_photo'),
            'contract_image'        => $request->file('contract_image'),
            'criminal_record_image' => $request->file('criminal_record_image'),
            'id_card_image'         => $request->file('id_card_image'),
        ];

        if ($isOwnEmployeeRequest) {
            $files = Arr::only($files, ['profile_photo']);
        }

        $result = $this->userService->updateUser($user, $data, array_filter($files));

        if ($result instanceof ChangeRequest) {
            return redirect()->route('users.show', $user)
                ->with('success', 'تم إرسال طلب تعديل بياناتك إلى الأدمن للمراجعة.');
        }

        return redirect()->route('users.show', $user)->with('success', 'تم تحديث بيانات المستخدم بنجاح');
    }

    public function destroy(User $user): RedirectResponse
    {
        $result = $this->userService->deleteUser($user);

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب حذف المستخدم للموافقة');
        }

        return redirect()->route('users.index')->with('success', 'تم حذف المستخدم بنجاح');
    }

    public function attachRole(User $user, Role $role): RedirectResponse
    {
        $this->userService->attachRole($user, (int)$role->id);
        return back();
    }

    public function detachRole(User $user, Role $role): RedirectResponse
    {
        $this->userService->detachRole($user, (int)$role->id);
        return back();
    }

    private function hasPendingRequest(User $user): bool
    {
        return ChangeRequest::where('model_type', User::class)
            ->where('model_id', $user->id)
            ->where('status', 'pending')
            ->exists();
    }
}
