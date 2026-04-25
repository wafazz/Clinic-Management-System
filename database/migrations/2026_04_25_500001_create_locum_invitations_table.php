<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locum_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('locum_doctor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->dateTime('valid_from');
            $table->dateTime('valid_to');
            $table->boolean('can_consultation')->default(true);
            $table->boolean('can_treatment_plan')->default(true);
            $table->boolean('treatment_plan_requires_approval')->default(true);
            $table->enum('status', ['pending', 'accepted', 'declined', 'revoked', 'expired'])->default('pending');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['locum_doctor_id', 'status']);
            $table->index(['valid_from', 'valid_to']);
        });

        // Add locum_doctor_id to consultations so locum-attributed visits can be tracked
        Schema::table('consultations', function (Blueprint $table) {
            if (!Schema::hasColumn('consultations', 'locum_doctor_id')) {
                $table->foreignId('locum_doctor_id')->nullable()->after('doctor_id')
                    ->constrained('locum_doctors')->nullOnDelete();
            }
            if (!Schema::hasColumn('consultations', 'locum_invitation_id')) {
                $table->foreignId('locum_invitation_id')->nullable()->after('locum_doctor_id')
                    ->constrained('locum_invitations')->nullOnDelete();
            }
        });

        // Treatment plan approval workflow
        Schema::table('treatment_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('treatment_plans', 'approval_status')) {
                $table->enum('approval_status', ['auto_approved', 'pending_approval', 'approved', 'rejected'])
                    ->default('auto_approved')->after('status');
            }
            if (!Schema::hasColumn('treatment_plans', 'created_by_locum_id')) {
                $table->foreignId('created_by_locum_id')->nullable()->after('doctor_id')
                    ->constrained('locum_doctors')->nullOnDelete();
            }
            if (!Schema::hasColumn('treatment_plans', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->after('created_by_locum_id')
                    ->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('treatment_plans', 'approved_at')) {
                $table->timestamp('approved_at')->nullable();
            }
            if (!Schema::hasColumn('treatment_plans', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('treatment_plans', function (Blueprint $table) {
            $table->dropForeign(['created_by_locum_id']);
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['approval_status', 'created_by_locum_id', 'approved_by', 'approved_at', 'rejection_reason']);
        });

        Schema::table('consultations', function (Blueprint $table) {
            $table->dropForeign(['locum_doctor_id']);
            $table->dropForeign(['locum_invitation_id']);
            $table->dropColumn(['locum_doctor_id', 'locum_invitation_id']);
        });

        Schema::dropIfExists('locum_invitations');
    }
};
