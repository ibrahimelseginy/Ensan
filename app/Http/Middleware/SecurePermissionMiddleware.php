<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

final class SecurePermissionMiddleware
{
    public function handle(Request $request, Closure $next, string ...$permissions)
    {
        $user = $request->user();
        abort_unless($user instanceof \App\Models\User, 401);

        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        $flat = [];
        foreach ($permissions as $permission) {
            foreach (explode('|', $permission) as $item) {
                $item = trim($item);
                if ($item !== '') {
                    $flat[] = $item;
                }
            }
        }

        abort_unless($user->hasPermission($flat), 403, 'ليس لديك صلاحية للوصول إلى هذا القسم.');

        // Enforce granular action permissions (.create, .edit, .delete) on top-level resource routes
        $routeName = (string) ($request->route()?->getName() ?? '');
        $parts = $routeName !== '' ? explode('.', $routeName) : [];

        if (count($parts) === 2) {
            $resource = str_replace('-', '_', $parts[0]);
            $action = $parts[1];

            if ($resource === 'payroll') {
                $resource = 'payrolls';
            } elseif ($resource === 'closures') {
                $resource = 'financial_closures';
            }

            $actionSuffix = match ($action) {
                'create', 'store' => 'create',
                'edit', 'update', 'bulk' => 'edit',
                'destroy', 'bulk-destroy', 'bulkDestroy' => 'delete',
                default => null,
            };

            if ($actionSuffix !== null) {
                $allowed = $user->hasPermission("{$resource}.{$actionSuffix}");

                if (!$allowed) {
                    // Own user profile edit fallback
                    if ($resource === 'users' && $actionSuffix === 'edit') {
                        $routeUser = $request->route('user');
                        $routeUserId = is_object($routeUser) ? $routeUser->id : $routeUser;
                        if ((int) $routeUserId === (int) $user->id) {
                            $allowed = true;
                        }
                    }

                    // Entity-scoped manager fallbacks for editing their assigned project/campaign/guest house
                    if ($resource === 'projects' && $actionSuffix === 'edit') {
                        $project = $request->route('project');
                        $projectId = is_object($project) ? $project->id : $project;
                        if ($projectId && $user->hasPermission("projects.manage.{$projectId}")) {
                            $allowed = true;
                        }
                    } elseif ($resource === 'campaigns' && $actionSuffix === 'edit') {
                        $campaign = $request->route('campaign');
                        $campaignId = is_object($campaign) ? $campaign->id : $campaign;
                        if ($campaignId && $user->hasPermission("campaigns.manage.{$campaignId}")) {
                            $allowed = true;
                        }
                    } elseif ($resource === 'guest_houses' && $actionSuffix === 'edit') {
                        $gh = $request->route('guest_house');
                        $ghId = is_object($gh) ? $gh->id : $gh;
                        if ($ghId && $user->hasPermission("guest_houses.manage.{$ghId}")) {
                            $allowed = true;
                        }
                    } elseif ($resource === 'treasuries') {
                        $allowed = $user->hasPermission(["accounts.{$actionSuffix}", 'manage_finance']);
                    } elseif ($resource === 'leaves' && $actionSuffix === 'edit') {
                        $allowed = $user->hasPermission('leaves.manage');
                    }
                }

                abort_unless($allowed, 403, "ليس لديك صلاحية تنفيذ هذا الإجراء ({$resource}.{$actionSuffix}).");
            }
        }

        return $next($request);
    }
}
