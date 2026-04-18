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
        Schema::create('income_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('income_report_id')->constrained('income_reports')->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('chart_of_accounts')->onDelete('set null');
            $table->date('date');
            $table->string('description');
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
        Schema::dropIfExists('income_lines');
    }
};
