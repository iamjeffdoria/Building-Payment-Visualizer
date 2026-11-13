function getTopbarMenu() {
    return {
        btn: document.getElementById('topbar-profile-btn'),
        menu: document.getElementById('topbar-profile-menu'),
    };
}

function setTopbarMenu(open) {
    const { btn, menu } = getTopbarMenu();
    if (!btn || !menu) return;

    menu.classList.toggle('hidden', !open);
    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
}

document.addEventListener('click', (event) => {
    const { btn, menu } = getTopbarMenu();
    if (!btn || !menu) return;

    // Toggle when the avatar button is clicked.
    if (btn.contains(event.target)) {
        setTopbarMenu(menu.classList.contains('hidden'));
        return;
    }

    // Close after choosing "Edit profile" (the layout script opens the dialog).
    if (menu.contains(event.target) && event.target.closest('[data-open]')) {
        setTopbarMenu(false);
        return;
    }

    // Close when clicking anywhere outside the menu.
    if (!menu.contains(event.target)) {
        setTopbarMenu(false);
    }
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') setTopbarMenu(false);
});