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
        Schema::create('expense_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('expense_reports')->onDelete('cascade');
            $table->date('date');
            $table->string('description');
            $table->foreignId('category_id')->constrained('chart_of_accounts');
            $table->decimal('amount', 15, 2);
            $table->string('attachment_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_lines');
    }
};
