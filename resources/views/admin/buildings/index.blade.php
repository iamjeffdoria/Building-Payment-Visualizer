@extends('layouts.app')

@section('title', 'Buildings')
@section('active', 'buildings')

@section('content')
    <h1 class="mb-5 text-2xl font-semibold">Buildings</h1>

    <div class="rounded-xl bg-white p-5 shadow-sm">
        <div id="building-filters" class="mb-4">
            <label for="bf-search" class="mb-1 block text-sm font-medium">Search</label>
            <input type="text" id="bf-search" name="search" value="{{ request('search') }}"
                   placeholder="Building name (e.g. BLDG. 2, Baywalk)"
                   class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm sm:max-w-md">
        </div>

        <div id="building-results">
            @include('admin.buildings.partials.building-results')
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        var results = document.getElementById('building-results');
        var filterBox = document.getElementById('building-filters');
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
            filterBox.querySelectorAll('input').forEach(function (el) {
                if (el.value.trim() !== '') params.set(el.name, el.value.trim());
            });
            var qs = params.toString();
            return '{{ route('admin.buildings') }}' + (qs ? '?' + qs : '');
        }

        filterBox.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(function () { load(buildUrl()); }, 300);
        });
    })();
</script>
@endpush