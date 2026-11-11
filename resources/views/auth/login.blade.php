<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - {{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @keyframes float-slow {
            0%, 100% { transform: translate3d(0, 0, 0) scale(1); }
            50%      { transform: translate3d(20px, -30px, 0) scale(1.08); }
        }
        @keyframes float-slower {
            0%, 100% { transform: translate3d(0, 0, 0) scale(1); }
            50%      { transform: translate3d(-25px, 25px, 0) scale(1.1); }
        }
        @keyframes fade-up {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-float-slow   { animation: float-slow 14s ease-in-out infinite; }
        .animate-float-slower { animation: float-slower 18s ease-in-out infinite; }
        .animate-fade-up      { animation: fade-up .6s ease-out both; }
        @media (prefers-reduced-motion: reduce) {
            .animate-float-slow, .animate-float-slower, .animate-fade-up { animation: none; }
        }
    </style>
</head>
<body class="min-h-screen bg-white font-sans text-gray-800 antialiased">
<div class="grid min-h-screen lg:grid-cols-2">

    {{-- Brand panel --}}
    <aside class="relative hidden overflow-hidden bg-linear-to-br from-blue-600 via-indigo-600 to-violet-700 lg:flex lg:flex-col lg:justify-between lg:p-12">
        <div class="animate-float-slow pointer-events-none absolute -left-24 -top-24 size-96 rounded-full bg-white/10 blur-3xl"></div>
        <div class="animate-float-slower pointer-events-none absolute -bottom-32 -right-20 size-112 rounded-full bg-cyan-300/20 blur-3xl"></div>
        <div class="pointer-events-none absolute inset-0 opacity-[0.07]"
             style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 24px 24px;"></div>

        <div class="relative flex items-center gap-3 text-white">
            <span class="inline-flex size-10 items-center justify-center rounded-xl bg-white/15 text-lg font-bold ring-1 ring-white/30 backdrop-blur">
                {{ strtoupper(substr(config('app.name'), 0, 1)) }}
            </span>
            <span class="text-lg font-semibold tracking-tight">{{ config('app.name') }}</span>
        </div>

        <div class="relative max-w-md text-white">
            <h2 class="text-4xl font-semibold leading-tight tracking-tight">
                Manage everything from one place.
            </h2>
            <p class="mt-4 text-base leading-relaxed text-white/75">
                Sign in to access your admin dashboard, manage users, and keep your workspace running smoothly.
            </p>

            <ul class="mt-8 space-y-3 text-sm text-white/90">
                <li class="flex items-center gap-3">
                    <span class="inline-flex size-6 items-center justify-center rounded-full bg-white/15 ring-1 ring-white/25">
                        <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.7 5.3a1 1 0 0 1 0 1.4l-7.5 7.5a1 1 0 0 1-1.4 0L3.3 9.7a1 1 0 1 1 1.4-1.4l3.8 3.8 6.8-6.8a1 1 0 0 1 1.4 0Z" clip-rule="evenodd"/></svg>
                    </span>
                    Role-based access control
                </li>
                <li class="flex items-center gap-3">
                    <span class="inline-flex size-6 items-center justify-center rounded-full bg-white/15 ring-1 ring-white/25">
                        <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.7 5.3a1 1 0 0 1 0 1.4l-7.5 7.5a1 1 0 0 1-1.4 0L3.3 9.7a1 1 0 1 1 1.4-1.4l3.8 3.8 6.8-6.8a1 1 0 0 1 1.4 0Z" clip-rule="evenodd"/></svg>
                    </span>
                    Secure, session-based sign in
                </li>
                <li class="flex items-center gap-3">
                    <span class="inline-flex size-6 items-center justify-center rounded-full bg-white/15 ring-1 ring-white/25">
                        <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.7 5.3a1 1 0 0 1 0 1.4l-7.5 7.5a1 1 0 0 1-1.4 0L3.3 9.7a1 1 0 1 1 1.4-1.4l3.8 3.8 6.8-6.8a1 1 0 0 1 1.4 0Z" clip-rule="evenodd"/></svg>
                    </span>
                    Built for fast daily workflows
                </li>
            </ul>
        </div>

        <p class="relative text-xs text-white/60">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </aside>

    {{-- Form panel --}}
    <main class="relative flex items-center justify-center bg-linear-to-b from-gray-50 to-white px-6 py-12 sm:px-12">
        <div class="animate-fade-up w-full max-w-sm">

            {{-- Mobile brand --}}
            <div class="mb-8 flex items-center gap-3 lg:hidden">
                <span class="inline-flex size-10 items-center justify-center rounded-xl bg-linear-to-br from-blue-600 to-indigo-600 text-lg font-bold text-white shadow-lg shadow-blue-600/30">
                    {{ strtoupper(substr(config('app.name'), 0, 1)) }}
                </span>
                <span class="text-lg font-semibold tracking-tight">{{ config('app.name') }}</span>
            </div>

            <h1 class="text-3xl font-semibold tracking-tight text-gray-900">Welcome back</h1>
            <p class="mt-2 text-sm text-gray-500">Enter your credentials to access your account.</p>

            @if ($errors->any())
                <div role="alert" class="mt-6 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <svg class="mt-0.5 size-4 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-4a1 1 0 0 0-1 1v3a1 1 0 0 0 2 0V7a1 1 0 0 0-1-1Zm0 8a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="login-form" class="mt-8 space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700">Email</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                            <svg class="size-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M3 4a2 2 0 0 0-2 2v1.16l9 5.4 9-5.4V6a2 2 0 0 0-2-2H3Z"/>
                                <path d="M19 8.84l-8.49 5.09a1 1 0 0 1-1.02 0L1 8.84V14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8.84Z"/>
                            </svg>
                        </span>
                        <input type="email" id="email" name="email"
                               value="{{ old('email', request()->cookie('remembered_email')) }}" required autofocus
                               autocomplete="email" placeholder="you@example.com"
                               class="w-full rounded-xl border bg-white py-3 pl-11 pr-4 text-sm shadow-sm outline-none transition placeholder:text-gray-400 focus:ring-4 {{ $errors->has('email') ? 'border-red-300 focus:border-red-500 focus:ring-red-500/10' : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500/15' }}">
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700">Password</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                            <svg class="size-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 0 0-4.5 4.5V9H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-.5V5.5A4.5 4.5 0 0 0 10 1Zm3 8V5.5a3 3 0 1 0-6 0V9h6Z" clip-rule="evenodd"/>
                            </svg>
                        </span>
                        <input type="password" id="password" name="password" required
                               autocomplete="current-password" placeholder="Enter your password"
                               class="w-full rounded-xl border border-gray-300 bg-white py-3 pl-11 pr-12 text-sm shadow-sm outline-none transition placeholder:text-gray-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/15">
                        <button type="button" id="toggle-password" aria-label="Show password"
                                class="absolute inset-y-0 right-0 flex items-center rounded-r-xl px-3.5 text-gray-400 transition hover:text-gray-600 focus:outline-none focus-visible:text-blue-600">
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
                <div class="flex items-center">
                    <label for="remember" class="flex cursor-pointer select-none items-center gap-2.5 text-sm text-gray-600">
                        <input type="checkbox" id="remember" name="remember" value="1"
                               class="size-4 cursor-pointer rounded border-gray-300 accent-blue-600">
                        Keep me signed in
                    </label>
                </div>

                {{-- Submit --}}
                <button type="submit" id="submit-btn"
                        class="group relative flex w-full items-center justify-center gap-2 overflow-hidden rounded-xl bg-linear-to-r from-blue-600 to-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/25 transition hover:from-blue-700 hover:to-indigo-700 hover:shadow-xl hover:shadow-blue-600/30 focus:outline-none focus-visible:ring-4 focus-visible:ring-blue-500/30 active:scale-[0.99] disabled:cursor-not-allowed disabled:opacity-80">
                    <svg id="spinner" class="hidden size-4 animate-spin" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"/>
                        <path d="M4 12a8 8 0 0 1 8-8" stroke="currentColor" stroke-width="4" stroke-linecap="round" class="opacity-90"/>
                    </svg>
                    <span id="submit-label">Sign in</span>
                    <svg id="submit-arrow" class="size-4 transition-transform group-hover:translate-x-0.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.64l-3.22-3.22a.75.75 0 1 1 1.06-1.06l4.5 4.5a.75.75 0 0 1 0 1.06l-4.5 4.5a.75.75 0 1 1-1.06-1.06l3.22-3.22H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </form>

            <p class="mt-10 text-center text-xs text-gray-400">
                Protected area &middot; Authorized personnel only
            </p>
        </div>
    </main>
</div>

<script>
    (function () {
        var pwd = document.getElementById('password');
        var toggle = document.getElementById('toggle-password');
        var eyeOpen = document.getElementById('eye-open');
        var eyeClosed = document.getElementById('eye-closed');

        toggle.addEventListener('click', function () {
            var show = pwd.type === 'password';
            pwd.type = show ? 'text' : 'password';
            eyeOpen.classList.toggle('hidden', show);
            eyeClosed.classList.toggle('hidden', !show);
            toggle.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
        });

        var form = document.getElementById('login-form');
        var btn = document.getElementById('submit-btn');
        form.addEventListener('submit', function () {
            btn.disabled = true;
            document.getElementById('spinner').classList.remove('hidden');
            document.getElementById('submit-arrow').classList.add('hidden');
            document.getElementById('submit-label').textContent = 'Signing in...';
        });
    })();
</script>
</body>
</html>