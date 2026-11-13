// Live per-column search for the buildings list.
var filterBox = document.getElementById('building-filters');
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
            // Swap only the rows so the search boxes keep focus while typing.
            document.getElementById('building-body').innerHTML = doc.getElementById('building-body').innerHTML;
            history.replaceState(null, '', url);
        });
}

function buildUrl() {
    var params = new URLSearchParams();
    filterBox.querySelectorAll('input').forEach(function (el) {
        if (el.value.trim() !== '') params.set(el.name, el.value.trim());
    });
    var qs = params.toString();
    return baseUrl + (qs ? '?' + qs : '');
}

filterBox.addEventListener('input', function () {
    clearTimeout(timer);
    timer = setTimeout(function () { load(buildUrl()); }, 300);
});