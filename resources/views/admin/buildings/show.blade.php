@extends('layouts.app')

@section('title', $building->name)
@section('active', 'buildings')

@php
    $badges = [
        'paid' => 'bg-green-100 text-green-800',
        'unpaid' => 'bg-amber-100 text-amber-800',
        'overdue' => 'bg-red-100 text-red-800',
    ];
@endphp

@section('content')
    <div class="mb-5">
        <a href="{{ route('admin.buildings') }}" class="text-sm text-blue-600 hover:underline">&laquo; All buildings</a>
        <h1 class="mt-1 text-2xl font-semibold">{{ $building->name }}</h1>
        <p class="text-sm text-gray-500">{{ $building->stalls->count() }} stalls &middot; default rent ₱{{ number_format($building->rent, 2) }}</p>
    </div>

    {{-- Add stall --}}
    <div class="mb-5 rounded-xl bg-white p-5 shadow-sm">
        <strong>Add a stall</strong>
        <form method="POST" action="{{ route('admin.stalls.store', $building) }}"
              class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-5">
            @csrf
            <div class="lg:col-span-2">
                <label class="mb-1 block text-sm font-medium">Person name</label>
                <input type="text" name="tenant_name" value="{{ old('tenant_name') }}" required
                       class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">Stall no. <span class="text-xs text-gray-500">(auto)</span></label>
                <input type="text" name="stall_no" value="{{ old('stall_no') }}" placeholder="{{ $building->short }}-01"
                       class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">Amount</label>
                <input type="number" step="0.01" min="0" name="amount" value="{{ old('amount') }}" placeholder="{{ $building->rent }}"
                       class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">Status</label>
                <select name="status" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm">
                    <option value="unpaid">Unpaid</option>
                    <option value="paid">Paid</option>
                    <option value="overdue">Overdue</option>
                </select>
            </div>
            <div class="sm:col-span-2 lg:col-span-5">
                <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700">+ Add stall</button>
            </div>
        </form>
    </div>

    {{-- Stall list --}}
    <div class="rounded-xl bg-white p-5 shadow-sm">
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
                    @forelse ($building->stalls as $stall)
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
                        <tr><td colspan="5" class="px-2 py-3 text-gray-500">No stalls yet. Add the first one above.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Edit dialog --}}
    <dialog id="edit-stall-dialog" class="m-auto w-full max-w-md rounded-xl p-6 shadow-2xl backdrop:bg-black/50">
        <h2 class="mb-2 text-lg font-semibold">Edit stall</h2>
        <form method="POST" id="edit-stall-form" action="" data-base="{{ url('/admin/stalls') }}" class="space-y-3">
            @csrf
            @method('PUT')
            <div>
                <label class="mb-1 block text-sm font-medium">Person name</label>
                <input type="text" name="tenant_name" id="es-name" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">Stall no.</label>
                <input type="text" name="stall_no" id="es-no" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">Amount</label>
                <input type="number" step="0.01" min="0" name="amount" id="es-amount" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">Status</label>
                <select name="status" id="es-status" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm">
                    <option value="unpaid">Unpaid</option>
                    <option value="paid">Paid</option>
                    <option value="overdue">Overdue</option>
                </select>
            </div>
            <div class="flex justify-end gap-2 pt-3">
                <button type="button" data-close class="rounded-md bg-gray-200 px-4 py-2 text-sm hover:bg-gray-300">Cancel</button>
                <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700">Save</button>
            </div>
        </form>
    </dialog>
@endsection

@push('scripts')
<script>
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-edit]');
        if (!btn) return;
        var form = document.getElementById('edit-stall-form');
        form.action = form.dataset.base + '/' + btn.dataset.id;
        document.getElementById('es-name').value = btn.dataset.name;
        document.getElementById('es-no').value = btn.dataset.stallNo;
        document.getElementById('es-amount').value = btn.dataset.amount;
        document.getElementById('es-status').value = btn.dataset.status;
        document.getElementById('edit-stall-dialog').showModal();
    });
</script>
@endpush