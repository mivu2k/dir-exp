<?php

namespace App\Http\Controllers\Director;

use App\Http\Controllers\Controller;
use App\Models\IncomeReport;
use App\Models\IncomeLine;
use App\Models\ChartOfAccount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IncomeReportController extends Controller
{
    public function index(Request $request)
    {
        $query = IncomeReport::with(['director', 'lines']);

        // Filtering
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->filled('director_id')) {
            $query->where('director_id', $request->director_id);
        }

        if (!Auth::user()->hasRole('admin') && !Auth::user()->hasRole('accountant')) {
            $query->where('director_id', Auth::id());
        }

        $reports = $query->orderBy('created_at', 'desc')->paginate(10);
        
        $directors = Auth::user()->hasRole('admin') || Auth::user()->hasRole('accountant') 
            ? User::role('director')->orderBy('name')->get() 
            : [];

        return view('director.income.index', compact('reports', 'directors'));
    }

    public function create()
    {
        $directors = [];
        $selectedDirectorId = Auth::id();

        if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('accountant')) {
            $directors = User::role('director')->get();
        }

        $categories = ChartOfAccount::where('type', 'income')
            ->where('is_active', true)
            ->where(function($q) use ($selectedDirectorId) {
                $q->where('director_id', $selectedDirectorId)->orWhereNull('director_id');
            })
            ->get();

        return view('director.income.create', compact('categories', 'directors'));
    }

    public function store(Request $request)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'period_month' => 'required|integer|between:1,12',
            'period_year' => 'required|integer',
            'lines' => 'required|array|min:1',
            'lines.*.date' => 'required|date',
            'lines.*.description' => 'required|string|max:255',
            'lines.*.category_id' => 'required|exists:chart_of_accounts,id',
            'lines.*.amount' => 'required|numeric|min:0.01',
        ];

        if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('accountant')) {
            $rules['director_id'] = 'required|exists:users,id';
        }

        $request->validate($rules);

        DB::beginTransaction();
        try {
            $directorId = (Auth::user()->hasRole('admin') || Auth::user()->hasRole('accountant')) ? $request->director_id : Auth::id();

            $report = IncomeReport::create([
                'director_id' => $directorId,
                'title' => $request->title,
                'period_month' => $request->period_month,
                'period_year' => $request->period_year,
                'notes' => $request->notes,
                'status' => $request->has('submit_report') ? 'submitted' : 'draft',
                'submitted_at' => $request->has('submit_report') ? now() : null,
            ]);

            foreach ($request->lines as $line) {
                $report->lines()->create([
                    'date' => $line['date'],
                    'description' => $line['description'],
                    'category_id' => $line['category_id'],
                    'amount' => $line['amount'],
                ]);
            }

            DB::commit();
            return redirect()->route('director.income.index')->with('success', 'Income report processed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function show(IncomeReport $incomeReport)
    {
        $this->authorizeReport($incomeReport);
        $incomeReport->load(['lines.category', 'director', 'reviewer']);

        $categorySummary = $incomeReport->lines->groupBy('category_id')
            ->map(function ($lines) {
                return [
                    'name' => $lines->first()->category->name,
                    'total' => $lines->sum('amount')
                ];
            });

        return view('director.income.show', compact('incomeReport', 'categorySummary'));
    }

    public function exportExcel(IncomeReport $incomeReport)
    {
        $this->authorizeReport($incomeReport);
        $incomeReport->load(['lines.category', 'director']);

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\SingleReportExport($incomeReport), 
            'Revenue-Voucher-'.$incomeReport->voucher_no.'.xlsx'
        );
    }

    public function print(IncomeReport $incomeReport)
    {
        $this->authorizeReport($incomeReport);
        $incomeReport->load(['lines.category', 'director', 'reviewer']);

        $categorySummary = $incomeReport->lines->groupBy('category_id')
            ->map(function ($lines) {
                return [
                    'name' => $lines->first()->category->name,
                    'total' => $lines->sum('amount')
                ];
            });

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('director.income.print', [
            'report' => $incomeReport,
            'categorySummary' => $categorySummary
        ]);

        return $pdf->download('Revenue-Voucher-'.$incomeReport->voucher_no.'.pdf');
    }

    public function edit(IncomeReport $incomeReport)
    {
        $this->authorizeReport($incomeReport);

        if (!Auth::user()->hasRole('admin') && !in_array($incomeReport->status, ['draft', 'rejected'])) {
            return redirect()->route('director.income.index')->with('error', 'Report is locked and cannot be edited.');
        }

        $categories = ChartOfAccount::where('type', 'income')
            ->where('is_active', true)
            ->where(function($q) use ($incomeReport) {
                $q->where('director_id', $incomeReport->director_id)->orWhereNull('director_id');
            })
            ->get();
            
        $incomeReport->load('lines');

        return view('director.income.edit', compact('incomeReport', 'categories'));
    }

    public function update(Request $request, IncomeReport $incomeReport)
    {
        $this->authorizeReport($incomeReport);

        $request->validate([
            'title' => 'required|string|max:255',
            'period_month' => 'required|integer|between:1,12',
            'period_year' => 'required|integer',
            'lines' => 'required|array|min:1',
            'lines.*.date' => 'required|date',
            'lines.*.description' => 'required|string|max:255',
            'lines.*.category_id' => 'required|exists:chart_of_accounts,id',
            'lines.*.amount' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();
        try {
            $incomeReport->update([
                'title' => $request->title,
                'period_month' => $request->period_month,
                'period_year' => $request->period_year,
                'notes' => $request->notes,
                'status' => $request->has('submit_report') ? 'submitted' : 'draft',
                'submitted_at' => $request->has('submit_report') ? now() : $incomeReport->submitted_at,
            ]);

            $incomeReport->lines()->delete();

            foreach ($request->lines as $lineData) {
                $incomeReport->lines()->create([
                    'date' => $lineData['date'],
                    'description' => $lineData['description'],
                    'category_id' => $lineData['category_id'],
                    'amount' => $lineData['amount'],
                ]);
            }

            DB::commit();
            return redirect()->route('director.income.index')->with('success', 'Income report updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    private function authorizeReport(IncomeReport $report)
    {
        if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('accountant')) {
            return;
        }

        if ($report->director_id !== Auth::id()) {
            abort(403);
        }
    }
}
