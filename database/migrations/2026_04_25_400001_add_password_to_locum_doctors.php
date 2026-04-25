<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('locum_doctors', function (Blueprint $table) {
            if (!Schema::hasColumn('locum_doctors', 'password')) {
                $table->string('password')->nullable()->after('email');
            }
            if (!Schema::hasColumn('locum_doctors', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('locum_doctors', function (Blueprint $table) {
            if (Schema::hasColumn('locum_doctors', 'password')) {
                $table->dropColumn('password');
            }
            if (Schema::hasColumn('locum_doctors', 'last_login_at')) {
                $table->dropColumn('last_login_at');
            }
        });
    }
};
