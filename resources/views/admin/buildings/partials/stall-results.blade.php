<div class="max-h-[65vh] overflow-auto rounded-lg border border-gray-200 [scrollbar-color:#cbd5e1_transparent] [scrollbar-width:thin]">
    <table class="w-full min-w-[860px] text-left text-sm">
        <thead class="sticky top-0 z-10 bg-white shadow-[0_1px_0_0_#e5e7eb]">
            <tr class="text-xs uppercase text-gray-500">
                <th class="w-40 px-2 pb-1 pt-2.5">Actions</th>
                <th class="px-2 pb-1 pt-2.5">Stall</th>
                <th class="px-2 pb-1 pt-2.5">Person</th>
                <th class="px-2 pb-1 pt-2.5">Paid / Due</th>
                <th class="px-2 pb-1 pt-2.5">Due date</th>
                <th class="px-2 pb-1 pt-2.5">Status</th>
            </tr>
            <tr id="stall-filters" data-url="{{ route('admin.buildings.show', $building) }}" class="border-b border-gray-200">
                <th class="px-2 pb-2.5"></th>
                <th class="px-2 pb-2.5 font-normal">
                    <input type="text" name="stall_no" value="{{ request('stall_no') }}" placeholder="Search stall"
                           aria-label="Search by stall number"
                           class="w-full rounded-md border border-gray-300 bg-white px-2.5 py-2 text-sm text-gray-800">
                </th>
                <th class="px-2 pb-2.5 font-normal">
                    <input type="text" name="tenant" value="{{ request('tenant') }}" placeholder="Search person"
                           aria-label="Search by person name"
                           class="w-full rounded-md border border-gray-300 bg-white px-2.5 py-2 text-sm text-gray-800">
                </th>
                <th class="px-2 pb-2.5 font-normal">
                    <input type="text" name="amount" value="{{ request('amount') }}" placeholder="Search amount"
                           aria-label="Search by amount" inputmode="decimal"
                           class="w-full rounded-md border border-gray-300 bg-white px-2.5 py-2 text-sm text-gray-800">
                </th>
                <th class="px-2 pb-2.5 font-normal">
                    <input type="date" name="due_date" value="{{ request('due_date') }}"
                           aria-label="Filter by due date"
                           class="w-full rounded-md border border-gray-300 bg-white px-2.5 py-2 text-sm text-gray-800">
                </th>
                <th class="px-2 pb-2.5 font-normal">
                    <select name="status" aria-label="Filter by status"
                            class="w-full rounded-md border border-gray-300 bg-white px-2.5 py-2 text-sm text-gray-800">
                        <option value="">All</option>
                        <option value="paid" @selected(request('status') === 'paid')>Paid</option>
                        <option value="partial" @selected(request('status') === 'partial')>Partial</option>
                        <option value="unpaid" @selected(request('status') === 'unpaid')>Unpaid</option>
                        <option value="overdue" @selected(request('status') === 'overdue')>Overdue</option>
                    </select>
                </th>
            </tr>
        </thead>
        <tbody id="stall-body">
            @forelse ($stalls as $stall)
                @php($status = $stall->status)
                <tr class="border-b border-gray-100">
                    <td class="px-2 py-2.5">
                        <div class="inline-flex divide-x divide-white/50 overflow-hidden rounded-lg shadow-sm">
                            <a href="{{ route('admin.stalls.show', $stall) }}" title="Payments" aria-label="Payments"
                               class="inline-flex size-10 items-center justify-center bg-green-600 text-white hover:bg-green-700">
                                <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </a>

                            <button type="button" data-edit title="Edit stall" aria-label="Edit stall"
                                    data-id="{{ $stall->id }}"
                                    data-stall-no="{{ $stall->stall_no }}"
                                    data-name="{{ $stall->tenant_name }}"
                                    data-amount="{{ $stall->amount }}"
                                    data-due="{{ $stall->due_date?->format('Y-m-d') }}"
                                    class="inline-flex size-10 items-center justify-center bg-amber-500 text-white hover:bg-amber-600">
                                <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>

                            <form method="POST" action="{{ route('admin.stalls.destroy', $stall) }}" class="flex"
                                  data-confirm="Remove this stall and its payment history?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Delete stall" aria-label="Delete stall"
                                        class="inline-flex size-10 items-center justify-center bg-red-600 text-white hover:bg-red-700">
                                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                    <td class="px-2 py-2.5 font-medium">{{ $stall->stall_no }}</td>
                    <td class="px-2 py-2.5">{{ $stall->tenant_name }}</td>
                    <td class="px-2 py-2.5">
                        <div>₱{{ number_format($stall->amount_paid, 2) }} <span class="text-gray-400">/ ₱{{ number_format($stall->amount, 2) }}</span></div>
                        <div class="mt-1 h-1.5 w-32 overflow-hidden rounded-full bg-gray-200">
                            <div class="h-full bg-green-500" style="width: {{ $stall->percent_paid }}%"></div>
                        </div>
                    </td>
                    <td class="px-2 py-2.5 {{ $status === 'overdue' ? 'font-medium text-red-600' : '' }}">
                        {{ $stall->due_date?->format('M j, Y') ?? '—' }}
                    </td>
                    <td class="px-2 py-2.5">
                        <span class="rounded-full px-2 py-0.5 text-xs {{ \App\Models\Stall::STATUS_BADGES[$status] }}">{{ ucfirst($status) }}</span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-2 py-3 text-gray-500">No stalls found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div id="stall-pager">
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
</div>