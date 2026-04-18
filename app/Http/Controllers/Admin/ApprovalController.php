<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ExpenseReport;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ApprovalController extends Controller
{
    /**
     * Display a queue of all Submitted reports.
     */
    public function index(Request $request)
    {
        $query = ExpenseReport::with(['director', 'lines']);

        // Filters
        if ($request->filled('director_id')) {
            $query->where('director_id', $request->director_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Default to showing Submitted first
            $query->orderByRaw("CASE WHEN status = 'submitted' THEN 0 ELSE 1 END");
        }

        $reports = $query->orderBy('submitted_at', 'desc')->paginate(15)->withQueryString();
        $directors = User::role('director')->get();

        return view('admin.approvals.index', compact('reports', 'directors'));
    }

    /**
     * View detailed report for review.
     */
    public function show(ExpenseReport $report)
    {
        $report->load(['lines.category', 'director', 'reviewer', 'versions']);
        return view('admin.approvals.show', compact('report'));
    }

    /**
     * Approve the report.
     */
    public function approve(Request $request, ExpenseReport $report)
    {
        if ($report->status !== 'submitted') {
            return back()->with('error', 'Only submitted reports can be approved.');
        }

        $report->update([
            'status' => 'approved',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => Carbon::now(),
            'rejection_reason' => null, // Clear past rejections
        ]);

        return redirect()->route('admin.approvals.index')->with('success', "Report {$report->voucher_no} approved and locked.");
    }

    /**
     * Reject the report.
     */
    public function reject(Request $request, ExpenseReport $report)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        if ($report->status !== 'submitted') {
            return back()->with('error', 'Only submitted reports can be rejected.');
        }

        $report->update([
            'status' => 'rejected',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => Carbon::now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return redirect()->route('admin.approvals.index')->with('success', "Report {$report->voucher_no} rejected and sent back to director.");
    }

    /**
     * Reverse Approval (Admin move Approved back to Submitted).
     */
    public function reverse(Request $request, ExpenseReport $report)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        if ($report->status !== 'approved') {
            return back()->with('error', 'Only approved reports can be reversed.');
        }

        DB::beginTransaction();
        try {
            // Log the reverse action (Audit trail will be implemented separately, but we logic it here)
            $report->update([
                'status' => 'submitted',
                'reviewed_by' => null,
                'reviewed_at' => null,
                'notes' => ($report->notes ? $report->notes . "\n" : "") . "[REVERSED by " . Auth::user()->name . "]: " . $request->reason,
            ]);

            DB::commit();
            return redirect()->route('admin.approvals.show', $report)->with('success', 'Approval successfully reversed.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error reversing approval.');
        }
    }
}

