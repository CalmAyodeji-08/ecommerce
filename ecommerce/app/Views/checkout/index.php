<?php
/**
 * Checkout page — Phase 7
 * Variables: $cartItems, $totals, $subtotal, $user, $addresses, $deliveryZones, $states
 */

// Embed delivery zones for JS fee calculation
$zonesMap = array_column($deliveryZones, 'fee', 'state');
?>

<script>
const DELIVERY_ZONES      = <?= json_encode($zonesMap, JSON_HEX_TAG | JSON_HEX_AMP) ?>;
const FREE_DELIVERY_THRESHOLD = <?= freeDeliveryThreshold() ?>;
</script>

<!-- Breadcrumb -->
<nav class="breadcrumb" aria-label="Breadcrumb">
    <a href="<?= url('/') ?>">Home</a>
    <span class="breadcrumb__sep">›</span>
    <a href="<?= url('/cart') ?>">Bag</a>
    <span class="breadcrumb__sep">›</span>
    <span class="breadcrumb__current">Checkout</span>
</nav>

<h1 class="page-title">Checkout</h1>

<div class="checkout-layout">

    <!-- ══ Left: Delivery details form ══════════════════════════════════════ -->
    <div>
        <form method="POST" action="<?= url('/checkout') ?>" id="checkout-form" novalidate>
            <?= csrfField() ?>

            <!-- ── Saved addresses (logged-in users) ── -->
            <?php if (!empty($addresses)): ?>
            <section class="checkout-section">
                <h2 class="checkout-section__title">Saved Addresses</h2>
                <div class="saved-addresses">
                    <?php foreach ($addresses as $addr): ?>
                    <label class="saved-address-card <?= $addr['is_default'] ? 'selected' : '' ?>">
                        <input
                            type="radio"
                            name="saved_address_id"
                            value="<?= (int) $addr['id'] ?>"
                            class="js-saved-address"
                            <?= $addr['is_default'] ? 'checked' : '' ?>
                            data-first-name="<?= e($addr['first_name']) ?>"
                            data-last-name="<?= e($addr['last_name']) ?>"
                            data-phone="<?= e($addr['phone']) ?>"
                            data-line1="<?= e($addr['address_line1']) ?>"
                            data-line2="<?= e($addr['address_line2'] ?? '') ?>"
                            data-city="<?= e($addr['city']) ?>"
                            data-state="<?= e($addr['state']) ?>"
                        >
                        <div>
                            <p style="font-weight:600;margin:0 0 2px;"><?= e($addr['first_name']) ?> <?= e($addr['last_name']) ?></p>
                            <p style="font-size:var(--text-sm);color:var(--clr-grey-600);margin:0;">
                                <?= e($addr['address_line1']) ?>, <?= e($addr['city']) ?>, <?= e($addr['state']) ?>
                            </p>
                        </div>
                    </label>
                    <?php endforeach; ?>
                    <label class="saved-address-card">
                        <input type="radio" name="saved_address_id" value="" class="js-saved-address">
                        <div style="font-size:var(--text-sm);font-weight:600;">+ Enter a new address</div>
                    </label>
                </div>
            </section>
            <?php endif; ?>

            <!-- ── Contact ── -->
            <section class="checkout-section">
                <h2 class="checkout-section__title">Contact Details</h2>
                <div class="form-group">
                    <label class="form-label form-label--required" for="co-email">Email address</label>
                    <input type="email" id="co-email" name="email" class="form-control"
                           autocomplete="email"
                           value="<?= e($user['email'] ?? $_POST['email'] ?? '') ?>"
                           required aria-required="true">
                </div>
                <div class="form-group">
                    <label class="form-label form-label--required" for="co-phone">Phone number</label>
                    <input type="tel" id="co-phone" name="phone" class="form-control"
                           autocomplete="tel" placeholder="08012345678"
                           value="<?= e($_POST['phone'] ?? '') ?>"
                           required aria-required="true">
                </div>
            </section>

            <!-- ── Delivery address ── -->
            <section class="checkout-section" id="address-section">
                <h2 class="checkout-section__title">Delivery Address</h2>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);">
                    <div class="form-group">
                        <label class="form-label form-label--required" for="co-first-name">First name</label>
                        <input type="text" id="co-first-name" name="first_name" class="form-control"
                               autocomplete="given-name"
                               value="<?= e($user['first_name'] ?? $_POST['first_name'] ?? '') ?>"
                               required aria-required="true">
                    </div>
                    <div class="form-group">
                        <label class="form-label form-label--required" for="co-last-name">Last name</label>
                        <input type="text" id="co-last-name" name="last_name" class="form-control"
                               autocomplete="family-name"
                               value="<?= e($user['last_name'] ?? $_POST['last_name'] ?? '') ?>"
                               required aria-required="true">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label form-label--required" for="co-addr1">Street address</label>
                    <input type="text" id="co-addr1" name="address_line1" class="form-control"
                           autocomplete="address-line1"
                           value="<?= e($_POST['address_line1'] ?? '') ?>"
                           required aria-required="true">
                </div>

                <div class="form-group">
                    <label class="form-label" for="co-addr2">Apartment, suite etc. <span style="color:var(--clr-grey-400);font-weight:400;">(optional)</span></label>
                    <input type="text" id="co-addr2" name="address_line2" class="form-control"
                           autocomplete="address-line2"
                           value="<?= e($_POST['address_line2'] ?? '') ?>">
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);">
                    <div class="form-group">
                        <label class="form-label form-label--required" for="co-city">City / Town</label>
                        <input type="text" id="co-city" name="city" class="form-control"
                               autocomplete="address-level2"
                               value="<?= e($_POST['city'] ?? '') ?>"
                               required aria-required="true">
                    </div>
                    <div class="form-group">
                        <label class="form-label form-label--required" for="co-state">State</label>
                        <select id="co-state" name="state" class="form-control js-state-select"
                                required aria-required="true">
                            <option value="">— Select state —</option>
                            <?php foreach ($states as $st): ?>
                            <option value="<?= e($st) ?>" <?= ($_POST['state'] ?? '') === $st ? 'selected' : '' ?>>
                                <?= e($st) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </section>

            <!-- ── Order notes ── -->
            <section class="checkout-section">
                <h2 class="checkout-section__title">Order Notes <span style="font-size:var(--text-sm);font-weight:400;color:var(--clr-grey-500);">(optional)</span></h2>
                <div class="form-group">
                    <label class="form-label" for="co-notes">Any special instructions?</label>
                    <textarea id="co-notes" name="notes" class="form-control" rows="3"
                              placeholder="e.g. Leave at gate, call on arrival…"><?= e($_POST['notes'] ?? '') ?></textarea>
                </div>

                <!-- Save address checkbox — only for logged-in users entering a new address -->
                <?php if (isLoggedIn()): ?>
                <div style="display:flex;align-items:center;gap:var(--space-3);margin-top:var(--space-4);padding-top:var(--space-4);border-top:1px solid var(--clr-grey-200);" id="save-address-wrap">
                    <input
                        type="checkbox"
                        id="save_address"
                        name="save_address"
                        value="1"
                        style="width:18px;height:18px;accent-color:var(--clr-accent);flex-shrink:0;cursor:pointer;"
                        <?= !empty($_POST['save_address']) ? 'checked' : '' ?>
                    >
                    <label for="save_address" style="font-size:var(--text-sm);font-weight:500;cursor:pointer;line-height:1.4;">
                        Save this address to my account for future orders
                    </label>
                </div>
                <?php endif; ?>
            </section>

            <!-- Submit button (mobile — inside form) -->
            <button type="submit" class="btn btn--primary btn--full" id="place-order-btn" style="margin-top:var(--space-4);">
                Place Order &amp; Pay →
            </button>

        </form>
    </div>

    <!-- ══ Right: Order summary ══════════════════════════════════════════════ -->
    <aside class="checkout-summary" aria-label="Order summary" data-subtotal="<?= (int) $subtotal ?>">
        <h2 style="font-size:var(--text-md);font-weight:800;margin-bottom:var(--space-5);">Your Order</h2>

        <!-- Items -->
        <div style="margin-bottom:var(--space-5);">
            <?php foreach ($cartItems as $item):
                $img = $item['primary_image']
                    ? (str_starts_with($item['primary_image'], 'http')
                        ? $item['primary_image']
                        : url('/images/products/' . $item['primary_image']))
                    : url('/images/placeholder.svg');
            ?>
            <div style="display:flex;gap:var(--space-3);align-items:center;padding:var(--space-3) 0;border-bottom:1px solid var(--clr-grey-200);">
                <div style="position:relative;width:56px;flex-shrink:0;">
                    <img src="<?= $img ?>" alt="<?= e($item['name']) ?>"
                         width="56" height="74"
                         style="width:56px;height:74px;object-fit:cover;border-radius:var(--radius-sm);background:var(--clr-grey-100);">
                    <span style="position:absolute;top:-6px;right:-6px;background:var(--clr-grey-600);color:#fff;font-size:10px;font-weight:700;width:20px;height:20px;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                        <?= (int) $item['quantity'] ?>
                    </span>
                </div>
                <div style="flex:1;min-width:0;">
                    <p style="font-size:var(--text-sm);font-weight:600;margin:0 0 2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= e($item['name']) ?></p>
                    <?php if ($item['size'] || $item['colour']): ?>
                    <p style="font-size:var(--text-xs);color:var(--clr-grey-600);margin:0;">
                        <?= $item['size'] ? e($item['size']) : '' ?>
                        <?= ($item['size'] && $item['colour']) ? ' · ' : '' ?>
                        <?= $item['colour'] ? e($item['colour']) : '' ?>
                    </p>
                    <?php endif; ?>
                </div>
                <div style="font-size:var(--text-sm);font-weight:700;white-space:nowrap;">
                    <?= money((int) $item['price_at_add'] * (int) $item['quantity']) ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Totals -->
        <div style="display:flex;flex-direction:column;gap:var(--space-2);">
            <div style="display:flex;justify-content:space-between;font-size:var(--text-sm);">
                <span>Subtotal</span>
                <span><?= money($subtotal) ?></span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:var(--text-sm);">
                <span>Delivery</span>
                <span id="summary-delivery-fee">Select a state</span>
            </div>
            <div style="display:flex;justify-content:space-between;font-weight:800;font-size:var(--text-md);padding-top:var(--space-3);border-top:2px solid var(--clr-grey-200);margin-top:var(--space-2);">
                <span>Total</span>
                <span id="summary-total"><?= money($subtotal) ?></span>
            </div>
        </div>

        <?php if ($subtotal >= freeDeliveryThreshold()): ?>
        <p style="font-size:var(--text-xs);color:var(--clr-success);text-align:center;margin-top:var(--space-3);">
            ✅ You qualify for free delivery!
        </p>
        <?php endif; ?>

        <!-- Trust -->
        <div style="margin-top:var(--space-5);padding-top:var(--space-5);border-top:1px solid var(--clr-grey-200);display:flex;flex-direction:column;gap:var(--space-2);">
            <div style="display:flex;align-items:center;gap:var(--space-2);font-size:var(--text-xs);color:var(--clr-grey-600);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                Payments secured by Paystack
            </div>
            <div style="display:flex;align-items:center;gap:var(--space-2);font-size:var(--text-xs);color:var(--clr-grey-600);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14" aria-hidden="true"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 100-.49"/></svg>
                7-day returns policy
            </div>
        </div>

    </aside>

</div><!-- /.checkout-layout -->
