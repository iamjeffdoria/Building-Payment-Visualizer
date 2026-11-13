// Remember the desktop collapsed/expanded state between pages.
(function () {
    var box = document.getElementById('sidebar-collapse');
    if (!box) return;
    try { box.checked = localStorage.getItem('sidebar-collapsed') === '1'; } catch (e) {}
    box.addEventListener('change', function () {
        try { localStorage.setItem('sidebar-collapsed', box.checked ? '1' : '0'); } catch (e) {}
    });
})();