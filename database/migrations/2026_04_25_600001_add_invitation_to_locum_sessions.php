<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('locum_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('locum_sessions', 'locum_invitation_id')) {
                $table->foreignId('locum_invitation_id')->nullable()->after('branch_id')
                    ->constrained()->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('locum_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('locum_sessions', 'locum_invitation_id')) {
                $table->dropForeign(['locum_invitation_id']);
                $table->dropColumn('locum_invitation_id');
            }
        });
    }
};
