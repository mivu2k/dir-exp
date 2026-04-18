<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpenseReportVersion extends Model
{
    protected $fillable = [
        'report_id',
        'version_no',
        'snapshot_data',
    ];

    protected $casts = [
        'snapshot_data' => 'json',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(ExpenseReport::class, 'report_id');
    }
}

