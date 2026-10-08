<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page Not Found — <?= defined('APP_NAME') ? e(APP_NAME) : 'AZFAT BEST BOUTIQUE' ?></title>
    <link rel="stylesheet" href="<?= defined('APP_URL') ? asset('css/main.css') : '/public/css/main.css' ?>">
</head>
<body>
    <?php if (defined('APP_URL')): ?>
        <?php require VIEW_PATH . '/partials/announcement-bar.php'; ?>
        <?php require VIEW_PATH . '/partials/header.php'; ?>
    <?php endif; ?>

    <main class="page-main">
        <div class="container">
            <div class="empty-state" style="padding: 5rem 1rem;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <h1 style="font-size:1.5rem;">Page not found</h1>
                <p style="margin: 0.75rem 0 1.5rem; color: #666;">
                    The page you're looking for doesn't exist or has been moved.
                </p>
                <?php if (defined('APP_URL')): ?>
                <a href="<?= url('/') ?>" class="btn btn--primary">Back to Home</a>
                <?php else: ?>
                <a href="/" class="btn btn--primary">Back to Home</a>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php if (defined('APP_URL')): ?>
        <?php require VIEW_PATH . '/partials/footer.php'; ?>
        <?php require VIEW_PATH . '/partials/bottom-nav.php'; ?>
    <?php endif; ?>
</body>
</html>
