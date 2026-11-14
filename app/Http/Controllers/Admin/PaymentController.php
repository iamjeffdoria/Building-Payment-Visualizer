<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Stall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    // Mark a month of the ledger as paid (always the full monthly amount).
    // mode=through also pays every unpaid month before it in that year (arrears or advance).
    public function store(Request $request, Stall $stall)
    {
        $validated = $request->validate([
            'year' => ['required', 'integer', 'between:2000,2100'],
            'month' => ['required', 'integer', 'between:1,12'],
            'mode' => ['nullable', 'in:single,through'],
            'paid_on' => ['required', 'date', 'before_or_equal:today'],
            'note' => ['nullable', 'string', 'max:20'],
            'dates.*' => ['nullable', 'date', 'before_or_equal:today'],
            'notes.*' => ['nullable', 'string', 'max:20'],
        ]);

        $year = (int) $validated['year'];
        $month = (int) $validated['month'];
        $first = ($validated['mode'] ?? 'single') === 'through' ? 1 : $month;

        $alreadyPaid = $stall->payments()->where('year', $year)->pluck('month');
        $toPay = collect(range($first, $month))->diff($alreadyPaid)->values();

        if ($toPay->isEmpty()) {
            return back()->withErrors([
                'month' => Stall::MONTHS[$month] . ' ' . $year . ' is already marked as paid.',
            ]);
        }

        // In "through" mode each row sends its own date and note; fall back to the clicked row's date only.
        $dates = $validated['dates'] ?? [];
        $notes = $validated['notes'] ?? [];

        DB::transaction(function () use ($stall, $toPay, $year, $month, $validated, $dates, $notes) {
            foreach ($toPay as $m) {
                $isClicked = $m === $month;

                $stall->payments()->create([
                    'year' => $year,
                    'month' => $m,
                    'amount' => $stall->amount,
                    'paid_on' => $dates[$m] ?? $validated['paid_on'],
                    'note' => array_key_exists($m, $notes)
                        ? $notes[$m]
                        : ($isClicked ? ($validated['note'] ?? null) : null),
                ]);
            }
        });

        $total = '₱' . number_format($stall->amount * $toPay->count(), 2);

        if ($toPay->count() === 1) {
            return back()->with('success', Stall::MONTHS[$toPay->first()] . ' ' . $year . ' marked as paid (' . $total . ').');
        }

        return back()->with('success',
            Stall::MONTHS[$toPay->first()] . ' to ' . Stall::MONTHS[$toPay->last()] . ' ' . $year
            . ' marked as paid (' . $toPay->count() . ' months, ' . $total . ').');
    }

    // Remove one month, or (mode=from) that month and every later paid month of the same year.
    public function destroy(Request $request, Payment $payment)
    {
        $label = Stall::MONTHS[$payment->month] . ' ' . $payment->year;

        if ($request->input('mode') === 'from') {
            $later = Payment::where('stall_id', $payment->stall_id)
                ->where('year', $payment->year)
                ->where('month', '>=', $payment->month);

            $months = (clone $later)->orderBy('month')->pluck('month');

            if ($months->count() > 1) {
                $later->delete();

                return back()->with('success',
                    Stall::MONTHS[$months->first()] . ' to ' . Stall::MONTHS[$months->last()] . ' ' . $payment->year
                    . ' payments removed (' . $months->count() . ' months).');
            }
        }

        $payment->delete();

        return back()->with('success', $label . ' payment removed.');
    }
}