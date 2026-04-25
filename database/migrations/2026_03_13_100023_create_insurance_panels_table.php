<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insurance_panels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('company_name');
            $table->enum('type', ['corporate', 'insurance', 'tpa', 'government'])->default('insurance'); // TPA = Third Party Administrator
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->integer('credit_terms')->default(30); // days
            $table->decimal('consultation_limit', 10, 2)->nullable(); // max per visit
            $table->decimal('annual_limit', 10, 2)->nullable(); // max per year per member
            $table->text('covered_services')->nullable(); // JSON or text description
            $table->text('exclusions')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('requires_gl')->default(false); // Guarantee Letter required?
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurance_panels');
    }
};
