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

    // Click a profile picture to see the full, unframed image.
    var preview = e.target.closest('[data-preview]');
    if (preview) {
        document.getElementById('photo-preview-img').src = preview.dataset.preview;
        document.getElementById('photo-preview-dialog').showModal();
    }

    // Clicking the dark backdrop closes the preview.
    if (e.target.id === 'photo-preview-dialog') {
        e.target.close();
    }
});