<?php
/**
 * Wishlist page — Phase 6
 * Variables: $user (array), $wishlistItems (array)
 */
?>

<div style="display:flex;align-items:center;gap:var(--space-3);margin-bottom:var(--space-6);">
    <a href="<?= url('/account') ?>" style="font-size:var(--text-sm);color:var(--clr-grey-600);display:flex;align-items:center;gap:4px;">← My Account</a>
</div>

<h1 class="page-title" style="margin-bottom:var(--space-6);">
    My Wishlist
    <?php if (!empty($wishlistItems)): ?>
    <span style="font-size:var(--text-base);font-weight:500;color:var(--clr-grey-600);">
        (<?= count($wishlistItems) ?> item<?= count($wishlistItems) !== 1 ? 's' : '' ?>)
    </span>
    <?php endif; ?>
</h1>

        <?php if (empty($wishlistItems)): ?>
        <!-- ── Empty state ── -->
        <div class="empty-state" style="padding:var(--space-10) var(--space-4);">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>
            </svg>
            <h3>Your wishlist is empty</h3>
            <p>Save items you love by tapping the heart icon on any product.</p>
            <a href="<?= url('/shop') ?>" class="btn btn--primary" style="margin-top:var(--space-4);">
                Browse Products
            </a>
        </div>

        <?php else: ?>
        <!-- ── Product grid ── -->
        <div class="product-grid">
            <?php foreach ($wishlistItems as $product): ?>
            <div style="position:relative;">
                <?php require VIEW_PATH . '/partials/product-card.php'; ?>

                <!-- Remove from wishlist button -->
                <form method="POST" action="<?= url('/account/wishlist/toggle') ?>"
                      class="js-wishlist-remove-form"
                      style="position:absolute;top:var(--space-2);left:var(--space-2);z-index:2;">
                    <?= csrfField() ?>
                    <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                    <button type="submit"
                            class="btn btn--ghost btn--sm"
                            style="background:rgba(255,255,255,.9);border:1px solid var(--clr-grey-200);border-radius:var(--radius-sm);font-size:var(--text-xs);padding:4px 10px;"
                            aria-label="Remove <?= e($product['name']) ?> from wishlist">
                        <svg viewBox="0 0 24 24" fill="currentColor" stroke="none" width="12" height="12"
                             style="vertical-align:middle;margin-right:3px;color:var(--clr-error);" aria-hidden="true">
                            <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>
                        </svg>
                        Remove
                    </button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
