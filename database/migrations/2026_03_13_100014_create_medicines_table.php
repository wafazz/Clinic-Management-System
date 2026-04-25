<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pharmacy_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('generic_name')->nullable();
            $table->string('sku')->nullable()->unique();
            $table->string('unit')->default('tablet'); // tablet, capsule, ml, bottle, tube, sachet
            $table->decimal('cost_price', 10, 2)->default(0);
            $table->decimal('selling_price', 10, 2)->default(0);
            $table->integer('reorder_level')->default(10);
            $table->integer('current_stock')->default(0);
            $table->date('expiry_date')->nullable();
            $table->string('manufacturer')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};
