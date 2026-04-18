<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseReport extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'director_id',
        'voucher_no',
        'title',
        'period_month',
        'period_year',
        'notes',
        'status',
        'version',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'period_month' => 'integer',
        'period_year' => 'integer',
        'version' => 'integer',
    ];

    public function director(): BelongsTo
    {
        return $this->belongsTo(User::class, 'director_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(ExpenseLine::class, 'report_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(ExpenseReportVersion::class, 'report_id');
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
        $prefix = 'DIR-' . $now->format('Ym') . '-';
        
        $i = 1;
        while (true) {
            $lastReport = static::withTrashed()
                ->where('voucher_no', 'like', $prefix . '%')
                ->orderBy('voucher_no', 'desc')
                ->first();

            if ($lastReport) {
                // Ensure we handle potential non-numeric endings gracefully if needed
                $lastNoPart = substr($lastReport->voucher_no, strrpos($lastReport->voucher_no, '-') + 1);
                $lastNo = intval($lastNoPart);
                $newNo = str_pad($lastNo + $i, 3, '0', STR_PAD_LEFT);
            } else {
                $newNo = str_pad($i, 3, '0', STR_PAD_LEFT);
            }

            $candidate = $prefix . $newNo;
            if (!static::withTrashed()->where('voucher_no', $candidate)->exists()) {
                return $candidate;
            }
            $i++; // If collision, try next
        }
    }
}

