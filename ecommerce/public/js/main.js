/**
 * AZFAT BEST BOUTIQUE — Main JS
 * Small amounts of vanilla JS only. No frameworks.
 */

'use strict';

// ─── Cart badge update ────────────────────────────────────────────────────────
// Updated via custom event dispatched after cart actions (Phase 5)
document.addEventListener('cartUpdated', (e) => {
    const count = e.detail?.count ?? 0;
    document.querySelectorAll('.cart-badge').forEach(badge => {
        badge.textContent = count > 0 ? count : '';
        badge.dataset.count = count;
    });
    // Update bottom nav cart label
    document.querySelectorAll('.bottom-nav__item[aria-label^="Cart"]').forEach(el => {
        el.querySelector('span').textContent = count > 0 ? `Cart (${count})` : 'Cart';
    });
});

// ─── Flash message auto-dismiss ───────────────────────────────────────────────
document.querySelectorAll('.alert').forEach(alert => {
    setTimeout(() => {
        alert.style.transition = 'opacity 0.4s ease';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 400);
    }, 5000);
});

// ─── FAQ toggle ───────────────────────────────────────────────────────────────
function toggleFaq(btn) {
    const expanded = btn.getAttribute('aria-expanded') === 'true';
    const answerId = btn.getAttribute('aria-controls');
    const answer   = document.getElementById(answerId);

    // Close all others
    document.querySelectorAll('.faq-item__question[aria-expanded="true"]').forEach(b => {
        if (b !== btn) {
            b.setAttribute('aria-expanded', 'false');
            const a = document.getElementById(b.getAttribute('aria-controls'));
            if (a) a.hidden = true;
        }
    });

    btn.setAttribute('aria-expanded', String(!expanded));
    if (answer) answer.hidden = expanded;
}

// ─── Countdown timer ─────────────────────────────────────────────────────────
(function () {
    const endDateEl = document.getElementById('countdown');
    if (!endDateEl) return;

    // End date: 30 days from now if not set
    const end = new Date();
    end.setDate(end.getDate() + 30);

    function update() {
        const diff = end - new Date();
        if (diff <= 0) return;

        const days  = Math.floor(diff / 864e5);
        const hours = Math.floor((diff % 864e5) / 36e5);
        const mins  = Math.floor((diff % 36e5)  / 6e4);
        const secs  = Math.floor((diff % 6e4)   / 1e3);

        const pad = n => String(n).padStart(2, '0');

        const d = document.getElementById('cd-days');
        const h = document.getElementById('cd-hours');
        const m = document.getElementById('cd-mins');
        const s = document.getElementById('cd-secs');

        if (d) d.textContent = pad(days);
        if (h) h.textContent = pad(hours);
        if (m) m.textContent = pad(mins);
        if (s) s.textContent = pad(secs);
    }

    update();
    setInterval(update, 1000);
})();

// ─── Wishlist toggle (Phase 6) ────────────────────────────────────────────────
// Handles .js-wishlist-btn buttons on product cards (AJAX).
// URL is read from data-toggle-url on the button, or falls back to the
// window.WISHLIST_TOGGLE_URL global set in the layout head.
(function () {
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.js-wishlist-btn');
        if (!btn) return;

        e.preventDefault();
        const productId = btn.dataset.productId;
        if (!productId) return;

        // URL comes from data attribute (set by PHP via url()) or a page-level global
        const toggleUrl = btn.dataset.toggleUrl
            || (typeof window.WISHLIST_TOGGLE_URL !== 'undefined' ? window.WISHLIST_TOGGLE_URL : null);

        if (!toggleUrl) return; // no URL available, do nothing

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
            ?? document.querySelector('input[name="csrf_token"]')?.value
            ?? '';

        fetch(toggleUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: 'product_id=' + encodeURIComponent(productId) + '&csrf_token=' + encodeURIComponent(csrfToken),
        })
        .then(res => {
            if (res.status === 401) {
                // Not logged in — redirect to login
                const loginUrl = btn.dataset.loginUrl
                    || (typeof window.LOGIN_URL !== 'undefined' ? window.LOGIN_URL : '/account/login');
                window.location.href = loginUrl;
                return null;
            }
            return res.json();
        })
        .then(data => {
            if (!data) return;
            if (data.success) {
                const svg     = btn.querySelector('svg');
                const isAdded = data.action === 'added';
                btn.setAttribute('aria-label', (isAdded ? 'Remove from' : 'Add to') + ' wishlist');
                if (svg) svg.style.fill = isAdded ? 'currentColor' : 'none';
                btn.classList.toggle('is-wishlisted', isAdded);
            }
        })
        .catch(() => { /* fail silently — non-JS form fallback still works */ });
    });
})();

// ─── Mobile sidebar ───────────────────────────────────────────────────────────
(function () {
    const hamburger = document.getElementById('hamburger-btn');
    const sidebar   = document.getElementById('mobile-sidebar');
    const overlay   = document.getElementById('sidebar-overlay');
    const closeBtn  = document.getElementById('sidebar-close');
    if (!hamburger || !sidebar) return;

    function openSidebar() {
        sidebar.classList.add('open');
        sidebar.setAttribute('aria-hidden', 'false');
        overlay.classList.add('visible');
        overlay.setAttribute('aria-hidden', 'false');
        hamburger.classList.add('open');
        hamburger.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar.classList.remove('open');
        sidebar.setAttribute('aria-hidden', 'true');
        overlay.classList.remove('visible');
        overlay.setAttribute('aria-hidden', 'true');
        hamburger.classList.remove('open');
        hamburger.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
    }

    hamburger.addEventListener('click', function () {
        sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
    });

    if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
    if (overlay)  overlay.addEventListener('click', closeSidebar);

    // Close on Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && sidebar.classList.contains('open')) closeSidebar();
    });
})();

// ─── Dark mode toggle ─────────────────────────────────────────────────────────
(function () {
    const btn = document.getElementById('theme-toggle');
    if (!btn) return;

    btn.addEventListener('click', function () {
        const html    = document.documentElement;
        const current = html.getAttribute('data-theme') || 'light';
        const next    = current === 'dark' ? 'light' : 'dark';
        html.setAttribute('data-theme', next);
        try { localStorage.setItem('azfat-theme', next); } catch (e) {}
        // Update theme-color meta
        const meta = document.querySelector('meta[name="theme-color"]');
        if (meta) meta.content = next === 'dark' ? '#111111' : '#ffffff';
    });
})();

// ─── Size chip selector (filter sidebar) ─────────────────────────────────────
(function () {
    document.addEventListener('click', function (e) {
        const chip = e.target.closest('.js-size-chip');
        if (!chip) return;

        const input = document.getElementById('size-input');
        const allChips = document.querySelectorAll('.js-size-chip');

        // Toggle
        const alreadyActive = chip.classList.contains('active');

        // Deselect all
        allChips.forEach(function (c) {
            c.classList.remove('active');
            c.setAttribute('aria-pressed', 'false');
        });

        if (!alreadyActive) {
            chip.classList.add('active');
            chip.setAttribute('aria-pressed', 'true');
            if (input) input.value = chip.dataset.size;
        } else {
            // Clicking again deselects
            if (input) input.value = '';
        }
    });
})();

// ─── Search overlay ───────────────────────────────────────────────────────────
(function () {
    const toggleBtn  = document.getElementById('search-toggle-btn');
    const bottomBtn  = document.getElementById('bottom-search-btn');
    const overlay    = document.getElementById('search-overlay');
    const backdrop   = document.getElementById('search-backdrop');
    const closeBtn   = document.getElementById('search-close-btn');
    const input      = document.getElementById('search-overlay-input');
    if (!overlay) return;

    function openSearch() {
        overlay.classList.add('open');
        overlay.setAttribute('aria-hidden', 'false');
        backdrop.classList.add('visible');
        document.body.style.overflow = 'hidden';
        if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'true');
        setTimeout(function () { if (input) input.focus(); }, 50);
    }

    function closeSearch() {
        overlay.classList.remove('open');
        overlay.setAttribute('aria-hidden', 'true');
        backdrop.classList.remove('visible');
        document.body.style.overflow = '';
        if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'false');
    }

    if (toggleBtn) toggleBtn.addEventListener('click', function () {
        overlay.classList.contains('open') ? closeSearch() : openSearch();
    });

    if (bottomBtn) bottomBtn.addEventListener('click', openSearch);
    if (closeBtn)  closeBtn.addEventListener('click', closeSearch);
    if (backdrop)  backdrop.addEventListener('click', closeSearch);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && overlay.classList.contains('open')) closeSearch();
        // Ctrl+K / Cmd+K shortcut
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            overlay.classList.contains('open') ? closeSearch() : openSearch();
        }
    });
})();
