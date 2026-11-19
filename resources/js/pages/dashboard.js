var svg = document.getElementById('market-map');
var panel = document.getElementById('map-panel');
var NS = 'http://www.w3.org/2000/svg';
var DATA = JSON.parse(svg.dataset.buildings || '{}');

// x, y, w, h are SVG coordinates. v = vertical label.
var BUILDINGS = [
    { id: 'b7b', label: 'BLDG. 7B', x: 128, y: 8,   w: 51,  h: 187, v: true },
    { id: 'b7a', label: 'BLDG. 7A', x: 192, y: 8,   w: 51,  h: 187, v: true },
    { id: 'b2',  label: 'BLDG. 2',  x: 292, y: 8,   w: 178, h: 52 },
    { id: 'b1',  label: 'BLDG. 1',  x: 485, y: 9,   w: 170, h: 52 },
    { id: 'b5',  label: 'BLDG. 5',  x: 392, y: 112, w: 172, h: 50 },
    { id: 'b4',  label: 'BLDG. 4',  x: 288, y: 112, w: 55,  h: 196, v: true },
    { id: 'b3',  label: 'BLDG. 3',  x: 612, y: 110, w: 56,  h: 195, v: true },
    { id: 'b7',  label: 'BLDG. 7 1ST FL.', x: 35,  y: 240, w: 200, h: 70, fs: 13 },
    { id: 'b7f2', label: 'BLDG. 7 2ND FL.', x: 35, y: 316, w: 200, h: 28, fs: 11 },
    { id: 'rl',  label: 'ROW L',    x: 23,  y: 350, w: 217, h: 48 },
    { id: 'rk',  label: 'ROW K',    x: 254, y: 348, w: 134, h: 47 },
    { id: 'vs',  label: 'VEG. SEC.',   x: 360, y: 195, w: 115, h: 50, fs: 12 },
    { id: 'ds',  label: 'DRIED SEC.',  x: 487, y: 195, w: 115, h: 50, fs: 12 },
    { id: 'ms',  label: 'MEAT SEC.',   x: 405, y: 345, w: 135, h: 50, fs: 13 },
    { id: 'fs',  label: 'FISH SEC.',   x: 552, y: 345, w: 135, h: 50, fs: 13 },
    { id: 'p1',  label: 'PAINITAN SEC.', x: 720, y: 258, w: 170, h: 27, fs: 11 }
];

// Non-interactive landmarks
var LANDMARKS = [
    { label: 'PALOMPON', label2: 'CIVIC CENTER', x: 711, y: 9, w: 242, h: 204 },
    { label: 'METRO BANK', x: 773, y: 214, w: 115, h: 40, fs: 10 }
];

function fillFor(rate) {
    if (rate >= 0.7) return '#86efac';
    if (rate >= 0.5) return '#fcd34d';
    return '#fca5a5';
}

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

// Draw buildings (only the counts come from the server, used for the color)
BUILDINGS.forEach(function (b) {
    var d = DATA[b.id] || { total: 0, paid: 0, panel: null };
    b.total = d.total;
    b.panel = d.panel;
    var rate = d.rate || 0;

    var g = el('g', { 'data-id': b.id, class: 'building', style: 'cursor:pointer' });
    b.rect = el('rect', {
        x: b.x, y: b.y, width: b.w, height: b.h,
        fill: d.total ? fillFor(rate) : '#e5e7eb', stroke: '#374151', 'stroke-width': 2
    });
    g.appendChild(b.rect);
    addText(g, b.label, b.x + b.w / 2, b.y + b.h / 2, b.fs || 14, !!b.v);
    svg.appendChild(g);
});

var pinned = null;     // building id the user clicked to pin
var shown = null;      // building id currently displayed in the panel
var shownKey = null;   // avoids re-rendering the same panel (prevents flicker)
var cache = {};
var reqId = 0;
var hoverTimer = null;
var HEADERS = { 'X-Requested-With': 'XMLHttpRequest' };

function byId(id) {
    return BUILDINGS.filter(function (b) { return b.id === id; })[0] || null;
}

function highlight(activeId) {
    BUILDINGS.forEach(function (b) {
        var on = b.id === activeId;
        b.rect.setAttribute('stroke', on ? '#2563eb' : '#374151');
        b.rect.setAttribute('stroke-width', on ? 4 : 2);
        b.rect.style.filter = on ? 'brightness(0.95)' : '';
    });
}

function setPanel(html) {
    panel.innerHTML = html;
    panel.style.opacity = '1';
}

function renderEmpty() {
    reqId++;
    shown = null;
    shownKey = null;
    setPanel(
        '<strong>Stall details</strong>' +
        '<p class="mt-1.5 text-sm text-gray-500">Hover over a building on the map to see its stalls and payment status.</p>'
    );
}

// The panel HTML is rendered by Laravel. We only fetch and drop it in.
function showPanel(b) {
    if (!b || !b.panel) { renderEmpty(); return; }

    var isPinned = pinned === b.id;
    var key = b.id + (isPinned ? ':1' : ':0');

    highlight(b.id);
    if (shownKey === key) return; // already showing this one, nothing to redraw

    var req = ++reqId;
    shown = b.id;
    shownKey = key;

    if (cache[key]) {
        setPanel(cache[key]);
        return;
    }

    // Keep the old content on screen (dimmed) instead of flashing a "Loading..." text.
    panel.style.opacity = '0.5';

    fetch(b.panel + (isPinned ? '?pinned=1' : ''), { headers: HEADERS })
        .then(function (r) { return r.text(); })
        .then(function (html) {
            cache[key] = html;
            if (req === reqId) setPanel(html);
        })
        .catch(function () {
            if (req !== reqId) return;
            shownKey = null;
            setPanel('<p class="text-sm text-red-600">Could not load stalls.</p>');
        });
}

// Load every building's panel quietly in the background so hovering feels instant.
function prefetch() {
    var queue = BUILDINGS.filter(function (b) { return b.panel; });
    (function next() {
        var b = queue.shift();
        if (!b) return;
        var key = b.id + ':0';
        if (cache[key]) { next(); return; }
        fetch(b.panel, { headers: HEADERS })
            .then(function (r) { return r.text(); })
            .then(function (html) { cache[key] = html; })
            .catch(function () {})
            .then(function () { setTimeout(next, 150); });
    })();
}

function findBuilding(target) {
    var g = target.closest('[data-id]');
    if (!g) return null;
    return byId(g.dataset.id);
}

// On small screens the panel sits below the map, so bring it into view after a click.
function scrollToPanel() {
    if (window.innerWidth < 1280) {
        panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}

// Hover: short delay so quickly sweeping the mouse across the map doesn't flash every panel.
svg.addEventListener('mouseover', function (e) {
    var b = findBuilding(e.target);
    clearTimeout(hoverTimer);
    if (!b) return;
    highlight(b.id);
    hoverTimer = setTimeout(function () { showPanel(b); }, 100);
});

// Leaving the map: go back to the pinned building, otherwise keep the last one shown.
svg.addEventListener('mouseleave', function () {
    clearTimeout(hoverTimer);
    var p = byId(pinned);
    if (p) { showPanel(p); return; }
    highlight(shown);
});

// Click a building to pin it. Click it again (or empty space) to unpin.
svg.addEventListener('click', function (e) {
    clearTimeout(hoverTimer);
    var b = findBuilding(e.target);

    if (b && pinned !== b.id) {
        pinned = b.id;
        showPanel(b);
        scrollToPanel();
        return;
    }

    pinned = null;
    var cur = byId(shown);
    if (cur) showPanel(cur); else highlight(null);
});

panel.addEventListener('click', function (e) {
    if (e.target.closest('[data-unpin]')) {
        pinned = null;
        var cur = byId(shown);
        if (cur) showPanel(cur); else highlight(null);
    }
});

renderEmpty();
prefetch();