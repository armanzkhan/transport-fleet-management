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
        Schema::create('secondary_journey_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('journey_number')->unique();
            $table->string('contractor_name');
            $table->string('company');
            $table->date('journey_date');
            $table->decimal('total_freight', 15, 2)->default(0);
            $table->decimal('total_shortage', 15, 2)->default(0);
            $table->decimal('total_company_deduction', 15, 2)->default(0);
            $table->decimal('total_vehicle_commission', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2)->default(0);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['company', 'contractor_name']);
            $table->index('journey_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('secondary_journey_vouchers');
    }
};
