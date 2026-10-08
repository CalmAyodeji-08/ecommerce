<?php
// Use DB setting if available, fall back to hardcoded default
try {
    $threshold = money(freeDeliveryThreshold());
} catch (Throwable $e) {
    $threshold = '₦50,000';
}
?>
<div class="announcement-bar" role="banner" aria-label="Promotions">
    <div class="ann-item">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 5v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>
        </svg>
        Free delivery on orders above <?= $threshold ?>
    </div>
    <div class="ann-item">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
        </svg>
        Summer Sale — Up To 30% Off
    </div>
    <div class="ann-item">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
        </svg>
        Limited Time Flash Deals
    </div>
</div>
