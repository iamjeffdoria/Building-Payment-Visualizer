@props(['title' => ''])

<header class="sticky top-0 z-20 border-b border-gray-200 bg-white">
    <div class="flex h-16 items-center justify-between gap-3 pl-16 pr-4 sm:pr-7 lg:pl-7">
        {{-- pl-16 on mobile leaves room for the floating hamburger button --}}
        <h1 class="min-w-0 truncate text-base font-semibold text-gray-900 sm:text-lg">{{ $title }}</h1>

        <div class="relative shrink-0">
            <button type="button" id="topbar-profile-btn" aria-haspopup="true" aria-expanded="false"
                class="flex items-center gap-2.5 rounded-full p-1 transition-colors hover:bg-gray-100 sm:pr-3">
                @if (auth()->user()->photo_url)
                    <img src="{{ auth()->user()->photo_url }}" alt="" class="size-9 shrink-0 rounded-full border border-gray-200 object-cover">
                @else
                    <span class="inline-flex size-9 shrink-0 items-center justify-center rounded-full bg-blue-600 text-sm font-semibold text-white">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>
                @endif
                <span class="hidden max-w-40 truncate text-sm font-medium text-gray-900 sm:block">{{ auth()->user()->name }}</span>
                <svg class="hidden size-4 text-gray-400 sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div id="topbar-profile-menu"
                class="absolute right-0 z-30 mt-2 hidden w-64 max-w-[calc(100vw-2rem)] overflow-hidden rounded-xl border border-gray-200 bg-white py-1 shadow-lg">
                <div class="flex items-center gap-3 border-b border-gray-100 px-4 py-3">
                    @if (auth()->user()->photo_url)
                        <img src="{{ auth()->user()->photo_url }}" alt="" class="size-10 shrink-0 rounded-full border border-gray-200 object-cover">
                    @else
                        <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-full bg-blue-600 font-semibold text-white">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </span>
                    @endif
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                        <p class="truncate text-xs text-gray-500">{{ auth()->user()->email }}</p>
                        <span class="mt-1 inline-block rounded-full px-2 py-0.5 text-[10px] font-semibold {{ auth()->user()->role === 'superadmin' ? 'bg-blue-100 text-blue-800' : 'bg-gray-200 text-gray-700' }}">
                            {{ auth()->user()->role }}
                        </span>
                    </div>
                </div>

                <button type="button" data-open="profile-dialog"
                    class="flex w-full items-center gap-2 px-4 py-2.5 text-left text-sm text-gray-700 hover:bg-gray-50">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit profile
                </button>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex w-full items-center gap-2 px-4 py-2.5 text-left text-sm text-red-600 hover:bg-red-50">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Log out
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>