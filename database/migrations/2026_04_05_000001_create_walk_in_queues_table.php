<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('walk_in_queues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->foreignId('patient_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('doctor_id')->nullable()->constrained()->onDelete('set null');
            $table->string('queue_number', 10);
            $table->string('patient_name');
            $table->string('patient_phone')->nullable();
            $table->date('queue_date');
            $table->string('reason')->nullable();
            $table->enum('status', ['waiting', 'serving', 'completed', 'skipped', 'cancelled'])->default('waiting');
            $table->integer('position')->default(0);
            $table->timestamp('called_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['branch_id', 'queue_number', 'queue_date']);
            $table->index(['branch_id', 'queue_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('walk_in_queues');
    }
};
