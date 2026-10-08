<?php
/**
 * Search results page
 * Variables: $query (string), $products (array), $pagination (array)
 */
$safeQuery = e($query ?? '');
$hasQuery  = !empty($query);
?>

<!-- Breadcrumb -->
<nav class="breadcrumb" aria-label="Breadcrumb">
    <a href="<?= url('/') ?>">Home</a>
    <span class="breadcrumb__sep">›</span>
    <span class="breadcrumb__current">Search</span>
</nav>

<!-- Search form — always visible, prominent -->
<div class="search-page-header">
    <h1 class="page-title" style="margin-bottom:var(--space-5);">
        <?php if ($hasQuery): ?>
            Results for <em style="color:var(--clr-accent);font-style:italic;">"<?= $safeQuery ?>"</em>
        <?php else: ?>
            Search Products
        <?php endif; ?>
    </h1>

    <form method="GET" action="<?= url('/search') ?>" class="search-page-form" role="search">
        <div class="search-page-form__wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20" aria-hidden="true" class="search-page-form__icon">
                <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
            </svg>
            <label for="search-page-q" class="sr-only">Search products</label>
            <input
                type="search"
                id="search-page-q"
                name="q"
                value="<?= $safeQuery ?>"
                class="search-page-form__input"
                placeholder="Search for tees, hoodies, caps, joggers…"
                autocomplete="off"
                autofocus
                aria-label="Search products"
            >
            <?php if ($hasQuery): ?>
            <a href="<?= url('/search') ?>" class="search-page-form__clear" aria-label="Clear search">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </a>
            <?php endif; ?>
            <button type="submit" class="btn btn--primary">Search</button>
        </div>
        <?php if ($hasQuery && $pagination['total_items'] > 0): ?>
        <p class="search-page-form__count">
            <?= (int)$pagination['total_items'] ?> result<?= $pagination['total_items'] !== 1 ? 's' : '' ?> found
        </p>
        <?php endif; ?>
    </form>
</div>

<?php if (!$hasQuery): ?>
<!-- No query yet — show suggestions -->
<div style="margin-top:var(--space-8);">
    <p style="color:var(--clr-grey-600);font-size:var(--text-sm);margin-bottom:var(--space-4);">Popular searches:</p>
    <div style="display:flex;flex-wrap:wrap;gap:var(--space-2);">
        <?php foreach (['Hoodies','Black Tee','Caps','Joggers','Sets','White Tee','Oversized'] as $tag): ?>
        <a href="<?= url('/search') ?>?q=<?= urlencode($tag) ?>" class="filter-chip"><?= e($tag) ?></a>
        <?php endforeach; ?>
    </div>
</div>

<?php elseif (!empty($products)): ?>
<!-- Results grid -->
<div class="product-grid" role="list" aria-label="Search results" style="margin-top:var(--space-6);">
    <?php foreach ($products as $product): ?>
    <div role="listitem"><?php require VIEW_PATH . '/partials/product-card.php'; ?></div>
    <?php endforeach; ?>
</div>
<?php if ($pagination['total_pages'] > 1): ?>
    <?php require VIEW_PATH . '/partials/pagination.php'; ?>
<?php endif; ?>

<?php else: ?>
<!-- No results -->
<div style="text-align:center;padding:var(--space-16) var(--space-4);">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="64" height="64" style="color:var(--clr-grey-300);margin-bottom:var(--space-4);" aria-hidden="true">
        <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
        <line x1="8" y1="11" x2="14" y2="11"/>
    </svg>
    <h2 style="font-size:var(--text-lg);margin-bottom:var(--space-2);">No results for "<?= $safeQuery ?>"</h2>
    <p style="color:var(--clr-grey-600);margin-bottom:var(--space-6);max-width:400px;margin-left:auto;margin-right:auto;">
        Try a different search term or check for typos.
    </p>
    <div style="display:flex;gap:var(--space-3);justify-content:center;flex-wrap:wrap;margin-bottom:var(--space-8);">
        <a href="<?= url('/shop') ?>" class="btn btn--primary">Browse All Products</a>
    </div>
    <p style="color:var(--clr-grey-600);font-size:var(--text-sm);margin-bottom:var(--space-3);">Try searching for:</p>
    <div style="display:flex;flex-wrap:wrap;gap:var(--space-2);justify-content:center;">
        <?php foreach (['Hoodies','Black Tee','Caps','Joggers','Sets'] as $tag): ?>
        <a href="<?= url('/search') ?>?q=<?= urlencode($tag) ?>" class="filter-chip"><?= e($tag) ?></a>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>
