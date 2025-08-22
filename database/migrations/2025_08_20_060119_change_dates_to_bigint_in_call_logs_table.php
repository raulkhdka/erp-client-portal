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
        Schema::table('call_logs', function (Blueprint $table) {
            // Drop old timestamp columns
            $table->dropColumn(['call_date', 'follow_up_date']);
        });

        Schema::table('call_logs', function (Blueprint $table) {
            // Recreate as BIGINT to store UNIX timestamps
            $table->bigInteger('call_date')->nullable();
            $table->bigInteger('follow_up_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('call_logs', function (Blueprint $table) {
            // Drop bigint columns
            $table->dropColumn(['call_date', 'follow_up_date']);
        });

        Schema::table('call_logs', function (Blueprint $table) {
            // Revert back to TIMESTAMP
            $table->timestamp('call_date')->nullable();
            $table->timestamp('follow_up_date')->nullable();
        });
    }
};
