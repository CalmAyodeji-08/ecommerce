<?php
/**
 * Pagination partial.
 * Requires: $pagination array with keys: current, total_pages, total_items, per_page
 * All existing $_GET params are preserved in links.
 */

if (!isset($pagination) || $pagination['total_pages'] <= 1) return;

$current     = (int) $pagination['current'];
$totalPages  = (int) $pagination['total_pages'];

// Build a URL for a given page number, keeping all other params
function paginationUrl(int $page): string {
    $params = $_GET;
    if ($page <= 1) {
        unset($params['page']);
    } else {
        $params['page'] = $page;
    }
    $base = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
    // Rebuild base without APP_URL path prefix
    $basePath = parse_url(APP_URL, PHP_URL_PATH) ?? '';
    if ($basePath && str_starts_with($base, $basePath)) {
        $base = substr($base, strlen($basePath));
    }
    $base = '/' . ltrim($base, '/');
    $qs   = http_build_query($params);
    return url($base) . ($qs ? '?' . $qs : '');
}

// Show at most 5 page buttons centred on current page
$window = 2;
$start  = max(1, $current - $window);
$end    = min($totalPages, $current + $window);
// Pad window if near edges
if ($current - $window < 1) {
    $end = min($totalPages, $end + ($window - ($current - 1)));
}
if ($current + $window > $totalPages) {
    $start = max(1, $start - ($window - ($totalPages - $current)));
}
?>

<nav class="pagination" aria-label="Pagination" style="display:flex; align-items:center; justify-content:center; gap:var(--space-1); flex-wrap:wrap; margin-top:var(--space-8);">

    <!-- Prev -->
    <?php if ($current > 1): ?>
    <a href="<?= paginationUrl($current - 1) ?>"
       class="pagination__btn"
       aria-label="Previous page"
       style="padding:var(--space-2) var(--space-3); border:1.5px solid var(--clr-grey-300); border-radius:var(--radius-sm); font-size:var(--text-sm); background:var(--clr-white); text-decoration:none; color:var(--clr-black);">
        ← Prev
    </a>
    <?php else: ?>
    <span class="pagination__btn" aria-disabled="true"
          style="padding:var(--space-2) var(--space-3); border:1.5px solid var(--clr-grey-200); border-radius:var(--radius-sm); font-size:var(--text-sm); background:var(--clr-grey-100); color:var(--clr-grey-400); cursor:not-allowed;">
        ← Prev
    </span>
    <?php endif; ?>

    <!-- First page + ellipsis -->
    <?php if ($start > 1): ?>
    <a href="<?= paginationUrl(1) ?>" style="padding:var(--space-2) var(--space-3); border:1.5px solid var(--clr-grey-300); border-radius:var(--radius-sm); font-size:var(--text-sm); background:var(--clr-white); text-decoration:none; color:var(--clr-black);">1</a>
    <?php if ($start > 2): ?>
    <span style="color:var(--clr-grey-400); padding:0 var(--space-1);">…</span>
    <?php endif; ?>
    <?php endif; ?>

    <!-- Page buttons -->
    <?php for ($i = $start; $i <= $end; $i++): ?>
    <?php if ($i === $current): ?>
    <span aria-current="page"
          style="padding:var(--space-2) var(--space-3); border:1.5px solid var(--clr-black); border-radius:var(--radius-sm); font-size:var(--text-sm); background:var(--clr-black); color:var(--clr-white); font-weight:700;">
        <?= $i ?>
    </span>
    <?php else: ?>
    <a href="<?= paginationUrl($i) ?>"
       aria-label="Page <?= $i ?>"
       style="padding:var(--space-2) var(--space-3); border:1.5px solid var(--clr-grey-300); border-radius:var(--radius-sm); font-size:var(--text-sm); background:var(--clr-white); text-decoration:none; color:var(--clr-black);">
        <?= $i ?>
    </a>
    <?php endif; ?>
    <?php endfor; ?>

    <!-- Last page + ellipsis -->
    <?php if ($end < $totalPages): ?>
    <?php if ($end < $totalPages - 1): ?>
    <span style="color:var(--clr-grey-400); padding:0 var(--space-1);">…</span>
    <?php endif; ?>
    <a href="<?= paginationUrl($totalPages) ?>" style="padding:var(--space-2) var(--space-3); border:1.5px solid var(--clr-grey-300); border-radius:var(--radius-sm); font-size:var(--text-sm); background:var(--clr-white); text-decoration:none; color:var(--clr-black);"><?= $totalPages ?></a>
    <?php endif; ?>

    <!-- Next -->
    <?php if ($current < $totalPages): ?>
    <a href="<?= paginationUrl($current + 1) ?>"
       class="pagination__btn"
       aria-label="Next page"
       style="padding:var(--space-2) var(--space-3); border:1.5px solid var(--clr-grey-300); border-radius:var(--radius-sm); font-size:var(--text-sm); background:var(--clr-white); text-decoration:none; color:var(--clr-black);">
        Next →
    </a>
    <?php else: ?>
    <span class="pagination__btn" aria-disabled="true"
          style="padding:var(--space-2) var(--space-3); border:1.5px solid var(--clr-grey-200); border-radius:var(--radius-sm); font-size:var(--text-sm); background:var(--clr-grey-100); color:var(--clr-grey-400); cursor:not-allowed;">
        Next →
    </span>
    <?php endif; ?>

</nav>
