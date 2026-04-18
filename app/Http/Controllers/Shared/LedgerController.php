<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExpenseLine;
use App\Models\IncomeLine;
use App\Models\ChartOfAccount;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class LedgerController extends Controller
{
    /**
     * Display a unified ledger of all income and expense transactions.
     * Scoped by role: Director sees only their own, Admin/Accountant see all.
     */
    public function index(Request $request)
    {
        $perPage    = 30;
        $currentPage = max(1, (int) $request->get('page', 1));

        // Build two base queries with identical filter logic
        $expenseQuery = $this->buildLineQuery('expense', $request);
        $incomeQuery  = $this->buildLineQuery('income', $request);

        // Get total count efficiently before loading data
        $totalExpenses = (clone $expenseQuery)->count();
        $totalIncomes  = (clone $incomeQuery)->count();
        $totalCount    = $totalExpenses + $totalIncomes;

        // Load only the current page's worth — split proportionally isn't ideal
        // so we union and let DB handle ordering + offset
        $expenses = (clone $expenseQuery)
            ->with(['report:id,voucher_no,director_id,status', 'report.director:id,name', 'category:id,name'])
            ->get()
            ->map(function ($l) {
                $l->type          = 'expense';
                $l->amount_signed = -abs($l->amount);
                return $l;
            });

        $incomes = (clone $incomeQuery)
            ->with(['report:id,voucher_no,director_id,status', 'report.director:id,name', 'category:id,name'])
            ->get()
            ->map(function ($l) {
                $l->type          = 'income';
                $l->amount_signed = abs($l->amount);
                return $l;
            });

        $allLines = $expenses->concat($incomes)
            ->sortByDesc(fn ($l) => $l->date->format('Y-m-d') . ($l->report->voucher_no ?? ''));

        // Manual pagination over the sorted merged collection
        $pagedData = $allLines->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $lines = new \Illuminate\Pagination\LengthAwarePaginator(
            $pagedData,
            $allLines->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Sidebar filter data — scoped for directors
        $categories = ChartOfAccount::select('id', 'name', 'type')
            ->when(Auth::user()->hasRole('director'), fn ($q) => $q->where(function ($sq) {
                $sq->where('director_id', Auth::id())->orWhereNull('director_id');
            }))
            ->orderBy('type')->orderBy('name')
            ->get();

        $directors = Auth::user()->hasRole('director')
            ? collect([Auth::user()])
            : User::role('director')->select('id', 'name')->orderBy('name')->get();

        return view('shared.ledger.index', compact('lines', 'categories', 'directors'));
    }

    /**
     * Export the full ledger to PDF.
     */
    public function exportPdf(Request $request)
    {
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : null;
        $endDate   = $request->filled('end_date')   ? Carbon::parse($request->end_date)   : null;

        $expenses = $this->buildLineQuery('expense', $request)
            ->with(['report:id,voucher_no,director_id,status', 'report.director:id,name', 'category:id,name'])
            ->get()
            ->map(fn ($l) => tap($l, fn ($l) => $l->type = 'expense'));

        $incomes = $this->buildLineQuery('income', $request)
            ->with(['report:id,voucher_no,director_id,status', 'report.director:id,name', 'category:id,name'])
            ->get()
            ->map(fn ($l) => tap($l, fn ($l) => $l->type = 'income'));

        $lines = $expenses->concat($incomes)
            ->sortByDesc(fn ($l) => $l->date->format('Y-m-d'))
            ->values();

        $pdf = Pdf::loadView('shared.ledger.pdf', [
            'lines'     => $lines,
            'startDate' => $startDate,
            'endDate'   => $endDate,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('Ledger-' . date('Ymd') . '.pdf');
    }

    /**
     * Export the full ledger to Excel.
     */
    public function exportExcel(Request $request)
    {
        $expenses = $this->buildLineQuery('expense', $request)
            ->with(['report:id,voucher_no,director_id,status', 'report.director:id,name', 'category:id,name'])
            ->get()
            ->map(fn ($l) => tap($l, fn ($l) => $l->type = 'expense'));

        $incomes = $this->buildLineQuery('income', $request)
            ->with(['report:id,voucher_no,director_id,status', 'report.director:id,name', 'category:id,name'])
            ->get()
            ->map(fn ($l) => tap($l, fn ($l) => $l->type = 'income'));

        $lines = $expenses->concat($incomes)
            ->sortByDesc(fn ($l) => $l->date->format('Y-m-d'))
            ->values();

        return Excel::download(
            new \App\Exports\LedgerExport($lines),
            'Ledger-' . date('Ymd') . '.xlsx'
        );
    }

    /**
     * Build a query for either expense_lines or income_lines with shared filters.
     */
    private function buildLineQuery(string $type, Request $request)
    {
        $query = $type === 'expense'
            ? ExpenseLine::whereHas('report')
            : IncomeLine::whereHas('report');

        // Role-based scoping
        if (Auth::user()->hasRole('director')) {
            $query->whereHas('report', fn ($q) => $q->where('director_id', Auth::id()));
        }

        // Filters
        if ($request->filled('director_id')) {
            $query->whereHas('report', fn ($q) => $q->where('director_id', $request->director_id));
        }

        if ($request->filled('start_date')) {
            $query->where('date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('date', '<=', $request->end_date);
        }

        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        return $query;
    }
}
