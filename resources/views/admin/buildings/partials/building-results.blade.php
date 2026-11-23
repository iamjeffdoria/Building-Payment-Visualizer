<div class="rounded-lg border border-gray-200"
     style="max-height: calc(100dvh - 10rem); min-height: 15rem; overflow: auto; scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent;">
    <style>
        .tbl-sticky { border-collapse: separate; border-spacing: 0; }
        .tbl-sticky thead { position: sticky; top: 0; z-index: 10; }
        .tbl-sticky thead tr:first-child th { background: #f9fafb; border-bottom: 1px solid #e5e7eb; }
        .tbl-sticky thead tr:last-child th { background: #ffffff; border-bottom: 1px solid #e5e7eb; }
        .tbl-sticky tbody td { border-bottom: 1px solid #f3f4f6; }
    </style>
    <table class="tbl-sticky w-full min-w-[860px] text-left text-sm">
        <thead>
            <tr class="border-b border-gray-200 bg-gray-50 text-xs uppercase text-gray-500">
                <th class="w-32 px-2 py-2.5">Actions</th>
                <th class="px-2 py-2.5">Building</th>
                <th class="px-2 py-2.5">Stalls</th>
                <th class="px-2 py-2.5">Fully paid</th>
                <th class="px-2 py-2.5">Collected</th>
                <th class="px-2 py-2.5">Collectible</th>
                <th class="px-2 py-2.5">Default rent</th>
            </tr>
            <tr id="building-filters" data-url="{{ route('admin.buildings') }}" class="border-b border-gray-200">
                <th class="px-2 py-2.5"></th>
                <th class="px-2 py-2.5 font-normal">
                    <input type="text" name="name" value="{{ request('name') }}"
                           aria-label="Search by building name"
                           class="w-full rounded-md border border-gray-300 bg-white px-2.5 py-2 text-sm text-gray-800">
                </th>
                <th class="px-2 py-2.5 font-normal">
                    <input type="text" name="stalls" value="{{ request('stalls') }}" 
                           aria-label="Search by number of stalls" inputmode="numeric"
                           class="w-full rounded-md border border-gray-300 bg-white px-2.5 py-2 text-sm text-gray-800">
                </th>
                <th class="px-2 py-2.5 font-normal">
                    <input type="text" name="paid" value="{{ request('paid') }}"
                           aria-label="Search by number of fully paid stalls" inputmode="numeric"
                           class="w-full rounded-md border border-gray-300 bg-white px-2.5 py-2 text-sm text-gray-800">
                </th>
                <th class="px-2 py-2.5 font-normal">
                    <input type="text" name="collected" value="{{ request('collected') }}"
                           aria-label="Search by collected amount" inputmode="decimal"
                           class="w-full rounded-md border border-gray-300 bg-white px-2.5 py-2 text-sm text-gray-800">
                </th>
                <th class="px-2 py-2.5 font-normal">
                    <input type="text" name="collectible" value="{{ request('collectible') }}"
                           aria-label="Search by collectible amount" inputmode="decimal"
                           class="w-full rounded-md border border-gray-300 bg-white px-2.5 py-2 text-sm text-gray-800">
                </th>
                <th class="px-2 py-2.5 font-normal">
                    <input type="text" name="rent" value="{{ request('rent') }}"
                           aria-label="Search by default rent" inputmode="decimal"
                           class="w-full rounded-md border border-gray-300 bg-white px-2.5 py-2 text-sm text-gray-800">
                </th>
            </tr>
        </thead>
        <tbody id="building-body">
            @forelse ($buildings as $building)
                @php
                    $collected = (float) ($building->collected ?? 0);
                    $expected = (float) ($building->expected ?? 0);
                    $pct = $expected > 0 ? (int) round($collected / $expected * 100) : 0;
                    $collectible = (float) ($building->collectible ?? 0);
                @endphp
                <tr class="border-b border-gray-100">
                    <td class="px-2 py-2.5">
                        <a href="{{ route('admin.buildings.show', $building) }}"
                           class="whitespace-nowrap rounded-md bg-blue-600 px-2.5 py-1 text-xs text-white hover:bg-blue-700">Manage stalls</a>
                    </td>
                    <td class="px-2 py-2.5 font-medium">{{ $building->name }}</td>
                    <td class="px-2 py-2.5">{{ $building->stalls_count }}</td>
                    <td class="whitespace-nowrap px-2 py-2.5">{{ $building->paid_count }} / {{ $building->stalls_count }}</td>
                    <td class="px-2 py-2.5">
                        <div>₱{{ number_format($collected, 2) }} <span class="text-gray-400">/ ₱{{ number_format($expected, 2) }}</span></div>
                        <div class="mt-1 h-1.5 w-32 overflow-hidden rounded-full bg-gray-200">
                            <div class="h-full bg-green-500" style="width: {{ $pct }}%"></div>
                        </div>
                    </td>
                    <td class="whitespace-nowrap px-2 py-2.5 font-medium {{ $collectible > 0 ? 'text-amber-700' : 'text-green-700' }}">₱{{ number_format($collectible, 2) }}</td>
                    <td class="px-2 py-2.5">₱{{ number_format($building->rent, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-2 py-3 text-gray-500">No buildings found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>