@extends('layouts.app')

@section('title', 'Dashboard')
@section('active', 'dashboard')

@section('content')
    <h1 class="mb-5 text-2xl font-semibold">Dashboard</h1>

    <div class="mb-5 rounded-xl bg-white p-5 shadow-sm">
        <strong>Hello, {{ $user->name }}.</strong>
        <p class="mt-1.5 text-sm text-gray-500">
            You are signed in as
            <span class="rounded-full px-2 py-0.5 text-xs {{ $user->role === 'superadmin' ? 'bg-blue-100 text-blue-800' : 'bg-gray-200' }}">{{ $user->role }}</span>
            &middot; {{ now()->format('l, F j, Y') }}
        </p>
    </div>

    <div class="grid grid-cols-1 gap-4 2xl:grid-cols-[minmax(0,1fr)_22rem]">
        {{-- Map --}}
        <div class="rounded-xl bg-white p-5 shadow-sm">
            <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                <div>
                    <strong>LGU Palompon Market Map</strong>
                    <p class="text-sm text-gray-500">Hover a building to see its stalls. Click to pin it.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3 text-xs text-gray-600">
                    <span class="flex items-center gap-1.5"><span class="size-3 rounded-sm bg-green-300"></span>Mostly paid</span>
                    <span class="flex items-center gap-1.5"><span class="size-3 rounded-sm bg-amber-300"></span>Partial</span>
                    <span class="flex items-center gap-1.5"><span class="size-3 rounded-sm bg-red-300"></span>Mostly unpaid</span>
                </div>
            </div>

            <div class="overflow-x-auto rounded-lg border border-gray-200 bg-gray-50">
            <svg id="market-map" viewBox="10 0 960 415" class="min-w-[760px] w-full select-none" data-buildings="{{ $buildingData->toJson() }}"></svg>
            </div>
        </div>

        {{-- Details panel (HTML is rendered by the server) --}}
        <div class="rounded-xl bg-white p-5 shadow-sm">
            <div id="map-panel"></div>
        </div>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/pages/dashboard.js'])
@endpush