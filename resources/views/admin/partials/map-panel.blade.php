@php
    $badges = [
        'paid' => 'bg-green-100 text-green-800',
        'unpaid' => 'bg-amber-100 text-amber-800',
        'overdue' => 'bg-red-100 text-red-800',
    ];
    $pct = $s['total'] ? round($s['paid'] / $s['total'] * 100) : 0;
@endphp

<div class="flex items-start justify-between gap-2">
    <div>
        <strong>{{ $building->name }}</strong>
        <p class="text-sm text-gray-500">
            {{ $s['total'] }} stalls{{ $pinned ? ' · pinned' : '' }}
            &middot; <a href="{{ route('admin.buildings.show', $building) }}" class="text-blue-600 hover:underline">Manage stalls</a>
        </p>
    </div>
    @if ($pinned)
        <button type="button" data-unpin class="rounded-md bg-gray-200 px-2.5 py-1 text-xs hover:bg-gray-300">Unpin</button>
    @endif
</div>

<div class="mt-3 grid grid-cols-3 gap-2 text-center text-xs">
    <div class="rounded-lg bg-green-50 p-2"><div class="text-lg font-semibold text-green-800">{{ $s['paid'] }}</div>Paid</div>
    <div class="rounded-lg bg-amber-50 p-2"><div class="text-lg font-semibold text-amber-800">{{ $s['unpaid'] }}</div>Unpaid</div>
    <div class="rounded-lg bg-red-50 p-2"><div class="text-lg font-semibold text-red-800">{{ $s['overdue'] }}</div>Overdue</div>
</div>

<div class="mt-3">
    <div class="mb-1 flex justify-between text-xs text-gray-500">
        <span>Collected ₱{{ number_format($s['collected']) }} of ₱{{ number_format($s['expected']) }}</span>
        <span>{{ $pct }}%</span>
    </div>
    <div class="h-2 overflow-hidden rounded-full bg-gray-200">
        <div class="h-full bg-green-500" style="width: {{ $pct }}%"></div>
    </div>
</div>

<div class="mt-3 max-h-80 overflow-y-auto">
    <table class="w-full text-left text-sm">
        <thead class="sticky top-0 bg-white">
            <tr class="border-b border-gray-200 text-xs uppercase text-gray-500">
                <th class="px-2 py-2">Stall</th>
                <th class="px-2 py-2">Person</th>
                <th class="px-2 py-2">Amount</th>
                <th class="px-2 py-2">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($stalls as $stall)
                <tr class="border-b border-gray-100">
                    <td class="px-2 py-1.5 font-medium">{{ $stall->stall_no }}</td>
                    <td class="px-2 py-1.5">{{ $stall->tenant_name }}</td>
                    <td class="px-2 py-1.5">₱{{ number_format($stall->amount) }}</td>
                    <td class="px-2 py-1.5">
                        <span class="rounded-full px-2 py-0.5 text-xs {{ $badges[$stall->status] ?? 'bg-gray-200' }}">{{ ucfirst($stall->status) }}</span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-2 py-3 text-gray-500">No stalls yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>