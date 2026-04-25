<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insurance_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('insurance_panel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_insurance_id')->constrained()->cascadeOnDelete();
            $table->string('claim_number')->unique();
            $table->string('gl_number')->nullable(); // Guarantee Letter number
            $table->enum('gl_status', ['not_required', 'pending', 'approved', 'rejected'])->default('not_required');
            $table->decimal('claim_amount', 10, 2);
            $table->decimal('approved_amount', 10, 2)->nullable();
            $table->decimal('patient_copay', 10, 2)->default(0); // patient pays this portion
            $table->enum('status', ['draft', 'submitted', 'approved', 'partial', 'rejected', 'paid'])->default('draft');
            $table->date('submission_date')->nullable();
            $table->date('approval_date')->nullable();
            $table->date('payment_date')->nullable();
            $table->string('payment_reference')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurance_claims');
    }
};
