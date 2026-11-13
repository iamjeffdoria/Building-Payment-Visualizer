var pwd = document.getElementById('password');
var toggle = document.getElementById('toggle-password');
var eyeOpen = document.getElementById('eye-open');
var eyeClosed = document.getElementById('eye-closed');

toggle.addEventListener('click', function () {
    var show = pwd.type === 'password';
    pwd.type = show ? 'text' : 'password';
    eyeOpen.classList.toggle('hidden', show);
    eyeClosed.classList.toggle('hidden', !show);
    toggle.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
});

var form = document.getElementById('login-form');
var btn = document.getElementById('submit-btn');
form.addEventListener('submit', function () {
    btn.disabled = true;
    document.getElementById('spinner').classList.remove('hidden');
    document.getElementById('submit-arrow').classList.add('hidden');
    document.getElementById('submit-label').textContent = 'Signing in...';
});