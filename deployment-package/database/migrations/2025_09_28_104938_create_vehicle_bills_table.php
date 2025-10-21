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
        Schema::create('vehicle_bills', function (Blueprint $table) {
            $table->id();
            $table->string('bill_number')->unique();
            $table->foreignId('vehicle_id')->constrained('vehicles');
            $table->string('billing_month');
            $table->decimal('previous_bill_balance', 15, 2)->default(0);
            $table->decimal('total_freight', 15, 2)->default(0);
            $table->decimal('total_advance', 15, 2)->default(0);
            $table->decimal('total_expense', 15, 2)->default(0);
            $table->decimal('total_shortage', 15, 2)->default(0);
            $table->decimal('gross_profit', 15, 2)->default(0);
            $table->decimal('net_profit', 15, 2)->default(0);
            $table->decimal('total_vehicle_balance', 15, 2)->default(0);
            $table->enum('status', ['draft', 'final'])->default('draft');
            $table->boolean('is_finalized')->default(false);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_bills');
    }
};
