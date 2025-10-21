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
        Schema::create('tariffs', function (Blueprint $table) {
            $table->id();
            $table->string('tariff_number')->unique();
            $table->date('from_date');
            $table->date('to_date');
            $table->string('carriage_name');
            $table->string('company');
            $table->string('loading_point');
            $table->string('destination');
            $table->decimal('company_freight_rate', 10, 2);
            $table->decimal('vehicle_freight_rate', 10, 2)->nullable();
            $table->decimal('company_shortage_rate', 10, 2);
            $table->decimal('vehicle_shortage_rate', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tariffs');
    }
};
