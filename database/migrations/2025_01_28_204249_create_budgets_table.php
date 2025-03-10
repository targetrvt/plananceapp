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
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();  // Auto-incrementing primary key
            $table->string('name'); // Budget name
            $table->decimal('amount', 10, 2)->default(0);// Budget amount, with 2 decimal places
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('start_date'); // Start date
            $table->date('end_date'); // End date
            $table->timestamps(); // Timestamps (created_at and updated_at)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
