<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('walk_in_queues', function (Blueprint $table) {
            $table->foreignId('appointment_id')->nullable()->after('doctor_id')->constrained()->onDelete('set null');
            $table->enum('type', ['walk_in', 'appointment'])->default('walk_in')->after('queue_number');
        });
    }

    public function down(): void
    {
        Schema::table('walk_in_queues', function (Blueprint $table) {
            $table->dropForeign(['appointment_id']);
            $table->dropColumn(['appointment_id', 'type']);
        });
    }
};
