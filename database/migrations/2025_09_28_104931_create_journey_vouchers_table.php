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
        Schema::create('journey_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('journey_number')->unique();
            $table->date('journey_date');
            $table->foreignId('vehicle_id')->constrained('vehicles');
            $table->string('carriage_name');
            $table->string('loading_point');
            $table->string('loading_point_urdu')->nullable();
            $table->decimal('capacity', 10, 2);
            $table->decimal('company_freight_rate', 10, 2);
            $table->decimal('vehicle_freight_rate', 10, 2)->nullable();
            $table->decimal('shortage_quantity', 10, 2)->default(0);
            $table->decimal('shortage_rate', 10, 2)->default(0);
            $table->decimal('company_deduction_percentage', 5, 2)->default(0);
            $table->decimal('vehicle_deduction_percentage', 5, 2)->default(0);
            $table->string('billing_month');
            $table->string('product');
            $table->string('product_urdu')->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('destination');
            $table->string('destination_urdu')->nullable();
            $table->string('company');
            $table->decimal('decant_capacity', 10, 2)->nullable();
            $table->boolean('is_direct_bill')->default(false);
            $table->enum('journey_type', ['primary', 'secondary']);
            $table->decimal('freight_amount', 15, 2)->default(0);
            $table->decimal('shortage_amount', 15, 2)->default(0);
            $table->decimal('commission_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->boolean('is_processed')->default(false);
            $table->boolean('is_billed')->default(false);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journey_vouchers');
    }
};
