<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->foreignId('consultation_id')->nullable()->after('appointment_id')->constrained()->nullOnDelete();
        });

        Schema::table('lab_reports', function (Blueprint $table) {
            $table->foreignId('consultation_id')->nullable()->after('appointment_id')->constrained()->nullOnDelete();
        });

        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'consultation_id')) {
                $table->foreignId('consultation_id')->nullable()->after('appointment_id')->constrained()->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropForeign(['consultation_id']);
            $table->dropColumn('consultation_id');
        });

        Schema::table('lab_reports', function (Blueprint $table) {
            $table->dropForeign(['consultation_id']);
            $table->dropColumn('consultation_id');
        });

        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'consultation_id')) {
                $table->dropForeign(['consultation_id']);
                $table->dropColumn('consultation_id');
            }
        });
    }
};
