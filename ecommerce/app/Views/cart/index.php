<?php
/**
 * Cart page — Phase 5
 * Variables: $cartItems, $totals, $deliveryZones
 */
$subtotal = $totals['subtotal'] ?? 0;
$count    = $totals['count']    ?? 0;
?>

<!-- Delivery zones for JS -->
<script>
const DELIVERY_ZONES = <?= json_encode(
    array_column($deliveryZones, 'fee', 'state'),
    JSON_HEX_TAG | JSON_HEX_AMP
) ?>;
const FREE_DELIVERY_THRESHOLD = <?= freeDeliveryThreshold() ?>;
</script>

<!-- Breadcrumb -->
<nav class="breadcrumb" aria-label="Breadcrumb">
    <a href="<?= url('/') ?>">Home</a>
    <span class="breadcrumb__sep">›</span>
    <span class="breadcrumb__current">Your Bag</span>
</nav>

<h1 class="page-title">Your Bag <?php if ($count > 0): ?><span style="font-size:var(--text-base);font-weight:500;color:var(--clr-grey-600);">(<?= $count ?> item<?= $count !== 1 ? 's' : '' ?>)</span><?php endif; ?></h1>

<?php if (empty($cartItems)): ?>
<!-- ── Empty cart ── -->
<div class="empty-state">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
        <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
        <line x1="3" y1="6" x2="21" y2="6"/>
        <path d="M16 10a4 4 0 01-8 0"/>
    </svg>
    <h3>Your bag is empty</h3>
    <p>Looks like you haven't added anything yet.</p>
    <a href="<?= url('/shop') ?>" class="btn btn--primary" style="margin-top:var(--space-4);">Start Shopping</a>
</div>

<?php else: ?>
<!-- ── Cart layout ── -->
<div class="cart-layout">

    <!-- ── Items ── -->
    <div class="cart-items-wrap">
        <div id="cart-items-list">
        <?php foreach ($cartItems as $item):
            $lineTotal = (int) $item['price_at_add'] * (int) $item['quantity'];
            $imgSrc    = $item['primary_image']
                ? (str_starts_with($item['primary_image'], 'http')
                    ? $item['primary_image']
                    : url('/images/products/' . $item['primary_image']))
                : url('/images/placeholder.svg');
        ?>
        <div class="cart-item" id="cart-item-<?= (int) $item['id'] ?>">

            <!-- Image -->
            <a href="<?= url('/product/' . e($item['slug'])) ?>" class="cart-item__img" tabindex="-1" aria-hidden="true">
                <img src="<?= $imgSrc ?>" alt="<?= e($item['name']) ?>" width="80" height="106" loading="lazy">
            </a>

            <!-- Details -->
            <div>
                <a href="<?= url('/product/' . e($item['slug'])) ?>" class="cart-item__name"><?= e($item['name']) ?></a>
                <?php if ($item['size'] || $item['colour']): ?>
                <p class="cart-item__meta">
                    <?= $item['size']   ? e($item['size'])   : '' ?>
                    <?= ($item['size'] && $item['colour']) ? ' · ' : '' ?>
                    <?= $item['colour'] ? e($item['colour']) : '' ?>
                </p>
                <?php endif; ?>

                <div class="cart-item__row2">
                    <!-- Quantity controls -->
                    <form class="js-update-form" data-item-id="<?= (int) $item['id'] ?>" method="POST" action="<?= url('/cart/update') ?>">
                        <?= csrfField() ?>
                        <input type="hidden" name="cart_item_id" value="<?= (int) $item['id'] ?>">
                        <div class="cart-item__qty">
                            <button type="button" class="cart-item__qty-btn js-qty-minus" aria-label="Decrease quantity">−</button>
                            <input
                                type="number"
                                name="quantity"
                                class="cart-item__qty-input js-qty-input"
                                value="<?= (int) $item['quantity'] ?>"
                                min="1"
                                max="<?= $item['variant_id'] ? (int)$item['variant_stock'] : (int)$item['total_stock'] ?>"
                                aria-label="Quantity for <?= e($item['name']) ?>"
                            >
                            <button type="button" class="cart-item__qty-btn js-qty-plus" aria-label="Increase quantity">+</button>
                        </div>
                    </form>

                    <!-- Line total + remove -->
                    <div style="display:flex;align-items:center;gap:var(--space-4);">
                        <span class="cart-item__price js-line-total" data-item-id="<?= (int) $item['id'] ?>" data-price-at-add="<?= (int) $item['price_at_add'] ?>"><?= money($lineTotal) ?></span>
                        <form class="js-remove-form" method="POST" action="<?= url('/cart/remove') ?>">
                            <?= csrfField() ?>
                            <input type="hidden" name="cart_item_id" value="<?= (int) $item['id'] ?>">
                            <button type="submit" class="cart-item__remove" aria-label="Remove <?= e($item['name']) ?> from bag">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16" aria-hidden="true"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
        <?php endforeach; ?>
        </div>

        <a href="<?= url('/shop') ?>" style="display:inline-flex;align-items:center;gap:var(--space-2);font-size:var(--text-sm);color:var(--clr-grey-600);margin-top:var(--space-4);">
            ← Continue Shopping
        </a>
    </div>

    <!-- ── Order summary ── -->
    <aside class="order-summary" aria-label="Order summary">
        <h2 class="order-summary__title">Order Summary</h2>

        <div class="order-summary__row">
            <span>Subtotal</span>
            <span id="summary-subtotal"><?= money($subtotal) ?></span>
        </div>

        <!-- Delivery estimate -->
        <div style="margin:var(--space-4) 0;">
            <label for="delivery-state" style="font-size:var(--text-sm);font-weight:600;display:block;margin-bottom:var(--space-2);">
                Estimate Delivery
            </label>
            <select id="delivery-state" class="form-control order-summary__delivery-select" aria-label="Select your state for delivery estimate">
                <option value="">— Select your state —</option>
                <?php foreach ($deliveryZones as $zone): ?>
                <option value="<?= e($zone['state']) ?>" data-fee="<?= (int) $zone['fee'] ?>">
                    <?= e($zone['state']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="order-summary__row">
            <span>Delivery</span>
            <span id="summary-delivery">—</span>
        </div>

        <div class="order-summary__row order-summary__row--total">
            <span>Total</span>
            <span id="summary-total"><?= money($subtotal) ?></span>
        </div>

        <?php if ($subtotal < freeDeliveryThreshold()): ?>
        <p style="font-size:var(--text-xs);color:var(--clr-accent);margin:var(--space-3) 0;text-align:center;">
            Add <?= money(freeDeliveryThreshold() - $subtotal) ?> more for free delivery!
        </p>
        <?php else: ?>
        <p style="font-size:var(--text-xs);color:var(--clr-success);margin:var(--space-3) 0;text-align:center;">
            ✅ You qualify for free delivery!
        </p>
        <?php endif; ?>

        <a href="<?= url('/checkout') ?>" class="btn btn--primary btn--full" style="margin-top:var(--space-4);">
            Proceed to Checkout →
        </a>

        <!-- Trust badges -->
        <div style="display:flex;justify-content:center;gap:var(--space-4);margin-top:var(--space-5);flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:var(--space-1);font-size:var(--text-xs);color:var(--clr-grey-600);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                Secure Payment
            </div>
            <div style="display:flex;align-items:center;gap:var(--space-1);font-size:var(--text-xs);color:var(--clr-grey-600);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14" aria-hidden="true"><rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 5v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                Fast Delivery
            </div>
        </div>
    </aside>

</div><!-- /.cart-layout -->
<?php endif; ?>
