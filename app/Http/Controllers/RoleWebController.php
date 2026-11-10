<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\ChangeRequest;
use App\Services\RoleService;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class RoleWebController extends Controller
{
    public function __construct(
        private RoleService $roleService
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['q']);
        $roles   = $this->roleService->getFilteredRoles($filters, 20);

        return view('roles.index', compact('roles'));
    }

    public function create(): View
    {
        $permissions = $this->roleService->getPermissionGroups();
        $users       = User::with('roles:id,name')->orderBy('name')->get(['id', 'name', 'email', 'job_title', 'is_employee', 'is_volunteer']);
        return view('roles.create', compact('permissions', 'users'));
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $role = $this->roleService->createRole($request->validated());
        return redirect()->route('roles.show', $role)->with('success', 'تم إنشاء الدور وتخصيص الصلاحيات والموظفين بنجاح');
    }

    public function edit(Role $role): View
    {
        $role->load(['permissions', 'users']);
        $permissions     = $this->roleService->getPermissionGroups();
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        $users           = User::with('roles:id,name')->orderBy('name')->get(['id', 'name', 'email', 'job_title', 'is_employee', 'is_volunteer']);
        $roleUserIds     = $role->users->pluck('id')->toArray();

        return view('roles.edit', compact('role', 'permissions', 'rolePermissions', 'users', 'roleUserIds'));
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $result = $this->roleService->updateRole($role, $request->validated());

        if ($result instanceof ChangeRequest) {
            return redirect()->route('roles.index')->with('success', 'تم إرسال طلب تعديل الدور للموافقة');
        }

        return redirect()->route('roles.show', $role)->with('success', 'تم تحديث الدور والصلاحيات والموظفين بنجاح');
    }

    public function show(Role $role): View
    {
        $role->load(['permissions', 'users.roles'])->loadCount('users');
        $permissions = $this->roleService->getGroupedRolePermissions($role);
        $allUsers    = User::with('roles:id,name')->orderBy('name')->get(['id', 'name', 'email', 'job_title', 'is_employee', 'is_volunteer']);

        return view('roles.show', compact('role', 'permissions', 'allUsers'));
    }

    public function attachUser(Request $request, Role $role): RedirectResponse
    {
        abort_unless((bool) $request->user()?->hasPermission('roles.edit'), 403);

        $data = $request->validate([
            'user_id'         => 'required|exists:users,id',
            'exclusive_role'  => 'nullable|boolean',
        ]);

        $replaceExisting = $request->boolean('exclusive_role', true);
        $this->roleService->attachUserToRole($role, (int) $data['user_id'], $replaceExisting);

        return back()->with('success', 'تم إسناد الدور للموظف بنجاح');
    }

    public function detachUser(Request $request, Role $role, User $user): RedirectResponse
    {
        abort_unless((bool) $request->user()?->hasPermission('roles.edit'), 403);

        $this->roleService->detachUserFromRole($role, (int) $user->id);

        return back()->with('success', 'تم إزالة الموظف من هذا الدور بنجاح');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $result = $this->roleService->deleteRole($role);

        if ($result instanceof ChangeRequest) {
            return redirect()->route('roles.index')->with('success', 'تم إرسال طلب حذف الدور للموافقة');
        }

        return redirect()->route('roles.index')->with('success', 'تم حذف الدور بنجاح');
    }
}
