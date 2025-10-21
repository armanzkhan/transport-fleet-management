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
        Schema::create('developer_accesses', function (Blueprint $table) {
            $table->id();
            $table->string('developer_name');
            $table->string('developer_email');
            $table->enum('access_type', ['read_only', 'limited_write', 'full_access', 'emergency']);
            $table->json('permissions')->nullable();
            $table->datetime('start_date');
            $table->datetime('end_date')->nullable();
            $table->text('reason');
            $table->enum('status', ['pending', 'approved', 'active', 'expired', 'revoked'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('revoked_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('revoked_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['status', 'start_date', 'end_date']);
            $table->index('developer_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('developer_accesses');
    }
};
