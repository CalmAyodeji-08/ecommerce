<div class="admin-card" style="max-width:700px;">
    <div class="admin-card__header"><span class="admin-card__title">Store Settings</span></div>
    <div class="admin-card__body">
        <form method="POST" action="<?= url('/admin/settings') ?>">
            <?= csrfField() ?>

            <?php
            $labels = [
                'shop_name'               => 'Shop Name',
                'free_delivery_threshold' => 'Free Delivery Threshold (kobo)',
                'currency'                => 'Currency Code',
                'currency_symbol'         => 'Currency Symbol',
                'whatsapp_number'         => 'WhatsApp Number',
                'instagram_handle'        => 'Instagram Handle',
                'announcement_bar_text'   => 'Announcement Bar Text',
                'promo_end_date'          => 'Promo End Date',
            ];
            foreach ($labels as $key => $label):
            ?>
            <div class="admin-form-group">
                <label class="admin-label"><?= e($label) ?></label>
                <input type="text" name="<?= e($key) ?>" value="<?= e($settings[$key] ?? '') ?>" class="admin-input">
                <?php if ($key === 'free_delivery_threshold'): ?>
                <p class="admin-form-hint">In kobo — ₦50,000 = 5000000</p>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>

            <button type="submit" class="admin-btn admin-btn--primary admin-btn--lg">Save Settings</button>
        </form>
    </div>
</div>
