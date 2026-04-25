<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('ic_number')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('source')->nullable();
            $table->string('service_interest')->nullable();
            $table->enum('status', [
                'new_lead', 'contacted', 'followup_1', 'followup_2', 'followup_3', 'followup_4', 'followup_5',
                'appointment_booked', 'success', 'not_showing', 'reject', 'kiv', 'no_answer', 'wrong_number', 'duplicate',
            ])->default('new_lead');
            $table->text('notes')->nullable();
            $table->text('last_followup_notes')->nullable();
            $table->timestamp('last_contacted_at')->nullable();
            $table->timestamp('next_followup_at')->nullable();
            $table->timestamp('converted_at')->nullable();
            $table->foreignId('patient_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index(['assigned_to', 'status']);
            $table->index('status');
            $table->index('next_followup_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
