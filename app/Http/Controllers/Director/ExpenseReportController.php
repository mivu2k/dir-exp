<?php

namespace App\Http\Controllers\Director;

use App\Http\Controllers\Controller;
use App\Models\ExpenseReport;
use Illuminate\Http\Request;

use App\Models\ChartOfAccount;
use App\Models\ExpenseLine;
use App\Models\ExpenseReportVersion;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ExpenseReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ExpenseReport::with(['director', 'lines']);

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

        // Directors only see their own reports
        if (Auth::user()->hasRole('director')) {
            $query->where('director_id', Auth::id());
        }
        // Admin & Accountant see all

        $reports = $query->orderBy('created_at', 'desc')->paginate(10);
        
        $directors = Auth::user()->hasRole('admin') || Auth::user()->hasRole('accountant') 
            ? User::role('director')->orderBy('name')->get() 
            : [];

        return view('director.reports.index', compact('reports', 'directors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Accountants cannot create reports
        if (Auth::user()->hasRole('accountant')) {
            abort(403, 'Accountants have read-only access to reports.');
        }

        $directors = [];
        $selectedDirectorId = Auth::id();

        // Admin can select any director
        if (Auth::user()->hasRole('admin')) {
            $directors = User::role('director')->orderBy('name')->get();
            $selectedDirectorId = null; // will be chosen in form
        }

        // Fetch categories scoped to the selected director or global templates
        $categories = ChartOfAccount::where('type', 'expense')
            ->where('is_active', true)
            ->where(function ($q) use ($selectedDirectorId) {
                $q->where('director_id', $selectedDirectorId)->orWhereNull('director_id');
            })
            ->orderBy('code')
            ->get();

        return view('director.reports.create', compact('categories', 'directors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Accountants cannot create reports
        if (Auth::user()->hasRole('accountant')) {
            abort(403, 'Accountants have read-only access to reports.');
        }

        $rules = [
            'title'                  => 'required|string|max:255',
            'period_month'           => 'required|integer|between:1,12',
            'period_year'            => 'required|integer|min:2000|max:2099',
            'lines'                  => 'required|array|min:1|max:100',
            'lines.*.date'           => 'required|date',
            'lines.*.description'    => 'required|string|max:500',
            'lines.*.category_id'    => 'required|exists:chart_of_accounts,id',
            'lines.*.amount'         => 'required|numeric|min:0.01|max:9999999',
            'lines.*.attachment'     => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ];

        // Admin must provide director_id
        if (Auth::user()->hasRole('admin')) {
            $rules['director_id'] = 'required|exists:users,id';
        }

        $request->validate($rules);

        DB::beginTransaction();
        try {
            $directorId = (Auth::user()->hasRole('admin') || Auth::user()->hasRole('accountant')) ? $request->director_id : Auth::id();

            $report = ExpenseReport::create([
                'director_id' => $directorId,
                'title' => $request->title,
                'period_month' => $request->period_month,
                'period_year' => $request->period_year,
                'notes' => $request->notes,
                'status' => $request->has('submit_report') ? 'submitted' : 'draft',
                'submitted_at' => $request->has('submit_report') ? now() : null,
                'version' => 1,
            ]);

            foreach ($request->lines as $line) {
                $attachmentUrl = null;
                if (isset($line['attachment'])) {
                    $attachmentUrl = $line['attachment']->store('attachments', 'public');
                }

                $report->lines()->create([
                    'date' => $line['date'],
                    'description' => $line['description'],
                    'category_id' => $line['category_id'],
                    'amount' => $line['amount'],
                    'attachment_url' => $attachmentUrl,
                ]);
            }

            DB::commit();
            return redirect()->route('director.reports.index')->with('success', 'Report processed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Critical Error: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ExpenseReport $expenseReport)
    {
        $this->authorizeReport($expenseReport);
        $expenseReport->load(['lines.category', 'director', 'reviewer']);

        // Calculate categorical totals for the summary
        $categorySummary = $expenseReport->lines->groupBy('category_id')
            ->map(function ($lines) {
                return [
                    'name' => $lines->first()->category->name,
                    'total' => $lines->sum('amount')
                ];
            });

        return view('director.reports.show', compact('expenseReport', 'categorySummary'));
    }

    /**
     * Generate a professional PDF print-out for a single voucher.
     */
    public function print(ExpenseReport $expenseReport)
    {
        $this->authorizeReport($expenseReport);
        $expenseReport->load(['lines.category', 'director', 'reviewer']);

        $categorySummary = $expenseReport->lines->groupBy('category_id')
            ->map(function ($lines) {
                return [
                    'name' => $lines->first()->category->name,
                    'total' => $lines->sum('amount')
                ];
            });

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('director.reports.print', [
            'report' => $expenseReport,
            'categorySummary' => $categorySummary
        ]);

        return $pdf->download('Voucher-'.$expenseReport->voucher_no.'.pdf');
    }

    public function exportExcel(ExpenseReport $expenseReport)
    {
        $this->authorizeReport($expenseReport);
        $expenseReport->load(['lines.category', 'director']);

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\SingleReportExport($expenseReport), 
            'Voucher-'.$expenseReport->voucher_no.'.xlsx'
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExpenseReport $expenseReport)
    {
        // Accountants cannot edit reports
        if (Auth::user()->hasRole('accountant')) {
            abort(403, 'Accountants have read-only access to reports.');
        }

        $this->authorizeReport($expenseReport);

        // Only draft/rejected reports can be edited by directors
        if (!Auth::user()->hasRole('admin') && !in_array($expenseReport->status, ['draft', 'rejected'])) {
            return redirect()->route('director.reports.index')
                ->with('error', 'Operational Lock: Only draft/rejected reports are editable.');
        }

        $categories = ChartOfAccount::where('type', 'expense')
            ->where('is_active', true)
            ->where(function ($q) use ($expenseReport) {
                $q->where('director_id', $expenseReport->director_id)->orWhereNull('director_id');
            })
            ->orderBy('code')
            ->get();

        $expenseReport->load('lines');

        return view('director.reports.edit', compact('expenseReport', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExpenseReport $expenseReport)
    {
        if (Auth::user()->hasRole('accountant')) {
            abort(403, 'Accountants have read-only access to reports.');
        }

        $this->authorizeReport($expenseReport);

        if (!Auth::user()->hasRole('admin') && !in_array($expenseReport->status, ['draft', 'rejected'])) {
            return redirect()->route('director.reports.index')->with('error', 'Operational Lock: State rejection.');
        }

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
            if ($expenseReport->status === 'rejected') {
                ExpenseReportVersion::create([
                    'report_id' => $expenseReport->id,
                    'version_no' => $expenseReport->version,
                    'snapshot_data' => $expenseReport->load('lines')->toArray(),
                ]);
                $expenseReport->version++;
            }

            $expenseReport->update([
                'title' => $request->title,
                'period_month' => $request->period_month,
                'period_year' => $request->period_year,
                'notes' => $request->notes,
                'status' => $request->has('submit_report') ? 'submitted' : 'draft',
                'submitted_at' => $request->has('submit_report') ? now() : $expenseReport->submitted_at,
            ]);

            $expenseReport->lines()->delete();

            foreach ($request->lines as $lineData) {
                $attachmentUrl = $lineData['existing_attachment'] ?? null;
                if (isset($lineData['attachment'])) {
                    $attachmentUrl = $lineData['attachment']->store('attachments', 'public');
                }

                $expenseReport->lines()->create([
                    'date' => $lineData['date'],
                    'description' => $lineData['description'],
                    'category_id' => $lineData['category_id'],
                    'amount' => $lineData['amount'],
                    'attachment_url' => $attachmentUrl,
                ]);
            }

            DB::commit();
            return redirect()->route('director.reports.index')->with('success', 'Report synchronized successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Critical Error: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExpenseReport $expenseReport)
    {
        if (Auth::user()->hasRole('accountant')) {
            abort(403, 'Accountants have read-only access to reports.');
        }

        $this->authorizeReport($expenseReport);

        if (!Auth::user()->hasRole('admin') && $expenseReport->status !== 'draft') {
            return redirect()->route('director.reports.index')
                ->with('error', 'Operational Lock: Only draft reports can be deleted.');
        }

        $expenseReport->delete();

        return redirect()->route('director.reports.index')->with('success', 'Report purged successfully.');
    }

    public function getCategories(Request $request, $directorId)
    {
        $type = $request->get('type', 'expense');
        $categories = ChartOfAccount::where('type', $type)
            ->where('is_active', true)
            ->where(function($q) use ($directorId) {
                $q->where('director_id', $directorId)->orWhereNull('director_id');
            })
            ->get();

        return response()->json($categories);
    }

    private function authorizeReport(ExpenseReport $report)
    {
        // Admin can do anything
        if (Auth::user()->hasRole('admin')) {
            return;
        }

        // Accountant can read any report (mutate checks happen before this call)
        if (Auth::user()->hasRole('accountant')) {
            return;
        }

        // Director can only access their own reports
        if ($report->director_id !== Auth::id()) {
            abort(403, 'You do not have access to this report.');
        }
    }
}
