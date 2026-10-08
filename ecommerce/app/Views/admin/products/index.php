<div class="admin-card">
    <div class="admin-card__header">
        <form method="GET" action="<?= url('/admin/products') ?>" class="admin-search-bar">
            <input type="search" name="search" value="<?= e($search) ?>" placeholder="Search products…" class="admin-input" style="max-width:240px;">
            <button type="submit" class="admin-btn admin-btn--outline">Search</button>
            <?php if ($search): ?><a href="<?= url('/admin/products') ?>" class="admin-btn admin-btn--outline">Clear</a><?php endif; ?>
        </form>
        <a href="<?= url('/admin/products/add') ?>" class="admin-btn admin-btn--primary">+ Add Product</a>
    </div>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>Product</th><th>Category</th><th>Price</th><th>Sale Price</th><th>Stock</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            <?php if (empty($products)): ?>
                <tr><td colspan="7" style="text-align:center;color:#64748b;padding:2rem;">No products found</td></tr>
            <?php else: foreach ($products as $p): ?>
                <tr>
                    <td><strong><?= e($p['name']) ?></strong><?php if ($p['sku']): ?><br><small style="color:#94a3b8;"><?= e($p['sku']) ?></small><?php endif; ?></td>
                    <td><?= e($p['category_name'] ?? '—') ?></td>
                    <td><?= money((int)$p['price']) ?></td>
                    <td><?= $p['sale_price'] ? money((int)$p['sale_price']) : '<span style="color:#94a3b8;">—</span>' ?></td>
                    <td>
                        <span class="admin-badge admin-badge--<?= $p['total_stock'] == 0 ? 'red' : ($p['total_stock'] <= 5 ? 'yellow' : 'green') ?>">
                            <?= $p['total_stock'] ?>
                        </span>
                    </td>
                    <td><span class="admin-badge admin-badge--<?= $p['is_active'] ? 'green' : 'grey' ?>"><?= $p['is_active'] ? 'Active' : 'Hidden' ?></span></td>
                    <td style="display:flex;gap:0.4rem;align-items:center;">
                        <a href="<?= url('/admin/products/edit/'.$p['id']) ?>" class="admin-btn admin-btn--outline admin-btn--sm">Edit</a>
                        <form method="POST" action="<?= url('/admin/products/delete/'.$p['id']) ?>" data-confirm="Hide this product from the shop?">
                            <?= csrfField() ?>
                            <button type="submit" class="admin-btn admin-btn--danger admin-btn--sm">Hide</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    <?php if ($pagination['total_pages'] > 1): ?>
    <div style="padding:1rem 1.25rem;display:flex;gap:0.5rem;justify-content:center;">
        <?php for ($i=1;$i<=$pagination['total_pages'];$i++): ?>
        <a href="?page=<?= $i ?><?= $search ? '&search='.urlencode($search) : '' ?>"
           style="padding:0.375rem 0.75rem;border:1.5px solid <?= $i==$pagination['current'] ? 'var(--admin-accent)' : '#cbd5e1' ?>;border-radius:6px;background:<?= $i==$pagination['current'] ? 'var(--admin-accent)' : 'transparent' ?>;color:<?= $i==$pagination['current'] ? '#fff' : '#334155' ?>;font-size:0.8rem;font-weight:600;">
            <?= $i ?>
        </a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>
