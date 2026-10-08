<?php
/**
 * Account dashboard — Phase 6
 * Variables: $user (array), $recentOrders (array)
 */
?>

<h1 class="page-title" style="margin-bottom:var(--space-6);">
    Welcome back, <?= e($user['first_name']) ?>!
</h1>

        <!-- Quick stats -->
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:var(--space-4);margin-bottom:var(--space-8);">
            <div style="background:var(--clr-white);border:1px solid var(--clr-grey-200);border-radius:var(--radius-md);padding:var(--space-5);text-align:center;">
                <div style="font-size:var(--text-2xl);font-weight:800;color:var(--clr-accent);"><?= count($recentOrders ?? []) ?></div>
                <div style="font-size:var(--text-sm);color:var(--clr-grey-600);margin-top:var(--space-1);">Recent Orders</div>
            </div>
            <div style="background:var(--clr-white);border:1px solid var(--clr-grey-200);border-radius:var(--radius-md);padding:var(--space-5);text-align:center;">
                <div style="font-size:var(--text-2xl);font-weight:800;color:var(--clr-accent);">
                    <?= date('M Y', strtotime($user['created_at'])) ?>
                </div>
                <div style="font-size:var(--text-sm);color:var(--clr-grey-600);margin-top:var(--space-1);">Member Since</div>
            </div>
        </div>

        <!-- Recent orders -->
        <section>
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:var(--space-4);">
                <h2 style="font-size:var(--text-lg);font-weight:700;">Recent Orders</h2>
                <a href="<?= url('/account/orders') ?>" style="font-size:var(--text-sm);color:var(--clr-accent);font-weight:600;">
                    View all →
                </a>
            </div>

            <?php if (empty($recentOrders)): ?>
            <div class="empty-state" style="padding:var(--space-8) var(--space-4);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <h3>No orders yet</h3>
                <p>When you place an order it will appear here.</p>
                <a href="<?= url('/shop') ?>" class="btn btn--primary" style="margin-top:var(--space-4);">
                    Start Shopping
                </a>
            </div>
            <?php else: ?>
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $order): ?>
                        <tr>
                            <td><strong><?= e($order['order_number']) ?></strong></td>
                            <td><?= e(date('d M Y', strtotime($order['created_at']))) ?></td>
                            <td>
                                <span class="badge status-<?= e($order['status']) ?>">
                                    <?= e(ucfirst(str_replace('_', ' ', $order['status']))) ?>
                                </span>
                            </td>
                            <td><?= money((int) $order['total']) ?></td>
                            <td>
                                <a href="<?= url('/account/orders/' . (int) $order['id']) ?>"
                                   style="font-size:var(--text-sm);color:var(--clr-accent);font-weight:600;">
                                    View
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </section>

        <!-- Quick links -->
        <section style="margin-top:var(--space-8);">
            <h2 style="font-size:var(--text-lg);font-weight:700;margin-bottom:var(--space-4);">Quick Links</h2>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:var(--space-4);">
                <a href="<?= url('/account/orders') ?>" class="account-quick-link">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    My Orders
                </a>
                <a href="<?= url('/account/addresses') ?>" class="account-quick-link">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                    Addresses
                </a>
                <a href="<?= url('/account/wishlist') ?>" class="account-quick-link">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>
                    </svg>
                    Wishlist
                </a>
                <a href="<?= url('/account/logout') ?>" class="account-quick-link" style="color:var(--clr-error);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/>
                    </svg>
                    Log Out
                </a>
            </div>
        </section>
