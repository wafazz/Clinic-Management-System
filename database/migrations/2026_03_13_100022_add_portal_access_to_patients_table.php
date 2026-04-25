<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->string('portal_token')->nullable()->unique()->after('is_active');
            $table->timestamp('portal_token_expires_at')->nullable()->after('portal_token');
            $table->string('password')->nullable()->after('portal_token_expires_at');
            $table->timestamp('last_portal_login')->nullable()->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['portal_token', 'portal_token_expires_at', 'password', 'last_portal_login']);
        });
    }
};
