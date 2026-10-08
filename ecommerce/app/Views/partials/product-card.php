<?php
/**
 * Product card partial.
 * Expects: $product array with keys:
 *   id, name, slug, price, sale_price, total_stock, primary_image, image_alt
 */

$price        = (int) ($product['price'] ?? 0);
$salePrice    = isset($product['sale_price']) && $product['sale_price'] ? (int) $product['sale_price'] : null;
$stock        = (int) ($product['total_stock'] ?? 0);
$isSoldOut    = $stock === 0;
$isOnSale     = $salePrice !== null && $salePrice < $price;
$displayPrice = $isOnSale ? $salePrice : $price;

$image   = $product['primary_image'] ?? null;
$imgAlt  = e($product['image_alt'] ?? $product['name']);
$imgSrc  = $image
    ? (str_starts_with($image, 'http') ? $image : url('/images/products/' . $image))
    : url('/images/placeholder.svg');

// Product URL — guests are sent to login first, then redirected back
$productUrl = url('/product/' . e($product['slug']));
$cardUrl    = isLoggedIn()
    ? $productUrl
    : url('/account/login') . '?redirect=' . urlencode($productUrl);
?>
<article class="product-card" aria-label="<?= e($product['name']) ?>">
    <a href="<?= $cardUrl ?>" class="product-card__image-wrap">
        <img
            src="<?= $imgSrc ?>"
            alt="<?= $imgAlt ?>"
            width="400"
            height="533"
            loading="lazy"
        >

        <?php if ($isSoldOut): ?>
            <span class="product-card__badge product-card__badge--sold-out">Sold Out</span>
        <?php elseif ($isOnSale): ?>
            <span class="product-card__badge product-card__badge--sale">Sale</span>
        <?php endif; ?>

        <?php if (isLoggedIn()): ?>
        <button
            class="product-card__wishlist js-wishlist-btn"
            data-product-id="<?= (int) $product['id'] ?>"
            data-toggle-url="<?= url('/account/wishlist/toggle') ?>"
            data-login-url="<?= url('/account/login') ?>"
            aria-label="Add <?= e($product['name']) ?> to wishlist"
            type="button"
        >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
            </svg>
        </button>
        <?php endif; ?>
    </a>

    <div class="product-card__body">
        <a href="<?= $cardUrl ?>" class="product-card__name">
            <?= e($product['name']) ?>
        </a>
        <div class="product-card__price">
            <span class="product-card__price--current <?= $isOnSale ? 'product-card__price--sale' : '' ?>">
                <?= money($displayPrice) ?>
            </span>
            <?php if ($isOnSale): ?>
                <span class="product-card__price--original" aria-label="Original price">
                    <?= money($price) ?>
                </span>
            <?php endif; ?>
        </div>
    </div>
</article>
