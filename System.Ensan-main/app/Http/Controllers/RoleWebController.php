<?php

declare(strict_types=1);
namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

final class RoleWebController extends Controller
{
    public function index(Request $request)
    {
        $query = Role::orderBy('name');
        if ($request->has('q')) {
            $q = $request->q;
            $query->where(function ($qq) use ($q) {
                $qq->where('name', 'like', "%$q%")->orWhere('key', 'like', "%$q%");
            });
        }
        $roles = $query->paginate(20);
        return view('roles.index', compact('roles'));
    }

    private function getPermissionGroups()
    {
        $groupLabels = [
            'dashboard' => 'لوحة التحكم',
            'donors' => 'المتبرعون',
            'donations' => 'التبرعات',
            'beneficiaries' => 'المستفيدون',
            'delegates' => 'اللوجيستك - المناديب',
            'travel_routes' => 'اللوجيستك - خطوط السير',
            'trips' => 'اللوجيستك - الرحلات',
            'users' => 'الموظفين',
            'employee_attendance' => 'حضور الموظفين',
            'employee_tasks' => 'مهام الموظفين',
            'volunteers' => 'المتطوعين',
            'volunteer_attendance' => 'حضور المتطوعين',
            'volunteer_tasks' => 'مهام المتطوعين',
            'volunteer_hours' => 'ساعات التطوع',
            'hr' => 'الموارد البشرية - تقييمات',
            'accounts' => 'دليل الحسابات',
            'journal_entries' => 'القيود اليومية',
            'expenses' => 'المصروفات',
            'financial_closures' => 'الإقفال المالي',
            'warehouses' => 'المخازن',
            'items' => 'الأصناف',
            'inventory_transactions' => 'حركات المخزن',
            'suppliers' => 'الموردين',
            'projects' => 'المشاريع',
            'campaigns' => 'الحملات',
            'payroll' => 'الرواتب',
            'guest_houses' => 'دار الضيافة',
            'workspaces' => 'مساحات العمل',
            'notifications' => 'الإشعارات',
            'complaints' => 'الشكاوى',
            'logs' => 'السجلات',
            'roles' => 'الأدوار والصلاحيات',
            'attachments' => 'المرفقات',
            'tasks' => 'المهام العامة',
            'audits' => 'سجلات النظام (Logs)',
            'website' => 'إدارة الموقع الإلكتروني',
            'mobile' => 'إدارة تطبيق الموبايل',
        ];

        return Permission::orderBy('key')->get()->groupBy(function ($item) use ($groupLabels) {
            $prefix = explode('.', $item->key)[0];
            return $groupLabels[$prefix] ?? ucfirst($prefix);
        });
    }

    public function create()
    {
        $permissions = $this->getPermissionGroups();
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'key' => 'required|string|unique:roles,key',
            'permissions' => 'array'
        ]);

        $role = Role::create($request->only('name', 'key', 'description'));
        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        return redirect()->route('roles.index')->with('success', 'تم إنشاء الدور بنجاح');
    }

    public function edit(Role $role)
    {
        $permissions = $this->getPermissionGroups();
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'key' => 'required|string|unique:roles,key,' . $role->id,
            'permissions' => 'array',
            'description' => 'nullable|string'
        ]);

        $executor = function () use ($role, $data, $request) {
            $role->update($request->only('name', 'key', 'description'));
            if (isset($data['permissions'])) {
                $role->permissions()->sync($data['permissions']);
            }
            return $role;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Role::class,
            $role->id,
            'update',
            $data,
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('roles.index')->with('success', 'تم إرسال طلب تعديل الدور للموافقة');
        }

        return redirect()->route('roles.index')->with('success', 'تم تحديث الدور بنجاح');
    }

    public function destroy(Role $role)
    {
        $executor = function () use ($role) {
            $role->delete();
            return true;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Role::class,
            $role->id,
            'delete',
            [
                'note' => 'حذف دور وصلاحيات',
                'name' => $role->name,
                'key' => $role->key
            ],
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('roles.index')->with('success', 'تم إرسال طلب حذف الدور للموافقة');
        }

        return redirect()->route('roles.index')->with('success', 'تم حذف الدور بنجاح');
    }

    public function show(Role $role)
    {
        $role->load('permissions');
        $groupLabels = [
            'dashboard' => 'لوحة التحكم',
            'donors' => 'المتبرعون',
            'donations' => 'التبرعات',
            'beneficiaries' => 'المستفيدون',
            'delegates' => 'اللوجيستك - المناديب',
            'travel_routes' => 'اللوجيستك - خطوط السير',
            'trips' => 'اللوجيستك - الرحلات',
            'users' => 'الموظفين',
            'employee_attendance' => 'حضور الموظفين',
            'employee_tasks' => 'مهام الموظفين',
            'volunteers' => 'المتطوعين',
            'volunteer_attendance' => 'حضور المتطوعين',
            'volunteer_tasks' => 'مهام المتطوعين',
            'volunteer_hours' => 'ساعات التطوع',
            'hr' => 'الموارد البشرية - تقييمات',
            'accounts' => 'دليل الحسابات',
            'journal_entries' => 'القيود اليومية',
            'expenses' => 'المصروفات',
            'financial_closures' => 'الإقفال المالي',
            'warehouses' => 'المخازن',
            'items' => 'الأصناف',
            'suppliers' => 'الموردين',
            'inventory_transactions' => 'حركات المخزن',
            'projects' => 'المشاريع',
            'campaigns' => 'الحملات',
            'payroll' => 'الرواتب',
            'guest_houses' => 'دار الضيافة',
            'workspaces' => 'مساحات العمل',
            'notifications' => 'الإشعارات',
            'complaints' => 'الشكاوى',
            'logs' => 'السجلات',
            'roles' => 'الأدوار والصلاحيات',
            'attachments' => 'المرفقات',
            'tasks' => 'المهام العامة',
            'audits' => 'سجلات النظام (Logs)',
            'website' => 'إدارة الموقع الإلكتروني',
            'mobile' => 'إدارة تطبيق الموبايل',
        ];

        $permissions = $role->permissions->groupBy(function ($p) use ($groupLabels) {
            $prefix = explode('.', $p->key)[0];
            return $groupLabels[$prefix] ?? ucfirst($prefix);
        });

        return view('roles.show', compact('role', 'permissions'));
    }
}
