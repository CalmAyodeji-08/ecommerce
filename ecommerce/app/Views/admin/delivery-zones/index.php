<div class="admin-card">
    <div class="admin-card__header">
        <span class="admin-card__title">Delivery Fees by State</span>
        <span style="font-size:0.8rem;color:#64748b;">Enter fees in ₦ (Naira)</span>
    </div>
    <div class="admin-card__body">
        <form method="POST" action="<?= url('/admin/delivery-zones/update') ?>">
            <?= csrfField() ?>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:0.75rem;margin-bottom:1.5rem;">
                <?php foreach ($zones as $zone): ?>
                <div style="display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:0.625rem 0.875rem;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;">
                    <div>
                        <p style="font-size:0.875rem;font-weight:600;margin:0;"><?= e($zone['state']) ?></p>
                        <p style="font-size:0.7rem;color:#94a3b8;margin:0;"><?= e($zone['zone_name']) ?></p>
                    </div>
                    <div style="display:flex;align-items:center;gap:0.25rem;">
                        <span style="font-size:0.875rem;color:#64748b;">₦</span>
                        <input
                            type="number"
                            name="fees[<?= e($zone['state']) ?>]"
                            value="<?= round((int)$zone['fee']/100) ?>"
                            min="0"
                            step="50"
                            class="admin-input"
                            style="width:90px;height:34px;font-size:0.875rem;"
                        >
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="submit" class="admin-btn admin-btn--primary admin-btn--lg">Save All Fees</button>
        </form>
    </div>
</div>
