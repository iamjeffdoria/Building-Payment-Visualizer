@extends('layouts.app')

@section('title', 'Buildings')
@section('active', 'buildings')

@section('content')
    <h1 class="mb-5 text-2xl font-semibold">Buildings</h1>

    <div class="rounded-xl bg-white p-5 shadow-sm">
        <div id="building-filters" class="mb-4" data-url="{{ route('admin.buildings') }}">
            <label for="bf-search" class="mb-1 block text-sm font-medium">Search</label>
            <input type="text" id="bf-search" name="search" value="{{ request('search') }}"
                   placeholder="Building name (e.g. BLDG. 2, Baywalk)"
                   class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm sm:max-w-md">
        </div>

        <div id="building-results">
            @include('admin.buildings.partials.building-results')
        </div>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/pages/buildings-index.js'])
@endpush