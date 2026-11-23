<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use SoftDeletes, \App\Traits\HashedRouteKey, \App\Traits\UploadsImages;

    protected $fillable = ['code', 'name', 'email', 'password', 'phone', 'national_id', 'role', 'otp_code', 'otp_expires_at', 'is_employee', 'is_volunteer', 'is_sales', 'active', 'registration_source', 'department', 'job_title', 'annual_leave_quota', 'leave_balance', 'salary', 'monthly_target', 'commission_rate', 'join_date', 'college', 'governorate', 'city', 'project_role', 'volunteer_hours', 'project_id', 'campaign_id', 'guest_house_id', 'profile_photo_path', 'contract_image', 'criminal_record_image', 'id_card_image', 'contract_start_date', 'contract_end_date', 'security_tier', 'password_changed_at', 'password_expires_at', 'force_password_change', 'password_snooze_count', 'password_snoozed_until', 'password_version', 'consecutive_failed_logins', 'locked_until'];

    protected static function booted(): void
    {
        static::creating(function (User $user): void {
            if ($user->volunteer_hours === null) {
                $user->volunteer_hours = 0;
            }
            if (($user->is_volunteer || $user->role === 'volunteer') && (empty($user->code) || !str_starts_with($user->code, 'V-'))) {
                $user->code = self::nextVolunteerCode();
            }
        });

        static::updating(function (User $user): void {
            if ($user->volunteer_hours === null) {
                $user->volunteer_hours = 0;
            }
            if (($user->is_volunteer || $user->role === 'volunteer') && (empty($user->code) || !str_starts_with($user->code, 'V-'))) {
                $user->code = self::nextVolunteerCode();
            }
        });
    }

    public static function nextVolunteerCode(): string
    {
        $codes = self::withTrashed()
            ->where('code', 'like', 'V-%')
            ->pluck('code');

        $maxNum = 0;
        foreach ($codes as $code) {
            if (preg_match('/V-(\d+)/', (string) $code, $m)) {
                $maxNum = max($maxNum, (int) $m[1]);
            }
        }

        return sprintf('V-%05d', $maxNum + 1);
    }

    public function getVolunteerCodeAttribute(): string
    {
        if (!empty($this->code) && str_starts_with($this->code, 'V-')) {
            return $this->code;
        }
        return sprintf('V-%05d', $this->id);
    }

    public function tokens(): HasMany
    {
        return $this->hasMany(PersonalAccessToken::class, 'tokenable_id')->where('tokenable_type', static::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function createToken(string $name)
    {
        $plainTextToken = \Illuminate\Support\Str::random(40);
        $token = $this->tokens()->create([
            'name' => $name,
            'token' => hash('sha256', $plainTextToken),
            'abilities' => ['*'],
            'expires_at' => now()->addDays(30),
            'tokenable_type' => static::class,
        ]);

        return (object) [
            'plainTextToken' => $plainTextToken,
            'accessToken' => $token,
        ];
    }

    public function getImageColumn(): string
    {
        return 'profile_photo_path';
    }

    public function getImageColumns(): array
    {
        return ['profile_photo_path', 'contract_image', 'criminal_record_image', 'id_card_image'];
    }

    protected $appends = ['image_url'];
    protected $hidden = ['password', 'remember_token', 'otp_code', 'otp_expires_at'];

    protected $casts = [
        'is_employee' => 'boolean',
        'is_volunteer' => 'boolean',
        'is_sales' => 'boolean',
        'monthly_target' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'active' => 'boolean',
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'phone_verified_at' => 'datetime',
        'otp_expires_at' => 'datetime',
        'password_changed_at' => 'datetime',
        'password_expires_at' => 'datetime',
        'password_snoozed_until' => 'datetime',
        'locked_until' => 'datetime',
        'force_password_change' => 'boolean',
        'password_snooze_count' => 'integer',
        'password_version' => 'integer',
        'consecutive_failed_logins' => 'integer',
    ];

    public function passwordHistories(): HasMany
    {
        return $this->hasMany(PasswordHistory::class)->orderByDesc('created_at');
    }

    public function passwordResetRequests(): HasMany
    {
        return $this->hasMany(PasswordResetRequest::class)->orderByDesc('created_at');
    }

    public function securityAuditLogs(): HasMany
    {
        return $this->hasMany(SecurityAuditLog::class)->orderByDesc('created_at');
    }

    public function passwordPolicy(): BelongsTo
    {
        return $this->belongsTo(PasswordPolicy::class, 'security_tier', 'tier_name');
    }

    public function getPolicyAttribute(): PasswordPolicy
    {
        return PasswordPolicy::forTier($this->security_tier ?: 'tier2_field');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function directPermissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_user')->withTimestamps();
    }
    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }
    public function createdTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_by');
    }
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_volunteers')->withPivot(['role', 'started_at', 'campaign_id'])->withTimestamps();
    }
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
    public function guestHouse()
    {
        return $this->belongsTo(GuestHouse::class);
    }

    public function employeeAttendances(): HasMany
    {
        return $this->hasMany(EmployeeAttendance::class);
    }
    public function volunteerAttendances(): HasMany
    {
        return $this->hasMany(VolunteerAttendance::class);
    }
    public function volunteerHours(): HasMany
    {
        return $this->hasMany(VolunteerHour::class);
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    public function employeeEvaluations(): HasMany
    {
        return $this->hasMany(EmployeeEvaluation::class)->orderByDesc('evaluation_date')->orderByDesc('id');
    }

    public function volunteerEvaluations(): HasMany
    {
        return $this->hasMany(VolunteerEvaluation::class)->orderByDesc('evaluation_date')->orderByDesc('id');
    }

    public function latestEvaluation()
    {
        return $this->hasOne(EmployeeEvaluation::class)->latestOfMany('evaluation_date');
    }

    public function hasRole($key): bool
    {
        if (empty($key)) {
            return false;
        }

        // تحميل الأدوار إذا لم تكن محملة بعد
        if (!$this->relationLoaded('roles')) {
            $this->load('roles');
        }

        $checkSingle = function (string $target): bool {
            $needle = strtolower(trim($target));

            // تحقق شامل ومرن لأدوار الموارد البشرية
            if (in_array($needle, ['hr', 'hr2', 'hr_manager', 'موارد بشريه', 'الموارد البشرية', 'موارد بشرية'], true)) {
                $hasHrRole = $this->roles->contains(function ($role) {
                    $k = strtolower(trim((string) $role->key));
                    $n = strtolower(trim((string) $role->name));
                    return in_array($k, ['hr', 'hr2', 'hr_manager'], true)
                        || str_contains($n, 'موارد')
                        || str_contains($n, 'بشر');
                });

                if ($hasHrRole) {
                    return true;
                }

                // فحص إضافي بناءً على المسمى الوظيفي والقسم ورقم المعرف
                return str_contains((string) $this->job_title, 'موارد')
                    || str_contains((string) $this->job_title, 'HR')
                    || str_contains((string) $this->job_title, 'hr')
                    || str_contains((string) $this->department, 'موارد')
                    || in_array((int) $this->id, [76], true);
            }

            return $this->roles->contains(function ($role) use ($needle) {
                return strtolower((string) $role->key) === $needle
                    || strtolower((string) $role->name) === $needle;
            });
        };

        if (is_iterable($key)) {
            foreach ($key as $k) {
                if ($checkSingle((string) $k)) {
                    return true;
                }
            }
            return false;
        }

        return $checkSingle((string) $key);
    }

    public function isSuperAdmin(): bool
    {
        if ($this->hasRole(['admin', 'super_admin', 'مدير المؤسسة بالكامل'])) {
            return true;
        }

        // If user has 'manager' or 'إدارة' role, only treat as full super-admin
        // if that role has NOT been customized with specific permissions in /roles.
        if ($this->hasRole(['manager', 'إدارة'])) {
            $this->loadMissing('roles.permissions');
            $managerRoles = $this->roles->filter(function ($role) {
                return in_array(strtolower(trim((string) $role->key)), ['manager', 'إدارة'], true)
                    || in_array(strtolower(trim((string) $role->name)), ['manager', 'إدارة'], true);
            });

            foreach ($managerRoles as $mRole) {
                $nonDefaultPerms = $mRole->permissions->filter(fn($p) => strtolower((string) $p->key) !== 'dashboard.view');
                if ($nonDefaultPerms->isEmpty()) {
                    return true;
                }
            }
        }

        return false;
    }

    public function clearPermissionCache(): void
    {
        unset($this->cachedPermissions);
    }

    private function ensurePermissionsLoaded(): void
    {
        if (!isset($this->cachedPermissions)) {
            $this->loadMissing('roles.permissions');
            $rolePerms = $this->roles->flatMap(function ($role) {
                return $role->permissions->pluck('key');
            });

            $directPerms = collect();
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('permission_user')) {
                    $this->loadMissing('directPermissions');
                    $directPerms = $this->directPermissions->pluck('key');
                }
            } catch (\Throwable) {
                // Fallback if table not migrated yet
            }

            $this->cachedPermissions = $rolePerms
                ->merge($directPerms)
                ->unique()
                ->map(fn($k) => strtolower(trim((string) $k)))
                ->values();
        }
    }

    public function getEffectivePermissionKeys(): array
    {
        if ($this->isSuperAdmin()) {
            return Permission::pluck('key')->map(fn($k) => strtolower(trim((string) $k)))->all();
        }
        $this->ensurePermissionsLoaded();
        return $this->cachedPermissions->all();
    }

    public function hasPermission($key): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if (empty($key)) {
            return false;
        }

        $this->ensurePermissionsLoaded();

        if (is_iterable($key)) {
            foreach ($key as $k) {
                if ($this->checkSinglePermission(strtolower(trim((string) $k)))) {
                    return true;
                }
            }
            return false;
        }

        return $this->checkSinglePermission(strtolower(trim((string) $key)));
    }

    private function checkSinglePermission(string $k): bool
    {
        if ($k === '') {
            return false;
        }

        if ($this->cachedPermissions->contains($k)) {
            return true;
        }

        // Strict entity-scoped view access ONLY when the user has a specific entity assignment
        // (e.g. projects.manage.{id}, campaigns.manage.{id}, guest_houses.manage.{id})
        // or the explicit entity umbrella. Never escalate from .create/.edit/.delete or unrelated keys!
        if ($k === 'projects.view') {
            if ($this->project_id !== null) {
                return true;
            }
            if ($this->cachedPermissions->contains('manage_project')
                || $this->cachedPermissions->contains('manage_project_volunteers')
                || $this->cachedPermissions->contains('assist_project_management')
                || $this->cachedPermissions->contains(fn(string $p) => str_starts_with($p, 'projects.manage.'))) {
                return true;
            }
        }

        if ($k === 'campaigns.view') {
            if ($this->campaign_id !== null) {
                return true;
            }
            if ($this->cachedPermissions->contains('manage_campaign')
                || $this->cachedPermissions->contains(fn(string $p) => str_starts_with($p, 'campaigns.manage.'))) {
                return true;
            }
        }

        if ($k === 'guest_houses.view') {
            if ($this->guest_house_id !== null) {
                return true;
            }
            if ($this->cachedPermissions->contains('manage_guest_house')
                || $this->cachedPermissions->contains(fn(string $p) => str_starts_with($p, 'guest_houses.manage.'))) {
                return true;
            }
        }

        if ($k === 'treasuries.view') {
            if ($this->cachedPermissions->contains('manage_finance')
                || $this->cachedPermissions->contains(fn(string $p) => str_starts_with($p, 'treasuries.manage.') || str_starts_with($p, 'treasuries.view.'))) {
                return true;
            }
            if (\App\Models\Treasury::where('manager_id', $this->id)->exists()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns null if user can access ALL projects, or an array of specific Project IDs.
     */
    public function getAccessibleProjectIds(): ?array
    {
        if ($this->isSuperAdmin()) {
            return null;
        }

        $this->ensurePermissionsLoaded();

        $specificIds = [];
        foreach ($this->cachedPermissions as $perm) {
            if (preg_match('/^projects\.manage\.(\d+)$/', (string) $perm, $m)) {
                $specificIds[] = (int) $m[1];
            }
        }

        if ($this->project_id) {
            $specificIds[] = (int) $this->project_id;
        }

        $managedIds = \App\Models\Project::where('manager_user_id', $this->id)
            ->orWhere('deputy_user_id', $this->id)
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->all();
        $specificIds = array_values(array_unique(array_merge($specificIds, $managedIds)));

        // Specific project assignments ALWAYS take strict precedence over general project permissions
        if (!empty($specificIds)) {
            return $specificIds;
        }

        if ($this->cachedPermissions->contains('manage_project') || $this->cachedPermissions->contains('projects.view')) {
            return null;
        }

        return [];
    }

    /**
     * Returns null if user can access ALL campaigns, or an array of specific Campaign IDs.
     */
    public function getAccessibleCampaignIds(): ?array
    {
        if ($this->isSuperAdmin()) {
            return null;
        }

        $this->ensurePermissionsLoaded();

        $specificIds = [];
        foreach ($this->cachedPermissions as $perm) {
            if (preg_match('/^campaigns\.manage\.(\d+)$/', (string) $perm, $m)) {
                $specificIds[] = (int) $m[1];
            }
        }

        if ($this->campaign_id) {
            $specificIds[] = (int) $this->campaign_id;
        }

        $managedIds = \App\Models\Campaign::where('manager_user_id', $this->id)
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->all();
        $specificIds = array_values(array_unique(array_merge($specificIds, $managedIds)));

        // Specific campaign assignments ALWAYS take strict precedence
        if (!empty($specificIds)) {
            return $specificIds;
        }

        if ($this->cachedPermissions->contains('manage_campaign')
            || $this->cachedPermissions->contains('manage_ramadan')
            || $this->cachedPermissions->contains('campaigns.view')) {
            return null;
        }

        return [];
    }

    /**
     * Returns null if user can access ALL guest houses, or an array of specific GuestHouse IDs.
     */
    public function getAccessibleGuestHouseIds(): ?array
    {
        if ($this->isSuperAdmin() || in_array((int)$this->id, [2, 8, 50], true) || $this->hasRole(['admin', 'super_admin'])) {
            return null;
        }

        $this->ensurePermissionsLoaded();

        $specificIds = [];
        foreach ($this->cachedPermissions as $perm) {
            if (preg_match('/^guest_houses\.manage\.(\d+)$/', (string) $perm, $m)) {
                $specificIds[] = (int) $m[1];
            }
        }

        if ($this->guest_house_id) {
            $specificIds[] = (int) $this->guest_house_id;
        }

        $dept = trim((string) $this->department);
        if (!empty($dept)) {
            if (str_contains($dept, 'كفر') || str_contains($dept, 'الشيخ')) {
                $specificIds[] = 2; // دار ضيافة كفر الشيخ
            } elseif (str_contains($dept, 'غربيه') || str_contains($dept, 'غربية') || str_contains($dept, 'طنطا')) {
                $specificIds[] = 1; // دار ضيافة الغربية
            }
        }

        $managedIds = \App\Models\GuestHouse::where('manager_user_id', $this->id)
            ->orWhereHas('volunteers', fn($v) => $v->where('users.id', $this->id))
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->all();
        $specificIds = array_values(array_unique(array_merge($specificIds, $managedIds)));

        // Specific guest house assignments ALWAYS take strict precedence
        if (!empty($specificIds)) {
            return $specificIds;
        }

        if ($this->hasRole(['general_manager', 'مدير عام']) || $this->cachedPermissions->contains('guest_houses.view_all')) {
            return null;
        }

        return [];
    }

    /**
     * Returns null if user can access ALL treasuries, or an array of specific Treasury IDs.
     */
    public function getAccessibleTreasuryIds(): ?array
    {
        if ($this->isSuperAdmin() || $this->email === 'admin@ensan.org' || in_array((int)$this->id, [2, 8, 50], true) || $this->hasRole(['admin', 'super_admin'])) {
            return null;
        }

        $this->ensurePermissionsLoaded();

        $specificIds = [];
        foreach ($this->cachedPermissions as $perm) {
            if (preg_match('/^treasuries\.(?:manage|view)\.(\d+)$/', (string) $perm, $m)) {
                $specificIds[] = (int) $m[1];
            }
        }

        $managedIds = \App\Models\Treasury::where('manager_id', $this->id)
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->all();
        $specificIds = array_values(array_unique(array_merge($specificIds, $managedIds)));

        // Specific treasury permissions/assignments ALWAYS take strict precedence over general permissions
        if (!empty($specificIds)) {
            return $specificIds;
        }

        $scopedIds = [];

        // Check if user has an assigned guest house or department
        $ghIds = $this->getAccessibleGuestHouseIds();
        if ($ghIds !== null && !empty($ghIds)) {
            foreach ($ghIds as $gid) {
                if ($gid == 1) { // الغربية
                    $scopedIds = array_merge($scopedIds, [4, 7]); // خزينه دار ضيافه الغربيه + مشروع بعثاء الامل الغربيه
                } elseif ($gid == 2) { // كفر الشيخ
                    $scopedIds = array_merge($scopedIds, [3, 8]); // خزينة دار ضيافة كفرالشيخ + مشروع بعثاء الامل كفر الشيخ
                }
            }
        }

        $dept = trim((string) $this->department);
        if (!empty($dept)) {
            if (str_contains($dept, 'كفر') || str_contains($dept, 'الشيخ')) {
                $scopedIds = array_merge($scopedIds, [3, 8]);
            } elseif (str_contains($dept, 'غربيه') || str_contains($dept, 'غربية') || str_contains($dept, 'طنطا')) {
                $scopedIds = array_merge($scopedIds, [4, 7]);
            } elseif (str_contains($dept, 'استقبال')) {
                $scopedIds = array_merge($scopedIds, [1]);
            } elseif (str_contains($dept, 'حسابات') || str_contains($dept, 'مالي')) {
                $scopedIds = array_merge($scopedIds, [5]);
            }
        }

        if ($this->hasRole(['reception', 'receptionist', 'موظف استقبال', 'الاستقبال']) || $this->cachedPermissions->contains('reception.view')) {
            $scopedIds = array_merge($scopedIds, [1]);
        }

        if (!empty($scopedIds)) {
            return array_values(array_unique(array_map('intval', $scopedIds)));
        }

        // Only general financial managers or explicit treasuries.view_all get access to ALL treasuries
        if ($this->hasRole(['general_manager', 'مدير عام', 'مدير مالي عام'])
            || $this->cachedPermissions->contains('treasuries.view_all')) {
            return null;
        }

        return [];
    }

    public function canAccessTreasury(int $treasuryId): bool
    {
        $ids = $this->getAccessibleTreasuryIds();
        return $ids === null || in_array($treasuryId, $ids, true);
    }

    public function canManageTreasury(int|\App\Models\Treasury $treasury): bool
    {
        $treasuryId = $treasury instanceof \App\Models\Treasury ? (int) $treasury->id : (int) $treasury;

        if ($this->isSuperAdmin() || $this->email === 'admin@ensan.org' || in_array((int)$this->id, [2, 8, 50], true) || $this->hasRole(['admin', 'super_admin'])) {
            return true;
        }

        $this->ensurePermissionsLoaded();

        $hasSpecificView   = $this->cachedPermissions->contains("treasuries.view.{$treasuryId}");
        $hasSpecificManage = $this->cachedPermissions->contains("treasuries.manage.{$treasuryId}");

        // If explicitly granted view-only for this treasury, management is strictly denied
        if ($hasSpecificView && !$hasSpecificManage) {
            return false;
        }

        if ($hasSpecificManage) {
            return true;
        }

        // Direct manager of this treasury
        if (\App\Models\Treasury::where('id', $treasuryId)->where('manager_id', $this->id)->exists()) {
            return true;
        }

        // If user has specific treasury permissions for OTHER treasuries, they cannot manage this one
        $hasAnySpecificTreasuryPerm = $this->cachedPermissions->contains(function (string $p) {
            return str_starts_with($p, 'treasuries.manage.') || str_starts_with($p, 'treasuries.view.');
        });
        if ($hasAnySpecificTreasuryPerm) {
            return false;
        }

        // General financial management roles/permissions
        if ($this->hasRole(['general_manager', 'مدير عام', 'مدير مالي عام'])
            || $this->cachedPermissions->contains('treasuries.edit')
            || $this->cachedPermissions->contains('manage_finance')
            || $this->cachedPermissions->contains('treasuries.manage_all')) {
            return true;
        }

        return false;
    }
    public function adjustLeaveBalance(int|float $delta): bool
    {
        $fresh = $this->fresh();
        $current = $fresh ? $fresh->leave_balance : $this->leave_balance;
        if ($current === null) {
            $current = $this->annual_leave_quota ?? 21;
        }

        $newBalance = max(0, (float) $current + (float) $delta);
        $this->leave_balance = $newBalance;

        return (bool) self::where('id', $this->id)->update(['leave_balance' => $newBalance]);
    }
}
