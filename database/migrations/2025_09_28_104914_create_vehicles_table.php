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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number')->unique();
            $table->string('vrn')->unique(); // Vehicle Registration Number
            $table->foreignId('owner_id')->constrained('vehicle_owners')->onDelete('cascade');
            $table->string('driver_name');
            $table->string('driver_contact');
            $table->decimal('capacity', 10, 2);
            $table->string('engine_number')->nullable();
            $table->string('chassis_number')->nullable();
            $table->date('token_tax_expiry')->nullable();
            $table->date('dip_chart_expiry')->nullable();
            $table->date('induction_date')->nullable();
            $table->string('tracker_name')->nullable();
            $table->string('tracker_link')->nullable();
            $table->string('tracker_id')->nullable();
            $table->string('tracker_password')->nullable();
            $table->date('tracker_expiry')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
