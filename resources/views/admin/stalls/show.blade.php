@extends('layouts.app')

@section('title', $stall->stall_no)
@section('active', 'buildings')

@php
    $status = $stall->status;
    $badge = \App\Models\Stall::STATUS_BADGES[$status];
@endphp

@section('content')
    <div class="mb-5">
        <a href="{{ route('admin.buildings.show', $stall->building) }}"
           class="inline-flex items-center gap-1.5 rounded-md border border-blue-200 bg-blue-50 px-3 py-1.5 text-sm font-medium text-blue-700 transition-colors hover:border-blue-300 hover:bg-blue-100 hover:text-blue-800">
            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            {{ $stall->building->name }}
        </a>
        <div class="mt-1 flex flex-wrap items-center gap-3">
            <h1 class="text-2xl font-semibold">{{ $stall->stall_no }} &middot; {{ $stall->tenant_name }}</h1>
            <span class="rounded-full px-2 py-0.5 text-xs {{ $badge }}">{{ ucfirst($status) }}</span>
        </div>
    </div>

    {{-- Summary --}}
    <div class="mb-5 rounded-xl bg-white p-5 shadow-sm">
        <div class="grid grid-cols-2 gap-3 text-center sm:grid-cols-4">
            <div class="rounded-lg bg-gray-50 p-3">
                <div class="text-lg font-semibold">₱{{ number_format($stall->amount, 2) }}</div>
                <div class="text-xs text-gray-500">Amount due</div>
            </div>
            <div class="rounded-lg bg-green-50 p-3">
                <div class="text-lg font-semibold text-green-800">₱{{ number_format($stall->amount_paid, 2) }}</div>
                <div class="text-xs text-gray-500">Paid so far</div>
            </div>
            <div class="rounded-lg bg-amber-50 p-3">
                <div class="text-lg font-semibold text-amber-800">₱{{ number_format($stall->balance, 2) }}</div>
                <div class="text-xs text-gray-500">Balance</div>
            </div>
            <div class="rounded-lg bg-gray-50 p-3">
                <div class="text-lg font-semibold">{{ $stall->due_date?->format('M j, Y') ?? '—' }}</div>
                <div class="text-xs text-gray-500">Due date</div>
            </div>
        </div>

        <div class="mt-4">
            <div class="mb-1 flex justify-between text-xs text-gray-500">
                <span>Progress</span><span>{{ $stall->percent_paid }}%</span>
            </div>
            <div class="h-2 overflow-hidden rounded-full bg-gray-200">
                <div class="h-full bg-green-500" style="width: {{ $stall->percent_paid }}%"></div>
            </div>
        </div>
    </div>

    {{-- Record payment + History: side by side on desktop, stacked on mobile --}}
    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2 lg:items-start">

    {{-- Record payment --}}
    <div class="rounded-xl bg-white p-5 shadow-sm">
        <strong>Record a payment</strong>

        @if ($stall->balance > 0)
            <form method="POST" action="{{ route('admin.payments.store', $stall) }}"
                  class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                @csrf
                <div>
                    <label for="pay-amount" class="mb-1 block text-sm font-medium">Amount</label>
                    <input type="number" id="pay-amount" name="amount" step="0.01" min="0.01" max="{{ $stall->balance }}"
                           value="{{ old('amount') }}" placeholder="Max {{ number_format($stall->balance, 2) }}" required
                           class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    <button type="button" id="fill-balance" data-balance="{{ $stall->balance }}"
                            class="mt-1 text-xs text-blue-600 hover:underline">Pay full balance</button>
                </div>
                <div>
                    <label for="pay-date" class="mb-1 block text-sm font-medium">Date paid</label>
                    <input type="date" id="pay-date" name="paid_on" value="{{ old('paid_on', now()->toDateString()) }}"
                           max="{{ now()->toDateString() }}" required
                           class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label for="pay-note" class="mb-1 block text-sm font-medium">Note <span class="text-xs text-gray-500">(optional, e.g. OR number)</span></label>
                    <input type="text" id="pay-note" name="note" value="{{ old('note') }}" maxlength="255"
                           class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div class="sm:col-span-2">
                    <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700">Save payment</button>
                </div>
            </form>
        @else
            <p class="mt-2 text-sm text-green-700">This stall is fully paid. Nothing left to collect.</p>
        @endif
    </div>

    {{-- History --}}
    <div class="rounded-xl bg-white p-5 shadow-sm">
        <strong>Payment history</strong>
        <div class="mt-3 overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-xs uppercase text-gray-500">
                        <th class="px-2 py-2.5">Date</th>
                        <th class="px-2 py-2.5">Amount</th>
                        <th class="px-2 py-2.5">Note</th>
                        <th class="w-24 px-2 py-2.5">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($stall->payments as $payment)
                        <tr class="border-b border-gray-100">
                            <td class="px-2 py-2.5">{{ $payment->paid_on->format('M j, Y') }}</td>
                            <td class="px-2 py-2.5 font-medium">₱{{ number_format($payment->amount, 2) }}</td>
                            <td class="px-2 py-2.5 text-gray-600">{{ $payment->note ?? '—' }}</td>
                            <td class="px-2 py-2.5">
                                <form method="POST" action="{{ route('admin.payments.destroy', $payment) }}"
                                      data-confirm="Remove this payment?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-md bg-red-600 px-2.5 py-1 text-xs text-white hover:bg-red-700">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-2 py-3 text-gray-500">No payments recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    </div>
@endsection

@push('scripts')
    @vite(['resources/js/pages/stall-show.js'])
@endpush