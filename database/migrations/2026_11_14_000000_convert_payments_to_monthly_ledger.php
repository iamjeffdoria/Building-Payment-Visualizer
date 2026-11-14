<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Old installment payments don't map to months, so the ledger starts fresh.
        Schema::dropIfExists('payments');

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stall_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month'); // 1-12
            $table->decimal('amount', 10, 2);     // monthly amount at the time it was paid
            $table->date('paid_on');
            $table->string('note')->nullable();
            $table->timestamps();

            $table->unique(['stall_id', 'year', 'month']);
        });

        Schema::table('stalls', function (Blueprint $table) {
            $table->dropColumn(['amount_paid', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::table('stalls', function (Blueprint $table) {
            $table->decimal('amount_paid', 10, 2)->default(0)->after('amount');
            $table->date('due_date')->nullable()->after('amount_paid');
        });

        Schema::dropIfExists('payments');

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stall_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->date('paid_on');
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }
};