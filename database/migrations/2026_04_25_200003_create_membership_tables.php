<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->enum('billing_cycle', ['free', 'monthly', 'yearly'])->default('yearly');
            $table->json('benefits')->nullable();
            $table->decimal('discount_consultation', 5, 2)->default(0);
            $table->decimal('discount_medicine', 5, 2)->default(0);
            $table->decimal('discount_lab', 5, 2)->default(0);
            $table->integer('free_consultations_per_year')->default(0);
            $table->integer('free_lab_tests_per_year')->default(0);
            $table->boolean('priority_queue')->default(false);
            $table->integer('max_family_members')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('patient_memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tier_id')->constrained('membership_tiers')->cascadeOnDelete();
            $table->string('membership_number')->unique();
            $table->enum('status', ['active', 'expired', 'cancelled', 'suspended'])->default('active');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('auto_renew')->default(false);
            $table->date('next_billing_date')->nullable();
            $table->integer('free_consultations_used')->default(0);
            $table->integer('free_lab_tests_used')->default(0);
            $table->decimal('total_savings', 10, 2)->default(0);
            $table->enum('payment_method', ['cash', 'card', 'online'])->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancel_reason')->nullable();
            $table->timestamps();
        });

        Schema::create('family_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membership_id')->constrained('patient_memberships')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->enum('relationship', ['spouse', 'child', 'parent', 'sibling', 'other']);
            $table->foreignId('added_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('membership_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membership_id')->constrained('patient_memberships')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->enum('usage_type', ['free_consultation', 'free_lab', 'discount_applied']);
            $table->string('description');
            $table->decimal('savings_amount', 10, 2)->default(0);
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_usage_logs');
        Schema::dropIfExists('family_members');
        Schema::dropIfExists('patient_memberships');
        Schema::dropIfExists('membership_tiers');
    }
};
