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

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-xl bg-white p-5 shadow-sm"><strong>Module 1</strong><p class="text-sm text-gray-500">Placeholder card.</p></div>
        <div class="rounded-xl bg-white p-5 shadow-sm"><strong>Module 2</strong><p class="text-sm text-gray-500">Placeholder card.</p></div>
        <div class="rounded-xl bg-white p-5 shadow-sm"><strong>Module 3</strong><p class="text-sm text-gray-500">Placeholder card.</p></div>
    </div>
@endsection