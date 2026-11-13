// Open the edit dialog and fill it from the clicked button's data attributes.
document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-edit]');
    if (!btn) return;
    var form = document.getElementById('edit-stall-form');
    form.action = form.dataset.base + '/' + btn.dataset.id;
    document.getElementById('es-name').value = btn.dataset.name;
    document.getElementById('es-no').value = btn.dataset.stallNo;
    document.getElementById('es-amount').value = btn.dataset.amount;
    document.getElementById('es-due').value = btn.dataset.due;
    document.getElementById('edit-stall-dialog').showModal();
});

// Live per-column search / filter / pagination for the stall list.
var results = document.getElementById('stall-results');
var filterBox = document.getElementById('stall-filters');
var baseUrl = filterBox.dataset.url;
var timer = null;
var reqId = 0;

function load(url) {
    var req = ++reqId;
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(function (r) { return r.text(); })
        .then(function (html) {
            if (req !== reqId) return; // a newer search already started
            var doc = new DOMParser().parseFromString(html, 'text/html');
            // Swap only the rows and pager so the search boxes keep focus while typing.
            document.getElementById('stall-body').innerHTML = doc.getElementById('stall-body').innerHTML;
            document.getElementById('stall-pager').innerHTML = doc.getElementById('stall-pager').innerHTML;
            history.replaceState(null, '', url);
        });
}

function buildUrl() {
    var params = new URLSearchParams();
    filterBox.querySelectorAll('input, select').forEach(function (el) {
        if (el.value.trim() !== '') params.set(el.name, el.value.trim());
    });
    var qs = params.toString();
    return baseUrl + (qs ? '?' + qs : '');
}

// Text boxes search while typing (short pause so it doesn't fire on every key).
filterBox.addEventListener('input', function (e) {
    if (e.target.type !== 'text') return;
    clearTimeout(timer);
    timer = setTimeout(function () { load(buildUrl()); }, 300);
});

// Dropdown and date pick search immediately.
filterBox.addEventListener('change', function (e) {
    if (e.target.tagName === 'SELECT' || e.target.type === 'date') {
        clearTimeout(timer);
        load(buildUrl());
    }
});

results.addEventListener('click', function (e) {
    var pageLink = e.target.closest('[data-page]');
    if (pageLink) {
        e.preventDefault();
        load(pageLink.href);
    }
});