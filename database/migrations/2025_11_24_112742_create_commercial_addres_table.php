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
        Schema::create('commercial_addres', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('contact_type',['monthly', 'quarterly', 'yearly']);
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('payment_status',['paid', 'unpaid', 'overdue']);
            $table->boolean('status')->default(false);
            $table->decimal('contact_value', 10, 2)->default(0);
            $table->decimal('business_center_cost', 10, 2)->default(0);
            $table->decimal('net_profit', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commercial_addres');
    }
};
