<?php

declare(strict_types=1);

/**
 * Format a kobo integer as a Naira string.
 * money(1800000) → "₦18,000"
 * money(185050)  → "₦1,850.50"
 */
function money(int $kobo): string {
    $naira = $kobo / 100;
    // Show decimals only when there are kobo fractions
    if ($kobo % 100 === 0) {
        return '₦' . number_format($naira, 0);
    }
    return '₦' . number_format($naira, 2);
}

/**
 * Escape output for HTML context.
 */
function e(mixed $value): string {
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Get a value from $_GET with optional default.
 */
function get(string $key, mixed $default = null): mixed {
    return $_GET[$key] ?? $default;
}

/**
 * Get a value from $_POST with optional default.
 */
function post(string $key, mixed $default = null): mixed {
    return $_POST[$key] ?? $default;
}

/**
 * Redirect to a URL and exit.
 */
function redirect(string $url): never {
    header('Location: ' . $url);
    exit;
}

/**
 * Build a URL relative to APP_URL.
 */
function url(string $path = ''): string {
    $base = rtrim(APP_URL, '/');
    $path = '/' . ltrim($path, '/');
    return $base . $path;
}

/**
 * Get a setting value from the settings table (cached in memory).
 */
function setting(string $key, string $default = ''): string {
    static $cache = null;
    if ($cache === null) {
        try {
            $db   = getDB();
            $rows = $db->query('SELECT setting_key, setting_value FROM settings')->fetchAll();
            $cache = array_column($rows, 'setting_value', 'setting_key');
        } catch (Throwable) {
            $cache = [];
        }
    }
    return $cache[$key] ?? $default;
}

/**
 * Free delivery threshold in kobo.
 */
function freeDeliveryThreshold(): int {
    return (int) setting('free_delivery_threshold', '5000000');
}

/**
 * Set a flash message (stored in session, shown once).
 */
function flash(string $type, string $message): void {
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

/**
 * Get and clear all flash messages.
 */
function getFlash(): array {
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

/**
 * Generate a CSRF token and store it in the session.
 */
function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Render a hidden CSRF input field.
 */
function csrfField(): string {
    return '<input type="hidden" name="csrf_token" value="' . e(csrfToken()) . '">';
}

/**
 * Verify the CSRF token from a POST request.
 */
function verifyCsrf(): bool {
    $token = $_POST['csrf_token'] ?? '';
    return hash_equals(csrfToken(), $token);
}

/**
 * Check if the current user is logged in.
 */
function isLoggedIn(): bool {
    return !empty($_SESSION['user_id']);
}

/**
 * Check if the current user is an admin.
 */
function isAdmin(): bool {
    return !empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Get the current user's ID (or null).
 */
function currentUserId(): ?int {
    return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
}

/**
 * Require the user to be logged in; redirect to login otherwise.
 */
function requireLogin(): void {
    if (!isLoggedIn()) {
        flash('error', 'Please log in to continue.');
        redirect(url('/account/login'));
    }
}

/**
 * Require the user to be an admin; send 403 otherwise.
 */
function requireAdmin(): void {
    if (!isAdmin()) {
        http_response_code(403);
        require VIEW_PATH . '/errors/403.php';
        exit;
    }
}

/**
 * Get the cart item count from session.
 */
function cartCount(): int {
    return (int) ($_SESSION['cart_count'] ?? 0);
}

/**
 * Sanitise a Nigerian phone number to 11-digit local format.
 * Accepts: 08012345678, +2348012345678, 2348012345678
 * Returns: 08012345678 or original if not recognised.
 */
function sanitisePhone(string $phone): string {
    $phone = preg_replace('/\D/', '', $phone);
    if (str_starts_with($phone, '234') && strlen($phone) === 13) {
        return '0' . substr($phone, 3);
    }
    return $phone;
}

/**
 * Generate a slugified string from a name.
 */
function slugify(string $text): string {
    $text = mb_strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

/**
 * Generate a unique order number. E.g. AZF-20250101-A3F9
 */
function generateOrderNumber(): string {
    return 'AZF-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 4));
}

/**
 * Truncate a string to a max length, adding ellipsis.
 */
function truncate(string $text, int $maxLength = 100): string {
    if (mb_strlen($text) <= $maxLength) return $text;
    return mb_substr($text, 0, $maxLength - 3) . '…';
}

/**
 * Get asset URL with cache-busting version.
 */
function asset(string $path): string {
    $full = PUBLIC_PATH . '/' . ltrim($path, '/');
    $ver  = file_exists($full) ? '?v=' . substr(md5((string)filemtime($full)), 0, 8) : '';
    return url($path) . $ver;
}

/**
 * Render a view file from app/Views.
 */
function view(string $template, array $data = []): void {
    extract($data, EXTR_SKIP);
    $file = VIEW_PATH . '/' . str_replace('.', '/', $template) . '.php';
    if (!file_exists($file)) {
        http_response_code(500);
        die('View not found: ' . e($template));
    }
    require $file;
}
