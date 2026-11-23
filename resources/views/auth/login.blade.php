<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - {{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @keyframes fade-in-up { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fade-in-up .45s ease-out both; }
        @media (prefers-reduced-motion: reduce) { .animate-fade-in { animation: none; } }
    </style>
</head>
<body class="bg-gray-50 font-sans text-gray-800 antialiased">
<div aria-hidden="true" class="pointer-events-none fixed inset-0 -z-10 overflow-hidden bg-linear-to-br from-blue-50 via-gray-50 to-indigo-50">
    <div class="absolute -left-24 -top-24 size-72 rounded-full bg-blue-200/40 blur-3xl"></div>
    <div class="absolute -bottom-24 -right-24 size-80 rounded-full bg-indigo-200/40 blur-3xl"></div>
</div>
<main class="relative flex min-h-screen items-center justify-center px-4 py-10">
    <div class="animate-fade-in w-full max-w-sm">

        {{-- Brand --}}
        <div class="mb-6 flex flex-col items-center text-center">
            <span class="inline-flex size-12 items-center justify-center rounded-xl bg-blue-600 text-lg font-bold text-white shadow-lg shadow-blue-600/30">
                {{ strtoupper(substr(config('app.name'), 0, 1)) }}
            </span>
            <h1 class="mt-4 text-2xl font-semibold text-gray-900">Welcome back</h1>
            <p class="mt-1 text-sm text-gray-500">Sign in to {{ config('app.name') }}</p>
        </div>

        <div class="rounded-2xl border border-gray-200/80 bg-white/90 p-7 shadow-xl shadow-gray-900/5 backdrop-blur">

            @if ($errors->any())
                <div role="alert" class="mb-4 rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="login-form" class="space-y-4">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email" name="email"
                           value="{{ old('email', request()->cookie('remembered_email')) }}" required autofocus
                           autocomplete="email"
                           class="w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm outline-none transition focus:ring-4 {{ $errors->has('email') ? 'border-red-300 focus:border-red-500 focus:ring-red-500/15' : 'border-gray-300 focus:border-blue-600 focus:ring-blue-600/15' }}">
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="mb-1 block text-sm font-medium text-gray-700">Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required
                               autocomplete="current-password"
                               class="w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-3.5 pr-10 text-sm outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-600/15">
                        <button type="button" id="toggle-password" aria-label="Show password"
                                class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 transition hover:text-gray-600 focus:outline-none focus-visible:text-blue-600">
                            <svg id="eye-open" class="size-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/>
                                <path fill-rule="evenodd" d="M.66 10.37a1 1 0 0 1 0-.74C1.9 6.4 5.6 3.5 10 3.5s8.1 2.9 9.34 6.13a1 1 0 0 1 0 .74C18.1 13.6 14.4 16.5 10 16.5S1.9 13.6.66 10.37ZM14 10a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" clip-rule="evenodd"/>
                            </svg>
                            <svg id="eye-closed" class="hidden size-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3.28 2.22a.75.75 0 0 0-1.06 1.06l14.5 14.5a.75.75 0 1 0 1.06-1.06l-1.75-1.75a10.03 10.03 0 0 0 2.31-3.57 1 1 0 0 0 0-.74C18.1 6.4 14.4 3.5 10 3.5a9.7 9.7 0 0 0-3.4.6L3.28 2.22ZM7.8 6.74l1.2 1.2a2.5 2.5 0 0 1 3.06 3.06l1.2 1.2A4 4 0 0 0 7.8 6.74Z" clip-rule="evenodd"/>
                                <path d="M10.75 16.45 8.2 13.9A4 4 0 0 1 6.1 11.8L3.6 9.3a10.03 10.03 0 0 0-2.94 1.07 1 1 0 0 0 0 .74C1.9 14.3 5.6 17.2 10 17.2c.25 0 .5-.01.75-.03Z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Remember --}}
                <label for="remember" class="flex cursor-pointer select-none items-center gap-2 text-sm text-gray-600">
                    <input type="checkbox" id="remember" name="remember" value="1"
                           class="size-4 cursor-pointer rounded border-gray-300 accent-blue-600">
                    Keep me signed in
                </label>

                {{-- Submit --}}
                <button type="submit" id="submit-btn"
                        class="group flex w-full items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-md shadow-blue-600/25 transition hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-600/30 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600/40 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-70">
                    <svg id="spinner" class="hidden size-4 animate-spin" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"/>
                        <path d="M4 12a8 8 0 0 1 8-8" stroke="currentColor" stroke-width="4" stroke-linecap="round" class="opacity-90"/>
                    </svg>
                    <span id="submit-label">Sign in</span>
                    <svg id="submit-arrow" class="size-4 transition-transform group-hover:translate-x-0.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.638L10.23 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.158-3.96H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </form>
        </div>

        <p class="mt-6 text-center text-xs text-gray-400">
            &copy; {{ date('Y') }} {{ config('app.name') }} &middot; Authorized personnel only
        </p>
    </div>
</main>

@vite(['resources/js/pages/login.js'])
</body>
</html>