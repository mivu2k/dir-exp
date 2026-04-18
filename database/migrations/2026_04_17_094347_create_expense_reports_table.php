<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('expense_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('director_id')->constrained('users');
            $table->string('voucher_no')->unique();
            $table->string('title');
            $table->tinyInteger('period_month');
            $table->year('period_year');
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
            $table->integer('version')->default(1);
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_reports');
    }
};
