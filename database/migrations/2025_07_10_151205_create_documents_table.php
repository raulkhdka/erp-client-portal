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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type');
            $table->integer('file_size');
            $table->string('mime_type');
            $table->foreignId('categories_id')->nullable()->constrained('document_categories')->nullOnDelete();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('uploaded_by')->constrained('users');
            $table->json('access_permissions')->nullable(); // Store who can access
            $table->boolean('is_public')->default(false);
            $table->boolean('is_confidential')->default(false);
            $table->timestamp('expires_at')->nullable();
            $table->integer('download_count')->default(0);
            $table->timestamp('last_accessed_at')->nullable();
            $table->timestamps();

            $table->index(['categories_id', 'client_id']);
            $table->index(['uploaded_by', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
