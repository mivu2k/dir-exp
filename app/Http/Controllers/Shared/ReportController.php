<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExpenseLine;
use App\Models\ExpenseReport;
use App\Models\User;
use App\Models\ChartOfAccount;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Display a summary report of expenditures.
     */
    public function index(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate   = $request->end_date   ? Carbon::parse($request->end_date)   : Carbon::now()->endOfMonth();
        $type      = $request->get('type', 'expense');

        if ($type === 'income') {
            $query = \App\Models\IncomeLine::with(['report.director:id,name', 'category:id,name'])
                ->whereHas('report', fn ($q) => $q->where('status', 'approved'));
        } else {
            $query = ExpenseLine::with(['report.director:id,name', 'category:id,name'])
                ->whereHas('report', fn ($q) => $q->where('status', 'approved'));
        }

        $query->whereBetween('date', [$startDate, $endDate]);

        // Directors only see their own data
        if (Auth::user()->hasRole('director')) {
            $query->whereHas('report', fn ($q) => $q->where('director_id', Auth::id()));
        }

        // Advanced Filters (Admin/Accountant only — directors are already scoped)
        if ($request->filled('director_id') && !Auth::user()->hasRole('director')) {
            $query->whereHas('report', fn ($q) => $q->where('director_id', $request->director_id));
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $lines = $query->get();

        // Grouping: Director → [ Category Name => Total ]
        $groupedData = $lines->groupBy(fn ($line) => $line->report?->director?->name ?? 'Unassigned')
            ->map(fn ($directorLines) => [
                'total'      => round($directorLines->sum('amount')),
                'categories' => $directorLines->groupBy('category.name')
                    ->map(fn ($catLines) => round($catLines->sum('amount'))),
            ]);

        // Global category breakdown
        $categoryData = $lines->groupBy('category.name')
            ->map(fn ($catLines) => round($catLines->sum('amount')));

        $grandTotal = round($lines->sum('amount'));

        // Metadata for filters
        $directors  = Auth::user()->hasRole('director')
            ? collect([Auth::user()])
            : User::role('director')->select('id', 'name')->orderBy('name')->get();
        $categories = ChartOfAccount::where('type', $type)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('shared.reports.index', compact(
            'groupedData', 'categoryData', 'grandTotal',
            'startDate', 'endDate', 'directors', 'categories', 'type'
        ));
    }

    /**
     * Export the ledger to PDF.
     */
    public function exportPdf(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate   = $request->end_date   ? Carbon::parse($request->end_date)   : Carbon::now()->endOfMonth();

        $query = ExpenseLine::with(['report.director:id,name', 'category:id,name'])
            ->whereHas('report', fn ($q) => $q->where('status', 'approved'))
            ->whereBetween('expense_lines.date', [$startDate, $endDate])
            ->orderBy('expense_lines.date', 'asc');

        // Director scoping
        if (Auth::user()->hasRole('director')) {
            $query->whereHas('report', fn ($q) => $q->where('director_id', Auth::id()));
        } elseif ($request->filled('director_id')) {
            $query->whereHas('report', fn ($q) => $q->where('director_id', $request->director_id));
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $lines = $query->get();

        $pdf = Pdf::loadView('shared.reports.pdf', [
            'lines'     => $lines,
            'startDate' => $startDate,
            'endDate'   => $endDate,
            'total'     => $lines->sum('amount'),
        ]);

        return $pdf->download('Financial-Report-' . date('Ymd') . '.pdf');
    }

    /**
     * Export the ledger to Excel.
     */
    public function exportExcel(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate   = $request->end_date   ? Carbon::parse($request->end_date)   : Carbon::now()->endOfMonth();

        $query = ExpenseLine::with(['report.director:id,name', 'category:id,name'])
            ->whereHas('report', fn ($q) => $q->where('status', 'approved'))
            ->whereBetween('expense_lines.date', [$startDate, $endDate])
            ->orderBy('expense_lines.date', 'asc');

        // Director scoping
        if (Auth::user()->hasRole('director')) {
            $query->whereHas('report', fn ($q) => $q->where('director_id', Auth::id()));
        } elseif ($request->filled('director_id')) {
            $query->whereHas('report', fn ($q) => $q->where('director_id', $request->director_id));
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $lines = $query->get();

        return Excel::download(
            new \App\Exports\FinancialExport($lines),
            'Financial-Audit-' . date('Ymd') . '.xlsx'
        );
    }
}
