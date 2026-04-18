<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IncomeReport extends Model
{
    protected $fillable = [
        'director_id',
        'title',
        'voucher_no',
        'period_month',
        'period_year',
        'notes',
        'status',
        'reviewer_id',
        'reviewed_at',
        'version'
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function director(): BelongsTo
    {
        return $this->belongsTo(User::class, 'director_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(IncomeLine::class);
    }

    protected static function booted()
    {
        static::creating(function ($report) {
            if (!$report->voucher_no) {
                $report->voucher_no = static::generateNextVoucherNo();
            }
        });
    }

    public static function generateNextVoucherNo()
    {
        $now = \Carbon\Carbon::now();
        $prefix = 'INC-' . $now->format('Ym') . '-';
        
        $i = 1;
        while (true) {
            $lastReport = static::where('voucher_no', 'like', $prefix . '%')
                ->orderBy('voucher_no', 'desc')
                ->first();

            if ($lastReport) {
                $lastNoPart = substr($lastReport->voucher_no, strrpos($lastReport->voucher_no, '-') + 1);
                $lastNo = intval($lastNoPart);
                $newNo = str_pad($lastNo + $i, 3, '0', STR_PAD_LEFT);
            } else {
                $newNo = str_pad($i, 3, '0', STR_PAD_LEFT);
            }

            $candidate = $prefix . $newNo;
            if (!static::where('voucher_no', $candidate)->exists()) {
                return $candidate;
            }
            $i++;
        }
    }
}
