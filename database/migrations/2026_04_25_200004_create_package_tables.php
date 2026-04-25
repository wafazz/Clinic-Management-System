<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['one_time', 'subscription', 'bundle'])->default('one_time');
            $table->decimal('price', 10, 2);
            $table->enum('billing_cycle', ['once', 'monthly', 'quarterly', 'yearly'])->default('once');
            $table->integer('duration_days')->nullable();
            $table->integer('max_visits')->nullable();
            $table->boolean('allow_partial_payment')->default(false);
            $table->decimal('min_deposit_amount', 10, 2)->nullable();
            $table->decimal('min_deposit_percent', 5, 2)->nullable();
            $table->json('includes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('package_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('service_packages')->cascadeOnDelete();
            $table->enum('item_type', ['consultation', 'lab', 'medicine', 'service']);
            $table->integer('item_id')->nullable();
            $table->string('description');
            $table->integer('quantity');
            $table->decimal('unit_value', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('patient_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('package_id')->constrained('service_packages')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('subscription_number')->unique();
            $table->enum('status', ['pending', 'active', 'expired', 'cancelled', 'suspended'])->default('pending');
            $table->enum('payment_mode', ['full', 'partial'])->default('full');
            $table->decimal('total_amount', 10, 2);
            $table->decimal('deposit_amount', 10, 2)->default(0);
            $table->decimal('balance_amount', 10, 2)->default(0);
            $table->decimal('per_session_amount', 10, 2)->default(0);
            $table->decimal('total_paid', 10, 2)->default(0);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('next_billing_date')->nullable();
            $table->integer('visits_total')->nullable();
            $table->integer('visits_used')->default(0);
            $table->enum('payment_method', ['cash', 'card', 'online'])->default('cash');
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancel_reason')->nullable();
            $table->timestamps();
        });

        Schema::create('subscription_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('patient_subscriptions')->cascadeOnDelete();
            $table->enum('payment_type', ['deposit', 'session', 'full', 'renewal'])->default('session');
            $table->integer('session_number')->nullable();
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['cash', 'card', 'online'])->default('cash');
            $table->string('reference_number')->nullable();
            $table->enum('status', ['pending', 'paid', 'failed', 'waived'])->default('pending');
            $table->date('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('subscription_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('patient_subscriptions')->cascadeOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('consultation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('package_item_id')->nullable()->constrained('package_items')->nullOnDelete();
            $table->foreignId('subscription_payment_id')->nullable()->constrained('subscription_payments')->nullOnDelete();
            $table->enum('item_type', ['consultation', 'lab', 'medicine', 'service']);
            $table->string('description');
            $table->integer('quantity_used')->default(1);
            $table->timestamp('used_at')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_usages');
        Schema::dropIfExists('subscription_payments');
        Schema::dropIfExists('patient_subscriptions');
        Schema::dropIfExists('package_items');
        Schema::dropIfExists('service_packages');
    }
};
