// <button data-open="dialog-id"> opens a dialog, <button data-close> closes the one it is in.
document.addEventListener('click', function (e) {
    var opener = e.target.closest('[data-open]');
    if (opener) {
        document.getElementById(opener.dataset.open).showModal();
    }
    var closer = e.target.closest('[data-close]');
    if (closer) {
        closer.closest('dialog').close();
    }
});