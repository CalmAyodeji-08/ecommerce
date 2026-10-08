<?php
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$basePath    = parse_url(APP_URL, PHP_URL_PATH) ?? '';
if ($basePath && str_starts_with($currentPath, $basePath)) {
    $currentPath = substr($currentPath, strlen($basePath));
}
$currentPath = '/' . trim($currentPath, '/');

function navActive(string $path, string $current): string {
    if ($path === '/') return $current === '/' ? ' active' : '';
    return str_starts_with($current, $path) ? ' active' : '';
}
$whatsapp = setting('whatsapp_number', '2348000000000');
?>
<header class="site-header" role="banner">
    <div class="container site-header__inner">

        <!-- Hamburger (mobile only) -->
        <button class="hamburger-btn" id="hamburger-btn" aria-label="Open menu" aria-expanded="false" aria-controls="mobile-sidebar">
            <span></span><span></span><span></span>
        </button>

        <!-- Logo -->
        <a href="<?= url('/') ?>" class="site-header__logo" aria-label="<?= e(APP_NAME) ?> home">
            AZFAT <span>BEST BOUTIQUE</span>
        </a>

        <!-- Desktop navigation -->
        <nav class="site-header__nav" aria-label="Main navigation">
            <a href="<?= url('/') ?>"                  class="<?= navActive('/', $currentPath) ?>">Home</a>
            <a href="<?= url('/shop') ?>"               class="<?= navActive('/shop', $currentPath) ?>">Shop</a>
            <a href="<?= url('/category/tees') ?>"      class="<?= navActive('/category/tees', $currentPath) ?>">Tees</a>
            <a href="<?= url('/category/hoodies') ?>"   class="<?= navActive('/category/hoodies', $currentPath) ?>">Hoodies</a>
            <a href="<?= url('/category/caps') ?>"      class="<?= navActive('/category/caps', $currentPath) ?>">Caps</a>
            <a href="<?= url('/category/joggers') ?>"   class="<?= navActive('/category/joggers', $currentPath) ?>">Joggers</a>
        </nav>

        <!-- Header actions -->
        <div class="site-header__actions">
            <!-- Dark mode toggle -->
            <button class="site-header__action-btn" id="theme-toggle" aria-label="Toggle dark mode" title="Toggle dark mode">
                <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
                <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
            </button>

            <!-- Search -->
            <button class="site-header__action-btn" id="search-toggle-btn" aria-label="Search" aria-expanded="false" aria-controls="search-overlay">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            </button>
            <?php if (isLoggedIn()): ?>
            <a href="<?= url('/account/wishlist') ?>" class="site-header__action-btn" aria-label="Wishlist">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
            </a>
            <?php endif; ?>
            <a href="<?= url(isLoggedIn() ? '/account' : '/account/login') ?>" class="site-header__action-btn" aria-label="Account">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </a>
            <a href="<?= url('/cart') ?>" class="site-header__action-btn" aria-label="Cart (<?= cartCount() ?> items)">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                <span class="cart-badge" id="cart-badge" data-count="<?= cartCount() ?>"><?= cartCount() ?: '' ?></span>
            </a>
        </div>
    </div>
</header>

<!-- ══ Mobile Sidebar ══════════════════════════════════════════════════════ -->
<div class="mobile-sidebar-overlay" id="sidebar-overlay" aria-hidden="true"></div>

<aside class="mobile-sidebar" id="mobile-sidebar" aria-label="Mobile navigation" aria-hidden="true">
    <div class="mobile-sidebar__header">
        <span class="site-header__logo">AZFAT <span>BEST BOUTIQUE</span></span>
        <button class="mobile-sidebar__close" id="sidebar-close" aria-label="Close menu">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="24" height="24" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>

    <nav class="mobile-sidebar__nav">

        <!-- SHOP -->
        <div class="mobile-sidebar__group">
            <p class="mobile-sidebar__group-title">SHOP</p>
            <a href="<?= url('/shop') ?>"               class="mobile-sidebar__link">All Products</a>
            <a href="<?= url('/category/tees') ?>"      class="mobile-sidebar__link">Tees</a>
            <a href="<?= url('/category/hoodies') ?>"   class="mobile-sidebar__link">Hoodies</a>
            <a href="<?= url('/category/caps') ?>"      class="mobile-sidebar__link">Caps</a>
            <a href="<?= url('/category/joggers') ?>"   class="mobile-sidebar__link">Joggers</a>
            <a href="<?= url('/category/sets') ?>"      class="mobile-sidebar__link">Sets</a>
        </div>

        <!-- HELP -->
        <div class="mobile-sidebar__group">
            <p class="mobile-sidebar__group-title">HELP</p>
            <a href="<?= url('/#faq') ?>"                                       class="mobile-sidebar__link">FAQ</a>
            <a href="<?= url('/#delivery') ?>"                                  class="mobile-sidebar__link">Delivery Info</a>
            <a href="<?= url('/#returns') ?>"                                   class="mobile-sidebar__link">Returns Policy</a>
            <a href="https://wa.me/<?= e($whatsapp) ?>" target="_blank" rel="noopener" class="mobile-sidebar__link">WhatsApp Us</a>
        </div>

        <!-- ACCOUNT -->
        <div class="mobile-sidebar__group">
            <p class="mobile-sidebar__group-title">ACCOUNT</p>
            <?php if (isLoggedIn()): ?>
            <a href="<?= url('/account') ?>"            class="mobile-sidebar__link">My Account</a>
            <a href="<?= url('/account/orders') ?>"     class="mobile-sidebar__link">Order History</a>
            <a href="<?= url('/account/wishlist') ?>"   class="mobile-sidebar__link">Wishlist</a>
            <a href="<?= url('/account/logout') ?>"     class="mobile-sidebar__link mobile-sidebar__link--danger">Log Out</a>
            <?php else: ?>
            <a href="<?= url('/account/login') ?>"      class="mobile-sidebar__link">Log In</a>
            <a href="<?= url('/account/register') ?>"   class="mobile-sidebar__link">Create Account</a>
            <?php endif; ?>
        </div>

    </nav>
</aside>

<!-- ══ Search Overlay ══════════════════════════════════════════════════════ -->
<div class="search-overlay" id="search-overlay" aria-hidden="true" role="dialog" aria-label="Search">
    <div class="search-overlay__inner">
        <form class="search-overlay__form" method="GET" action="<?= url('/search') ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="22" height="22" class="search-overlay__icon" aria-hidden="true">
                <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
            </svg>
            <input
                type="search"
                name="q"
                id="search-overlay-input"
                class="search-overlay__input"
                placeholder="Search for tees, hoodies, caps…"
                autocomplete="off"
                aria-label="Search products"
            >
            <button type="submit" class="btn btn--primary btn--sm">Search</button>
            <button type="button" class="search-overlay__close" id="search-close-btn" aria-label="Close search">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </form>
        <p class="search-overlay__hint">Try: "black hoodie", "tees", "caps", "joggers"</p>
    </div>
</div>
<div class="search-overlay-backdrop" id="search-backdrop"></div>
