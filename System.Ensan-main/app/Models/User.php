<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use \App\Traits\UploadsImages;

    protected $fillable = ['name', 'email', 'password', 'phone', 'role', 'otp_code', 'otp_expires_at', 'is_employee', 'is_volunteer', 'active', 'registration_source', 'department', 'job_title', 'annual_leave_quota', 'leave_balance', 'salary', 'join_date', 'college', 'governorate', 'city', 'project_role', 'volunteer_hours', 'project_id', 'campaign_id', 'guest_house_id', 'profile_photo_path', 'contract_image', 'criminal_record_image', 'id_card_image', 'contract_start_date', 'contract_end_date'];

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
            'expires_at' => null,
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
    protected $hidden = ['password'];

    protected $casts = [
        'is_employee' => 'boolean',
        'is_volunteer' => 'boolean',
        'active' => 'boolean',
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
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

    public function hasRole($key)
    {
        return $this->roles->contains(function ($role) use ($key) {
            return strtolower($role->key) === strtolower($key);
        });
    }


    public function hasPermission($key)
    {
        // Check if we already loaded roles with permissions to avoid N+1
        // If not, this might trigger lazy loading
        if ($this->roles->contains('key', 'admin') || $this->roles->contains('key', 'manager')) {
            return true;
        }

        foreach ($this->roles as $role) {
            if ($role->permissions->contains('key', $key)) {
                return true;
            }
        }

        return false;
    }
    public function adjustLeaveBalance($days)
    {
        $this->leave_balance += $days;
        return $this->save();
    }
}
