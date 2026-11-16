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

    <div class="min-w-0 flex-1 transition-all duration-300 ease-in-out lg:ml-60 lg:group-has-[#sidebar-collapse:checked]/layout:ml-16">
        <x-topbar :title="trim($__env->yieldContent('title', 'Dashboard'))" />

        <main class="px-4 pb-6 pt-6 sm:px-7 sm:pb-7">
            @yield('content')
        </main>
    </div>
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
            <label class="mb-2 block text-sm font-medium">Profile picture</label>
            <div class="flex items-center gap-4">
                @if (auth()->user()->photo_url)
                    <button type="button" data-preview="{{ auth()->user()->photo_url }}" title="Click to view full photo"
                            class="shrink-0 cursor-zoom-in rounded-full focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                        <img src="{{ auth()->user()->photo_url }}" alt="Current profile picture"
                             class="size-24 rounded-full border border-gray-200 object-cover">
                    </button>
                @else
                    <span class="inline-flex size-24 shrink-0 items-center justify-center rounded-full bg-blue-600 text-3xl font-semibold text-white">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>
                @endif
                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-900">Current photo</p>
                    <p class="text-xs text-gray-500">You can crop it after choosing a new one.</p>
                    <button type="button" id="p-photo-pick"
                            class="mt-2 rounded-md bg-gray-200 px-3 py-1.5 text-sm hover:bg-gray-300">Choose new photo</button>
                    <input type="file" id="p-photo" name="photo" accept="image/*" class="hidden">
                </div>
            </div>

            {{-- Crop area (shown after choosing a photo) --}}
            <div id="p-crop" class="mt-4 hidden rounded-lg border border-gray-200 bg-gray-50 p-4">
                <p class="mb-3 text-center text-xs text-gray-500">Drag the photo to move it. Use the slider to zoom.</p>
                <div class="relative mx-auto size-64 select-none overflow-hidden rounded-lg bg-white">
                    <canvas id="p-crop-canvas" width="512" height="512" class="size-full cursor-grab touch-none"></canvas>
                    <div class="pointer-events-none absolute inset-0 rounded-full ring-2 ring-white shadow-[0_0_0_999px_rgba(0,0,0,0.5)]"></div>
                </div>
                <div class="mx-auto mt-4 flex max-w-64 items-center gap-3 text-gray-500">
                    <span class="text-lg leading-none">&minus;</span>
                    <input type="range" id="p-crop-zoom" min="1" max="3" step="0.01" value="1"
                           aria-label="Zoom" class="w-full accent-blue-600">
                    <span class="text-lg leading-none">+</span>
                </div>
                <div class="mt-3 text-center">
                    <button type="button" id="p-crop-cancel" class="text-xs text-red-600 hover:underline">Remove selected photo</button>
                </div>
            </div>
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

{{-- Full photo preview (opened by clicking the profile picture) --}}
<dialog id="photo-preview-dialog" class="m-auto max-w-[90vw] overflow-visible bg-transparent p-0 backdrop:bg-black/70">
    <div class="relative">
        <img id="photo-preview-img" src="" alt="Profile picture"
             class="max-h-[85vh] max-w-[90vw] rounded-lg bg-white object-contain shadow-2xl">
        <button type="button" data-close aria-label="Close preview"
                class="absolute right-2 top-2 inline-flex size-9 items-center justify-center rounded-full bg-black/60 text-white hover:bg-black/80">
            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</dialog>

{{-- Toast notifications (flash messages and errors are shown here, then auto-dismiss) --}}
@php
    $toasts = [];
    if (session('greeting')) {
        $toasts[] = ['type' => 'success', 'message' => session('greeting')];
    }
    if (session('success')) {
        $toasts[] = ['type' => 'success', 'message' => session('success')];
    }
    foreach ($errors->all() as $error) {
        $toasts[] = ['type' => 'error', 'message' => $error];
    }
@endphp
<div id="toast-root" data-toasts="{{ json_encode($toasts) }}"
     class="pointer-events-none fixed right-4 top-20 z-[100] flex w-[calc(100%-2rem)] max-w-sm flex-col gap-2"></div>

@stack('scripts')
</body>
</html>