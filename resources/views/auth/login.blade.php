<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-gray-100 text-gray-800 antialiased">
    <div class="w-full max-w-sm rounded-xl bg-white p-8 shadow-lg">
        <h1 class="text-2xl font-semibold">{{ config('app.name') }}</h1>
        <p class="mb-5 mt-1 text-sm text-gray-500">Sign in to continue</p>

        @if ($errors->any())
            <div class="mb-2 rounded-md bg-red-100 px-3 py-2.5 text-sm text-red-800">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <label for="email" class="mb-1 mt-3.5 block text-sm font-medium">Email</label>
            <input type="email" id="email" name="email"
                   value="{{ old('email', request()->cookie('remembered_email')) }}" required autofocus
                   class="w-full rounded-md border border-gray-300 px-3 py-2.5 text-sm">

            <label for="password" class="mb-1 mt-3.5 block text-sm font-medium">Password</label>
            <input type="password" id="password" name="password" required
                   class="w-full rounded-md border border-gray-300 px-3 py-2.5 text-sm">

            <div class="mt-3.5 flex items-center gap-2 text-sm">
                <input type="checkbox" id="remember" name="remember" value="1">
                <label for="remember">Keep me signed in</label>
            </div>

            <button type="submit"
                    class="mt-5 w-full rounded-md bg-blue-600 py-2.5 text-white hover:bg-blue-700">
                Sign in
            </button>
        </form>
    </div>
</body>
</html>