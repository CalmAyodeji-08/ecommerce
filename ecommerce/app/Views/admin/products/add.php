<div style="max-width:800px;">
<a href="<?= url('/admin/products') ?>" class="admin-btn admin-btn--outline" style="margin-bottom:1.25rem;display:inline-flex;">← Back to Products</a>

<div class="admin-card">
    <div class="admin-card__header"><span class="admin-card__title">Add New Product</span></div>
    <div class="admin-card__body">
        <form method="POST" action="<?= url('/admin/products/add') ?>" enctype="multipart/form-data">
            <?= csrfField() ?>

            <div class="admin-form-grid">
                <div class="admin-form-group">
                    <label class="admin-label">Product Name *</label>
                    <input type="text" name="name" required class="admin-input" placeholder="e.g. Classic Black Hoodie">
                </div>
                <div class="admin-form-group">
                    <label class="admin-label">Category *</label>
                    <select name="category_id" required class="admin-select">
                        <option value="">— Select category —</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= e($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="admin-form-group">
                <label class="admin-label">Description</label>
                <textarea name="description" class="admin-textarea" placeholder="Product description…"></textarea>
            </div>

            <div class="admin-form-grid">
                <div class="admin-form-group">
                    <label class="admin-label">Price (₦) *</label>
                    <input type="number" name="price" required min="0" step="50" class="admin-input" placeholder="e.g. 8500">
                    <p class="admin-form-hint">Enter price in Naira — saved as kobo automatically</p>
                </div>
                <div class="admin-form-group">
                    <label class="admin-label">Sale Price (₦) <span style="font-weight:400;color:#94a3b8;">(optional)</span></label>
                    <input type="number" name="sale_price" min="0" step="50" class="admin-input" placeholder="Leave blank for no sale">
                </div>
            </div>

            <div class="admin-form-grid">
                <div class="admin-form-group">
                    <label class="admin-label">SKU <span style="font-weight:400;color:#94a3b8;">(optional)</span></label>
                    <input type="text" name="sku" class="admin-input" placeholder="e.g. HOD-BLK-001">
                </div>
                <div class="admin-form-group">
                    <label class="admin-label">Product Image</label>
                    <input type="file" name="image" accept="image/*" class="admin-input" style="height:auto;padding:0.4rem;">
                    <img id="img-preview" style="display:none;margin-top:0.5rem;" class="admin-img-preview" alt="Preview">
                </div>
            </div>

            <div style="display:flex;gap:1.5rem;margin-bottom:1.25rem;">
                <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;font-size:0.875rem;">
                    <input type="checkbox" name="is_active" value="1" checked style="width:16px;height:16px;accent-color:var(--admin-accent);">
                    Active (visible in shop)
                </label>
                <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;font-size:0.875rem;">
                    <input type="checkbox" name="is_featured" value="1" style="width:16px;height:16px;accent-color:var(--admin-accent);">
                    Featured (show on homepage)
                </label>
            </div>

            <button type="submit" class="admin-btn admin-btn--primary admin-btn--lg">Add Product</button>
        </form>
    </div>
</div>
</div>
