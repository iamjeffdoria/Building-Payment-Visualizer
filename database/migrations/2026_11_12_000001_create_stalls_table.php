<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stalls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained()->cascadeOnDelete();
            $table->string('stall_no');
            $table->string('tenant_name');
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('status')->default('unpaid'); // paid | unpaid | overdue
            $table->timestamps();

            $table->unique(['building_id', 'stall_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stalls');
    }
};