<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($adminTitle ?? 'Admin') ?> — <?= e(APP_NAME) ?></title>
    <script>
        try {
            var t = localStorage.getItem('azfat-theme') || (window.matchMedia('(prefers-color-scheme:dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', t);
        } catch(e){}
    </script>
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
    <link rel="icon" href="<?= url('/images/favicon.ico') ?>">
</head>
<body class="admin-body">

<!-- Sidebar overlay (mobile) -->
<div class="admin-sidebar-overlay" id="admin-overlay"></div>

<!-- ── Sidebar ── -->
<aside class="admin-sidebar" id="admin-sidebar">
    <div class="admin-sidebar__logo">
        AZFAT <span>ADMIN</span>
    </div>

    <nav class="admin-sidebar__nav">
        <span class="admin-nav-section">Main</span>
        <a href="<?= url('/admin') ?>" class="admin-nav-link <?= ($activeNav ?? '') === 'dashboard' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
            Dashboard
        </a>
        <a href="<?= url('/admin/orders') ?>" class="admin-nav-link <?= ($activeNav ?? '') === 'orders' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Orders
        </a>

        <span class="admin-nav-section">Catalogue</span>
        <a href="<?= url('/admin/products') ?>" class="admin-nav-link <?= ($activeNav ?? '') === 'products' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
            Products
        </a>
        <a href="<?= url('/admin/categories') ?>" class="admin-nav-link <?= ($activeNav ?? '') === 'categories' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            Categories
        </a>

        <span class="admin-nav-section">Store</span>
        <a href="<?= url('/admin/delivery-zones') ?>" class="admin-nav-link <?= ($activeNav ?? '') === 'delivery' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 5v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
            Delivery Zones
        </a>
        <a href="<?= url('/admin/subscribers') ?>" class="admin-nav-link <?= ($activeNav ?? '') === 'subscribers' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            Subscribers
        </a>
        <a href="<?= url('/admin/settings') ?>" class="admin-nav-link <?= ($activeNav ?? '') === 'settings' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
            Settings
        </a>
    </nav>

    <div class="admin-sidebar__footer">
        <a href="<?= url('/shop') ?>" target="_blank">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
            View Shop
        </a>
        <a href="<?= url('/admin/logout') ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
            Log Out
        </a>
    </div>
</aside>

<!-- ── Content ── -->
<div class="admin-content">
    <header class="admin-topbar">
        <div class="admin-topbar__left">
            <button class="admin-hamburger" id="admin-hamburger" aria-label="Toggle menu">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
            <span class="admin-topbar__title"><?= e($adminTitle ?? 'Dashboard') ?></span>
        </div>
        <div class="admin-topbar__right">
            <button class="admin-theme-btn" id="admin-theme-toggle" aria-label="Toggle dark mode">
                <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/></svg>
                <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
            </button>
            <span class="admin-topbar__user">👤 <?= e($_SESSION['user_name'] ?? 'Admin') ?></span>
        </div>
    </header>

    <main class="admin-main">
        <?php if (!empty($flash = getFlash())): ?>
            <?php foreach ($flash as $msg): ?>
            <div class="admin-flash admin-flash--<?= e($msg['type']) ?>"><?= e($msg['message']) ?></div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?= $content ?? '' ?>
    </main>
</div>

<script src="<?= asset('js/admin.js') ?>"></script>
</body>
</html>
