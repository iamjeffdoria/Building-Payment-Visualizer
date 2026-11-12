<div class="overflow-x-auto">
    <table class="w-full text-left text-sm">
        <thead>
            <tr class="border-b border-gray-200 text-xs uppercase text-gray-500">
                <th class="px-2 py-2.5">Building</th>
                <th class="px-2 py-2.5">Stalls</th>
                <th class="px-2 py-2.5">Fully paid</th>
                <th class="px-2 py-2.5">Collected</th>
                <th class="px-2 py-2.5">Default rent</th>
                <th class="w-32 px-2 py-2.5">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($buildings as $building)
                @php
                    $collected = (float) ($building->collected ?? 0);
                    $expected = (float) ($building->expected ?? 0);
                    $pct = $expected > 0 ? (int) round($collected / $expected * 100) : 0;
                @endphp
                <tr class="border-b border-gray-100">
                    <td class="px-2 py-2.5 font-medium">{{ $building->name }}</td>
                    <td class="px-2 py-2.5">{{ $building->stalls_count }}</td>
                    <td class="px-2 py-2.5">{{ $building->paid_count }} / {{ $building->stalls_count }}</td>
                    <td class="px-2 py-2.5">
                        <div>₱{{ number_format($collected, 2) }} <span class="text-gray-400">/ ₱{{ number_format($expected, 2) }}</span></div>
                        <div class="mt-1 h-1.5 w-32 overflow-hidden rounded-full bg-gray-200">
                            <div class="h-full bg-green-500" style="width: {{ $pct }}%"></div>
                        </div>
                    </td>
                    <td class="px-2 py-2.5">₱{{ number_format($building->rent, 2) }}</td>
                    <td class="px-2 py-2.5">
                        <a href="{{ route('admin.buildings.show', $building) }}"
                           class="rounded-md bg-blue-600 px-2.5 py-1 text-xs text-white hover:bg-blue-700">Manage stalls</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-2 py-3 text-gray-500">No buildings found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>