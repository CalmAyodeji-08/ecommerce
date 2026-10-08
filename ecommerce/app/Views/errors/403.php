<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Access Denied — <?= defined('APP_NAME') ? e(APP_NAME) : 'AZFAT BEST BOUTIQUE' ?></title>
    <link rel="stylesheet" href="<?= defined('APP_URL') ? asset('css/main.css') : '/public/css/main.css' ?>">
</head>
<body>
    <main class="page-main">
        <div class="container">
            <div class="empty-state" style="padding: 5rem 1rem;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                <h1 style="font-size:1.5rem;">Access denied</h1>
                <p style="margin: 0.75rem 0 1.5rem; color: #666;">
                    You don't have permission to view this page.
                </p>
                <?php if (defined('APP_URL')): ?>
                <a href="<?= url('/') ?>" class="btn btn--primary">Back to Home</a>
                <?php else: ?>
                <a href="/" class="btn btn--primary">Back to Home</a>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>
