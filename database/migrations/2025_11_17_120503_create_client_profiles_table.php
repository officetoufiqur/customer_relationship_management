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
        Schema::create('client_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->unique();
            $table->string('whatsapp')->nullable();
            $table->enum('source_of_lead',['referral','social', 'media','website','walk-in']);
            $table->enum('service_type',['company_formation', 'accounting', 'visa_processing']);
            $table->enum('follow_up_status',['pending','in_discussion','approved','lost']);
            $table->enum('client_interaction',['calls', 'emails', 'messages']);
            $table->double('project_cost', 8, 2);
            $table->string('documents');
            $table->boolean('quotation_sent_status')->default(false);
            $table->date('follow_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_profiles');
    }
};
