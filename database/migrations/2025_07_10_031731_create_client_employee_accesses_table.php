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
        Schema::create('client_employee_accesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->json('permissions'); // specific permissions like view_accounting, edit_payroll, etc.
            $table->date('access_granted_date');
            $table->date('access_expires_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['client_id', 'employee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_employee_accesses');
    }
};
