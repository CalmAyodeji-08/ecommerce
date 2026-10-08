<?php

declare(strict_types=1);

// ─── Bootstrap ────────────────────────────────────────────────────────────────
require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/app/helpers.php';
require_once dirname(__DIR__) . '/app/Router.php';

// ─── Global error handler ─────────────────────────────────────────────────────
set_exception_handler(function (Throwable $e) {
    error_log((string) $e);
    if (APP_ENV === 'local') {
        echo '<pre style="padding:2rem;background:#fdecea;color:#c62828;border-radius:8px;margin:2rem">';
        echo '<strong>' . htmlspecialchars(get_class($e)) . '</strong>: ';
        echo htmlspecialchars($e->getMessage()) . "\n\n";
        echo htmlspecialchars($e->getTraceAsString());
        echo '</pre>';
    } else {
        http_response_code(500);
        require dirname(__DIR__) . '/app/Views/errors/500.php';
    }
    exit;
});

// ─── Router ───────────────────────────────────────────────────────────────────
$router = new Router();
require_once dirname(__DIR__) . '/app/routes.php';
$router->dispatch();
