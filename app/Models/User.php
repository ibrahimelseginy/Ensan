<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use SoftDeletes, \App\Traits\HashedRouteKey, \App\Traits\UploadsImages;

    protected $fillable = ['code', 'name', 'email', 'password', 'phone', 'role', 'otp_code', 'otp_expires_at', 'is_employee', 'is_volunteer', 'is_sales', 'active', 'registration_source', 'department', 'job_title', 'annual_leave_quota', 'leave_balance', 'salary', 'monthly_target', 'commission_rate', 'join_date', 'college', 'governorate', 'city', 'project_role', 'volunteer_hours', 'project_id', 'campaign_id', 'guest_house_id', 'profile_photo_path', 'contract_image', 'criminal_record_image', 'id_card_image', 'contract_start_date', 'contract_end_date'];

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


    public function hasPermission($key): bool
    {
        // Bypass for admin, super_admin, and general managers (in Arabic and English)
        if ($this->hasRole(['admin', 'super_admin', 'manager', 'مدير المؤسسة بالكامل', 'إدارة'])) {
            return true;
        }

        if (empty($key)) {
            return false;
        }

        // Load roles and permissions once and cache them on the model instance
        // to avoid repeated DB hits within the same request.
        if (!isset($this->cachedPermissions)) {
            if (!$this->relationLoaded('roles')) {
                $this->load('roles.permissions');
            }
            $this->cachedPermissions = $this->roles->flatMap(function ($role) {
                return $role->permissions->pluck('key');
            })->unique()->map(fn($k) => strtolower((string) $k));
        }

        if (is_iterable($key)) {
            foreach ($key as $k) {
                if ($this->cachedPermissions->contains(strtolower((string) $k))) {
                    return true;
                }
            }
            return false;
        }

        return $this->cachedPermissions->contains(strtolower((string) $key));
    }
    public function adjustLeaveBalance($days)
    {
        $this->leave_balance += $days;
        return $this->save();
    }
}
