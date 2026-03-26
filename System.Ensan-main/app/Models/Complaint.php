<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Complaint extends Model
{
    protected $fillable = ['source_type','source_id','against_user_id','status','subject','message','attachment_path'];

    public function against(): BelongsTo { return $this->belongsTo(User::class, 'against_user_id'); }
}
