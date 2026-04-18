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
        Schema::create('income_reports', function (Blueprint $header) {
            $header->id();
            $header->foreignId('director_id')->constrained('users')->onDelete('cascade');
            $header->string('title');
            $header->string('voucher_no')->unique();
            $header->integer('period_month');
            $header->integer('period_year');
            $header->text('notes')->nullable();
            $header->string('status')->default('draft');
            $header->foreignId('reviewer_id')->nullable()->constrained('users')->onDelete('set null');
            $header->timestamp('reviewed_at')->nullable();
            $header->integer('version')->default(1);
            $header->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('income_reports');
    }
};
