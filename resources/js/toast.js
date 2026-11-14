// Toast notifications.
// Flash messages from the server arrive through #toast-root (see layouts/app.blade.php).
// You can also call window.toast('success', 'Saved!') from any script.
(function () {
    var root = document.getElementById('toast-root');
    if (!root) return;

    var STYLES = {
        success: { bar: 'bg-green-500', icon: 'bg-green-100 text-green-600', path: 'M5 13l4 4L19 7', ms: 3000 },
        error: { bar: 'bg-red-500', icon: 'bg-red-100 text-red-600', path: 'M6 18L18 6M6 6l12 12', ms: 5000 }
    };

    function show(type, message) {
        var s = STYLES[type] || STYLES.success;

        var el = document.createElement('div');
        el.setAttribute('role', type === 'error' ? 'alert' : 'status');
        el.className = 'pointer-events-auto overflow-hidden rounded-lg border border-gray-200 bg-white shadow-lg';
        el.style.transition = 'opacity .25s ease, transform .25s ease';
        el.style.opacity = '0';
        el.style.transform = 'translateX(1rem)';

        el.innerHTML =
            '<div class="flex items-start gap-3 px-4 py-3">' +
                '<span class="mt-0.5 inline-flex size-6 shrink-0 items-center justify-center rounded-full ' + s.icon + '">' +
                    '<svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="' + s.path + '" />' +
                    '</svg>' +
                '</span>' +
                '<p class="min-w-0 flex-1 break-words text-sm text-gray-800" data-msg></p>' +
                '<button type="button" data-close-toast aria-label="Dismiss" class="shrink-0 text-gray-400 hover:text-gray-600">' +
                    '<svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />' +
                    '</svg>' +
                '</button>' +
            '</div>' +
            '<div class="h-1 ' + s.bar + '" data-bar style="width:100%;transition:width ' + s.ms + 'ms linear"></div>';

        // textContent keeps the message safe (no HTML injection).
        el.querySelector('[data-msg]').textContent = message;
        root.appendChild(el);

        var timer = null;
        function dismiss() {
            clearTimeout(timer);
            el.style.opacity = '0';
            el.style.transform = 'translateX(1rem)';
            setTimeout(function () { el.remove(); }, 250);
        }

        el.querySelector('[data-close-toast]').addEventListener('click', dismiss);

        // Slide in, then start the countdown bar.
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                el.style.opacity = '1';
                el.style.transform = 'translateX(0)';
                el.querySelector('[data-bar]').style.width = '0%';
            });
        });

        timer = setTimeout(dismiss, s.ms);
    }

    window.toast = show;

    var items = [];
    try { items = JSON.parse(root.dataset.toasts || '[]'); } catch (e) {}
    items.forEach(function (t) { show(t.type, t.message); });
})();