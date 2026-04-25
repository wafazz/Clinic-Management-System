<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedule_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->boolean('is_available')->default(false);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('reason')->nullable();
            $table->timestamps();
        });

        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('consultation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('referring_doctor_id')->nullable()->constrained('doctors')->nullOnDelete();
            $table->string('referral_number')->unique();
            $table->string('referred_to');
            $table->string('specialty')->nullable();
            $table->text('reason');
            $table->text('clinical_summary')->nullable();
            $table->date('referral_date');
            $table->enum('urgency', ['routine', 'urgent', 'emergency'])->default('routine');
            $table->enum('status', ['pending', 'sent', 'completed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('locum_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('locum_doctor_id')->constrained()->cascadeOnDelete();
            $table->string('payment_number')->unique();
            $table->date('period_start');
            $table->date('period_end');
            $table->integer('total_sessions')->default(0);
            $table->decimal('gross_amount', 10, 2)->default(0);
            $table->decimal('deductions', 10, 2)->default(0);
            $table->decimal('net_amount', 10, 2)->default(0);
            $table->enum('status', ['pending', 'approved', 'paid'])->default('pending');
            $table->enum('payment_method', ['bank_transfer', 'cash', 'cheque'])->default('bank_transfer');
            $table->string('payment_reference')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('paid_at')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('locum_payment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('locum_payment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('locum_session_id')->constrained()->cascadeOnDelete();
            $table->date('session_date');
            $table->decimal('rate_amount', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locum_payment_items');
        Schema::dropIfExists('locum_payments');
        Schema::dropIfExists('referrals');
        Schema::dropIfExists('schedule_overrides');
    }
};
