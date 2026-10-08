<?php
/**
 * Product detail page.
 * Variables: $product, $images, $variants, $related
 */

$price      = (int) ($product['price'] ?? 0);
$salePrice  = isset($product['sale_price']) && $product['sale_price'] ? (int) $product['sale_price'] : null;
$isOnSale   = $salePrice !== null && $salePrice < $price;
$displayPrice = $isOnSale ? $salePrice : $price;
$inStock    = (int) ($product['total_stock'] ?? 0) > 0;

// First image is main image
$mainImage  = !empty($images) ? $images[0] : null;
$mainSrc    = $mainImage
    ? (str_starts_with($mainImage['image_path'], 'http')
        ? $mainImage['image_path']
        : url('/images/products/' . $mainImage['image_path']))
    : url('/images/placeholder.svg');
$mainAlt    = $mainImage ? e($mainImage['alt_text'] ?: $product['name']) : e($product['name']);
?>

<!-- Variants JSON for JS -->
<script>const VARIANTS = <?= json_encode($variants['variants'], JSON_HEX_TAG | JSON_HEX_AMP) ?>;</script>

<!-- Breadcrumb -->
<nav class="breadcrumb" aria-label="Breadcrumb">
    <ol>
        <li><a href="<?= url('/') ?>">Home</a></li>
        <?php if (!empty($product['category_slug'])): ?>
        <li><a href="<?= url('/category/' . e($product['category_slug'])) ?>"><?= e($product['category_name'] ?? 'Category') ?></a></li>
        <?php endif; ?>
        <li aria-current="page"><?= e($product['name']) ?></li>
    </ol>
</nav>

<!-- ═══ Product Layout ═══ -->
<div class="product-layout">

    <!-- ── Gallery ───────────────────────────────────────────────────── -->
    <div class="product-gallery">
        <div class="product-gallery__main">
            <img
                id="gallery-main-img"
                src="<?= $mainSrc ?>"
                alt="<?= $mainAlt ?>"
                width="600"
                height="800"
            >
        </div>

        <?php if (count($images) > 1): ?>
        <div class="product-gallery__thumbs" role="list" aria-label="Product images">
            <?php foreach ($images as $i => $img):
                $thumbSrc = str_starts_with($img['image_path'], 'http')
                    ? $img['image_path']
                    : url('/images/products/' . $img['image_path']);
                $thumbAlt = e($img['alt_text'] ?: $product['name']);
            ?>
            <button
                type="button"
                class="product-gallery__thumb <?= $i === 0 ? 'active' : '' ?>"
                data-img-src="<?= $thumbSrc ?>"
                data-img-alt="<?= $thumbAlt ?>"
                onclick="switchGalleryImage(this)"
                aria-label="View image <?= $i + 1 ?>"
                role="listitem"
            >
                <img src="<?= $thumbSrc ?>" alt="<?= $thumbAlt ?>" width="72" height="96" loading="lazy">
            </button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- ── Product info ──────────────────────────────────────────────── -->
    <div class="product-info">

        <!-- Name -->
        <h1 class="product-info__name"><?= e($product['name']) ?></h1>

        <!-- Price -->
        <div class="product-info__price">
            <span class="product-info__price--current <?= $isOnSale ? 'product-info__price--sale' : '' ?>">
                <?= money($displayPrice) ?>
            </span>
            <?php if ($isOnSale): ?>
            <span class="product-info__price--original" aria-label="Original price <?= money($price) ?>">
                <?= money($price) ?>
            </span>
            <?php endif; ?>
        </div>

        <!-- Stock badge -->
        <?php if (!$inStock): ?>
        <p id="stock-badge" class="badge badge--error" style="margin-bottom:var(--space-4);" role="status">Sold Out</p>
        <?php elseif ((int)($product['total_stock'] ?? 0) <= 5): ?>
        <p id="stock-badge" class="badge badge--warning" style="margin-bottom:var(--space-4);" role="status">
            Only <?= (int) $product['total_stock'] ?> left!
        </p>
        <?php else: ?>
        <p id="stock-badge" class="badge badge--success" style="margin-bottom:var(--space-4);" role="status">In Stock</p>
        <?php endif; ?>

        <!-- Add to bag form -->
        <form method="POST" action="<?= url('/cart/add') ?>" id="add-to-cart-form" novalidate>
            <?= csrfField() ?>
            <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
            <input type="hidden" name="variant_id" id="selected-variant-id" value="">

            <!-- Size picker -->
            <?php if (!empty($variants['sizes'])): ?>
            <div class="size-picker">
                <p class="picker-label">
                    Size
                    <span id="selected-size-label" style="font-weight:400; color:var(--clr-grey-600);"></span>
                </p>
                <div class="size-btns" role="group" aria-label="Select a size">
                    <?php foreach ($variants['sizes'] as $sv):
                        $outOfStock = (int)$sv['stock'] === 0;
                    ?>
                    <button
                        type="button"
                        class="size-btn <?= $outOfStock ? 'out-of-stock' : '' ?>"
                        data-size="<?= e($sv['size']) ?>"
                        data-stock="<?= (int) $sv['stock'] ?>"
                        <?= $outOfStock ? 'disabled aria-disabled="true"' : '' ?>
                        aria-label="Size <?= e($sv['size']) ?><?= $outOfStock ? ' — out of stock' : '' ?>"
                    ><?= e($sv['size']) ?></button>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Colour picker -->
            <?php if (!empty($variants['colours'])): ?>
            <div class="colour-picker">
                <p class="picker-label">
                    Colour
                    <span id="selected-colour-label" style="font-weight:400; color:var(--clr-grey-600);"></span>
                </p>
                <div class="colour-btns" role="group" aria-label="Select a colour">
                    <?php foreach ($variants['colours'] as $cv): ?>
                    <button
                        type="button"
                        class="colour-btn"
                        data-colour="<?= e($cv['colour']) ?>"
                        style="background-color: <?= e($cv['colour']) ?>;"
                        aria-label="Colour: <?= e($cv['colour']) ?>"
                        title="<?= e($cv['colour']) ?>"
                    ></button>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Quantity -->
            <div class="qty-wrap">
                <label for="qty-input" style="font-size:var(--text-sm); font-weight:700;">Qty</label>
                <button type="button" class="qty-btn js-qty-minus" aria-label="Decrease quantity">−</button>
                <input
                    id="qty-input"
                    type="number"
                    name="quantity"
                    class="qty-input"
                    value="1"
                    min="1"
                    max="<?= (int) $product['total_stock'] ?>"
                    aria-label="Quantity"
                >
                <button type="button" class="qty-btn js-qty-plus" aria-label="Increase quantity">+</button>
            </div>

            <!-- Add to bag (desktop — inside form, normal flow) -->
            <button
                type="submit"
                id="atb-btn-desktop"
                class="btn btn--primary"
                style="width:100%; display:none;"
                <?= !$inStock ? 'disabled aria-disabled="true"' : '' ?>
            >
                <?= $inStock ? 'Add to Bag' : 'Sold Out' ?>
            </button>
            <button
                type="submit"
                id="atb-btn-main"
                class="btn btn--primary"
                style="width:100%;"
                <?= !$inStock ? 'disabled aria-disabled="true"' : '' ?>
            >
                <?= $inStock ? 'Add to Bag' : 'Sold Out' ?>
            </button>

        </form><!-- /#add-to-cart-form -->

        <!-- Description -->
        <?php if (!empty($product['description'])): ?>
        <div class="product-description" style="margin-top:var(--space-8); padding-top:var(--space-6); border-top:1px solid var(--clr-grey-200);">
            <h2 style="font-size:var(--text-md); font-weight:700; margin-bottom:var(--space-4);">Product Details</h2>
            <div style="font-size:var(--text-sm); color:var(--clr-grey-800); line-height:1.7;">
                <?= nl2br(e($product['description'])) ?>
            </div>
        </div>
        <?php endif; ?>

    </div><!-- /.product-info -->

</div><!-- /.product-layout -->

<!-- ═══ Sticky bottom bar (mobile) ═══ -->
<div class="sticky-atb" aria-hidden="true">
    <div class="sticky-atb__price">
        <?= money($displayPrice) ?>
        <?php if ($isOnSale): ?>
        <span style="font-size:var(--text-xs); color:var(--clr-grey-400); text-decoration:line-through; margin-left:var(--space-1); font-weight:400;"><?= money($price) ?></span>
        <?php endif; ?>
    </div>
    <button
        type="button"
        id="sticky-atb-btn"
        class="btn btn--primary"
        onclick="document.getElementById('add-to-cart-form').requestSubmit ? document.getElementById('add-to-cart-form').requestSubmit() : document.getElementById('add-to-cart-form').submit()"
        <?= !$inStock ? 'disabled aria-disabled="true"' : '' ?>
    >
        <?= $inStock ? 'Add to Bag' : 'Sold Out' ?>
    </button>
</div>

<!-- ═══ Related products ═══ -->
<?php if (!empty($related)): ?>
<section class="section" style="margin-top:var(--space-12);" aria-labelledby="related-heading">
    <div class="section-header">
        <h2 id="related-heading" style="font-size:var(--text-xl);">You May Also Like</h2>
        <?php if (!empty($product['category_slug'])): ?>
        <a href="<?= url('/category/' . e($product['category_slug'])) ?>" class="section-header__link">
            View all <?= e($product['category_name'] ?? '') ?> →
        </a>
        <?php endif; ?>
    </div>
    <div class="product-grid" role="list" aria-label="Related products">
        <?php foreach ($related as $product): ?>
        <div role="listitem">
            <?php require VIEW_PATH . '/partials/product-card.php'; ?>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>
