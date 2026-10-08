<?php
/**
 * Order confirmation page — Phase 7
 * Variables: $order, $orderFull
 */
$order = $orderFull ?? $order;
$items = $order['items'] ?? [];
?>

<div style="max-width:680px;margin:0 auto;text-align:center;padding:var(--space-8) 0;">

    <!-- Success icon -->
    <div style="width:72px;height:72px;background:var(--clr-success-bg);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto var(--space-5);">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="36" height="36" style="color:var(--clr-success);" aria-hidden="true">
            <polyline points="20 6 9 17 4 12"/>
        </svg>
    </div>

    <h1 style="font-size:var(--text-2xl);font-weight:800;margin-bottom:var(--space-2);">Order Placed!</h1>
    <p style="color:var(--clr-grey-600);margin-bottom:var(--space-2);">
        Thank you, <strong><?= e($order['first_name']) ?></strong>. Your order has been received.
    </p>
    <p style="font-size:var(--text-sm);color:var(--clr-grey-600);margin-bottom:var(--space-8);">
        Order number: <strong style="color:var(--clr-black);"><?= e($order['order_number']) ?></strong>
    </p>

    <!-- Next step: Pay -->
    <div style="background:var(--clr-accent-light);border:1px solid var(--clr-accent);border-radius:var(--radius-md);padding:var(--space-6);margin-bottom:var(--space-8);">
        <p style="font-weight:700;color:var(--clr-accent);margin-bottom:var(--space-2);">⚡ Complete your payment</p>
        <p style="font-size:var(--text-sm);color:var(--clr-grey-800);margin-bottom:var(--space-4);">
            Your order is reserved but not confirmed until payment is made.
        </p>
        <p style="font-size:var(--text-lg);font-weight:800;margin-bottom:var(--space-4);">
            Total: <?= money((int) $order['total']) ?>
        </p>
        <!-- Phase 8 will wire up Paystack here -->
        <a href="<?= url('/payment/initiate') ?>"
           class="btn btn--primary btn--lg"
           id="pay-now-btn"
           data-order-id="<?= (int) $order['id'] ?>"
           data-order-number="<?= e($order['order_number']) ?>"
           data-amount="<?= (int) $order['total'] ?>"
           data-email="<?= e($order['guest_email'] ?? '') ?>">
            Pay Now with Paystack →
        </a>
    </div>

    <!-- Order summary -->
    <div style="text-align:left;background:var(--clr-white);border:1px solid var(--clr-grey-200);border-radius:var(--radius-md);padding:var(--space-6);margin-bottom:var(--space-6);">
        <h2 style="font-size:var(--text-md);font-weight:700;margin-bottom:var(--space-4);">Order Summary</h2>

        <?php foreach ($items as $item): ?>
        <div style="display:flex;justify-content:space-between;align-items:flex-start;padding:var(--space-3) 0;border-bottom:1px solid var(--clr-grey-100);gap:var(--space-3);">
            <div>
                <p style="font-size:var(--text-sm);font-weight:600;margin:0 0 2px;"><?= e($item['product_name']) ?></p>
                <?php if ($item['variant_size'] || $item['variant_colour']): ?>
                <p style="font-size:var(--text-xs);color:var(--clr-grey-600);margin:0;">
                    <?= $item['variant_size']   ? e($item['variant_size'])   : '' ?>
                    <?= ($item['variant_size'] && $item['variant_colour']) ? ' · ' : '' ?>
                    <?= $item['variant_colour'] ? e($item['variant_colour']) : '' ?>
                    · Qty <?= (int) $item['quantity'] ?>
                </p>
                <?php endif; ?>
            </div>
            <span style="font-size:var(--text-sm);font-weight:700;white-space:nowrap;"><?= money((int) $item['total_price']) ?></span>
        </div>
        <?php endforeach; ?>

        <div style="margin-top:var(--space-4);display:flex;flex-direction:column;gap:var(--space-2);">
            <div style="display:flex;justify-content:space-between;font-size:var(--text-sm);color:var(--clr-grey-700);">
                <span>Subtotal</span><span><?= money((int) $order['subtotal']) ?></span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:var(--text-sm);color:var(--clr-grey-700);">
                <span>Delivery</span>
                <span><?= (int) $order['delivery_fee'] === 0 ? '<span style="color:var(--clr-success)">Free</span>' : money((int) $order['delivery_fee']) ?></span>
            </div>
            <div style="display:flex;justify-content:space-between;font-weight:800;font-size:var(--text-base);padding-top:var(--space-3);border-top:1px solid var(--clr-grey-200);">
                <span>Total</span><span><?= money((int) $order['total']) ?></span>
            </div>
        </div>
    </div>

    <!-- Delivery address -->
    <div style="text-align:left;background:var(--clr-white);border:1px solid var(--clr-grey-200);border-radius:var(--radius-md);padding:var(--space-6);margin-bottom:var(--space-6);">
        <h2 style="font-size:var(--text-md);font-weight:700;margin-bottom:var(--space-3);">Delivering To</h2>
        <p style="font-weight:600;margin:0 0 2px;"><?= e($order['first_name']) ?> <?= e($order['last_name']) ?></p>
        <p style="color:var(--clr-grey-700);margin:0 0 2px;"><?= e($order['address_line1']) ?></p>
        <?php if ($order['address_line2']): ?>
        <p style="color:var(--clr-grey-700);margin:0 0 2px;"><?= e($order['address_line2']) ?></p>
        <?php endif; ?>
        <p style="color:var(--clr-grey-700);margin:0 0 2px;"><?= e($order['city']) ?>, <?= e($order['state']) ?></p>
        <p style="color:var(--clr-grey-600);font-size:var(--text-sm);margin:var(--space-2) 0 0;"><?= e($order['phone']) ?></p>
    </div>

    <div style="display:flex;gap:var(--space-3);justify-content:center;flex-wrap:wrap;">
        <?php if (isLoggedIn()): ?>
        <a href="<?= url('/account/orders/' . (int) $order['id']) ?>" class="btn btn--outline">View Order</a>
        <?php endif; ?>
        <a href="<?= url('/shop') ?>" class="btn btn--ghost">Continue Shopping</a>
    </div>

</div>
