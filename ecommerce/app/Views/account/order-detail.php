<?php
/**
 * Order detail page — Phase 6
 * Variables: $user (array), $order (array), $items (array)
 */

$statusLabels = [
    'pending_payment' => 'Pending Payment',
    'paid'            => 'Paid',
    'processing'      => 'Processing',
    'shipped'         => 'Shipped',
    'delivered'       => 'Delivered',
    'cancelled'       => 'Cancelled',
    'failed'          => 'Failed',
];
$statusLabel = $statusLabels[$order['status']] ?? ucwords(str_replace('_', ' ', $order['status']));
?>

<div class="account-layout">
    <div>
        <!-- Header -->
        <div style="display:flex;align-items:center;gap:var(--space-4);flex-wrap:wrap;margin-bottom:var(--space-6);">
            <a href="<?= url('/account/orders') ?>"
               style="font-size:var(--text-sm);color:var(--clr-grey-600);display:flex;align-items:center;gap:4px;">
                ← All Orders
            </a>
        </div>

        <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:var(--space-4);margin-bottom:var(--space-6);">
            <div>
                <h1 class="page-title" style="margin-bottom:var(--space-1);">
                    Order <?= e($order['order_number']) ?>
                </h1>
                <p style="color:var(--clr-grey-600);font-size:var(--text-sm);">
                    Placed on <?= e(date('d F Y, g:ia', strtotime($order['created_at']))) ?>
                </p>
            </div>
            <span class="badge status-<?= e($order['status']) ?>"
                  style="font-size:var(--text-sm);padding:var(--space-1) var(--space-3);">
                <?= e($statusLabel) ?>
            </span>
        </div>

        <div style="display:grid;gap:var(--space-6);">

            <!-- ── Order items ── -->
            <section style="background:var(--clr-white);border:1px solid var(--clr-grey-200);border-radius:var(--radius-md);padding:var(--space-6);">
                <h2 style="font-size:var(--text-base);font-weight:700;margin-bottom:var(--space-4);">
                    Items (<?= count($items) ?>)
                </h2>

                <?php if (empty($items)): ?>
                <p style="color:var(--clr-grey-600);">No items found for this order.</p>
                <?php else: ?>
                <div style="display:grid;gap:var(--space-4);">
                    <?php foreach ($items as $item): ?>
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:var(--space-4);padding-bottom:var(--space-4);border-bottom:1px solid var(--clr-grey-100);">
                        <div>
                            <?php if ($item['product_slug']): ?>
                            <a href="<?= url('/product/' . e($item['product_slug'])) ?>"
                               style="font-weight:600;color:var(--clr-grey-900);">
                                <?= e($item['product_name']) ?>
                            </a>
                            <?php else: ?>
                            <span style="font-weight:600;"><?= e($item['product_name']) ?></span>
                            <?php endif; ?>

                            <?php if ($item['variant_size'] || $item['variant_colour']): ?>
                            <p style="font-size:var(--text-sm);color:var(--clr-grey-600);margin:var(--space-1) 0 0;">
                                <?= $item['variant_size']   ? e($item['variant_size'])   : '' ?>
                                <?= ($item['variant_size'] && $item['variant_colour']) ? ' · ' : '' ?>
                                <?= $item['variant_colour'] ? e($item['variant_colour']) : '' ?>
                            </p>
                            <?php endif; ?>

                            <p style="font-size:var(--text-sm);color:var(--clr-grey-600);margin:var(--space-1) 0 0;">
                                Qty: <?= (int) $item['quantity'] ?>
                                &nbsp;·&nbsp;
                                <?= money((int) $item['unit_price']) ?> each
                            </p>
                        </div>
                        <div style="font-weight:700;white-space:nowrap;">
                            <?= money((int) $item['total_price']) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Totals -->
                <div style="margin-top:var(--space-4);display:grid;gap:var(--space-2);">
                    <div style="display:flex;justify-content:space-between;font-size:var(--text-sm);color:var(--clr-grey-700);">
                        <span>Subtotal</span>
                        <span><?= money((int) $order['subtotal']) ?></span>
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:var(--text-sm);color:var(--clr-grey-700);">
                        <span>Delivery</span>
                        <span>
                            <?php if ((int) $order['delivery_fee'] === 0): ?>
                                <span style="color:var(--clr-success);">Free</span>
                            <?php else: ?>
                                <?= money((int) $order['delivery_fee']) ?>
                            <?php endif; ?>
                        </span>
                    </div>
                    <?php if ((int) ($order['discount_amount'] ?? 0) > 0): ?>
                    <div style="display:flex;justify-content:space-between;font-size:var(--text-sm);color:var(--clr-success);">
                        <span>Discount</span>
                        <span>−<?= money((int) $order['discount_amount']) ?></span>
                    </div>
                    <?php endif; ?>
                    <div style="display:flex;justify-content:space-between;font-weight:700;font-size:var(--text-base);padding-top:var(--space-3);border-top:1px solid var(--clr-grey-200);">
                        <span>Total</span>
                        <span><?= money((int) $order['total']) ?></span>
                    </div>
                </div>
            </section>

            <!-- ── Delivery address + Payment ── -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-6);">

                <!-- Delivery address -->
                <section style="background:var(--clr-white);border:1px solid var(--clr-grey-200);border-radius:var(--radius-md);padding:var(--space-6);">
                    <h2 style="font-size:var(--text-base);font-weight:700;margin-bottom:var(--space-3);">
                        Delivery Address
                    </h2>
                    <p style="font-weight:600;margin:0 0 var(--space-1);">
                        <?= e($order['first_name']) ?> <?= e($order['last_name']) ?>
                    </p>
                    <p style="color:var(--clr-grey-700);margin:0 0 2px;"><?= e($order['address_line1']) ?></p>
                    <?php if ($order['address_line2']): ?>
                    <p style="color:var(--clr-grey-700);margin:0 0 2px;"><?= e($order['address_line2']) ?></p>
                    <?php endif; ?>
                    <p style="color:var(--clr-grey-700);margin:0 0 2px;">
                        <?= e($order['city']) ?>, <?= e($order['state']) ?>
                    </p>
                    <p style="color:var(--clr-grey-600);font-size:var(--text-sm);margin:var(--space-2) 0 0;">
                        <?= e($order['phone']) ?>
                    </p>
                </section>

                <!-- Payment info -->
                <section style="background:var(--clr-white);border:1px solid var(--clr-grey-200);border-radius:var(--radius-md);padding:var(--space-6);">
                    <h2 style="font-size:var(--text-base);font-weight:700;margin-bottom:var(--space-3);">
                        Payment
                    </h2>
                    <p style="color:var(--clr-grey-700);margin:0 0 var(--space-2);">
                        <span style="font-weight:600;">Method:</span>
                        <?= $order['payment_method'] ? e(ucwords(str_replace('_', ' ', $order['payment_method']))) : '—' ?>
                    </p>
                    <?php if ($order['payment_reference']): ?>
                    <p style="color:var(--clr-grey-600);font-size:var(--text-xs);margin:0;word-break:break-all;">
                        Ref: <?= e($order['payment_reference']) ?>
                    </p>
                    <?php endif; ?>
                    <p style="margin-top:var(--space-3);">
                        <span class="badge status-<?= e($order['status']) ?>">
                            <?= e($statusLabel) ?>
                        </span>
                    </p>
                </section>

            </div>

            <!-- Notes -->
            <?php if ($order['notes']): ?>
            <section style="background:var(--clr-white);border:1px solid var(--clr-grey-200);border-radius:var(--radius-md);padding:var(--space-6);">
                <h2 style="font-size:var(--text-base);font-weight:700;margin-bottom:var(--space-2);">
                    Order Notes
                </h2>
                <p style="color:var(--clr-grey-700);margin:0;"><?= e($order['notes']) ?></p>
            </section>
            <?php endif; ?>

        </div><!-- /grid -->
    </div>
</div>
