<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialClosure extends Model
{
    protected $fillable = ['date', 'branch', 'closed_by', 'approved_by', 'approved'];
    protected $casts = ['date' => 'date', 'approved' => 'boolean'];

    public function getTotalStatsAttribute()
    {
        $donations = Donation::whereDate('received_at', $this->date)
            ->where('type', 'cash')
            ->get();

        $total = $donations->sum('amount');
        $delegate = $donations->where('cash_channel', 'delegate')->sum('amount');
        $office = $total - $delegate;

        return compact('total', 'delegate', 'office');
    }
}
