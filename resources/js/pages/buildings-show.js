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

// Live search / filter / pagination for the stall list.
var results = document.getElementById('stall-results');
var filterBox = document.getElementById('stall-filters');
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
    filterBox.querySelectorAll('input, select').forEach(function (el) {
        if (el.value.trim() !== '') params.set(el.name, el.value.trim());
    });
    var qs = params.toString();
    return baseUrl + (qs ? '?' + qs : '');
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
    }
});