var results = document.getElementById('building-results');
var filterBox = document.getElementById('building-filters');
var baseUrl = filterBox.dataset.url;
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
    return baseUrl + (qs ? '?' + qs : '');
}

filterBox.addEventListener('input', function () {
    clearTimeout(timer);
    timer = setTimeout(function () { load(buildUrl()); }, 300);
});