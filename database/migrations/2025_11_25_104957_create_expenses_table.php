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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('company_name');
            $table->decimal('amount', 10, 2);
            $table->enum('recipient_name',['vendor', 'government']);
            $table->enum('payment_method',['cash', 'transfer', 'card']);
            $table->boolean('client_paid')->default(false);
            $table->enum('status',['pending', 'approved', 'rejected'])->default('pending');
            $table->longText('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
