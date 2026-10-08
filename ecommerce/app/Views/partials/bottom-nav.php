<?php
// Bottom nav hidden on mobile — replaced by sidebar toggle in header
// Only show on mobile as a quick-access bar (no menu items — sidebar handles that)
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$basePath    = parse_url(APP_URL, PHP_URL_PATH) ?? '';
if ($basePath && str_starts_with($currentPath, $basePath)) {
    $currentPath = substr($currentPath, strlen($basePath));
}
$currentPath = '/' . trim($currentPath, '/');

function bottomNavActive(string $path, string $current): string {
    if ($path === '/') return $current === '/' ? ' active' : '';
    return str_starts_with($current, $path) ? ' active' : '';
}
?>
<nav class="bottom-nav" aria-label="Quick navigation" role="navigation">

    <a href="<?= url('/') ?>" class="bottom-nav__item<?= bottomNavActive('/', $currentPath) ?>" aria-label="Home">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        <span>Home</span>
    </a>

    <a href="<?= url('/shop') ?>" class="bottom-nav__item<?= bottomNavActive('/shop', $currentPath) ?>" aria-label="Shop">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
        <span>Shop</span>
    </a>

    <button class="bottom-nav__item<?= bottomNavActive('/search', $currentPath) ?>" id="bottom-search-btn" aria-label="Search" type="button">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        <span>Search</span>
    </button>

    <a href="<?= url(isLoggedIn() ? '/account' : '/account/login') ?>" class="bottom-nav__item<?= bottomNavActive('/account', $currentPath) ?>" aria-label="Account">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        <span><?= isLoggedIn() ? 'Account' : 'Login' ?></span>
    </a>

    <a href="<?= url('/cart') ?>" class="bottom-nav__item<?= bottomNavActive('/cart', $currentPath) ?>" aria-label="Cart">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
        <span>Cart<?= cartCount() ? ' (' . cartCount() . ')' : '' ?></span>
    </a>

</nav>
