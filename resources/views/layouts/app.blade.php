<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-800 antialiased">
<div class="group/layout min-h-screen lg:flex">
    <x-sidebar :active="trim($__env->yieldContent('active'))" />

    <main class="min-w-0 flex-1 px-4 pb-6 pt-16 transition-all duration-300 ease-in-out sm:px-7 sm:pb-7 lg:ml-60 lg:pt-7 lg:group-has-[#sidebar-collapse:checked]/layout:ml-16">
        @if (session('greeting'))
            <div class="mb-4 rounded-md bg-green-100 px-4 py-3 text-sm text-green-800">{{ session('greeting') }}</div>
        @endif
        @if (session('success'))
            <div class="mb-4 rounded-md bg-green-100 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-4 rounded-md bg-red-100 px-4 py-3 text-sm text-red-800">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</div>

{{-- Profile dialog (available on every page) --}}
<dialog id="profile-dialog" class="m-auto w-full max-w-md rounded-xl p-6 shadow-2xl backdrop:bg-black/50">
    <h2 class="mb-2 text-lg font-semibold">Edit my profile</h2>
    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-3">
        @csrf
        @method('PUT')

        <div>
            <label for="p-name" class="mb-1 block text-sm font-medium">Name</label>
            <input type="text" id="p-name" name="name" value="{{ auth()->user()->name }}" required
                   class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label for="p-email" class="mb-1 block text-sm font-medium">Email</label>
            <input type="email" id="p-email" name="email" value="{{ auth()->user()->email }}" required
                   class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label for="p-photo" class="mb-1 block text-sm font-medium">Photo</label>
            <input type="file" id="p-photo" name="photo" accept="image/*"
                   class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label for="p-current" class="mb-1 block text-sm font-medium">
                Current password <span class="text-xs text-gray-500">(only if changing it)</span>
            </label>
            <input type="password" id="p-current" name="current_password" autocomplete="current-password"
                   class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label for="p-new" class="mb-1 block text-sm font-medium">New password</label>
            <input type="password" id="p-new" name="password" autocomplete="new-password"
                   class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label for="p-confirm" class="mb-1 block text-sm font-medium">Confirm new password</label>
            <input type="password" id="p-confirm" name="password_confirmation" autocomplete="new-password"
                   class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
        </div>

        <div class="flex justify-end gap-2 pt-3">
            <button type="button" data-close class="rounded-md bg-gray-200 px-4 py-2 text-sm hover:bg-gray-300">Cancel</button>
            <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700">Save</button>
        </div>
    </form>
</dialog>

@stack('scripts')
</body>
</html>