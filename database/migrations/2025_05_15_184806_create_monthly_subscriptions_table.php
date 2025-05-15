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
        Schema::create('monthly_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->decimal('amount', 10, 2);
            $table->date('billing_date');
            $table->string('category');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('billing_cycle')->default('monthly');
            $table->boolean('auto_create_transaction')->default(false);
            $table->timestamps();
            $table->enum('status', ['paid', 'unpaid'])->default('unpaid')->after('is_active');
            $table->date('last_paid_date')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_subscriptions');
    }
};