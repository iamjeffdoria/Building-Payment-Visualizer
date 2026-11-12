@extends('layouts.app')

@section('title', 'Dashboard')
@section('active', 'dashboard')

@section('content')
    <h1 class="mb-5 text-2xl font-semibold">Dashboard</h1>

    <div class="mb-5 rounded-xl bg-white p-5 shadow-sm">
        <strong>Hello, {{ $user->name }}.</strong>
        <p class="mt-1.5 text-sm text-gray-500">
            You are signed in as
            <span class="rounded-full px-2 py-0.5 text-xs {{ $user->role === 'superadmin' ? 'bg-blue-100 text-blue-800' : 'bg-gray-200' }}">{{ $user->role }}</span>
            &middot; {{ now()->format('l, F j, Y') }}
        </p>
    </div>

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
        {{-- Map --}}
        <div class="rounded-xl bg-white p-5 shadow-sm xl:col-span-2">
            <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                <div>
                    <strong>LGU Palompon Market Map</strong>
                    <p class="text-sm text-gray-500">Hover a building to see its stalls. Click to pin it.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3 text-xs text-gray-600">
                    <span class="flex items-center gap-1.5"><span class="size-3 rounded-sm bg-green-300"></span>Mostly paid</span>
                    <span class="flex items-center gap-1.5"><span class="size-3 rounded-sm bg-amber-300"></span>Partial</span>
                    <span class="flex items-center gap-1.5"><span class="size-3 rounded-sm bg-red-300"></span>Mostly unpaid</span>
                </div>
            </div>

            <div class="overflow-x-auto rounded-lg border border-gray-200 bg-gray-50">
                <svg id="market-map" viewBox="0 0 1000 420" class="min-w-[640px] w-full select-none"></svg>
            </div>
        </div>

        {{-- Details panel --}}
        <div class="rounded-xl bg-white p-5 shadow-sm">
            <div id="map-panel"></div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        var svg = document.getElementById('market-map');
        var panel = document.getElementById('map-panel');
        var NS = 'http://www.w3.org/2000/svg';
        var DATA = @json($buildingData);

        var NAMES = ['Maria Santos', 'Juan Dela Cruz', 'Ana Villanueva', 'Pedro Reyes', 'Rosa Bacalso',
            'Carlo Tan', 'Lita Gonzales', 'Ramon Cabrera', 'Elena Pacatang', 'Jose Mangubat',
            'Nena Abella', 'Roel Quirog', 'Cora Labra', 'Dante Ocon', 'Gina Ybanez', 'Mario Alcala'];

        // x, y, w, h are SVG coordinates. v = vertical label.
        var BUILDINGS = [
            { id: 'b7a', label: 'BLDG. 7A', short: '7A', x: 192, y: 8,   w: 51,  h: 187, v: true, stalls: 12, rent: 1500 },
            { id: 'b2',  label: 'BLDG. 2',  short: 'B2', x: 292, y: 8,   w: 178, h: 52,  stalls: 16, rent: 1800 },
            { id: 'b1',  label: 'BLDG. 1',  short: 'B1', x: 485, y: 9,   w: 170, h: 52,  stalls: 12, rent: 1800 },
            { id: 'b5',  label: 'BLDG. 5',  short: 'B5', x: 392, y: 112, w: 172, h: 50,  stalls: 10, rent: 2000 },
            { id: 'b4',  label: 'BLDG. 4',  short: 'B4', x: 288, y: 112, w: 55,  h: 196, v: true, stalls: 14, rent: 1600 },
            { id: 'b6',  label: 'BLDG. 6',  short: 'B6', x: 392, y: 183, w: 178, h: 124, stalls: 24, rent: 2200 },
            { id: 'b3',  label: 'BLDG. 3',  short: 'B3', x: 612, y: 110, w: 56,  h: 195, v: true, stalls: 14, rent: 1600 },
            { id: 'b7',  label: 'BLDG. 7',  short: 'B7', x: 35,  y: 240, w: 200, h: 70,  stalls: 18, rent: 1500 },
            { id: 'rl',  label: 'ROW L',    short: 'RL', x: 23,  y: 350, w: 217, h: 48,  stalls: 10, rent: 1200 },
            { id: 'rk',  label: 'ROW K',    short: 'RK', x: 254, y: 348, w: 134, h: 47,  stalls: 8,  rent: 1200 },
            { id: 'bw',  label: 'BAYWALK',  short: 'BW', x: 390, y: 344, w: 335, h: 61,  fs: 15, stalls: 20, rent: 2500 },
            { id: 'p1',  label: 'PAINITAN SEC. 1', short: 'P1', x: 720, y: 258, w: 170, h: 27, fs: 9, stalls: 6, rent: 1400 },
            { id: 'p2',  label: 'PAINITAN SEC. 2', short: 'P2', x: 725, y: 300, w: 138, h: 35, fs: 9, stalls: 6, rent: 1400 },
            { id: 'p3',  label: 'PAINITAN SEC. 3', short: 'P3', x: 730, y: 360, w: 135, h: 35, fs: 9, stalls: 6, rent: 1400 }
        ];

        // Non-interactive landmarks
        var LANDMARKS = [
            { label: 'PALOMPON', label2: 'CIVIC CENTER', x: 711, y: 9, w: 242, h: 204 },
            { label: 'METRO BANK', x: 773, y: 214, w: 115, h: 40, fs: 8 }
        ];

        var STATUS = {
            paid:    { text: 'Paid',    badge: 'bg-green-100 text-green-800' },
            unpaid:  { text: 'Unpaid',  badge: 'bg-amber-100 text-amber-800' },
            overdue: { text: 'Overdue', badge: 'bg-red-100 text-red-800' }
        };

        // Deterministic mock data (same every refresh). Replace with backend data later.
        function seeded(seed) {
            return function () {
                seed |= 0; seed = seed + 0x6D2B79F5 | 0;
                var t = Math.imul(seed ^ seed >>> 15, 1 | seed);
                t = t + Math.imul(t ^ t >>> 7, 61 | t) ^ t;
                return ((t ^ t >>> 14) >>> 0) / 4294967296;
            };
        }

        BUILDINGS.forEach(function (b) {
            var data = DATA[b.id];
            b.list = data ? data.stalls : [];
            b.url = data ? data.url : '#';
        });

        function summarize(b) {
            var s = { paid: 0, unpaid: 0, overdue: 0, total: b.list.length, collected: 0, expected: 0 };
            b.list.forEach(function (st) {
                s[st.status]++;
                s.expected += st.amount;
                if (st.status === 'paid') s.collected += st.amount;
            });
            s.rate = s.total ? s.paid / s.total : 0;
            return s;
        }

        function fillFor(rate) {
            if (rate >= 0.7) return '#86efac';
            if (rate >= 0.5) return '#fcd34d';
            return '#fca5a5';
        }

        function peso(n) { return '₱' + n.toLocaleString(); }

        function el(name, attrs) {
            var node = document.createElementNS(NS, name);
            Object.keys(attrs || {}).forEach(function (k) { node.setAttribute(k, attrs[k]); });
            return node;
        }

        function addText(g, str, cx, cy, fs, rotate, weight) {
            var t = el('text', {
                x: cx, y: cy, 'text-anchor': 'middle', 'dominant-baseline': 'middle',
                'font-size': fs || 11, 'font-weight': weight || 700, fill: '#1f2937',
                'pointer-events': 'none'
            });
            if (rotate) t.setAttribute('transform', 'rotate(90 ' + cx + ' ' + cy + ')');
            t.textContent = str;
            g.appendChild(t);
        }

        // Draw landmarks
        LANDMARKS.forEach(function (l) {
            var g = el('g');
            g.appendChild(el('rect', { x: l.x, y: l.y, width: l.w, height: l.h, fill: '#f3f4f6', stroke: '#9ca3af', 'stroke-width': 2 }));
            if (l.label2) {
                addText(g, l.label, l.x + l.w / 2, l.y + l.h / 2 - 10, 14, false, 600);
                addText(g, l.label2, l.x + l.w / 2, l.y + l.h / 2 + 10, 14, false, 600);
            } else {
                addText(g, l.label, l.x + l.w / 2, l.y + l.h / 2, l.fs || 11, false, 600);
            }
            svg.appendChild(g);
        });

        // Draw buildings
        BUILDINGS.forEach(function (b) {
            b.sum = summarize(b);
            var g = el('g', { 'data-id': b.id, class: 'building', style: 'cursor:pointer' });
            b.rect = el('rect', {
                x: b.x, y: b.y, width: b.w, height: b.h,
                fill: b.sum.total ? fillFor(b.sum.rate) : '#e5e7eb', stroke: '#374151', 'stroke-width': 2
            });
            g.appendChild(b.rect);
            addText(g, b.label, b.x + b.w / 2, b.y + b.h / 2, b.fs || 11, !!b.v);
            svg.appendChild(g);
        });

        var pinned = null;

        function highlight(activeId) {
            BUILDINGS.forEach(function (b) {
                var on = b.id === activeId;
                b.rect.setAttribute('stroke', on ? '#2563eb' : '#374151');
                b.rect.setAttribute('stroke-width', on ? 4 : 2);
                b.rect.style.filter = on ? 'brightness(0.95)' : '';
            });
        }

        function renderPanel(b) {
            if (!b) {
                panel.innerHTML =
                    '<strong>Stall details</strong>' +
                    '<p class="mt-1.5 text-sm text-gray-500">Hover over a building on the map to see its stalls and payment status.</p>';
                return;
            }
            var s = b.sum;
            var pct = Math.round(s.rate * 100);
            var rows = b.list.map(function (st) {
                return '<tr class="border-b border-gray-100">' +
                    '<td class="px-2 py-1.5 font-medium">' + st.no + '</td>' +
                    '<td class="px-2 py-1.5">' + st.tenant + '</td>' +
                    '<td class="px-2 py-1.5">' + peso(st.amount) + '</td>' +
                    '<td class="px-2 py-1.5"><span class="rounded-full px-2 py-0.5 text-xs ' + STATUS[st.status].badge + '">' + STATUS[st.status].text + '</span></td>' +
                    '</tr>';
            }).join('');

            if (!rows) {
                rows = '<tr><td colspan="4" class="px-2 py-3 text-gray-500">No stalls yet.</td></tr>';
            }

            panel.innerHTML =
                '<div class="flex items-start justify-between gap-2">' +
                    '<div><strong>' + b.label + '</strong>' +
                    '<p class="text-sm text-gray-500">' + s.total + ' stalls' + (pinned === b.id ? ' &middot; pinned' : '') + ' &middot; <a href="' + b.url + '" class="text-blue-600 hover:underline">Manage stalls</a></p></div>' +
                    (pinned === b.id ? '<button type="button" data-unpin class="rounded-md bg-gray-200 px-2.5 py-1 text-xs hover:bg-gray-300">Unpin</button>' : '') +
                '</div>' +
                '<div class="mt-3 grid grid-cols-3 gap-2 text-center text-xs">' +
                    '<div class="rounded-lg bg-green-50 p-2"><div class="text-lg font-semibold text-green-800">' + s.paid + '</div>Paid</div>' +
                    '<div class="rounded-lg bg-amber-50 p-2"><div class="text-lg font-semibold text-amber-800">' + s.unpaid + '</div>Unpaid</div>' +
                    '<div class="rounded-lg bg-red-50 p-2"><div class="text-lg font-semibold text-red-800">' + s.overdue + '</div>Overdue</div>' +
                '</div>' +
                '<div class="mt-3">' +
                    '<div class="mb-1 flex justify-between text-xs text-gray-500"><span>Collected ' + peso(s.collected) + ' of ' + peso(s.expected) + '</span><span>' + pct + '%</span></div>' +
                    '<div class="h-2 overflow-hidden rounded-full bg-gray-200"><div class="h-full bg-green-500" style="width:' + pct + '%"></div></div>' +
                '</div>' +
                '<div class="mt-3 max-h-80 overflow-y-auto">' +
                    '<table class="w-full text-left text-sm">' +
                        '<thead class="sticky top-0 bg-white"><tr class="border-b border-gray-200 text-xs uppercase text-gray-500">' +
                            '<th class="px-2 py-2">Stall</th><th class="px-2 py-2">Tenant</th><th class="px-2 py-2">Amount</th><th class="px-2 py-2">Status</th>' +
                        '</tr></thead><tbody>' + rows + '</tbody>' +
                    '</table>' +
                '</div>';
        }

        function findBuilding(target) {
            var g = target.closest('[data-id]');
            if (!g) return null;
            return BUILDINGS.filter(function (b) { return b.id === g.dataset.id; })[0] || null;
        }

        function current() {
            return BUILDINGS.filter(function (b) { return b.id === pinned; })[0] || null;
        }

        svg.addEventListener('mouseover', function (e) {
            var b = findBuilding(e.target);
            if (b) { highlight(b.id); renderPanel(b); }
        });

        svg.addEventListener('mouseleave', function () {
            var p = current();
            highlight(p ? p.id : null);
            renderPanel(p);
        });

        svg.addEventListener('click', function (e) {
            var b = findBuilding(e.target);
            pinned = b ? b.id : null;
            highlight(pinned);
            renderPanel(b);
        });

        panel.addEventListener('click', function (e) {
            if (e.target.closest('[data-unpin]')) {
                pinned = null;
                highlight(null);
                renderPanel(null);
            }
        });

        renderPanel(null);
    })();
</script>
@endpush