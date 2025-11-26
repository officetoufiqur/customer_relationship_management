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
        Schema::create('financial_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('expense_id')->nullable()->constrained('expenses')->onDelete('set null');
            $table->foreignId('employee_id')->nullable()->constrained('employees')->onDelete('set null');
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('set null');

            $table->decimal('amount', 14, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_logs');
    }
};
