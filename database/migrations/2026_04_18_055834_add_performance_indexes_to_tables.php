<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Chart of Accounts — most filtered by director_id + type + is_active
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->index(['director_id', 'type', 'is_active'], 'coa_director_type_active_idx');
        });

        // Expense Lines — filtered by date + category heavily
        Schema::table('expense_lines', function (Blueprint $table) {
            $table->index(['date', 'category_id'], 'el_date_category_idx');
        });

        // Income Lines — same pattern
        Schema::table('income_lines', function (Blueprint $table) {
            $table->index(['date', 'category_id'], 'il_date_category_idx');
        });

        // Expense Reports — filtered by director_id + status constantly
        Schema::table('expense_reports', function (Blueprint $table) {
            $table->index(['director_id', 'status'], 'er_director_status_idx');
            $table->index('submitted_at', 'er_submitted_at_idx');
        });

        // Income Reports — same
        Schema::table('income_reports', function (Blueprint $table) {
            $table->index(['director_id', 'status'], 'ir_director_status_idx');
            $table->index('submitted_at', 'ir_submitted_at_idx');
        });
    }

    public function down(): void
    {
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->dropIndex('coa_director_type_active_idx');
        });
        Schema::table('expense_lines', function (Blueprint $table) {
            $table->dropIndex('el_date_category_idx');
        });
        Schema::table('income_lines', function (Blueprint $table) {
            $table->dropIndex('il_date_category_idx');
        });
        Schema::table('expense_reports', function (Blueprint $table) {
            $table->dropIndex('er_director_status_idx');
            $table->dropIndex('er_submitted_at_idx');
        });
        Schema::table('income_reports', function (Blueprint $table) {
            $table->dropIndex('ir_director_status_idx');
            $table->dropIndex('ir_submitted_at_idx');
        });
    }
};
