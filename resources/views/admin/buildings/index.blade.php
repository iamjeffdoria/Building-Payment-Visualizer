@extends('layouts.app')

@section('title', 'Buildings')
@section('active', 'buildings')

@section('content')
    <h1 class="mb-5 text-2xl font-semibold">Buildings</h1>

    <div class="rounded-xl bg-white p-5 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-xs uppercase text-gray-500">
                        <th class="px-2 py-2.5">Building</th>
                        <th class="px-2 py-2.5">Stalls</th>
                        <th class="px-2 py-2.5">Paid</th>
                        <th class="px-2 py-2.5">Default rent</th>
                        <th class="w-32 px-2 py-2.5">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($buildings as $building)
                        <tr class="border-b border-gray-100">
                            <td class="px-2 py-2.5 font-medium">{{ $building->name }}</td>
                            <td class="px-2 py-2.5">{{ $building->stalls_count }}</td>
                            <td class="px-2 py-2.5">{{ $building->paid_count }} / {{ $building->stalls_count }}</td>
                            <td class="px-2 py-2.5">₱{{ number_format($building->rent, 2) }}</td>
                            <td class="px-2 py-2.5">
                                <a href="{{ route('admin.buildings.show', $building) }}"
                                   class="rounded-md bg-blue-600 px-2.5 py-1 text-xs text-white hover:bg-blue-700">Manage stalls</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-2 py-3 text-gray-500">No buildings yet. Run <code>php artisan db:seed --class=BuildingSeeder</code>.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection