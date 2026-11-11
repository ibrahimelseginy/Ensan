<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use SoftDeletes, \App\Traits\HashedRouteKey, \App\Traits\UploadsImages;

    protected $fillable = ['code', 'name', 'email', 'password', 'phone', 'national_id', 'role', 'otp_code', 'otp_expires_at', 'is_employee', 'is_volunteer', 'is_sales', 'active', 'registration_source', 'department', 'job_title', 'annual_leave_quota', 'leave_balance', 'salary', 'monthly_target', 'commission_rate', 'join_date', 'college', 'governorate', 'city', 'project_role', 'volunteer_hours', 'project_id', 'campaign_id', 'guest_house_id', 'profile_photo_path', 'contract_image', 'criminal_record_image', 'id_card_image', 'contract_start_date', 'contract_end_date'];

    protected static function booted(): void
    {
        static::creating(function (User $user): void {
            if (($user->is_volunteer || $user->role === 'volunteer') && (empty($user->code) || !str_starts_with($user->code, 'V-'))) {
                $user->code = self::nextVolunteerCode();
            }
        });

        static::updating(function (User $user): void {
            if (($user->is_volunteer || $user->role === 'volunteer') && (empty($user->code) || !str_starts_with($user->code, 'V-'))) {
                $user->code = self::nextVolunteerCode();
            }
        });
    }

    public static function nextVolunteerCode(): string
    {
        $codes = self::query()
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
        'otp_expires_at' => 'datetime'
    ];

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

        if (is_iterable($key)) {
            $keys = array_map(fn($k) => strtolower(trim((string) $k)), is_array($key) ? $key : iterator_to_array($key));
            return $this->roles->contains(function ($role) use ($keys) {
                return in_array(strtolower((string) $role->key), $keys, true)
                    || in_array(strtolower((string) $role->name), $keys, true);
            });
        }

        $needle = strtolower(trim((string) $key));
        return $this->roles->contains(function ($role) use ($needle) {
            return strtolower((string) $role->key) === $needle
                || strtolower((string) $role->name) === $needle;
        });
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
        if ($this->isSuperAdmin()) {
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

        if ($this->cachedPermissions->contains('manage_guest_house')
            || $this->cachedPermissions->contains('guest_houses.view_all')
            || $this->cachedPermissions->contains('guest_houses.view')) {
            return null;
        }

        return [];
    }
    public function adjustLeaveBalance($days)
    {
        $this->leave_balance += $days;
        return $this->save();
    }
}
