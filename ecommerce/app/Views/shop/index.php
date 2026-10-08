<?php
/**
 * Shop index view — all products, filterable.
 */
$currentSort     = $sort ?? 'newest';
$currentFilters  = $filters ?? [];
$currentSize     = $currentFilters['size']   ?? '';
$currentMinPrice = $_GET['min_price'] ?? '';
$currentMaxPrice = $_GET['max_price'] ?? '';
$baseUrl         = url('/shop');
?>

<!-- Breadcrumb -->
<nav class="breadcrumb" aria-label="Breadcrumb">
    <a href="<?= url('/') ?>">Home</a>
    <span class="breadcrumb__sep">›</span>
    <span class="breadcrumb__current">Shop</span>
</nav>

<div class="shop-page-header">
    <div>
        <h1 class="page-title" style="margin-bottom:0;">All Products</h1>
        <?php if ($pagination['total_items'] > 0): ?>
        <p class="shop-result-count"><?= (int)$pagination['total_items'] ?> product<?= $pagination['total_items'] !== 1 ? 's' : '' ?></p>
        <?php endif; ?>
    </div>

    <!-- Professional sort bar -->
    <form method="GET" action="<?= $baseUrl ?>" class="sort-form" id="sort-form-top">
        <?php if (!empty($currentSize)):    ?><input type="hidden" name="size"      value="<?= e($currentSize) ?>"><?php endif; ?>
        <?php if ($currentMinPrice !== ''): ?><input type="hidden" name="min_price" value="<?= e($currentMinPrice) ?>"><?php endif; ?>
        <?php if ($currentMaxPrice !== ''): ?><input type="hidden" name="max_price" value="<?= e($currentMaxPrice) ?>"><?php endif; ?>
        <label for="sort-select" class="sort-form__label">Sort by</label>
        <div class="sort-form__select-wrap">
            <select id="sort-select" name="sort" onchange="this.form.submit()" aria-label="Sort products" class="sort-form__select">
                <option value="newest"     <?= $currentSort === 'newest'     ? 'selected' : '' ?>>Newest</option>
                <option value="price_asc"  <?= $currentSort === 'price_asc'  ? 'selected' : '' ?>>Price: Low → High</option>
                <option value="price_desc" <?= $currentSort === 'price_desc' ? 'selected' : '' ?>>Price: High → Low</option>
                <option value="name_asc"   <?= $currentSort === 'name_asc'   ? 'selected' : '' ?>>Name A–Z</option>
            </select>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
        </div>
    </form>
</div>

<!-- Mobile filter toggle -->
<button class="filter-mobile-btn js-filter-toggle" type="button" aria-expanded="false" aria-controls="filter-sidebar">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15" aria-hidden="true"><line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="20" y2="12"/><line x1="12" y1="18" x2="20" y2="18"/></svg>
    Filters<?php if (!empty($currentSize) || $currentMinPrice !== '' || $currentMaxPrice !== ''): ?> <span class="filter-active-dot"></span><?php endif; ?>
</button>

<div class="shop-layout">

    <!-- ── Filter sidebar ── -->
    <aside id="filter-sidebar" class="filter-sidebar" aria-label="Product filters">
        <form method="GET" action="<?= $baseUrl ?>" id="filter-form">

            <!-- Sort (sidebar version — hidden on desktop since we have top bar) -->
            <div class="filter-group filter-group--desktop-hide">
                <p class="filter-group__title">Sort By</p>
                <select name="sort" onchange="this.form.submit()" class="filter-select" aria-label="Sort">
                    <option value="newest"     <?= $currentSort === 'newest'     ? 'selected' : '' ?>>Newest</option>
                    <option value="price_asc"  <?= $currentSort === 'price_asc'  ? 'selected' : '' ?>>Price: Low to High</option>
                    <option value="price_desc" <?= $currentSort === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                    <option value="name_asc"   <?= $currentSort === 'name_asc'   ? 'selected' : '' ?>>Name A–Z</option>
                </select>
                <?php if ($currentMinPrice !== ''): ?><input type="hidden" name="min_price" value="<?= e($currentMinPrice) ?>"><?php endif; ?>
                <?php if ($currentMaxPrice !== ''): ?><input type="hidden" name="max_price" value="<?= e($currentMaxPrice) ?>"><?php endif; ?>
                <?php if (!empty($currentSize)):    ?><input type="hidden" name="size"      value="<?= e($currentSize) ?>"><?php endif; ?>
            </div>

            <!-- Size — click to select, submit with Apply button -->
            <div class="filter-group">
                <p class="filter-group__title">Size</p>
                <div class="filter-chips" role="group" aria-label="Filter by size" id="size-chips">
                    <?php foreach (['XS','S','M','L','XL','XXL'] as $sz): ?>
                    <button
                        type="button"
                        class="filter-chip js-size-chip <?= $currentSize === $sz ? 'active' : '' ?>"
                        data-size="<?= e($sz) ?>"
                        aria-pressed="<?= $currentSize === $sz ? 'true' : 'false' ?>"
                    ><?= e($sz) ?></button>
                    <?php endforeach; ?>
                </div>
                <!-- Hidden input updated by JS -->
                <input type="hidden" name="size" id="size-input" value="<?= e($currentSize) ?>">
                <?php if (!empty($currentSort) && $currentSort !== 'newest'): ?>
                <input type="hidden" name="sort" value="<?= e($currentSort) ?>">
                <?php endif; ?>
                <?php if ($currentMinPrice !== ''): ?><input type="hidden" name="min_price" value="<?= e($currentMinPrice) ?>"><?php endif; ?>
                <?php if ($currentMaxPrice !== ''): ?><input type="hidden" name="max_price" value="<?= e($currentMaxPrice) ?>"><?php endif; ?>
            </div>

            <!-- Price range -->
            <div class="filter-group">
                <p class="filter-group__title">Price Range</p>
                <div class="price-range-row">
                    <input type="number" name="min_price" placeholder="Min ₦" value="<?= $currentMinPrice !== '' ? e($currentMinPrice) : '' ?>" min="0" step="500" class="price-input" aria-label="Min price">
                    <span class="price-range-sep">–</span>
                    <input type="number" name="max_price" placeholder="Max ₦" value="<?= $currentMaxPrice !== '' ? e($currentMaxPrice) : '' ?>" min="0" step="500" class="price-input" aria-label="Max price">
                </div>
                <?php if (!empty($currentSize)):    ?><input type="hidden" name="size"  value="<?= e($currentSize) ?>"><?php endif; ?>
                <?php if (!empty($currentSort) && $currentSort !== 'newest'): ?>
                <input type="hidden" name="sort" value="<?= e($currentSort) ?>">
                <?php endif; ?>
            </div>

            <!-- Apply button -->
            <button type="submit" class="btn btn--primary btn--full" style="margin-top:var(--space-2);">Apply Filters</button>

            <!-- Clear all -->
            <?php if (!empty($currentSize) || $currentMinPrice !== '' || $currentMaxPrice !== '' || $currentSort !== 'newest'): ?>
            <a href="<?= $baseUrl ?>" class="btn btn--ghost btn--full" style="margin-top:var(--space-2);text-align:center;">Clear All</a>
            <?php endif; ?>

        </form>
    </aside>

    <!-- ── Product grid ── -->
    <div>
        <?php if (!empty($products)): ?>
        <div class="product-grid" role="list">
            <?php foreach ($products as $product): ?>
            <div role="listitem"><?php require VIEW_PATH . '/partials/product-card.php'; ?></div>
            <?php endforeach; ?>
        </div>
        <?php if ($pagination['total_pages'] > 1): ?>
            <?php require VIEW_PATH . '/partials/pagination.php'; ?>
        <?php endif; ?>
        <?php else: ?>
        <div style="text-align:center;padding:var(--space-16) var(--space-4);">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="56" height="56" style="color:var(--clr-grey-300);margin-bottom:var(--space-4);" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <h2 style="font-size:var(--text-lg);margin-bottom:var(--space-2);">No products found</h2>
            <p style="color:var(--clr-grey-600);margin-bottom:var(--space-6);">Try adjusting your filters.</p>
            <a href="<?= $baseUrl ?>" class="btn btn--primary">Clear Filters</a>
        </div>
        <?php endif; ?>
    </div>

</div>
