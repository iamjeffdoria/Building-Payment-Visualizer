<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stall_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->date('paid_on');
            $table->string('note')->nullable();
            $table->timestamps();
        });

        Schema::table('stalls', function (Blueprint $table) {
            $table->decimal('amount_paid', 10, 2)->default(0)->after('amount');
            $table->date('due_date')->nullable()->after('amount_paid');
        });

        // Convert existing data: "paid" stalls get one full payment record.
        foreach (DB::table('stalls')->where('status', 'paid')->get() as $stall) {
            DB::table('payments')->insert([
                'stall_id' => $stall->id,
                'amount' => $stall->amount,
                'paid_on' => now()->toDateString(),
                'note' => 'Migrated full payment',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('stalls')->where('id', $stall->id)->update(['amount_paid' => $stall->amount]);
        }

        // Old "overdue" stalls keep showing as overdue.
        DB::table('stalls')->where('status', 'overdue')
            ->update(['due_date' => now()->subDay()->toDateString()]);

        Schema::table('stalls', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }

    public function down(): void
    {
        Schema::table('stalls', function (Blueprint $table) {
            $table->string('status')->default('unpaid');
        });

        DB::table('stalls')->whereColumn('amount_paid', '>=', 'amount')->update(['status' => 'paid']);

        Schema::table('stalls', function (Blueprint $table) {
            $table->dropColumn(['amount_paid', 'due_date']);
        });

        Schema::dropIfExists('payments');
    }
};