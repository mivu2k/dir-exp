<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'role',
        'action',
        'entity_type',
        'entity_id',
        'old_value',
        'new_value',
        'ip_address',
        'timestamp',
    ];

    protected $casts = [
        'old_value' => 'json',
        'new_value' => 'json',
        'timestamp' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

