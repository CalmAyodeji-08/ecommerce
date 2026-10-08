'use strict';

(function () {

    // ─── Delivery fee calculator ──────────────────────────────────────────────
    function formatMoney(kobo) {
        const naira = kobo / 100;
        return '₦' + naira.toLocaleString('en-NG', {
            minimumFractionDigits: kobo % 100 !== 0 ? 2 : 0
        });
    }

    function updateDeliveryFee(state) {
        const feeEl   = document.getElementById('summary-delivery-fee');
        const totalEl = document.getElementById('summary-total');
        if (!feeEl || !totalEl) return;

        if (!state || !DELIVERY_ZONES[state]) {
            feeEl.textContent  = 'Select a state';
            return;
        }

        // Get subtotal from the page's hidden data
        const subtotalEl = document.querySelector('[data-subtotal]');
        const subtotal   = subtotalEl ? parseInt(subtotalEl.dataset.subtotal, 10) : 0;

        let fee = parseInt(DELIVERY_ZONES[state], 10) || 0;

        // Apply free delivery threshold
        if (subtotal >= FREE_DELIVERY_THRESHOLD) {
            fee = 0;
        }

        feeEl.textContent  = fee === 0 ? 'FREE 🎉' : formatMoney(fee);
        totalEl.textContent = formatMoney(subtotal + fee);
    }

    function initStateSelect() {
        const select = document.querySelector('.js-state-select');
        if (!select) return;

        select.addEventListener('change', function () {
            updateDeliveryFee(this.value);
        });

        // Trigger on load if state already selected
        if (select.value) updateDeliveryFee(select.value);
    }

    // ─── Saved address autofill ───────────────────────────────────────────────
    function initSavedAddresses() {
        const radios = document.querySelectorAll('.js-saved-address');
        if (!radios.length) return;

        const saveWrap = document.getElementById('save-address-wrap');

        radios.forEach(function (radio) {
            radio.addEventListener('change', function () {
                // Update selected styling
                document.querySelectorAll('.saved-address-card').forEach(function (card) {
                    card.classList.remove('selected');
                });
                this.closest('.saved-address-card').classList.add('selected');

                // Hide "save address" checkbox when picking a saved address
                if (saveWrap) {
                    saveWrap.style.display = this.value ? 'none' : 'flex';
                }

                // Fill in the form fields if data attributes exist
                const d = this.dataset;
                if (!d.firstName) return; // "New address" option

                const set = (id, val) => {
                    const el = document.getElementById(id);
                    if (el) el.value = val || '';
                };

                set('co-first-name', d.firstName);
                set('co-last-name',  d.lastName);
                set('co-phone',      d.phone);
                set('co-addr1',      d.line1);
                set('co-addr2',      d.line2);
                set('co-city',       d.city);

                const stateSelect = document.getElementById('co-state');
                if (stateSelect && d.state) {
                    stateSelect.value = d.state;
                    updateDeliveryFee(d.state);
                }
            });

            // Auto-fill on page load for the checked (default) address
            if (radio.checked && radio.dataset.firstName) {
                radio.dispatchEvent(new Event('change'));
            }
        });
    }

    // ─── Form validation ──────────────────────────────────────────────────────
    function initFormValidation() {
        const form = document.getElementById('checkout-form');
        const btn  = document.getElementById('place-order-btn');
        if (!form) return;

        form.addEventListener('submit', function (e) {
            const required = form.querySelectorAll('[required]');
            let valid = true;

            required.forEach(function (field) {
                if (!field.value.trim()) {
                    field.classList.add('error');
                    valid = false;
                } else {
                    field.classList.remove('error');
                }
            });

            if (!valid) {
                e.preventDefault();
                const firstError = form.querySelector('.error');
                if (firstError) firstError.focus();
                return;
            }

            // Disable button to prevent double-submit
            if (btn) {
                btn.disabled    = true;
                btn.textContent = 'Placing order…';
            }
        });
    }

    // ─── Bootstrap ───────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        initStateSelect();
        initSavedAddresses();
        initFormValidation();
    });

}());
