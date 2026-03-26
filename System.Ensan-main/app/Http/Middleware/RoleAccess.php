<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (!$user) {
            abort(403);
        }

        // Admin bypass
        if ($user->roles->contains('key', 'admin')) {
            return $next($request);
        }

        $routeName = $request->route() ? $request->route()->getName() : null;
        if (!$routeName) {
            return $next($request);
        }

        $parts = explode('.', $routeName);
        if (count($parts) < 2) {
            return $next($request);
        }

        $resourceRaw = $parts[0];
        $action = end($parts); // Use the last part as the action (e.g. index, store, etc.)

        // Normalize resource name (e.g. guest-houses -> guest_houses)
        $resource = str_replace('-', '_', $resourceRaw);

        // Special resource mapping
        if ($resource === 'payrolls') {
            $resource = 'payroll';
        }
        if ($resource === 'closures') {
            $resource = 'financial_closures';
        }

        // Mapping actions to permission suffixes
        $map = [
            'index' => 'view',
            'show' => 'view',
            'export' => 'view',
            'reports' => 'view',
            'create' => 'create',
            'store' => 'create',
            'edit' => 'edit',
            'update' => 'edit',
            'bulk' => 'edit',
            'destroy' => 'delete',

            // Custom actions
            'volunteers' => 'volunteers',
            'attachVolunteer' => 'volunteers',
            'detachVolunteer' => 'volunteers',
            'storeMonthlyVolunteer' => 'volunteers',
            'destroyMonthlyVolunteer' => 'volunteers',
            'setManager' => 'edit',
            'setDeputy' => 'edit',
            'storeActivity' => 'edit',
            'destroyActivity' => 'edit',
            'storeDailyMenu' => 'edit',
            'destroyDailyMenu' => 'edit',
            'rentals' => 'rentals',
            'storeRental' => 'rentals',
            'updateRentalStatus' => 'rentals',
            'destroyRental' => 'rentals',
            'storeTrip' => 'edit',
            'destroyTrip' => 'edit',
            'updateTripStatus' => 'edit',
            'evaluations' => 'evaluations',

            // Marketing & Content Actions
            'content' => 'view',
            'settings' => 'edit',
            'dashboard' => 'view',
            'status' => 'edit',
            'reorder' => 'edit',
            'read' => 'view',
            'markRead' => 'edit',
        ];

        $suffix = $map[$action] ?? null;

        // Specific overrides for guest_houses
        if ($resource === 'guest_houses') {
            if ($action === 'setManager') {
                $suffix = 'set_manager';
            } elseif (in_array($action, ['attachVolunteer', 'detachVolunteer', 'volunteers'])) {
                $suffix = 'manage_volunteers';
            } elseif (in_array($action, ['storeMonthlyVolunteer', 'destroyMonthlyVolunteer'])) {
                $suffix = 'manage_monthly_volunteers';
            }
        }

        if (!$suffix) {
            return $next($request);
        }

        $permissionKey = "$resource.$suffix";

        // Special handling for complaints: Allow index if user has create permission
        if ($resource === 'complaints' && $suffix === 'view') {
            $hasComplaintAccess = $user->roles()->whereHas('permissions', function ($q) {
                $q->whereIn('key', ['complaints.view', 'complaints.create']);
            })->exists();

            if ($hasComplaintAccess) {
                return $next($request);
            }
        }

        // Special handling for employee tasks: Allow view if user has view_own permission
        if ($resource === 'employee_tasks' && $suffix === 'view') {
            $hasTaskAccess = $user->roles()->whereHas('permissions', function ($q) {
                $q->whereIn('key', ['employee_tasks.view', 'employee_tasks.view_own']);
            })->exists();

            if ($hasTaskAccess) {
                return $next($request);
            }
        }

        $enforcedResources = [
            'users',
            'roles',
            'delegates',
            'projects',
            'campaigns',
            'guest_houses',
            'workspaces',
            'expenses',
            'payroll',
            'reports',
            'donors',
            'donations',
            'beneficiaries',
            'travel_routes',
            'trips',
            'volunteers',
            'volunteer_attendance',
            'volunteer_tasks',
            'volunteer_hours',
            'employee_attendance',
            'employee_tasks',
            'hr',
            'accounts',
            'journal_entries',
            'financial_closures',
            'warehouses',
            'items',
            'inventory_transactions',
            'complaints',
            'audits',
            'tasks',
            'attachments',
            'notifications',
            'dashboard',
            'website',
            'mobile'
        ];

        if (!in_array($resource, $enforcedResources)) {
            return $next($request);
        }

        // Payroll manage check
        // if ($resource === 'payroll') {
        //     $permissionKey = 'payroll.manage';
        // }

        // Special override for Finance role (Accountant)
        if ($user->roles->contains('key', 'finance')) {
            // Full access: Financial records and Users
            if (in_array($resource, ['accounts', 'journal_entries', 'users', 'expenses'])) {
                return $next($request);
            }
            // Read-only access: Strategic viewing for Net Flow and Inventory
            if (in_array($resource, ['donations', 'warehouses', 'items', 'beneficiaries', 'complaints']) && $suffix === 'view') {
                return $next($request);
            }
        }

        // Special override: Allow HR to access all HR related resources
        $hrResources = [
            'users', 
            'employee_attendance', 
            'leaves', 
            'employee_tasks', 
            'payroll', 
            'payrolls', // handling both just in case
            'volunteers', 
            'volunteer_attendance', 
            'volunteer_tasks', 
            'volunteer_hours', 
            'hr', // for evaluations
            'tasks'
        ];
        if (in_array($resource, $hrResources) && $user->roles->contains('key', 'hr')) {
            return $next($request);
        }

        // Allow user to view their own profile
        if ($resource === 'users' && $suffix === 'view') {
            $routeUser = $request->route('user');
            $routeUserId = is_object($routeUser) ? $routeUser->id : $routeUser;

            if ((int) $routeUserId === (int) $user->id) {
                return $next($request);
            }
        }



        // Check for specific permission
        $hasPermission = $user->roles()->whereHas('permissions', function ($q) use ($permissionKey) {
            $q->where('key', $permissionKey);
        })->exists();

        // FALLBACK: Check for "view_own" permission if the action is "view"
        if (!$hasPermission && $suffix === 'view') {
            $hasPermission = $user->roles()->whereHas('permissions', function ($q) use ($resource) {
                $q->where('key', $resource . '.view_own');
            })->exists();
        }

        if (!$hasPermission) {
            abort(403, 'ليس لديك صلاحية للقيام بهذا الإجراء (' . $permissionKey . ')');
        }

        return $next($request);
    }
}
