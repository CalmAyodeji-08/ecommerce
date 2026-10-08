<?php
$navPath  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$basePath = parse_url(APP_URL, PHP_URL_PATH) ?? '';
if ($basePath && str_starts_with($navPath, $basePath)) {
    $navPath = substr($navPath, strlen($basePath));
}
$navPath = '/' . trim($navPath, '/');

function accountNavActive(string $path, string $current): string {
    if ($path === '/account') return $current === '/account' ? ' active' : '';
    return str_starts_with($current, $path) ? ' active' : '';
}
?>
<nav class="account-nav" aria-label="Account navigation">
    <a href="<?= url('/account') ?>" class="account-nav__link<?= accountNavActive('/account', $navPath) ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        Dashboard
    </a>
    <a href="<?= url('/account/orders') ?>" class="account-nav__link<?= accountNavActive('/account/orders', $navPath) ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/></svg>
        My Orders
    </a>
    <a href="<?= url('/account/addresses') ?>" class="account-nav__link<?= accountNavActive('/account/addresses', $navPath) ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
        Addresses
    </a>
    <a href="<?= url('/account/wishlist') ?>" class="account-nav__link<?= accountNavActive('/account/wishlist', $navPath) ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
        Wishlist
    </a>
    <a href="<?= url('/account/logout') ?>" class="account-nav__link account-nav__link--danger">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
        Log Out
    </a>
</nav>
