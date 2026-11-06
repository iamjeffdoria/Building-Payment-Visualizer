@extends('layouts.app')

@section('title', 'Admin Users')
@section('active', 'users')

@section('content')
    <div class="mb-5 flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Admin Users</h1>
        <button type="button" data-open="create-dialog"
                class="rounded-md bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700">+ New admin</button>
    </div>

    <div class="rounded-xl bg-white p-5 shadow-sm">
        <div id="filters" class="mb-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
            <div>
                <label for="f-name" class="mb-1 block text-sm font-medium">Name</label>
                <input type="text" id="f-name" name="name" value="{{ request('name') }}" placeholder="Search name"
                       class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label for="f-email" class="mb-1 block text-sm font-medium">Email</label>
                <input type="text" id="f-email" name="email" value="{{ request('email') }}" placeholder="Search email"
                       class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label for="f-role" class="mb-1 block text-sm font-medium">Role</label>
                <select id="f-role" name="role" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm">
                    <option value="">All roles</option>
                    <option value="superadmin" @selected(request('role') === 'superadmin')>superadmin</option>
                    <option value="admin" @selected(request('role') === 'admin')>admin</option>
                </select>
            </div>
        </div>

        <div id="results">
            @include('admin.partials.admin-ajax-results')
        </div>
    </div>

    {{-- Create dialog --}}
    <dialog id="create-dialog" class="m-auto w-full max-w-md rounded-xl p-6 shadow-2xl backdrop:bg-black/50">
        <h2 class="mb-2 text-lg font-semibold">New admin</h2>
        <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data" class="space-y-3">
            @csrf
            <div>
                <label class="mb-1 block text-sm font-medium">Name</label>
                <input type="text" name="name" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">Email</label>
                <input type="email" name="email" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">Password</label>
                <input type="password" name="password" minlength="8" required autocomplete="new-password"
                       class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">Role</label>
                <select name="role" required class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm">
                    <option value="admin">admin</option>
                    <option value="superadmin">superadmin</option>
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">Photo</label>
                <input type="file" name="photo" accept="image/*" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div class="flex justify-end gap-2 pt-3">
                <button type="button" data-close class="rounded-md bg-gray-200 px-4 py-2 text-sm hover:bg-gray-300">Cancel</button>
                <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700">Create</button>
            </div>
        </form>
    </dialog>

    {{-- Edit dialog --}}
    <dialog id="edit-dialog" class="m-auto w-full max-w-md rounded-xl p-6 shadow-2xl backdrop:bg-black/50">
        <h2 class="mb-2 text-lg font-semibold">Edit admin</h2>
        <form method="POST" id="edit-form" action="" enctype="multipart/form-data"
              data-base="{{ url('/admin/users') }}" class="space-y-3">
            @csrf
            @method('PUT')
            <div>
                <label class="mb-1 block text-sm font-medium">Name</label>
                <input type="text" name="name" id="e-name" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">Email</label>
                <input type="email" name="email" id="e-email" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">
                    New password <span class="text-xs text-gray-500">(leave blank to keep current)</span>
                </label>
                <input type="password" name="password" minlength="8" autocomplete="new-password"
                       class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">Role</label>
                <select name="role" id="e-role" required class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm">
                    <option value="admin">admin</option>
                    <option value="superadmin">superadmin</option>
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">
                    Photo <span class="text-xs text-gray-500">(optional, replaces current)</span>
                </label>
                <input type="file" name="photo" accept="image/*" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div class="flex justify-end gap-2 pt-3">
                <button type="button" data-close class="rounded-md bg-gray-200 px-4 py-2 text-sm hover:bg-gray-300">Cancel</button>
                <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700">Save</button>
            </div>
        </form>
    </dialog>
@endsection

@push('scripts')
<script>
    (function () {
        var results = document.getElementById('results');
        var filterBox = document.getElementById('filters');
        var timer = null;

        function load(url) {
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function (r) { return r.text(); })
                .then(function (html) {
                    results.innerHTML = html;
                    history.replaceState(null, '', url);
                });
        }

        function buildUrl() {
            var params = new URLSearchParams();
            filterBox.querySelectorAll('input, select').forEach(function (el) {
                if (el.value.trim() !== '') params.set(el.name, el.value.trim());
            });
            var qs = params.toString();
            return '{{ route('admin.users') }}' + (qs ? '?' + qs : '');
        }

        filterBox.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(function () { load(buildUrl()); }, 300);
        });
        filterBox.addEventListener('change', function () { load(buildUrl()); });

        results.addEventListener('click', function (e) {
            var pageLink = e.target.closest('[data-page]');
            if (pageLink) {
                e.preventDefault();
                load(pageLink.href);
                return;
            }

            var editBtn = e.target.closest('[data-edit]');
            if (editBtn) {
                var form = document.getElementById('edit-form');
                form.action = form.dataset.base + '/' + editBtn.dataset.id;
                document.getElementById('e-name').value = editBtn.dataset.name;
                document.getElementById('e-email').value = editBtn.dataset.email;
                document.getElementById('e-role').value = editBtn.dataset.role;
                document.getElementById('edit-dialog').showModal();
            }
        });
    })();
</script>
@endpush