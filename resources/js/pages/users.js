var results = document.getElementById('results');
var filterBox = document.getElementById('filters');
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