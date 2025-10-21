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
        Schema::create('secondary_journey_voucher_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('secondary_journey_voucher_id')->constrained()->onDelete('cascade');
            $table->string('vrn');
            $table->string('invoice_number');
            $table->string('loading_point');
            $table->string('destination');
            $table->string('product');
            $table->decimal('rate', 10, 2);
            $table->decimal('load_quantity', 10, 2);
            $table->decimal('freight', 15, 2);
            $table->decimal('shortage_quantity', 10, 2)->default(0);
            $table->decimal('shortage_amount', 15, 2)->default(0);
            $table->decimal('company_deduction', 15, 2)->default(0);
            $table->decimal('vehicle_commission', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2);
            $table->boolean('pr04')->default(false);
            $table->timestamps();
            
            $table->index(['vrn', 'loading_point', 'destination']);
            $table->index('product');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('secondary_journey_voucher_entries');
    }
};
