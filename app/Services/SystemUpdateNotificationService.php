<?php

namespace App\Services;

use App\Models\User;
use App\Models\MobileNotification;
use App\Models\InternalMessage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class SystemUpdateNotificationService
{
    /**
     * Define the supported system modules and their metadata.
     */
    public static function getModules(): array
    {
        return [
            'beneficiaries' => [
                'id' => 'beneficiaries',
                'name' => 'قسم الحالات والمستفيدين والبحث الميداني',
                'short_name' => 'الحالات والمستفيدين',
                'icon' => 'bi-heart-pulse-fill',
                'color' => 'success',
                'roles' => ['باحث ميداني', 'باحث إجتماعي', 'مدراء المشاريع'],
                'job_keywords' => ['باحث', 'اجتماعي', 'حالات', 'ميداني', 'رعاية'],
                'permissions' => ['beneficiaries.view', 'cases.view', 'field_visits.view'],
            ],
            'finance' => [
                'id' => 'finance',
                'name' => 'قسم المالية والحسابات والخزينة',
                'short_name' => 'المالية والحسابات',
                'icon' => 'bi-cash-coin',
                'color' => 'primary',
                'roles' => ['الحسابات', 'مدير مالية'],
                'job_keywords' => ['حسابات', 'محاسب', 'مالية', 'خزينة'],
                'permissions' => ['finance.view', 'accounts.view', 'donations.view', 'expenses.view'],
            ],
            'complaints' => [
                'id' => 'complaints',
                'name' => 'قسم الشكاوى والمقترحات وخدمة المستفيدين',
                'short_name' => 'الشكاوى والاستقبال',
                'icon' => 'bi-chat-left-dots-fill',
                'color' => 'warning',
                'roles' => ['الاستقبال'],
                'job_keywords' => ['receptionist', 'استقبال', 'شكاوى', 'خدمة'],
                'permissions' => ['complaints.view', 'complaints.manage'],
            ],
            'guest_house' => [
                'id' => 'guest_house',
                'name' => 'إدارة دار الضيافة لمرضى الأورام',
                'short_name' => 'دار الضيافة',
                'icon' => 'bi-hospital-fill',
                'color' => 'info',
                'roles' => ['مدراء دار الضيافه', 'مدير دار ضيافة كفر الشيخ', 'مدير دار ضيافة طنطا'],
                'job_keywords' => ['دار الضيافة', 'ضيافة', 'فندق', 'سكن'],
                'permissions' => ['guest_house.view'],
            ],
            'hr' => [
                'id' => 'hr',
                'name' => 'قسم الموارد البشرية وشؤون الموظفين',
                'short_name' => 'الموارد البشرية',
                'icon' => 'bi-people-fill',
                'color' => 'secondary',
                'roles' => ['الموارد البشرية'],
                'job_keywords' => ['موارد بشرية', 'hr', 'شؤون موظفين'],
                'permissions' => ['users.view', 'leaves.view'],
            ],
            'projects' => [
                'id' => 'projects',
                'name' => 'إدارة المشاريع الإغاثية (بعثاء الأمل، زاد)',
                'short_name' => 'المشاريع الإغاثية',
                'icon' => 'bi-briefcase-fill',
                'color' => 'teal',
                'roles' => ['مدراء المشاريع', 'مدير مشروع بعثاء الأمل', 'مدير مشروع زاد', 'نائب مدير مشروع'],
                'job_keywords' => ['بعثاء الامل', 'زاد', 'مشروع'],
                'permissions' => ['projects.view'],
            ],
            'logistics' => [
                'id' => 'logistics',
                'name' => 'المستودعات والمخازن والخدمات اللوجستية',
                'short_name' => 'المخازن واللوجستيك',
                'icon' => 'bi-box-seam-fill',
                'color' => 'dark',
                'roles' => ['أمين المخزن', 'اللوجستيك', 'مدير لوجستيك'],
                'job_keywords' => ['مخزن', 'لوجستيك', 'مندوب', 'حركة'],
                'permissions' => ['inventory.view', 'delegates.view'],
            ],
            'all' => [
                'id' => 'all',
                'name' => 'تحديث شامل لكافة أقسام النظام',
                'short_name' => 'جميع أقسام النظام',
                'icon' => 'bi-stars',
                'color' => 'purple',
                'roles' => [],
                'job_keywords' => [],
                'permissions' => [],
            ],
        ];
    }

    /**
     * Find employees matching a specific module.
     *
     * @param string $moduleId
     * @return Collection<User>
     */
    public function getTargetEmployees(string $moduleId): Collection
    {
        $modules = self::getModules();
        $allActive = User::with('roles.permissions')
            ->where(function ($q) {
                $q->where('active', true)->orWhereNull('active');
            })
            ->get();

        if ($moduleId === 'all' || !isset($modules[$moduleId])) {
            return $allActive;
        }

        $config = $modules[$moduleId];
        $targetRoles = $config['roles'] ?? [];
        $jobKeywords = $config['job_keywords'] ?? [];
        $permissions = $config['permissions'] ?? [];

        return $allActive->filter(function ($user) use ($targetRoles, $jobKeywords, $permissions) {
            // 1. Check roles
            foreach ($targetRoles as $r) {
                if ($user->hasRole($r) || str_contains(mb_strtolower((string)$user->role), mb_strtolower($r))) {
                    return true;
                }
            }

            // 2. Check permissions
            foreach ($permissions as $p) {
                if ($user->hasPermission($p)) {
                    return true;
                }
            }

            // 3. Check job_title / department
            $jobTitle = mb_strtolower((string) $user->job_title);
            $department = mb_strtolower((string) $user->department);
            foreach ($jobKeywords as $kw) {
                $kwLower = mb_strtolower($kw);
                if (str_contains($jobTitle, $kwLower) || str_contains($department, $kwLower)) {
                    return true;
                }
            }

            return false;
        })->values();
    }

    /**
     * Broadcast a system update notification signed by Eng. Ibrahim.
     *
     * @param string $moduleId
     * @param string|null $updateDetails
     * @param string|null $customTitle
     * @param bool $notifyChatChannel
     * @return array
     */
    public function broadcastUpdate(
        string $moduleId,
        ?string $updateDetails = null,
        ?string $customTitle = null,
        bool $notifyChatChannel = true
    ): array {
        $modules = self::getModules();
        $module = $modules[$moduleId] ?? $modules['all'];
        $moduleName = $module['name'];
        $moduleShort = $module['short_name'];

        $details = trim((string) $updateDetails);
        if (empty($details)) {
            $details = "تم تحديث وتطوير الجزء الخاص بكم بنجاح لتسهيل العمل ورفع سرعة واستقرار النظام.";
        }

        // Title with Eng. Ibrahim signature
        $title = $customTitle ?: "🛠️ تحديث وتطوير في {$moduleShort}";

        // Body formatted clearly and signed by Eng. Ibrahim Elseginy
        $body = "{$details}\n\n✅ تم إنجاز هذا التحديث لخدمتكم وضمان أفضل أداء للعمل.\n— مهندس إبراهيم السجيني 🛠️";

        $employees = $this->getTargetEmployees($moduleId);
        $notifiedCount = 0;

        foreach ($employees as $emp) {
            try {
                MobileNotification::create([
                    'title' => $title,
                    'body' => $body,
                    'category' => MobileNotification::CATEGORY_GENERAL,
                    'target_type' => 'user',
                    'target_user_id' => $emp->id,
                    'target_audience' => $emp->name,
                    'is_sent' => true,
                    'sent_at' => now(),
                ]);
                $notifiedCount++;
            } catch (\Throwable $e) {
                Log::error("Failed to notify user {$emp->id} for system update: " . $e->getMessage());
            }
        }

        // Optional broadcast in the internal team channel
        if ($notifyChatChannel) {
            try {
                $ibrahimUser = User::where('name', 'like', '%ابراهيم%')
                    ->orWhere('email', 'like', '%ibrahim%')
                    ->orWhere('role', 'admin')
                    ->first() ?: User::first();

                if ($ibrahimUser) {
                    $channelMsg = "📢 **تحديث نظام جديد ({$moduleShort})**\n" .
                                  "{$details}\n" .
                                  "تم توجيه التحديث للموظفين المعنيين.\n— **مهندس إبراهيم السجيني** 🛠️";

                    InternalMessage::create([
                        'sender_id' => $ibrahimUser->id,
                        'receiver_id' => null,
                        'is_bot' => false,
                        'target_type' => ($moduleId === 'all') ? 'all' : 'only',
                        'visible_to_users' => ($moduleId === 'all') ? null : $employees->pluck('id')->values()->all(),
                        'message' => $channelMsg,
                        'read_at' => null,
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error("Failed to post update to chat channel: " . $e->getMessage());
            }
        }

        return [
            'success' => true,
            'module' => $moduleId,
            'module_name' => $moduleName,
            'notified_count' => $notifiedCount,
            'employees' => $employees->pluck('name')->toArray(),
            'title' => $title,
            'body' => $body,
        ];
    }
}
