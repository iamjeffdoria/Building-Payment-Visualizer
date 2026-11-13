@extends('layouts.app')

@section('title', $building->name)
@section('active', 'buildings')

@section('content')
    <div class="mb-5">
        <a href="{{ route('admin.buildings') }}" class="text-sm text-blue-600 hover:underline">&laquo; All buildings</a>
        <h1 class="mt-1 text-2xl font-semibold">{{ $building->name }}</h1>
        <p class="text-sm text-gray-500">
            {{ $building->stalls_count }} stalls
            &middot; Collected ₱{{ number_format($building->collected ?? 0, 2) }} of ₱{{ number_format($building->expected ?? 0, 2) }}
            &middot; default rent ₱{{ number_format($building->rent, 2) }}
        </p>
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
                <label class="mb-1 block text-sm font-medium">Amount due</label>
                <input type="number" step="0.01" min="0" name="amount" value="{{ old('amount') }}" placeholder="{{ $building->rent }}"
                       class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">Due date <span class="text-xs text-gray-500">(optional)</span></label>
                <input type="date" name="due_date" value="{{ old('due_date') }}"
                       class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div class="sm:col-span-2 lg:col-span-5">
                <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700">+ Add stall</button>
            </div>
        </form>
    </div>

    {{-- Stall list --}}
    <div class="rounded-xl bg-white p-5 shadow-sm">
        <div id="stall-results">
            @include('admin.buildings.partials.stall-results')
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
                <label class="mb-1 block text-sm font-medium">Amount due</label>
                <input type="number" step="0.01" min="0" name="amount" id="es-amount" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">Due date <span class="text-xs text-gray-500">(optional)</span></label>
                <input type="date" name="due_date" id="es-due" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div class="flex justify-end gap-2 pt-3">
                <button type="button" data-close class="rounded-md bg-gray-200 px-4 py-2 text-sm hover:bg-gray-300">Cancel</button>
                <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700">Save</button>
            </div>
        </form>
    </dialog>
@endsection

@push('scripts')
    @vite(['resources/js/pages/buildings-show.js'])
@endpush