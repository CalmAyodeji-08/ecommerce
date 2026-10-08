'use strict';

// ── Dark mode ─────────────────────────────────────────────────────────────────
(function () {
    var btn = document.getElementById('admin-theme-toggle');
    if (!btn) return;
    btn.addEventListener('click', function () {
        var html = document.documentElement;
        var next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        html.setAttribute('data-theme', next);
        try { localStorage.setItem('azfat-theme', next); } catch(e) {}
    });
})();

// ── Sidebar toggle (mobile) ───────────────────────────────────────────────────
(function () {
    var hamburger = document.getElementById('admin-hamburger');
    var sidebar   = document.getElementById('admin-sidebar');
    var overlay   = document.getElementById('admin-overlay');
    if (!hamburger || !sidebar) return;

    function open()  { sidebar.classList.add('open');    overlay.classList.add('visible');    document.body.style.overflow = 'hidden'; }
    function close() { sidebar.classList.remove('open'); overlay.classList.remove('visible'); document.body.style.overflow = ''; }

    hamburger.addEventListener('click', function () {
        sidebar.classList.contains('open') ? close() : open();
    });
    overlay.addEventListener('click', close);
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });
})();

// ── Image preview ─────────────────────────────────────────────────────────────
document.addEventListener('change', function (e) {
    if (!e.target.matches('input[type="file"]')) return;
    var file = e.target.files[0];
    if (!file || !file.type.startsWith('image/')) return;
    var preview = document.getElementById('img-preview');
    if (!preview) return;
    var reader = new FileReader();
    reader.onload = function (ev) { preview.src = ev.target.result; preview.style.display = 'block'; };
    reader.readAsDataURL(file);
});

// ── Confirm delete ────────────────────────────────────────────────────────────
document.addEventListener('submit', function (e) {
    var form = e.target;
    if (form.dataset.confirm) {
        if (!confirm(form.dataset.confirm)) e.preventDefault();
    }
});
