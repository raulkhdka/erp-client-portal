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
        Schema::create('call_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->string('caller_name')->nullable();
            $table->string('caller_phone')->nullable();
            $table->enum('call_type', ['incoming', 'outgoing'])->default('incoming');
            $table->text('subject');
            $table->text('description');
            $table->text('notes')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->tinyInteger('status')->default(1)->comment('1=pending, 2=in_progress, 3=on_hold, 4=escalated, 5=waiting_client, 6=testing, 7=completed, 8=resolved, 9=backlog');
            $table->timestamp('call_date');
            $table->integer('duration_minutes')->nullable()->comment('Call duration in minutes');
            $table->text('follow_up_required')->nullable();
            $table->timestamp('follow_up_date')->nullable();
            $table->timestamps();

            $table->index(['client_id', 'status']);
            $table->index(['employee_id', 'call_date']);
            $table->index(['status', 'priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('call_logs');
    }
};
