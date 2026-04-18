<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChartOfAccount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'director_id',
        'code',
        'name',
        'type',
        'description',
        'budget_limit',
        'is_active',
        'created_by',
    ];

    public function director(): BelongsTo
    {
        return $this->belongsTo(User::class, 'director_id');
    }

    protected $casts = [
        'is_active' => 'boolean',
        'budget_limit' => 'decimal:2',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function expenseLines(): HasMany
    {
        return $this->hasMany(ExpenseLine::class, 'category_id');
    }
}

