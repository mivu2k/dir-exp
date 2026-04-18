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
        Schema::create('expense_report_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('expense_reports')->onDelete('cascade');
            $table->integer('version_no');
            $table->json('snapshot_data');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_report_versions');
    }
};
