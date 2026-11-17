@extends('layouts.app')

@section('title', $stall->stall_no)
@section('active', 'buildings')

@php
    $today = today();

    // Status of the viewed year (the model's status only covers the current year).
    if ($year === $today->year) {
        $status = $stall->status;
    } elseif ($year < $today->year) {
        $status = $paid->count() >= 12 ? 'paid' : 'overdue';
    } else {
        $status = 'upcoming';
    }
    $badge = \App\Models\Stall::STATUS_BADGES[$status] ?? 'bg-gray-100 text-gray-500';

    // How many months of the viewed year are already due.
    $dueMonths = $year < $today->year ? 12 : ($year === $today->year ? $today->month : 0);
    // Balance counts unpaid due months only, so discounted payments don't leave a leftover amount.
    $unpaidDue = $dueMonths - $paid->where('month', '<=', $dueMonths)->count();
    $balance = max(0, $unpaidDue) * $stall->amount;
    $paidCount = $paid->count();
    // Months paid ahead of today (future months of this year, or any month of a future year).
    $advanceCount = $paid->filter(fn ($p) => $year > $today->year || ($year === $today->year && $p->month > $today->month))->count();
    $advanceThrough = $advanceCount ? \App\Models\Stall::MONTHS[$paid->keys()->max()] : null;
    // Progress is measured against the whole year: what was paid vs. what the 12 months add up to.
    $paidTotal = (float) $paid->sum('amount');
    $yearTotal = $paidTotal + (12 - $paidCount) * $stall->amount;
    $pct = $yearTotal > 0 ? (int) floor($paidTotal / $yearTotal * 100) : 0;
@endphp

@section('content')
    <div class="mb-3 flex flex-wrap items-center" style="gap: 0.5rem 1.25rem;">
        <a href="{{ route('admin.buildings.show', $stall->building) }}"
           class="inline-flex items-center gap-1 rounded-md border border-blue-200 bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700 transition-colors hover:border-blue-300 hover:bg-blue-100 hover:text-blue-800">
            <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            {{ $stall->building->name }}
        </a>
        <h1 class="text-lg font-semibold leading-tight">{{ $stall->stall_no }} &middot; {{ $stall->tenant_name }}</h1>
        <span class="rounded-full px-2 py-0.5 text-xs {{ $badge }}">{{ ucfirst($status) }}</span>
        @if ($advanceCount > 0)
            <span class="rounded-full bg-blue-100 px-2 py-0.5 text-xs text-blue-800">Paid ahead through {{ $advanceThrough }} {{ $year }}</span>
        @endif
    </div>

    {{-- Summary --}}
    <div class="mb-3 rounded-xl bg-white p-3 shadow-sm">
        <div class="grid grid-cols-2 gap-2 text-center sm:grid-cols-4">
            <div class="rounded-lg bg-gray-50 px-2 py-1.5">
                <div class="text-base font-semibold leading-tight">₱{{ number_format($stall->amount, 2) }}</div>
                <div class="text-xs text-gray-500">Monthly due</div>
            </div>
            <div class="rounded-lg bg-green-50 px-2 py-1.5">
                <div class="text-base font-semibold leading-tight text-green-800">₱{{ number_format($paid->sum('amount'), 2) }}</div>
                <div class="text-xs text-gray-500">Paid in {{ $year }}</div>
            </div>
            <div class="rounded-lg bg-amber-50 px-2 py-1.5">
                <div class="text-base font-semibold leading-tight text-amber-800">₱{{ number_format($balance, 2) }}</div>
                <div class="text-xs text-gray-500">Balance to date</div>
            </div>
            <div class="rounded-lg bg-gray-50 px-2 py-1.5">
                <div class="text-base font-semibold leading-tight">{{ $paidCount }} / 12</div>
                <div class="text-xs text-gray-500">Months paid</div>
            </div>
        </div>

        <div class="mt-2">
            <div class="mb-1 flex justify-between text-xs text-gray-500">
                <span>Paid vs. full year {{ $year }}</span><span>{{ $pct }}%</span>
            </div>
            <div class="h-1.5 overflow-hidden rounded-full bg-gray-200">
                <div class="h-full bg-green-500" style="width: {{ $pct }}%"></div>
            </div>
        </div>
    </div>

    {{-- Ledger --}}
    <div class="rounded-xl bg-white p-4 shadow-sm">
        <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
            <strong class="text-sm">Billing ledger</strong>
            <div class="flex items-center gap-2 text-xs">
                <a href="{{ route('admin.stalls.show', [$stall, 'year' => $year - 1]) }}"
                   class="rounded-md bg-gray-200 px-2.5 py-1 hover:bg-gray-300" aria-label="Previous year">&laquo;</a>
                <span class="min-w-12 text-center text-sm font-semibold">{{ $year }}</span>
                <a href="{{ route('admin.stalls.show', [$stall, 'year' => $year + 1]) }}"
                   class="rounded-md bg-gray-200 px-2.5 py-1 hover:bg-gray-300" aria-label="Next year">&raquo;</a>
                @if ($year !== $today->year)
                    <a href="{{ route('admin.stalls.show', $stall) }}" class="text-blue-600 hover:underline">This year</a>
                @endif
            </div>
        </div>

        <div class="rounded-lg border border-gray-200"
             style="max-height: calc(100dvh - 21rem); min-height: 14rem; overflow: auto; scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent;">
            <style>
                .tbl-sticky { border-collapse: separate; border-spacing: 0; }
                .tbl-sticky thead { position: sticky; top: 0; z-index: 10; }
                .tbl-sticky thead th { background: #f9fafb; border-bottom: 1px solid #e5e7eb; }
                .tbl-sticky tbody td { border-bottom: 1px solid #f3f4f6; }
            </style>
            <table class="tbl-sticky w-full min-w-[760px] text-left text-sm">
                <thead>
                    <tr class="text-xs uppercase text-gray-500">
                        <th class="px-2 py-2.5">Month</th>
                        <th class="px-2 py-2.5">Amount</th>
                        <th class="px-2 py-2.5">Status</th>
                        <th class="px-2 py-2.5">Date paid</th>
                        <th class="px-2 py-2.5">Note</th>
                        <th class="w-64 px-2 py-2.5">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (\App\Models\Stall::MONTHS as $m => $name)
                        @php
                            $p = $paid->get($m);
                            // Unpaid months this row would settle, and the first of them (skips months already paid).
                            $unpaidUpTo = $p ? 0 : $m - $paid->keys()->filter(fn ($k) => $k < $m)->count();
                            $firstUnpaid = $p ? null : collect(range(1, $m))->first(fn ($k) => ! $paid->has($k));
                            $fromName = $firstUnpaid ? \App\Models\Stall::MONTHS[$firstUnpaid] : $name;
                            // Total for "Pay up to here" if paid today (months not yet arrived are discounted).
                            $batchTotal = collect(range($firstUnpaid ?? $m, $m))
                                ->reject(fn ($k) => $paid->has($k))
                                ->sum(fn ($k) => \App\Models\Stall::amountFor($stall->amount, $year, $k, $today));
                            // Paid months this row's "Undo from here" would remove (this month and every later one).
                            $paidFromHere = $p ? $paid->keys()->filter(fn ($k) => $k >= $m)->count() : 0;
                            $lastPaidName = $p ? \App\Models\Stall::MONTHS[$paid->keys()->max()] : null;
                            if ($p) {
                                $isAdvance = $year > $today->year || ($year === $today->year && $m > $today->month);
                                $label = $isAdvance ? 'Advance' : 'Paid';
                                $cls = $isAdvance ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800';
                            } elseif ($year < $today->year || ($year === $today->year && $m < $today->month)) {
                                $label = 'Overdue';
                                $cls = 'bg-red-100 text-red-800';
                            } elseif ($year === $today->year && $m === $today->month) {
                                $label = 'Due';
                                $cls = 'bg-amber-100 text-amber-800';
                            } else {
                                $label = 'Upcoming';
                                $cls = 'bg-gray-100 text-gray-500';
                            }
                        @endphp
                        <tr class="border-b border-gray-100">
                            <td class="px-2 py-2.5 font-medium">{{ $name }}</td>
                            <td class="whitespace-nowrap px-2 py-2.5">₱{{ number_format($p ? $p->amount : $stall->amount, 2) }}</td>
                            <td class="px-2 py-2.5">
                                <span class="rounded-full px-2 py-0.5 text-xs {{ $cls }}">{{ $label }}</span>
                            </td>

                            @if ($p)
                                <td class="whitespace-nowrap px-2 py-2.5">{{ $p->paid_on->format('M j, Y') }}</td>
                                <td class="px-2 py-2.5 text-gray-600">{{ $p->note ?: '—' }}</td>
                                <td class="px-2 py-2.5">
                                    <form method="POST" action="{{ route('admin.payments.destroy', $p) }}"
                                          class="flex items-center gap-1.5">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" name="mode" value="single"
                                                onclick="return confirm('Remove the {{ $name }} {{ $year }} payment?')"
                                                class="whitespace-nowrap rounded-md bg-red-600 px-2.5 py-1 text-xs text-white hover:bg-red-700">Undo</button>
                                        @if ($paidFromHere > 1)
                                            <button type="submit" name="mode" value="from"
                                                    title="Removes {{ $paidFromHere }} payments ({{ $name }} to {{ $lastPaidName }})"
                                                    onclick="return confirm('Remove {{ $paidFromHere }} payments, {{ $name }} to {{ $lastPaidName }} {{ $year }}?')"
                                                    class="whitespace-nowrap rounded-md border border-red-600 px-2.5 py-1 text-xs text-red-700 hover:bg-red-50">Undo from here</button>
                                        @endif
                                    </form>
                                </td>
                            @else
                                <td class="px-2 py-2.5">
                                    <input type="date" name="paid_on" form="pay-{{ $m }}" required
                                           value="{{ $today->toDateString() }}" max="{{ $today->toDateString() }}"
                                           aria-label="Date paid for {{ $name }}"
                                           class="w-full rounded-md border border-gray-300 px-2 py-1.5 text-sm">
                                </td>
                                <td class="px-2 py-2.5">
                                    <input type="text" name="note" form="pay-{{ $m }}" maxlength="20"
                                           placeholder="OR no. (optional)" aria-label="Note for {{ $name }}"
                                           class="w-full rounded-md border border-gray-300 px-2 py-1.5 text-sm">
                                </td>
                                <td class="px-2 py-2.5">
                                    <form id="pay-{{ $m }}" method="POST" action="{{ route('admin.payments.store', $stall) }}"
                                          class="flex items-center gap-1.5">
                                        @csrf
                                        <input type="hidden" name="year" value="{{ $year }}">
                                        <input type="hidden" name="month" value="{{ $m }}">
                                        <button type="submit" name="mode" value="single"
                                                class="whitespace-nowrap rounded-md bg-blue-600 px-2.5 py-1 text-xs text-white hover:bg-blue-700">Mark paid</button>
                                        @if ($unpaidUpTo > 1)
                                            <button type="submit" name="mode" value="through"
                                                    title="Marks {{ $unpaidUpTo }} months as paid ({{ $fromName }} to {{ $name }}), total ₱{{ number_format($batchTotal, 2) }} if paid today"
                                                    onclick="return confirm('Mark {{ $unpaidUpTo }} months as paid, {{ $fromName }} to {{ $name }} {{ $year }}? Total: ₱{{ number_format($batchTotal, 2) }} (months paid in advance get 10% off)') && payThrough(this.form)"
                                                    class="whitespace-nowrap rounded-md border border-blue-600 px-2.5 py-1 text-xs text-blue-700 hover:bg-blue-50">Pay up to here</button>
                                        @endif
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // "Pay up to here": send every unpaid row's own date and note along with the click.
    function payThrough(form) {
        form.querySelectorAll('input[data-row]').forEach(function (i) { i.remove(); });

        var month = parseInt(form.querySelector('input[name="month"]').value, 10);

        for (var k = 1; k <= month; k++) {
            var date = document.querySelector('input[name="paid_on"][form="pay-' + k + '"]');
            var note = document.querySelector('input[name="note"][form="pay-' + k + '"]');
            if (!date) continue; // this month is already paid

            [['dates[' + k + ']', date.value], ['notes[' + k + ']', note ? note.value : '']].forEach(function (pair) {
                var hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = pair[0];
                hidden.value = pair[1];
                hidden.setAttribute('data-row', '');
                form.appendChild(hidden);
            });
        }

        return true;
    }
</script>
@endpush