<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Director\ExpenseReportController;
use App\Http\Controllers\Director\IncomeReportController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {

    // Profile (all authenticated users)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ─────────────────────────────────────────────
    // ADMIN ONLY — full management
    // ─────────────────────────────────────────────
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {

        // User Management
        Route::resource('users', \App\Http\Controllers\Admin\UserManagementController::class);

        // Chart of Accounts — single + bulk
        Route::resource('coa', \App\Http\Controllers\Admin\ChartOfAccountController::class);
        Route::get('coa-bulk',        [\App\Http\Controllers\Admin\ChartOfAccountController::class, 'bulkCreate'])->name('coa.bulk-create');
        Route::post('coa-bulk',       [\App\Http\Controllers\Admin\ChartOfAccountController::class, 'bulkStore'])->name('coa.bulk-store');
        Route::post('coa-clone',      [\App\Http\Controllers\Admin\ChartOfAccountController::class, 'clone'])->name('coa.clone');

        // Expense Approvals
        Route::get('approvals',                       [\App\Http\Controllers\Admin\ApprovalController::class, 'index'])->name('approvals.index');
        Route::get('approvals/{report}',              [\App\Http\Controllers\Admin\ApprovalController::class, 'show'])->name('approvals.show');
        Route::post('approvals/{report}/approve',     [\App\Http\Controllers\Admin\ApprovalController::class, 'approve'])->name('approvals.approve');
        Route::post('approvals/{report}/reject',      [\App\Http\Controllers\Admin\ApprovalController::class, 'reject'])->name('approvals.reject');
        Route::post('approvals/{report}/reverse',     [\App\Http\Controllers\Admin\ApprovalController::class, 'reverse'])->name('approvals.reverse');

        // Income Approvals
        Route::get('income-approvals',                         [\App\Http\Controllers\Admin\IncomeApprovalController::class, 'index'])->name('income_approvals.index');
        Route::get('income-approvals/{incomeReport}',          [\App\Http\Controllers\Admin\IncomeApprovalController::class, 'show'])->name('income_approvals.show');
        Route::post('income-approvals/{incomeReport}/approve', [\App\Http\Controllers\Admin\IncomeApprovalController::class, 'approve'])->name('income_approvals.approve');
        Route::post('income-approvals/{incomeReport}/reject',  [\App\Http\Controllers\Admin\IncomeApprovalController::class, 'reject'])->name('income_approvals.reject');
    });

    // ─────────────────────────────────────────────
    // ADMIN + DIRECTOR — create & edit reports
    // ─────────────────────────────────────────────
    Route::middleware('role:admin|director')->prefix('director')->name('director.')->group(function () {
        Route::get('reports/categories/{director}', [\App\Http\Controllers\Director\ExpenseReportController::class, 'getCategories'])->name('reports.categories');
        Route::get('reports/create',                [\App\Http\Controllers\Director\ExpenseReportController::class, 'create'])->name('reports.create');
        Route::post('reports',                      [\App\Http\Controllers\Director\ExpenseReportController::class, 'store'])->name('reports.store');
        Route::get('reports/{expenseReport}/edit',  [\App\Http\Controllers\Director\ExpenseReportController::class, 'edit'])->name('reports.edit');
        Route::put('reports/{expenseReport}',       [\App\Http\Controllers\Director\ExpenseReportController::class, 'update'])->name('reports.update');
        Route::delete('reports/{expenseReport}',    [\App\Http\Controllers\Director\ExpenseReportController::class, 'destroy'])->name('reports.destroy');

        // Income — mutate
        Route::get('income/create',               [\App\Http\Controllers\Director\IncomeReportController::class, 'create'])->name('income.create');
        Route::post('income',                     [\App\Http\Controllers\Director\IncomeReportController::class, 'store'])->name('income.store');
        Route::get('income/{incomeReport}/edit',  [\App\Http\Controllers\Director\IncomeReportController::class, 'edit'])->name('income.edit');
        Route::put('income/{incomeReport}',       [\App\Http\Controllers\Director\IncomeReportController::class, 'update'])->name('income.update');
        Route::delete('income/{incomeReport}',    [\App\Http\Controllers\Director\IncomeReportController::class, 'destroy'])->name('income.destroy');
    });

    // ─────────────────────────────────────────────
    // ADMIN + DIRECTOR + ACCOUNTANT — read-only (view/print/download)
    // ─────────────────────────────────────────────
    Route::middleware('role:admin|director|accountant')->prefix('director')->name('director.')->group(function () {
        Route::get('reports',                       [ExpenseReportController::class, 'index'])->name('reports.index');
        Route::get('reports/{expenseReport}',       [ExpenseReportController::class, 'show'])->name('reports.show');
        Route::get('reports/{expenseReport}/print', [ExpenseReportController::class, 'print'])->name('reports.print');
        Route::get('reports/{expenseReport}/excel', [ExpenseReportController::class, 'exportExcel'])->name('reports.excel');

        // Income — read-only
        Route::get('income',                  [IncomeReportController::class, 'index'])->name('income.index');
        Route::get('income/{incomeReport}',   [IncomeReportController::class, 'show'])->name('income.show');
        Route::get('income/{incomeReport}/print', [IncomeReportController::class, 'print'])->name('income.print');
        Route::get('income/{incomeReport}/excel', [IncomeReportController::class, 'exportExcel'])->name('income.excel');
    });

    // ─────────────────────────────────────────────
    // SHARED — Ledger & Reporting (all 3 roles)
    // ─────────────────────────────────────────────
    Route::middleware('role:admin|director|accountant')->group(function () {
        Route::get('ledger',            [\App\Http\Controllers\Shared\LedgerController::class, 'index'])->name('ledger.index');
        Route::get('ledger/pdf',        [\App\Http\Controllers\Shared\LedgerController::class, 'exportPdf'])->name('ledger.pdf');
        Route::get('ledger/excel',      [\App\Http\Controllers\Shared\LedgerController::class, 'exportExcel'])->name('ledger.excel');
        Route::get('reports',           [\App\Http\Controllers\Shared\ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/pdf',       [\App\Http\Controllers\Shared\ReportController::class, 'exportPdf'])->name('reports.pdf');
        Route::get('reports/excel',     [\App\Http\Controllers\Shared\ReportController::class, 'exportExcel'])->name('reports.excel');
    });
});

require __DIR__.'/auth.php';
