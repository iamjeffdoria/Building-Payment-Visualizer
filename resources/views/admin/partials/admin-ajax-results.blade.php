<div class="overflow-x-auto">
    <table class="w-full text-left text-sm">
        <thead>
            <tr class="border-b border-gray-200 text-xs uppercase text-gray-500">
                <th class="px-2 py-2.5">Name</th>
                <th class="px-2 py-2.5">Email</th>
                <th class="px-2 py-2.5">Role</th>
                <th class="w-40 px-2 py-2.5">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($admins as $admin)
                <tr class="border-b border-gray-100">
                    <td class="px-2 py-2.5">
                        <div class="flex items-center gap-2.5">
                            @if ($admin->photo_url)
                                <img src="{{ $admin->photo_url }}" alt="" class="size-7 rounded-full object-cover">
                            @else
                                <span class="inline-flex size-7 items-center justify-center rounded-full bg-gray-700 text-xs font-semibold text-white">
                                    {{ strtoupper(substr($admin->name, 0, 1)) }}
                                </span>
                            @endif
                            {{ $admin->name }}
                        </div>
                    </td>
                    <td class="px-2 py-2.5">{{ $admin->email }}</td>
                    <td class="px-2 py-2.5">
                        <span class="rounded-full px-2 py-0.5 text-xs {{ $admin->role === 'superadmin' ? 'bg-blue-100 text-blue-800' : 'bg-gray-200' }}">
                            {{ $admin->role }}
                        </span>
                    </td>
                    <td class="px-2 py-2.5">
                        <button type="button" data-edit
                                data-id="{{ $admin->id }}"
                                data-name="{{ $admin->name }}"
                                data-email="{{ $admin->email }}"
                                data-role="{{ $admin->role }}"
                                class="rounded-md bg-blue-600 px-2.5 py-1 text-xs text-white hover:bg-blue-700">Edit</button>

                        @if ($admin->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.destroy', $admin) }}"
                                  class="inline"
                                  onsubmit="return confirm('Remove this admin account?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-md bg-red-600 px-2.5 py-1 text-xs text-white hover:bg-red-700">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-2 py-3 text-gray-500">No admins found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($admins->hasPages())
    <div class="mt-4 flex items-center justify-end gap-3 text-sm">
        @unless ($admins->onFirstPage())
            <a data-page href="{{ $admins->previousPageUrl() }}" class="text-blue-600 hover:underline">&laquo; Previous</a>
        @endunless
        <span>Page {{ $admins->currentPage() }} of {{ $admins->lastPage() }}</span>
        @if ($admins->hasMorePages())
            <a data-page href="{{ $admins->nextPageUrl() }}" class="text-blue-600 hover:underline">Next &raquo;</a>
        @endif
    </div>
@endif