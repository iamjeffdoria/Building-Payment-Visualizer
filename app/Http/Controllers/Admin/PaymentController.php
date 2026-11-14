<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Stall;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    // Mark one month of the ledger as paid (always the full monthly amount).
    public function store(Request $request, Stall $stall)
    {
        $validated = $request->validate([
            'year' => ['required', 'integer', 'between:2000,2100'],
            'month' => ['required', 'integer', 'between:1,12'],
            'paid_on' => ['required', 'date', 'before_or_equal:today'],
            'note' => ['nullable', 'string', 'max:20'],
        ]);

        $label = Stall::MONTHS[(int) $validated['month']] . ' ' . $validated['year'];

        $already = $stall->payments()
            ->where('year', $validated['year'])
            ->where('month', $validated['month'])
            ->exists();

        if ($already) {
            return back()->withErrors(['month' => $label . ' is already marked as paid.']);
        }

        $stall->payments()->create([
            'year' => $validated['year'],
            'month' => $validated['month'],
            'amount' => $stall->amount,
            'paid_on' => $validated['paid_on'],
            'note' => $validated['note'] ?? null,
        ]);

        return back()->with('success', $label . ' marked as paid (₱' . number_format($stall->amount, 2) . ').');
    }

    public function destroy(Payment $payment)
    {
        $label = Stall::MONTHS[$payment->month] . ' ' . $payment->year;

        $payment->delete();

        return back()->with('success', $label . ' payment removed.');
    }
}