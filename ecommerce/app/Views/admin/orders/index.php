<?php $sb = $statusBadge; ?>
<div class="admin-card">
    <div class="admin-card__header">
        <form method="GET" action="<?= url('/admin/orders') ?>" class="admin-search-bar">
            <input type="search" name="search" value="<?= e($search) ?>" placeholder="Order # or customer…" class="admin-input" style="max-width:200px;">
            <select name="status" class="admin-select" style="width:170px;height:36px;">
                <option value="">All Statuses</option>
                <?php foreach (['pending_payment','paid','processing','shipped','delivered','cancelled','failed'] as $s): ?>
                <option value="<?= $s ?>" <?= $status===$s?'selected':'' ?>><?= ucwords(str_replace('_',' ',$s)) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="admin-btn admin-btn--outline">Filter</button>
            <?php if ($search||$status): ?><a href="<?= url('/admin/orders') ?>" class="admin-btn admin-btn--outline">Clear</a><?php endif; ?>
        </form>
        <span style="font-size:0.8rem;color:#64748b;"><?= $pagination['total_items'] ?> orders</span>
    </div>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>Order #</th><th>Date</th><th>Customer</th><th>Status</th><th>Total</th><th></th></tr></thead>
            <tbody>
            <?php if (empty($orders)): ?>
                <tr><td colspan="6" style="text-align:center;color:#64748b;padding:2rem;">No orders found</td></tr>
            <?php else: foreach ($orders as $o): ?>
                <tr>
                    <td><strong><?= e($o['order_number']) ?></strong></td>
                    <td style="white-space:nowrap;"><?= date('d M Y', strtotime($o['created_at'])) ?></td>
                    <td><?= e($o['customer_name']) ?></td>
                    <td><?= $sb($o['status']) ?></td>
                    <td><?= money((int)$o['total']) ?></td>
                    <td><a href="<?= url('/admin/orders/'.$o['id']) ?>" class="admin-btn admin-btn--outline admin-btn--sm">View</a></td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <?php if ($pagination['total_pages'] > 1): ?>
    <div style="padding:1rem 1.25rem;display:flex;gap:0.5rem;justify-content:center;flex-wrap:wrap;">
        <?php for ($i=1;$i<=$pagination['total_pages'];$i++): ?>
        <a href="?page=<?= $i ?><?= $status?'&status='.urlencode($status):'' ?><?= $search?'&search='.urlencode($search):'' ?>"
           style="padding:0.375rem 0.75rem;border:1.5px solid <?= $i==$pagination['current']?'var(--admin-accent)':'#cbd5e1' ?>;border-radius:6px;background:<?= $i==$pagination['current']?'var(--admin-accent)':'transparent' ?>;color:<?= $i==$pagination['current']?'#fff':'#334155' ?>;font-size:0.8rem;font-weight:600;">
            <?= $i ?>
        </a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>
