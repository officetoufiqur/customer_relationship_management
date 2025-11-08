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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('id_number')->unique();
            $table->string('position')->nullable();
            $table->string('department')->nullable();
            $table->string('employ_status')->nullable();
            $table->float('salary')->nullable();
            $table->decimal('allowances', 10, 2)->default(0);
            $table->decimal('deductions', 10, 2)->default(0);
            $table->integer('annual_leave_balance')->default(0);
            $table->integer('sick_leave_balance')->default(0);
            $table->text('location')->nullable();
            $table->longText('about')->nullable();
            $table->string('skills')->nullable();
            $table->string('socials')->nullable();
            $table->string('image')->nullable();
            $table->string('cover_photo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
