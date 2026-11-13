// <form data-confirm="Are you sure?"> asks before submitting.
// Uses event delegation so it also works on AJAX-loaded content.
document.addEventListener('submit', function (e) {
    var message = e.target.dataset ? e.target.dataset.confirm : null;
    if (message && !window.confirm(message)) {
        e.preventDefault();
    }
});