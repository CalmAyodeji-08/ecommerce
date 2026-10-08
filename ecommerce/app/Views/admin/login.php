<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login — <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding:1.5rem;background:#0f172a;">

<?php $flash = getFlash(); ?>

<div style="background:#1e293b;border:1px solid #334155;border-radius:12px;padding:2.5rem;width:100%;max-width:400px;">
    <div style="text-align:center;margin-bottom:2rem;">
        <div style="font-size:1.5rem;font-weight:800;color:#f1f5f9;margin-bottom:0.25rem;">
            AZFAT <span style="color:#1d4ed8;">ADMIN</span>
        </div>
        <p style="color:#64748b;font-size:0.875rem;">Sign in to manage your store</p>
    </div>

    <?php foreach ($flash as $msg): ?>
    <div style="padding:0.75rem 1rem;border-radius:8px;margin-bottom:1rem;font-size:0.875rem;font-weight:500;
        background:<?= $msg['type']==='success'?'#14532d':'#450a0a' ?>;
        color:<?= $msg['type']==='success'?'#86efac':'#fca5a5' ?>;
        border-left:3px solid <?= $msg['type']==='success'?'#16a34a':'#dc2626' ?>;">
        <?= e($msg['message']) ?>
    </div>
    <?php endforeach; ?>

    <form method="POST" action="<?= url('/admin/login') ?>">
        <?= csrfField() ?>

        <div style="margin-bottom:1.25rem;">
            <label style="display:block;font-size:0.8rem;font-weight:600;color:#94a3b8;margin-bottom:0.375rem;">Email address</label>
            <input
                type="email"
                name="email"
                required
                autocomplete="email"
                value="<?= e($_POST['email'] ?? '') ?>"
                placeholder="admin@azfatbest.com"
                style="width:100%;height:44px;padding:0 0.875rem;background:#0f172a;border:1.5px solid #334155;border-radius:8px;color:#f1f5f9;font-size:0.9rem;outline:none;font-family:inherit;"
                onfocus="this.style.borderColor='#1d4ed8'" onblur="this.style.borderColor='#334155'"
            >
        </div>

        <div style="margin-bottom:1.5rem;">
            <label style="display:block;font-size:0.8rem;font-weight:600;color:#94a3b8;margin-bottom:0.375rem;">Password</label>
            <input
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="••••••••"
                style="width:100%;height:44px;padding:0 0.875rem;background:#0f172a;border:1.5px solid #334155;border-radius:8px;color:#f1f5f9;font-size:0.9rem;outline:none;font-family:inherit;"
                onfocus="this.style.borderColor='#1d4ed8'" onblur="this.style.borderColor='#334155'"
            >
        </div>

        <button type="submit" style="width:100%;height:44px;background:#1d4ed8;color:#fff;font-weight:700;font-size:0.9rem;border:none;border-radius:8px;cursor:pointer;font-family:inherit;transition:background 0.2s;"
            onmouseover="this.style.background='#1e40af'" onmouseout="this.style.background='#1d4ed8'">
            Sign In →
        </button>
    </form>

    <div style="text-align:center;margin-top:1.5rem;">
        <a href="<?= url('/shop') ?>" style="font-size:0.8rem;color:#475569;text-decoration:none;">← Back to Shop</a>
    </div>
</div>

</body>
</html>
