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
        Schema::table('client_services', function (Blueprint $table) {
            $table->string('status')->default('active')->after('service_id');
            $table->text('description')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_services', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('status');
        });
    }
};
