<?php

declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Task extends Model
{
    protected $fillable = ['title','volunteer_activity_name','description','assigned_to','assigned_by','due_date','status','project_id','campaign_id','guest_house_id','rating','evaluation_notes'];

    protected $casts = ['due_date' => 'date'];

    public function assignee(): BelongsTo { return $this->belongsTo(User::class, 'assigned_to'); }
    public function assigner(): BelongsTo { return $this->belongsTo(User::class, 'assigned_by'); }
    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function campaign(): BelongsTo { return $this->belongsTo(Campaign::class); }
    public function guestHouse(): BelongsTo { return $this->belongsTo(GuestHouse::class); }
}
