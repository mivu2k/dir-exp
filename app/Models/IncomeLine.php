<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncomeLine extends Model
{
    protected $fillable = [
        'income_report_id',
        'category_id',
        'date',
        'description',
        'amount',
        'attachment_url'
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(IncomeReport::class, 'income_report_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'category_id');
    }
}
