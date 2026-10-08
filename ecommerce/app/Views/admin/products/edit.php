<div style="max-width:900px;">
<a href="<?= url('/admin/products') ?>" class="admin-btn admin-btn--outline" style="margin-bottom:1.25rem;display:inline-flex;">← Back to Products</a>

<div class="admin-card">
    <div class="admin-card__header"><span class="admin-card__title">Edit: <?= e($product['name']) ?></span></div>
    <div class="admin-card__body">
        <form method="POST" action="<?= url('/admin/products/edit/'.$product['id']) ?>" enctype="multipart/form-data">
            <?= csrfField() ?>

            <div class="admin-form-grid">
                <div class="admin-form-group">
                    <label class="admin-label">Product Name *</label>
                    <input type="text" name="name" required class="admin-input" value="<?= e($product['name']) ?>">
                </div>
                <div class="admin-form-group">
                    <label class="admin-label">Category *</label>
                    <select name="category_id" required class="admin-select">
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $product['category_id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="admin-form-group">
                <label class="admin-label">Description</label>
                <textarea name="description" class="admin-textarea"><?= e($product['description'] ?? '') ?></textarea>
            </div>

            <div class="admin-form-grid">
                <div class="admin-form-group">
                    <label class="admin-label">Price (₦) *</label>
                    <input type="number" name="price" required min="0" step="50" class="admin-input" value="<?= round((int)$product['price']/100) ?>">
                </div>
                <div class="admin-form-group">
                    <label class="admin-label">Sale Price (₦) <span style="font-weight:400;color:#94a3b8;">(leave blank to remove sale)</span></label>
                    <input type="number" name="sale_price" min="0" step="50" class="admin-input" value="<?= $product['sale_price'] ? round((int)$product['sale_price']/100) : '' ?>">
                </div>
            </div>

            <div class="admin-form-grid">
                <div class="admin-form-group">
                    <label class="admin-label">SKU</label>
                    <input type="text" name="sku" class="admin-input" value="<?= e($product['sku'] ?? '') ?>">
                </div>
                <div class="admin-form-group">
                    <label class="admin-label">Replace Image <span style="font-weight:400;color:#94a3b8;">(optional)</span></label>
                    <?php $primaryImg = null; foreach ($images as $img) { if ($img['is_primary']) { $primaryImg = $img; break; } } ?>
                    <?php if ($primaryImg): ?>
                    <div style="margin-bottom:0.5rem;">
                        <?php $imgSrc = str_starts_with($primaryImg['image_path'], 'http') ? $primaryImg['image_path'] : url('/images/products/'.$primaryImg['image_path']); ?>
                        <img id="img-preview" src="<?= $imgSrc ?>" class="admin-img-preview" alt="Current image">
                    </div>
                    <?php else: ?>
                    <img id="img-preview" style="display:none;" class="admin-img-preview" alt="Preview">
                    <?php endif; ?>
                    <input type="file" name="image" accept="image/*" class="admin-input" style="height:auto;padding:0.4rem;">
                </div>
            </div>

            <div style="display:flex;gap:1.5rem;margin-bottom:1.25rem;">
                <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;font-size:0.875rem;">
                    <input type="checkbox" name="is_active" value="1" <?= $product['is_active'] ? 'checked' : '' ?> style="width:16px;height:16px;accent-color:var(--admin-accent);">
                    Active (visible in shop)
                </label>
                <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;font-size:0.875rem;">
                    <input type="checkbox" name="is_featured" value="1" <?= $product['is_featured'] ? 'checked' : '' ?> style="width:16px;height:16px;accent-color:var(--admin-accent);">
                    Featured (show on homepage)
                </label>
            </div>

            <!-- Variants / Stock -->
            <?php if (!empty($variants)): ?>
            <div class="admin-form-group">
                <label class="admin-label">Stock Per Variant</label>
                <div class="admin-table-wrap" style="margin-top:0.5rem;">
                    <table class="admin-table">
                        <thead><tr><th>Size</th><th>Colour</th><th>SKU</th><th>Stock</th></tr></thead>
                        <tbody>
                        <?php foreach ($variants as $v): ?>
                        <tr>
                            <td><?= e($v['size'] ?? '—') ?></td>
                            <td><?= e($v['colour'] ?? '—') ?></td>
                            <td style="font-size:0.75rem;color:#94a3b8;"><?= e($v['sku'] ?? '') ?></td>
                            <td><input type="number" name="variant_stock[<?= $v['id'] ?>]" value="<?= (int)$v['stock'] ?>" min="0" class="admin-input" style="width:80px;height:34px;"></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <button type="submit" class="admin-btn admin-btn--primary admin-btn--lg">Save Changes</button>
        </form>
    </div>
</div>
</div>
