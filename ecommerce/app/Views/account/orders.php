<?php
/**
 * Orders list page — Phase 6
 * Variables: $user (array), $orders (array)
 */
?>

<div style="display:flex;align-items:center;gap:var(--space-3);margin-bottom:var(--space-6);">
    <a href="<?= url('/account') ?>" style="font-size:var(--text-sm);color:var(--clr-grey-600);display:flex;align-items:center;gap:4px;">← My Account</a>
</div>

<h1 class="page-title" style="margin-bottom:var(--space-6);">My Orders</h1>

        <?php if (empty($orders)): ?>
        <div class="empty-state" style="padding:var(--space-10) var(--space-4);">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <h3>No orders yet</h3>
            <p>Looks like you haven't placed any orders. Start shopping!</p>
            <a href="<?= url('/shop') ?>" class="btn btn--primary" style="margin-top:var(--space-4);">
                Shop Now
            </a>
        </div>
        <?php else: ?>

        <!-- Desktop table -->
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th scope="col">Order #</th>
                        <th scope="col">Date</th>
                        <th scope="col">Items</th>
                        <th scope="col">Status</th>
                        <th scope="col">Total</th>
                        <th scope="col"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>
                            <strong><?= e($order['order_number']) ?></strong>
                        </td>
                        <td><?= e(date('d M Y', strtotime($order['created_at']))) ?></td>
                        <td><?= (int) $order['item_count'] ?> item<?= (int) $order['item_count'] !== 1 ? 's' : '' ?></td>
                        <td>
                            <span class="badge status-<?= e($order['status']) ?>">
                                <?= e(ucwords(str_replace('_', ' ', $order['status']))) ?>
                            </span>
                        </td>
                        <td><?= money((int) $order['total']) ?></td>
                        <td>
                            <a href="<?= url('/account/orders/' . (int) $order['id']) ?>"
                               style="font-size:var(--text-sm);color:var(--clr-accent);font-weight:600;white-space:nowrap;">
                                View Details
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php endif; ?>
