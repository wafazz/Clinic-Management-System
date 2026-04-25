<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('treatment_plan_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('diagnosis')->nullable();
            $table->text('description')->nullable();
            $table->integer('total_sessions')->default(1);
            $table->integer('interval_days')->default(7);
            $table->json('session_defaults')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('treatment_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('consultation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('template_id')->nullable()->constrained('treatment_plan_templates')->nullOnDelete();
            $table->string('plan_number')->unique();
            $table->string('title');
            $table->string('diagnosis')->nullable();
            $table->text('description')->nullable();
            $table->integer('total_sessions')->default(1);
            $table->integer('completed_sessions')->default(0);
            $table->enum('status', ['active', 'completed', 'cancelled', 'on_hold'])->default('active');
            $table->date('start_date');
            $table->date('expected_end_date')->nullable();
            $table->date('actual_end_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('treatment_plan_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('treatment_plan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('consultation_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('session_number');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->date('scheduled_date')->nullable();
            $table->enum('status', ['pending', 'scheduled', 'completed', 'skipped', 'cancelled'])->default('pending');
            $table->text('doctor_notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('treatment_plan_sessions');
        Schema::dropIfExists('treatment_plans');
        Schema::dropIfExists('treatment_plan_templates');
    }
};
