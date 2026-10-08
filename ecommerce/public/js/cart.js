'use strict';

(function () {

    // ─── Helpers ─────────────────────────────────────────────────────────────

    function getCsrfToken() {
        const el = document.querySelector('input[name="csrf_token"]');
        return el ? el.value : '';
    }

    function formatMoney(kobo) {
        const naira = kobo / 100;
        return '₦' + naira.toLocaleString('en-NG', { minimumFractionDigits: kobo % 100 !== 0 ? 2 : 0 });
    }

    function updateCartBadge(count) {
        document.dispatchEvent(new CustomEvent('cartUpdated', { detail: { count } }));
    }

    // ─── Quantity controls ────────────────────────────────────────────────────

    function initQtyControls() {
        document.querySelectorAll('.js-update-form').forEach(function (form) {
            const itemId   = form.dataset.itemId;
            const input    = form.querySelector('.js-qty-input');
            const minusBtn = form.querySelector('.js-qty-minus');
            const plusBtn  = form.querySelector('.js-qty-plus');
            if (!input) return;

            function submitUpdate() {
                const qty = parseInt(input.value, 10);
                if (isNaN(qty)) return;

                fetch(form.action, {
                    method:  'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type':     'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        csrf_token:   getCsrfToken(),
                        cart_item_id: itemId,
                        quantity:     qty,
                    }),
                })
                .then(r => r.json())
                .then(function (data) {
                    if (!data.success) return;
                    updateCartBadge(data.count);

                    if (qty <= 0) {
                        // Remove the item row
                        const row = document.getElementById('cart-item-' + itemId);
                        if (row) row.remove();
                    } else {
                        // Update line total
                        recalcLineTotals();
                    }
                    recalcSummary();
                })
                .catch(console.error);
            }

            if (minusBtn) {
                minusBtn.addEventListener('click', function () {
                    const min = parseInt(input.min, 10) || 1;
                    input.value = Math.max(min, parseInt(input.value, 10) - 1);
                    submitUpdate();
                });
            }

            if (plusBtn) {
                plusBtn.addEventListener('click', function () {
                    const max = parseInt(input.max, 10) || 99;
                    input.value = Math.min(max, parseInt(input.value, 10) + 1);
                    submitUpdate();
                });
            }

            // Debounce manual input
            let debounceTimer;
            input.addEventListener('change', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(submitUpdate, 400);
            });
        });
    }

    // ─── Remove item ─────────────────────────────────────────────────────────

    function initRemoveButtons() {
        document.querySelectorAll('.js-remove-form').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const itemId = form.querySelector('input[name="cart_item_id"]').value;

                fetch(form.action, {
                    method:  'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type':     'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        csrf_token:   getCsrfToken(),
                        cart_item_id: itemId,
                    }),
                })
                .then(r => r.json())
                .then(function (data) {
                    if (!data.success) return;
                    const row = document.getElementById('cart-item-' + itemId);
                    if (row) {
                        row.style.transition = 'opacity 0.3s ease, height 0.3s ease';
                        row.style.opacity    = '0';
                        setTimeout(function () {
                            row.remove();
                            recalcSummary();
                            checkEmptyCart();
                        }, 300);
                    }
                    updateCartBadge(data.count);
                })
                .catch(console.error);
            });
        });
    }

    // ─── Recalculate line totals from current qty inputs ─────────────────────

    function recalcLineTotals() {
        document.querySelectorAll('.cart-item').forEach(function (row) {
            const itemId   = row.id.replace('cart-item-', '');
            const input    = row.querySelector('.js-qty-input');
            const priceEl  = row.querySelector('.js-line-total[data-item-id="' + itemId + '"]');
            if (!input || !priceEl) return;

            const qty        = parseInt(input.value, 10) || 1;
            const priceAtAdd = parseInt(priceEl.dataset.priceAtAdd || '0', 10);
            if (priceAtAdd) priceEl.textContent = formatMoney(priceAtAdd * qty);
        });
    }

    // ─── Recalculate order summary ────────────────────────────────────────────

    function recalcSummary() {
        let subtotal = 0;

        document.querySelectorAll('.cart-item').forEach(function (row) {
            const input    = row.querySelector('.js-qty-input');
            const priceEl  = row.querySelector('.js-line-total');
            if (!input || !priceEl) return;
            const qty        = parseInt(input.value, 10) || 1;
            const priceAtAdd = parseInt(priceEl.dataset.priceAtAdd || '0', 10);
            subtotal += priceAtAdd * qty;
        });

        const subtotalEl = document.getElementById('summary-subtotal');
        const totalEl    = document.getElementById('summary-total');
        if (subtotalEl) subtotalEl.textContent = formatMoney(subtotal);

        const deliveryFee = getCurrentDeliveryFee();
        const total       = subtotal + deliveryFee;
        if (totalEl) totalEl.textContent = formatMoney(total);
    }

    // ─── Delivery estimate ────────────────────────────────────────────────────

    function getCurrentDeliveryFee() {
        const select = document.getElementById('delivery-state');
        if (!select || !select.value) return 0;
        const opt = select.options[select.selectedIndex];
        return parseInt(opt.dataset.fee || '0', 10);
    }

    function initDeliveryEstimate() {
        const select      = document.getElementById('delivery-state');
        const deliveryEl  = document.getElementById('summary-delivery');
        const totalEl     = document.getElementById('summary-total');
        const subtotalEl  = document.getElementById('summary-subtotal');
        if (!select) return;

        select.addEventListener('change', function () {
            if (!select.value) {
                if (deliveryEl) deliveryEl.textContent = '—';
                return;
            }

            const fee      = getCurrentDeliveryFee();
            const subtotal = typeof FREE_DELIVERY_THRESHOLD !== 'undefined'
                ? parseSubtotalFromEl(subtotalEl)
                : 0;

            const actualFee = subtotal >= FREE_DELIVERY_THRESHOLD ? 0 : fee;

            if (deliveryEl) deliveryEl.textContent = actualFee === 0 ? 'FREE' : formatMoney(actualFee);
            if (totalEl)    totalEl.textContent    = formatMoney(subtotal + actualFee);
        });
    }

    function parseSubtotalFromEl(el) {
        if (!el) return 0;
        // Parse "₦18,000" → 1800000 kobo
        const text = el.textContent.replace(/[₦,]/g, '').trim();
        const naira = parseFloat(text);
        return isNaN(naira) ? 0 : Math.round(naira * 100);
    }

    // ─── Check if cart is now empty ──────────────────────────────────────────

    function checkEmptyCart() {
        const items = document.querySelectorAll('.cart-item');
        if (items.length === 0) {
            window.location.reload();
        }
    }

    // ─── Bootstrap ───────────────────────────────────────────────────────────

    document.addEventListener('DOMContentLoaded', function () {
        initQtyControls();
        initRemoveButtons();
        initDeliveryEstimate();
    });

}());
