<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IncomeReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncomeApprovalController extends Controller
{
    public function index()
    {
        $reports = IncomeReport::with('director')
            ->where('status', 'submitted')
            ->orderBy('created_at', 'asc')
            ->paginate(10);

        return view('admin.income_approvals.index', compact('reports'));
    }

    public function show(IncomeReport $incomeReport)
    {
        $incomeReport->load(['lines.category', 'director']);
        return view('admin.income_approvals.show', compact('incomeReport'));
    }

    public function approve(IncomeReport $incomeReport)
    {
        $incomeReport->update([
            'status' => 'approved',
            'reviewer_id' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->route('admin.income_approvals.index')->with('success', 'Revenue report approved and logged.');
    }

    public function reject(Request $request, IncomeReport $incomeReport)
    {
        $request->validate(['reason' => 'required|string|max:500']);

        $incomeReport->update([
            'status' => 'rejected',
            'notes' => $incomeReport->notes . "\n\nREJECTION REASON: " . $request->reason,
        ]);

        return redirect()->route('admin.income_approvals.index')->with('error', 'Revenue report rejected and returned for revision.');
    }
}
