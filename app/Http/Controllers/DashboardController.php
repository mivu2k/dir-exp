<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ExpenseLine;
use App\Models\ExpenseReport;
use App\Models\ChartOfAccount;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $now = Carbon::now();
        
        $stats = [
            'pending_expenses' => 0,
            'pending_income' => 0,
            'expense_mtd' => 0,
            'income_mtd' => 0,
            'rejected_count' => 0,
        ];

        if ($user->hasRole('admin') || $user->hasRole('accountant')) {
            $stats['pending_expenses'] = \App\Models\ExpenseReport::where('status', 'submitted')->count();
            $stats['pending_income'] = \App\Models\IncomeReport::where('status', 'submitted')->count();
            
            $stats['expense_mtd'] = ExpenseLine::whereHas('report', function($q) use ($now) {
                $q->where('status', 'approved')
                  ->where('period_month', $now->month)
                  ->where('period_year', $now->year);
            })->sum('amount');

            $stats['income_mtd'] = \App\Models\IncomeLine::whereHas('report', function($q) use ($now) {
                $q->where('status', 'approved')
                  ->where('period_month', $now->month)
                  ->where('period_year', $now->year);
            })->sum('amount');
            
            $recentReports = ExpenseReport::with('director')->orderBy('updated_at', 'desc')->limit(5)->get();
            $budgetSummary = ChartOfAccount::where('type', 'expense')
                ->whereNotNull('budget_limit')
                ->get()
                ->map(function($coa) use ($now) {
                    $spent = ExpenseLine::where('category_id', $coa->id)
                        ->whereHas('report', function($q) use ($now) {
                            $q->where('status', 'approved')
                              ->where('period_month', $now->month)
                              ->where('period_year', $now->year);
                        })->sum('amount');
                    $coa->spent = $spent;
                    $coa->percentage = $coa->budget_limit > 0 ? ($spent / $coa->budget_limit) * 100 : 0;
                    return $coa;
                });
        } else {
            // Director Logic
            $stats['pending_expenses'] = ExpenseReport::where('director_id', $user->id)->where('status', 'submitted')->count();
            $stats['pending_income'] = \App\Models\IncomeReport::where('director_id', $user->id)->where('status', 'submitted')->count();
            
            $stats['expense_mtd'] = ExpenseLine::whereHas('report', function($q) use ($user, $now) {
                $q->where('director_id', $user->id)
                  ->where('status', 'approved')
                  ->where('period_month', $now->month)
                  ->where('period_year', $now->year);
            })->sum('amount');

            $stats['income_mtd'] = \App\Models\IncomeLine::whereHas('report', function($q) use ($user, $now) {
                $q->where('director_id', $user->id)
                  ->where('status', 'approved')
                  ->where('period_month', $now->month)
                  ->where('period_year', $now->year);
            })->sum('amount');

            $stats['rejected_count'] = ExpenseReport::where('director_id', $user->id)->where('status', 'rejected')->count();
            
            $recentReports = ExpenseReport::where('director_id', $user->id)->orderBy('updated_at', 'desc')->limit(5)->get();
            $budgetSummary = collect();
        }

        return view('dashboard', compact('stats', 'recentReports', 'budgetSummary'));
    }
}

