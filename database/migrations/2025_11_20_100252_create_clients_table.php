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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->unique();
            $table->string('whatsapp')->nullable();
            $table->enum('source_of_lead',['referral','social', 'media','website','walk-in']);
            $table->enum('service_type',['company_formation', 'accounting', 'visa_processing']);
            $table->enum('follow_up_status',['pending','in_discussion','approved','lost']);
            $table->double('project_cost', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
