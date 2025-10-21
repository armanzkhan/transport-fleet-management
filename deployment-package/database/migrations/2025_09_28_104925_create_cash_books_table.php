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
        Schema::create('cash_books', function (Blueprint $table) {
            $table->id();
            $table->string('cash_book_number')->unique();
            $table->date('entry_date');
            $table->enum('transaction_type', ['receive', 'payment']);
            $table->string('transaction_number');
            $table->foreignId('account_id')->constrained('chart_of_accounts');
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles');
            $table->enum('payment_type', ['Advance', 'Expense', 'Shortage'])->nullable();
            $table->text('description');
            $table->decimal('amount', 15, 2);
            $table->decimal('previous_day_balance', 15, 2)->default(0);
            $table->decimal('total_cash_in_hand', 15, 2)->default(0);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_books');
    }
};
