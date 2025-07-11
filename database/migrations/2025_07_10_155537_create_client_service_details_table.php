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
        Schema::create('client_service_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended', 'expired'])->default('active');
            $table->json('service_links')->nullable(); // Array of links with username/password/token
            $table->json('credentials')->nullable(); // Additional credentials storage
            $table->decimal('amount', 10, 2)->nullable();
            $table->enum('cost_type', ['one_time', 'monthly', 'yearly'])->default('monthly');
            $table->date('next_due_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('assigned_employee_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_service_details');
    }
};
