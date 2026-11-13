@extends('layouts.app')

@section('title', 'Buildings')
@section('active', 'buildings')

@section('content')
    <div class="rounded-xl bg-white p-4 shadow-sm">
        <div id="building-results">
            @include('admin.buildings.partials.building-results')
        </div>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/pages/buildings-index.js'])
@endpush