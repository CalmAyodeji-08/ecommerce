<?php $sb = $statusBadge; ?>
<a href="<?= url('/admin/orders') ?>" class="admin-btn admin-btn--outline" style="margin-bottom:1.25rem;display:inline-flex;">← All Orders</a>

<div style="display:grid;gap:1.25rem;">

    <!-- Header -->
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
        <div>
            <h2 style="font-size:1.1rem;font-weight:700;margin-bottom:0.25rem;"><?= e($order['order_number']) ?></h2>
            <p style="color:#64748b;font-size:0.8rem;"><?= date('d F Y, g:ia', strtotime($order['created_at'])) ?></p>
        </div>
        <?= $sb($order['status']) ?>
    </div>

    <!-- Update status -->
    <div class="admin-card">
        <div class="admin-card__header"><span class="admin-card__title">Update Status</span></div>
        <div class="admin-card__body">
            <form method="POST" action="<?= url('/admin/orders/'.$order['id'].'/status') ?>" style="display:flex;gap:0.75rem;align-items:center;flex-wrap:wrap;">
                <?= csrfField() ?>
                <select name="status" class="admin-select" style="width:200px;">
                    <?php foreach (['pending_payment','paid','processing','shipped','delivered','cancelled','failed'] as $s): ?>
                    <option value="<?= $s ?>" <?= $order['status']===$s?'selected':'' ?>><?= ucwords(str_replace('_',' ',$s)) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="admin-btn admin-btn--primary">Update</button>
            </form>
        </div>
    </div>

    <!-- Items -->
    <div class="admin-card">
        <div class="admin-card__header"><span class="admin-card__title">Items (<?= count($items) ?>)</span></div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead><tr><th>Product</th><th>Variant</th><th>Qty</th><th>Unit Price</th><th>Total</th></tr></thead>
                <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= e($item['product_name']) ?></td>
                    <td><?= e(implode(' / ', array_filter([$item['variant_size'],$item['variant_colour']]))) ?: '—' ?></td>
                    <td><?= (int)$item['quantity'] ?></td>
                    <td><?= money((int)$item['unit_price']) ?></td>
                    <td><strong><?= money((int)$item['total_price']) ?></strong></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div style="padding:1rem 1.25rem;display:flex;flex-direction:column;gap:0.4rem;align-items:flex-end;border-top:1px solid #f1f5f9;">
            <div style="display:flex;gap:2rem;font-size:0.875rem;color:#64748b;"><span>Subtotal</span><span><?= money((int)$order['subtotal']) ?></span></div>
            <div style="display:flex;gap:2rem;font-size:0.875rem;color:#64748b;"><span>Delivery</span><span><?= (int)$order['delivery_fee']===0?'<span style="color:#16a34a">Free</span>':money((int)$order['delivery_fee']) ?></span></div>
            <div style="display:flex;gap:2rem;font-size:1rem;font-weight:700;padding-top:0.5rem;border-top:1px solid #e2e8f0;margin-top:0.25rem;"><span>Total</span><span><?= money((int)$order['total']) ?></span></div>
        </div>
    </div>

    <!-- Delivery & Payment -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
        <div class="admin-card">
            <div class="admin-card__header"><span class="admin-card__title">Delivery Address</span></div>
            <div class="admin-card__body" style="font-size:0.875rem;line-height:1.8;color:#475569;">
                <strong style="color:#0f172a;"><?= e($order['first_name'].' '.$order['last_name']) ?></strong><br>
                <?= e($order['address_line1']) ?><br>
                <?php if ($order['address_line2']): ?><?= e($order['address_line2']) ?><br><?php endif; ?>
                <?= e($order['city'].', '.$order['state']) ?><br>
                <?= e($order['phone']) ?>
                <?php if ($order['guest_email']): ?><br><?= e($order['guest_email']) ?><?php endif; ?>
            </div>
        </div>
        <div class="admin-card">
            <div class="admin-card__header"><span class="admin-card__title">Payment</span></div>
            <div class="admin-card__body" style="font-size:0.875rem;line-height:1.8;color:#475569;">
                <strong style="color:#0f172a;">Method:</strong> <?= e(ucwords(str_replace('_',' ',$order['payment_method'] ?? '—'))) ?><br>
                <strong style="color:#0f172a;">Status:</strong> <?= $sb($order['status']) ?><br>
                <?php if ($order['payment_reference']): ?>
                <strong style="color:#0f172a;">Ref:</strong> <span style="font-size:0.75rem;"><?= e($order['payment_reference']) ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>
