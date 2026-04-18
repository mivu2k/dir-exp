<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpenseLine extends Model
{
    protected $fillable = [
        'report_id',
        'date',
        'description',
        'category_id',
        'amount',
        'attachment_url',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(ExpenseReport::class, 'report_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'category_id');
    }
}

