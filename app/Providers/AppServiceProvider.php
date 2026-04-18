<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\ExpenseReport;
use App\Observers\ExpenseReportObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ExpenseReport::observe(ExpenseReportObserver::class);
    }
}
