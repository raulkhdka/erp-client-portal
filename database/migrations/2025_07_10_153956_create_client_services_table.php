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
                if (!Schema::hasTable('client_services')) {
                    Schema::create('client_services', function (Blueprint $table) {
                        $table->engine = 'InnoDB'; // Ensure InnoDB for foreign keys
                        $table->id();
                        $table->foreignId('client_id')->constrained()->nullOnDelete();
                        $table->string('name');
                        $table->text('description')->nullable();
                        $table->date('start_date')->nullable();
                        $table->date('end_date')->nullable();
                        $table->enum('status', ['active', 'inactive', 'suspended', 'expired'])->default('active');
                        $table->json('service_links')->nullable();
                        $table->json('credentials')->nullable();
                        $table->decimal('monthly_cost', 10, 2)->nullable();
                        $table->text('notes')->nullable();
                        $table->foreignId('assigned_employee_id')->nullable()->constrained()->nullOnDelete();
                        $table->timestamps();
                    });
                }
            }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_services');
    }
};
