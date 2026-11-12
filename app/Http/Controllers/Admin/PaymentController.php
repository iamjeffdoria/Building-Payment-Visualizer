<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Stall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function store(Request $request, Stall $stall)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'paid_on' => ['required', 'date', 'before_or_equal:today'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $amount = round((float) $validated['amount'], 2);

        if ($amount > $stall->balance) {
            return back()
                ->withErrors(['amount' => 'Payment is more than the remaining balance of ₱' . number_format($stall->balance, 2) . '.'])
                ->withInput();
        }

        DB::transaction(function () use ($stall, $validated, $amount) {
            $stall->payments()->create([
                'amount' => $amount,
                'paid_on' => $validated['paid_on'],
                'note' => $validated['note'] ?? null,
            ]);

            $stall->refreshPaid();
        });

        return back()->with('success', 'Payment of ₱' . number_format($amount, 2) . ' recorded.');
    }

    public function destroy(Payment $payment)
    {
        $stall = $payment->stall;

        DB::transaction(function () use ($payment, $stall) {
            $payment->delete();
            $stall->refreshPaid();
        });

        return back()->with('success', 'Payment removed.');
    }
}