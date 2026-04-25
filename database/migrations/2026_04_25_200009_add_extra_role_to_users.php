<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'doctor', 'staff', 'receptionist', 'super_admin', 'nurse', 'pharmacist', 'sales_team', 'locum_doctor') NOT NULL DEFAULT 'staff'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'doctor', 'staff', 'receptionist') NOT NULL DEFAULT 'staff'");
    }
};
