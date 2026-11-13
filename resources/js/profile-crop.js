// Profile picture cropper: pick a photo, drag and zoom it into a square, upload the cropped result.
(function () {
    var dialog = document.getElementById('profile-dialog');
    var input = document.getElementById('p-photo');
    var pick = document.getElementById('p-photo-pick');
    var box = document.getElementById('p-crop');
    var canvas = document.getElementById('p-crop-canvas');
    var zoomEl = document.getElementById('p-crop-zoom');
    var cancel = document.getElementById('p-crop-cancel');
    if (!dialog || !input || !pick || !box || !canvas) return;

    var form = dialog.querySelector('form');
    var ctx = canvas.getContext('2d');
    var N = canvas.width; // output size in pixels (square)
    var img = null, base = 1, zoom = 1, ox = 0, oy = 0, drag = null, submitting = false;

    function clamp() {
        var w = img.width * base * zoom, h = img.height * base * zoom;
        var mx = Math.max(0, (w - N) / 2), my = Math.max(0, (h - N) / 2);
        ox = Math.min(mx, Math.max(-mx, ox));
        oy = Math.min(my, Math.max(-my, oy));
    }

    function draw() {
        if (!img) return;
        clamp();
        var w = img.width * base * zoom, h = img.height * base * zoom;
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, N, N);
        ctx.drawImage(img, N / 2 + ox - w / 2, N / 2 + oy - h / 2, w, h);
    }

    function reset() {
        img = null; zoom = 1; ox = 0; oy = 0; drag = null;
        input.value = '';
        zoomEl.value = 1;
        box.classList.add('hidden');
    }

    pick.addEventListener('click', function () { input.click(); });

    input.addEventListener('change', function () {
        var file = input.files && input.files[0];
        if (!file) return;
        if (file.type.indexOf('image/') !== 0) { reset(); return; }

        var reader = new FileReader();
        reader.onload = function (e) {
            var image = new Image();
            image.onload = function () {
                img = image;
                base = Math.max(N / image.width, N / image.height); // fill the square
                zoom = 1; ox = 0; oy = 0;
                zoomEl.value = 1;
                box.classList.remove('hidden');
                draw();
            };
            image.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });

    // Drag to move
    canvas.addEventListener('pointerdown', function (e) {
        if (!img) return;
        drag = { x: e.clientX, y: e.clientY, ox: ox, oy: oy };
        canvas.setPointerCapture(e.pointerId);
        canvas.classList.replace('cursor-grab', 'cursor-grabbing');
    });
    canvas.addEventListener('pointermove', function (e) {
        if (!drag) return;
        var k = N / canvas.getBoundingClientRect().width;
        ox = drag.ox + (e.clientX - drag.x) * k;
        oy = drag.oy + (e.clientY - drag.y) * k;
        draw();
    });
    function endDrag() {
        drag = null;
        canvas.classList.replace('cursor-grabbing', 'cursor-grab');
    }
    canvas.addEventListener('pointerup', endDrag);
    canvas.addEventListener('pointercancel', endDrag);

    // Zoom with slider or mouse wheel
    zoomEl.addEventListener('input', function () {
        zoom = parseFloat(zoomEl.value);
        draw();
    });
    canvas.addEventListener('wheel', function (e) {
        if (!img) return;
        e.preventDefault();
        zoom = Math.min(3, Math.max(1, zoom - e.deltaY * 0.002));
        zoomEl.value = zoom;
        draw();
    }, { passive: false });

    cancel.addEventListener('click', reset);
    dialog.addEventListener('close', reset);

    // On save, replace the chosen file with the cropped square image.
    form.addEventListener('submit', function (e) {
        if (!img || submitting) return;
        e.preventDefault();
        canvas.toBlob(function (blob) {
            var file = new File([blob], 'profile.jpg', { type: 'image/jpeg' });
            var dt = new DataTransfer();
            dt.items.add(file);
            input.files = dt.files;
            submitting = true;
            form.submit();
        }, 'image/jpeg', 0.9);
    });
})();