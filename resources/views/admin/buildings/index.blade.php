@extends('layouts.app')

@section('title', 'Buildings')
@section('active', 'buildings')

@section('content')
    <h1 class="mb-5 text-2xl font-semibold">Buildings</h1>

    <div class="rounded-xl bg-white p-5 shadow-sm">
        <div id="building-results">
            @include('admin.buildings.partials.building-results')
        </div>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/pages/buildings-index.js'])
@endpush