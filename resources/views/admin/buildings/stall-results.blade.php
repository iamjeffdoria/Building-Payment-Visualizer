@php
    $badges = [
        'paid' => 'bg-green-100 text-green-800',
        'unpaid' => 'bg-amber-100 text-amber-800',
        'overdue' => 'bg-red-100 text-red-800',
    ];
@endphp

<div class="overflow-x-auto">
    <table class="w-full text-left text-sm">
        <thead>
            <tr class="border-b border-gray-200 text-xs uppercase text-gray-500">
                <th class="px-2 py-2.5">Stall</th>
                <th class="px-2 py-2.5">Person</th>
                <th class="px-2 py-2.5">Amount</th>
                <th class="px-2 py-2.5">Status</th>
                <th class="w-40 px-2 py-2.5">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($stalls as $stall)
                <tr class="border-b border-gray-100">
                    <td class="px-2 py-2.5 font-medium">{{ $stall->stall_no }}</td>
                    <td class="px-2 py-2.5">{{ $stall->tenant_name }}</td>
                    <td class="px-2 py-2.5">₱{{ number_format($stall->amount, 2) }}</td>
                    <td class="px-2 py-2.5">
                        <span class="rounded-full px-2 py-0.5 text-xs {{ $badges[$stall->status] ?? 'bg-gray-200' }}">{{ ucfirst($stall->status) }}</span>
                    </td>
                    <td class="px-2 py-2.5">
                        <button type="button" data-edit
                                data-id="{{ $stall->id }}"
                                data-stall-no="{{ $stall->stall_no }}"
                                data-name="{{ $stall->tenant_name }}"
                                data-amount="{{ $stall->amount }}"
                                data-status="{{ $stall->status }}"
                                class="rounded-md bg-blue-600 px-2.5 py-1 text-xs text-white hover:bg-blue-700">Edit</button>

                        <form method="POST" action="{{ route('admin.stalls.destroy', $stall) }}" class="inline"
                              onsubmit="return confirm('Remove this stall?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-md bg-red-600 px-2.5 py-1 text-xs text-white hover:bg-red-700">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-2 py-3 text-gray-500">No stalls found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($stalls->hasPages())
    <div class="mt-4 flex items-center justify-end gap-3 text-sm">
        @unless ($stalls->onFirstPage())
            <a data-page href="{{ $stalls->previousPageUrl() }}" class="text-blue-600 hover:underline">&laquo; Previous</a>
        @endunless
        <span>Page {{ $stalls->currentPage() }} of {{ $stalls->lastPage() }}</span>
        @if ($stalls->hasMorePages())
            <a data-page href="{{ $stalls->nextPageUrl() }}" class="text-blue-600 hover:underline">Next &raquo;</a>
        @endif
    </div>
@endif