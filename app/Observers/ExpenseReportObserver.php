<?php

namespace App\Observers;

use App\Models\ExpenseReport;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ExpenseReportObserver
{
    /**
     * Handle the ExpenseReport "created" event.
     */
    public function created(ExpenseReport $expenseReport): void
    {
        $this->logAction($expenseReport, 'created', null, $expenseReport->toArray());
    }

    /**
     * Handle the ExpenseReport "updated" event.
     */
    public function updated(ExpenseReport $expenseReport): void
    {
        $old = $expenseReport->getOriginal();
        $new = $expenseReport->getAttributes();
        
        // Filter out unchanged fields to keep logs clean
        $changed = array_diff_assoc($new, $old);
        if (empty($changed)) return;

        $action = 'updated';
        // Special actions based on status change
        if ($expenseReport->wasChanged('status')) {
            $action = 'status_changed_' . $expenseReport->status;
        }

        $this->logAction($expenseReport, $action, $old, $new);
    }

    /**
     * Handle the ExpenseReport "deleted" event.
     */
    public function deleted(ExpenseReport $expenseReport): void
    {
        $this->logAction($expenseReport, 'deleted', $expenseReport->toArray(), null);
    }

    private function logAction(ExpenseReport $report, string $action, ?array $old, ?array $new): void
    {
        $user = Auth::user();
        if (!$user) return;

        AuditLog::create([
            'user_id' => $user->id,
            'role' => $user->roles->first()?->name ?? 'unknown',
            'action' => $action,
            'entity_type' => 'ExpenseReport',
            'entity_id' => $report->id,
            'old_value' => $old,
            'new_value' => $new,
            'ip_address' => Request::ip(),
            'timestamp' => now(),
        ]);
    }
}
