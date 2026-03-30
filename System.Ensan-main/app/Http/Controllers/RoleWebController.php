<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Role;
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
        return view('roles.create', compact('permissions'));
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $this->roleService->createRole($request->validated());
        return redirect()->route('roles.index')->with('success', 'تم إنشاء الدور بنجاح');
    }

    public function edit(Role $role): View
    {
        $role->load('permissions');
        $permissions     = $this->roleService->getPermissionGroups();
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $result = $this->roleService->updateRole($role, $request->validated());

        if ($result instanceof ChangeRequest) {
            return redirect()->route('roles.index')->with('success', 'تم إرسال طلب تعديل الدور للموافقة');
        }

        return redirect()->route('roles.index')->with('success', 'تم تحديث الدور بنجاح');
    }

    public function show(Role $role): View
    {
        $role->load('permissions');
        $permissions = $this->roleService->getGroupedRolePermissions($role);

        return view('roles.show', compact('role', 'permissions'));
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
