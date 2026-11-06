<aside class="flex w-60 shrink-0 flex-col bg-gray-900 px-4 py-5 text-gray-200">
    <div class="mb-6 text-base font-bold text-white">{{ config('app.name') }}</div>

    <nav class="flex flex-1 flex-col gap-1">
        <a href="{{ route('admin.dashboard') }}"
           class="rounded-md px-3 py-2.5 text-sm {{ $active === 'dashboard' ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            Dashboard
        </a>

        @if (auth()->user()->isSuperadmin())
            <a href="{{ route('admin.users') }}"
               class="rounded-md px-3 py-2.5 text-sm {{ $active === 'users' ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                Admin Users
            </a>
        @endif
    </nav>

    <div class="border-t border-gray-700 pt-4 text-sm">
        <div class="mb-3 flex items-center gap-2.5">
            @if (auth()->user()->photo_url)
                <img src="{{ auth()->user()->photo_url }}" alt="" class="size-9 shrink-0 rounded-full object-cover">
            @else
                <span class="inline-flex size-9 shrink-0 items-center justify-center rounded-full bg-gray-700 font-semibold text-white">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </span>
            @endif
            <div class="min-w-0">
                <div class="truncate">{{ auth()->user()->name }}</div>
                <small class="text-gray-400">{{ auth()->user()->role }}</small>
            </div>
        </div>

        <button type="button" data-open="profile-dialog"
                class="mb-2 w-full rounded-md bg-gray-800 px-3 py-2 text-left hover:bg-gray-700">
            Edit profile
        </button>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full rounded-md bg-gray-800 px-3 py-2 text-left hover:bg-gray-700">
                Log out
            </button>
        </form>
    </div>
</aside>