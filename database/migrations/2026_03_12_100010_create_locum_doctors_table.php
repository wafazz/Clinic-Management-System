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
        Schema::create('locum_doctors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('ic_number')->nullable();
            $table->string('mmc_number')->nullable();
            $table->string('apc_number')->nullable();
            $table->string('specialization')->nullable();
            $table->decimal('hourly_rate', 10, 2)->default(0);
            $table->decimal('session_rate', 10, 2)->default(0);
            $table->text('bank_details')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locum_doctors');
    }
};
