<!-- Mobile open/close state -->
<input type="checkbox" id="sidebar-toggle" class="peer/mobile hidden" />

<!-- Floating hamburger button (mobile only) -->
<label for="sidebar-toggle" aria-label="Open menu"
    class="fixed top-4 left-4 z-50 cursor-pointer rounded-lg bg-gray-900 p-2.5 text-white shadow-lg peer-checked/mobile:hidden lg:hidden">
    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
    </svg>
</label>

<!-- Overlay (mobile only, shown when open) -->
<label for="sidebar-toggle" aria-label="Close menu"
    class="fixed inset-0 z-30 hidden bg-black/50 peer-checked/mobile:block lg:hidden"></label>

<!-- Sidebar -->
<aside class="group/collapse fixed top-0 left-0 z-40 flex h-dvh w-60 -translate-x-full flex-col overflow-hidden bg-gray-900 text-gray-200
    transition-all duration-300 ease-in-out
    peer-checked/mobile:translate-x-0 lg:translate-x-0 lg:has-[#sidebar-collapse:checked]:w-16">

    <!-- Desktop collapse state lives inside the sidebar -->
    <input type="checkbox" id="sidebar-collapse" class="hidden" />

    {{-- Header --}}
    <div class="flex items-center justify-between border-b border-white/10 px-4 py-4 lg:group-has-[#sidebar-collapse:checked]/collapse:justify-center lg:group-has-[#sidebar-collapse:checked]/collapse:px-2">
        <div class="flex min-w-0 items-center gap-2.5 lg:group-has-[#sidebar-collapse:checked]/collapse:hidden">
            <span class="inline-flex size-9 shrink-0 items-center justify-center rounded-lg bg-blue-600 text-sm font-bold text-white">
                {{ strtoupper(substr(config('app.name'), 0, 1)) }}
            </span>
            <span class="truncate text-base font-bold text-white">{{ config('app.name') }}</span>
        </div>

        <!-- Mobile close button -->
        <label for="sidebar-toggle" aria-label="Close menu" class="cursor-pointer p-1 text-gray-400 hover:text-white lg:hidden">
            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </label>

        <!-- Desktop collapse toggle -->
        <label for="sidebar-collapse" title="Collapse / expand" aria-label="Collapse or expand sidebar"
            class="hidden cursor-pointer p-1 text-gray-400 hover:text-white lg:flex">
            <svg class="size-5 transition-transform duration-300 lg:group-has-[#sidebar-collapse:checked]/collapse:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </label>
    </div>

    {{-- Navigation --}}
    <nav class="flex flex-1 flex-col gap-1 overflow-y-auto overflow-x-hidden px-3 py-5">
        <a href="{{ route('admin.dashboard') }}" title="Dashboard"
            class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors lg:group-has-[#sidebar-collapse:checked]/collapse:justify-center lg:group-has-[#sidebar-collapse:checked]/collapse:px-0
            {{ $active === 'dashboard' ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <svg class="size-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span class="truncate lg:group-has-[#sidebar-collapse:checked]/collapse:hidden">Dashboard</span>
        </a>

        <a href="{{ route('admin.buildings') }}" title="Buildings"
            class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors lg:group-has-[#sidebar-collapse:checked]/collapse:justify-center lg:group-has-[#sidebar-collapse:checked]/collapse:px-0
            {{ $active === 'buildings' ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <svg class="size-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <span class="truncate lg:group-has-[#sidebar-collapse:checked]/collapse:hidden">Buildings</span>
        </a>

        @if (auth()->user()->isSuperadmin())
            <a href="{{ route('admin.users') }}" title="Admin Users"
                class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors lg:group-has-[#sidebar-collapse:checked]/collapse:justify-center lg:group-has-[#sidebar-collapse:checked]/collapse:px-0
                {{ $active === 'users' ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                <svg class="size-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="truncate lg:group-has-[#sidebar-collapse:checked]/collapse:hidden">Admin Users</span>
            </a>
        @endif
    </nav>

    {{-- User / actions --}}
    <div class="border-t border-white/10 p-3 text-sm">
        <div class="mb-3 flex items-center gap-2.5 lg:group-has-[#sidebar-collapse:checked]/collapse:justify-center">
            @if (auth()->user()->photo_url)
                <img src="{{ auth()->user()->photo_url }}" alt="" class="size-9 shrink-0 rounded-full object-cover">
            @else
                <span class="inline-flex size-9 shrink-0 items-center justify-center rounded-full bg-gray-700 font-semibold text-white">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </span>
            @endif
            <div class="min-w-0 lg:group-has-[#sidebar-collapse:checked]/collapse:hidden">
                <div class="truncate">{{ auth()->user()->name }}</div>
                <small class="text-gray-400">{{ auth()->user()->role }}</small>
            </div>
        </div>

        <button type="button" data-open="profile-dialog" title="Edit profile"
            class="mb-2 flex w-full items-center gap-2.5 rounded-md bg-gray-800 px-3 py-2 text-left hover:bg-gray-700 lg:group-has-[#sidebar-collapse:checked]/collapse:justify-center lg:group-has-[#sidebar-collapse:checked]/collapse:px-0">
            <svg class="size-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            <span class="truncate lg:group-has-[#sidebar-collapse:checked]/collapse:hidden">Edit profile</span>
        </button>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" title="Log out"
                class="flex w-full items-center gap-2.5 rounded-md bg-gray-800 px-3 py-2 text-left hover:bg-gray-700 lg:group-has-[#sidebar-collapse:checked]/collapse:justify-center lg:group-has-[#sidebar-collapse:checked]/collapse:px-0">
                <svg class="size-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span class="truncate lg:group-has-[#sidebar-collapse:checked]/collapse:hidden">Log out</span>
            </button>
        </form>
    </div>
</aside>

<script>
    // Remember the desktop collapsed/expanded state between pages.
    (function () {
        var box = document.getElementById('sidebar-collapse');
        if (!box) return;
        try { box.checked = localStorage.getItem('sidebar-collapsed') === '1'; } catch (e) {}
        box.addEventListener('change', function () {
            try { localStorage.setItem('sidebar-collapsed', box.checked ? '1' : '0'); } catch (e) {}
        });
    })();
</script>