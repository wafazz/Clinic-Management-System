<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->enum('payment_type', ['cash', 'panel'])->default('cash')->after('status');
            $table->foreignId('insurance_panel_id')->nullable()->after('payment_type')->constrained()->nullOnDelete();
            $table->foreignId('patient_insurance_id')->nullable()->after('insurance_panel_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['insurance_panel_id']);
            $table->dropForeign(['patient_insurance_id']);
            $table->dropColumn(['payment_type', 'insurance_panel_id', 'patient_insurance_id']);
        });
    }
};
