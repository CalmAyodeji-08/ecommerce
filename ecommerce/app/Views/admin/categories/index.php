<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;align-items:start;">

    <!-- Current categories -->
    <div class="admin-card">
        <div class="admin-card__header"><span class="admin-card__title">Categories (<?= count($categories) ?>)</span></div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead><tr><th>Name</th><th>Slug</th><th>Sort</th><th>Status</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($categories as $cat): ?>
                <tr>
                    <td><strong><?= e($cat['name']) ?></strong></td>
                    <td style="font-size:0.75rem;color:#94a3b8;"><?= e($cat['slug']) ?></td>
                    <td><?= $cat['sort_order'] ?></td>
                    <td><span class="admin-badge admin-badge--<?= $cat['is_active']?'green':'grey' ?>"><?= $cat['is_active']?'Active':'Hidden' ?></span></td>
                    <td>
                        <button onclick="fillEditForm(<?= htmlspecialchars(json_encode($cat)) ?>)" class="admin-btn admin-btn--outline admin-btn--sm">Edit</button>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div style="display:flex;flex-direction:column;gap:1.25rem;">
        <!-- Add category -->
        <div class="admin-card">
            <div class="admin-card__header"><span class="admin-card__title">Add Category</span></div>
            <div class="admin-card__body">
                <form method="POST" action="<?= url('/admin/categories/add') ?>">
                    <?= csrfField() ?>
                    <div class="admin-form-group">
                        <label class="admin-label">Name *</label>
                        <input type="text" name="name" required class="admin-input" placeholder="e.g. Tracksuits">
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-label">Description</label>
                        <input type="text" name="description" class="admin-input" placeholder="Short description">
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-label">Sort Order</label>
                        <input type="number" name="sort_order" class="admin-input" value="0" min="0">
                    </div>
                    <button type="submit" class="admin-btn admin-btn--primary">Add Category</button>
                </form>
            </div>
        </div>

        <!-- Edit category -->
        <div class="admin-card" id="edit-cat-card" style="display:none;">
            <div class="admin-card__header"><span class="admin-card__title" id="edit-cat-title">Edit Category</span></div>
            <div class="admin-card__body">
                <form method="POST" id="edit-cat-form" action="">
                    <?= csrfField() ?>
                    <div class="admin-form-group">
                        <label class="admin-label">Name</label>
                        <input type="text" name="name" id="edit-cat-name" required class="admin-input">
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-label">Description</label>
                        <input type="text" name="description" id="edit-cat-desc" class="admin-input">
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-label">Sort Order</label>
                        <input type="number" name="sort_order" id="edit-cat-sort" class="admin-input" min="0">
                    </div>
                    <label style="display:flex;align-items:center;gap:0.5rem;margin-bottom:1rem;font-size:0.875rem;cursor:pointer;">
                        <input type="checkbox" name="is_active" id="edit-cat-active" value="1" style="accent-color:var(--admin-accent);width:16px;height:16px;">
                        Active
                    </label>
                    <div style="display:flex;gap:0.5rem;">
                        <button type="submit" class="admin-btn admin-btn--primary">Save</button>
                        <button type="button" onclick="document.getElementById('edit-cat-card').style.display='none'" class="admin-btn admin-btn--outline">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function fillEditForm(cat) {
    document.getElementById('edit-cat-card').style.display = 'block';
    document.getElementById('edit-cat-title').textContent = 'Edit: ' + cat.name;
    document.getElementById('edit-cat-form').action = '<?= url('/admin/categories/edit/') ?>' + cat.id;
    document.getElementById('edit-cat-name').value  = cat.name;
    document.getElementById('edit-cat-desc').value  = cat.description || '';
    document.getElementById('edit-cat-sort').value  = cat.sort_order;
    document.getElementById('edit-cat-active').checked = cat.is_active == 1;
    document.getElementById('edit-cat-card').scrollIntoView({behavior:'smooth'});
}
</script>
