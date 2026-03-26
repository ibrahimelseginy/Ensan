<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Beneficiary extends Model
{
    protected $fillable = [
        'code', 'full_name', 'national_id', 'phone', 'address', 'assistance_type', 'status', 'project_id', 'campaign_id', 'guest_house_id', 'notes', 'rejection_reason',
        'mother_name', 'children_names', 'backup_phone', 'children_count', 'sponsored_children_count', 'study_grade', 'poultry_type', 'notes_cases', 'meat',
        'allocation_type', 'child_sponsorship_type'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
    public function guestHouse(): BelongsTo
    {
        return $this->belongsTo(GuestHouse::class);
    }
    public function attachments()
    {
        return $this->morphMany(Attachment::class , 'attachable', 'entity_type', 'entity_id');
    }
    public function getNameAttribute()
    {
        return $this->full_name;
    }
}
