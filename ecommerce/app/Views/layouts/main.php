<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#111111">

    <title><?= e($pageTitle ?? APP_NAME) ?></title>

    <?php if (!empty($metaDescription)): ?>
    <meta name="description" content="<?= e($metaDescription) ?>">
    <?php endif; ?>

    <?php if (!empty($ogImage)): ?>
    <meta property="og:image" content="<?= e($ogImage) ?>">
    <?php endif; ?>

    <meta property="og:title"       content="<?= e($pageTitle ?? APP_NAME) ?>">
    <meta property="og:type"        content="website">
    <meta property="og:url"         content="<?= e(APP_URL . ($_SERVER['REQUEST_URI'] ?? '/')) ?>">
    <meta property="og:site_name"   content="<?= e(APP_NAME) ?>">

    <!-- Apply saved theme BEFORE paint to avoid flash -->
    <script>
        (function () {
            try {
                var saved = localStorage.getItem('azfat-theme');
                var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                var theme = saved || (prefersDark ? 'dark' : 'light');
                document.documentElement.setAttribute('data-theme', theme);
            } catch (e) {}
        })();
    </script>

    <!-- Styles -->
    <link rel="stylesheet" href="<?= asset('css/main.css') ?>">
    <?php if (!empty($extraCss)): ?>
        <?php foreach ((array) $extraCss as $css): ?>
        <link rel="stylesheet" href="<?= asset($css) ?>">
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap"
          media="print"
          onload="this.media='all'">
    <noscript>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap">
    </noscript>

    <!-- Inject CSRF token for AJAX requests -->
    <meta name="csrf-token" content="<?= e(csrfToken()) ?>">

    <link rel="icon" href="<?= url('/images/favicon.ico') ?>" type="image/x-icon">
</head>
<body>

    <?php require VIEW_PATH . '/partials/announcement-bar.php'; ?>
    <?php require VIEW_PATH . '/partials/header.php'; ?>

    <main id="main-content" class="page-main" tabindex="-1">
        <div class="container">
            <?php require VIEW_PATH . '/partials/flash-messages.php'; ?>
            <?= $content ?? '' ?>
        </div>
    </main>

    <?php require VIEW_PATH . '/partials/footer.php'; ?>
    <?php require VIEW_PATH . '/partials/bottom-nav.php'; ?>

    <!-- Scripts -->
    <script src="<?= asset('js/main.js') ?>" defer></script>
    <?php if (!empty($extraJs)): ?>
        <?php foreach ((array) $extraJs as $js): ?>
        <script src="<?= asset($js) ?>" defer></script>
        <?php endforeach; ?>
    <?php endif; ?>

</body>
</html>
