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
        Schema::create('dynamic_form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dynamic_form_id')->constrained()->onDelete('cascade');
            $table->string('field_name');
            $table->string('field_label');
            $table->enum('field_type', ['text', 'email', 'number', 'date', 'select', 'checkbox', 'radio', 'textarea', 'file']);
            $table->json('field_options')->nullable(); // for select, radio, checkbox options
            $table->boolean('is_required')->default(false);
            $table->integer('sort_order')->default(0);
            $table->json('validation_rules')->nullable();
            $table->string('placeholder')->nullable();
            $table->text('help_text')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dynamic_form_fields');
    }
};
