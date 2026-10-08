<?php $sb = $statusBadge; ?>
<!-- Stats -->
<div class="admin-stats">
    <div class="admin-stat-card">
        <div class="admin-stat-card__label">Orders Today</div>
        <div class="admin-stat-card__value"><?= $ordersToday ?></div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-card__label">Sales Today</div>
        <div class="admin-stat-card__value"><?= money($salesToday) ?></div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-card__label">Total Orders</div>
        <div class="admin-stat-card__value"><?= $ordersAll ?></div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-card__label">Total Sales</div>
        <div class="admin-stat-card__value"><?= money($salesAll) ?></div>
    </div>
</div>

<!-- Recent Orders -->
<div class="admin-card">
    <div class="admin-card__header">
        <span class="admin-card__title">Recent Orders</span>
        <a href="<?= url('/admin/orders') ?>" class="admin-btn admin-btn--outline admin-btn--sm">View All</a>
    </div>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>Order #</th><th>Customer</th><th>Date</th><th>Status</th><th>Total</th><th></th></tr></thead>
            <tbody>
            <?php if (empty($recentOrders)): ?>
                <tr><td colspan="6" style="text-align:center;color:#64748b;padding:2rem;">No orders yet</td></tr>
            <?php else: foreach ($recentOrders as $o): ?>
                <tr>
                    <td><strong><?= e($o['order_number']) ?></strong></td>
                    <td><?= e($o['customer_name']) ?></td>
                    <td><?= date('d M Y', strtotime($o['created_at'])) ?></td>
                    <td><?= $sb($o['status']) ?></td>
                    <td><?= money((int)$o['total']) ?></td>
                    <td><a href="<?= url('/admin/orders/'.$o['id']) ?>" class="admin-btn admin-btn--outline admin-btn--sm">View</a></td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Low stock -->
<?php if (!empty($lowStock)): ?>
<div class="admin-card">
    <div class="admin-card__header">
        <span class="admin-card__title">⚠️ Low Stock Alert</span>
    </div>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>Product</th><th>Stock Left</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($lowStock as $p): ?>
                <tr>
                    <td><?= e($p['name']) ?></td>
                    <td><span class="admin-badge admin-badge--<?= $p['total_stock'] == 0 ? 'red' : 'yellow' ?>"><?= $p['total_stock'] ?> left</span></td>
                    <td><a href="<?= url('/admin/products/edit/'.$p['id']) ?>" class="admin-btn admin-btn--outline admin-btn--sm">Edit</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
