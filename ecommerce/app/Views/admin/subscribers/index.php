<div class="admin-card">
    <div class="admin-card__header">
        <span class="admin-card__title">Email Subscribers (<?= count($subscribers) ?>)</span>
    </div>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>#</th><th>Email</th><th>Date Joined</th><th>Status</th></tr></thead>
            <tbody>
            <?php if (empty($subscribers)): ?>
                <tr><td colspan="4" style="text-align:center;color:#64748b;padding:2rem;">No subscribers yet</td></tr>
            <?php else: foreach ($subscribers as $i => $sub): ?>
                <tr>
                    <td style="color:#94a3b8;"><?= $i+1 ?></td>
                    <td><?= e($sub['email']) ?></td>
                    <td><?= date('d M Y', strtotime($sub['created_at'])) ?></td>
                    <td><span class="admin-badge admin-badge--<?= $sub['is_active']?'green':'grey' ?>"><?= $sub['is_active']?'Active':'Unsubscribed' ?></span></td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
