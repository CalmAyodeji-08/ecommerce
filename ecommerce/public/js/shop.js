/**
 * shop.js — Product page & filter drawer interactions
 * AZFAT BEST BOUTIQUE — Phase 4
 *
 * Relies on:
 *   - VARIANTS global (array of variant objects: {id, size, colour, stock, sku})
 *     injected via <script>const VARIANTS = ...;</script> in product/show.php
 */

(function () {
  'use strict';

  /* ─── Gallery switcher ──────────────────────────────────────────────── */

  /**
   * Called by onclick on each thumbnail button.
   * @param {HTMLElement} btn
   */
  window.switchGalleryImage = function (btn) {
    const mainImg = document.getElementById('gallery-main-img');
    if (!mainImg) return;

    const src = btn.dataset.imgSrc;
    const alt = btn.dataset.imgAlt;

    if (src) mainImg.src = src;
    if (alt) mainImg.alt = alt;

    // Update active state on thumbnails
    const thumbs = document.querySelectorAll('.product-gallery__thumb');
    thumbs.forEach(function (t) { t.classList.remove('active'); });
    btn.classList.add('active');
  };

  /* ─── Size & colour pickers ─────────────────────────────────────────── */

  let selectedSize   = null;
  let selectedColour = null;

  function updateVariantId() {
    if (typeof VARIANTS === 'undefined') return;

    const variantInput = document.getElementById('selected-variant-id');
    const atbBtn       = document.getElementById('atb-btn-main');
    const stickyBtn    = document.getElementById('sticky-atb-btn');
    const stockBadge   = document.getElementById('stock-badge');

    if (!variantInput) return;

    // Find a matching variant
    let matched = null;

    if (selectedSize && selectedColour) {
      matched = VARIANTS.find(function (v) {
        return v.size === selectedSize && v.colour === selectedColour;
      });
    } else if (selectedSize) {
      // Match any colour with this size that has stock
      matched = VARIANTS.find(function (v) {
        return v.size === selectedSize && parseInt(v.stock, 10) > 0;
      }) || VARIANTS.find(function (v) {
        return v.size === selectedSize;
      });
    } else if (selectedColour) {
      matched = VARIANTS.find(function (v) {
        return v.colour === selectedColour && parseInt(v.stock, 10) > 0;
      }) || VARIANTS.find(function (v) {
        return v.colour === selectedColour;
      });
    }

    if (matched) {
      variantInput.value = matched.id;
      const stock = parseInt(matched.stock, 10);

      // Update stock badge
      if (stockBadge) {
        if (stock === 0) {
          stockBadge.textContent = 'Out of Stock';
          stockBadge.className   = 'badge badge--error';
        } else if (stock <= 5) {
          stockBadge.textContent = 'Only ' + stock + ' left!';
          stockBadge.className   = 'badge badge--warning';
        } else {
          stockBadge.textContent = 'In Stock';
          stockBadge.className   = 'badge badge--success';
        }
      }

      // Update qty max
      const qtyInput = document.getElementById('qty-input');
      if (qtyInput) {
        qtyInput.max = stock > 0 ? stock : 1;
        if (parseInt(qtyInput.value, 10) > stock) {
          qtyInput.value = Math.max(1, stock);
        }
      }

      // Enable / disable Add to Bag
      const disabled = stock === 0;
      [atbBtn, stickyBtn].forEach(function (btn) {
        if (!btn) return;
        btn.disabled = disabled;
        btn.setAttribute('aria-disabled', disabled ? 'true' : 'false');
        btn.textContent = disabled ? 'Out of Stock' : 'Add to Bag';
      });

    } else {
      variantInput.value = '';
    }
  }

  function initSizePicker() {
    const btns = document.querySelectorAll('.size-btn:not([disabled])');
    if (!btns.length) return;

    btns.forEach(function (btn) {
      btn.addEventListener('click', function () {
        const size = btn.dataset.size;

        if (selectedSize === size) {
          // Deselect
          btn.classList.remove('selected');
          btn.setAttribute('aria-pressed', 'false');
          selectedSize = null;
          const label = document.getElementById('selected-size-label');
          if (label) label.textContent = '';
        } else {
          btns.forEach(function (b) {
            b.classList.remove('selected');
            b.setAttribute('aria-pressed', 'false');
          });
          btn.classList.add('selected');
          btn.setAttribute('aria-pressed', 'true');
          selectedSize = size;
          const label = document.getElementById('selected-size-label');
          if (label) label.textContent = '— ' + size;
        }
        updateVariantId();
      });
    });
  }

  function initColourPicker() {
    const btns = document.querySelectorAll('.colour-btn');
    if (!btns.length) return;

    btns.forEach(function (btn) {
      btn.addEventListener('click', function () {
        const colour = btn.dataset.colour;

        if (selectedColour === colour) {
          btn.classList.remove('selected');
          btn.setAttribute('aria-pressed', 'false');
          selectedColour = null;
          const label = document.getElementById('selected-colour-label');
          if (label) label.textContent = '';
        } else {
          btns.forEach(function (b) {
            b.classList.remove('selected');
            b.setAttribute('aria-pressed', 'false');
          });
          btn.classList.add('selected');
          btn.setAttribute('aria-pressed', 'true');
          selectedColour = colour;
          const label = document.getElementById('selected-colour-label');
          if (label) label.textContent = '— ' + colour;
        }
        updateVariantId();
      });
    });
  }

  /* ─── Quantity input ────────────────────────────────────────────────── */

  function initQty() {
    const qtyInput = document.getElementById('qty-input');
    if (!qtyInput) return;

    const minusBtn = document.querySelector('.js-qty-minus');
    const plusBtn  = document.querySelector('.js-qty-plus');

    function clampQty() {
      let val = parseInt(qtyInput.value, 10);
      const min = parseInt(qtyInput.min, 10) || 1;
      const max = parseInt(qtyInput.max, 10) || 999;
      if (isNaN(val) || val < min) val = min;
      if (val > max) val = max;
      qtyInput.value = val;
    }

    if (minusBtn) {
      minusBtn.addEventListener('click', function () {
        qtyInput.value = Math.max(1, parseInt(qtyInput.value, 10) - 1);
        clampQty();
      });
    }

    if (plusBtn) {
      plusBtn.addEventListener('click', function () {
        const max = parseInt(qtyInput.max, 10) || 999;
        qtyInput.value = Math.min(max, parseInt(qtyInput.value, 10) + 1);
        clampQty();
      });
    }

    qtyInput.addEventListener('change', clampQty);
    qtyInput.addEventListener('blur',   clampQty);
  }

  /* ─── Filter drawer (mobile) ────────────────────────────────────────── */

  function initFilterDrawer() {
    const toggleBtn = document.querySelector('.js-filter-toggle');
    const sidebar   = document.getElementById('filter-sidebar');
    if (!toggleBtn || !sidebar) return;

    toggleBtn.addEventListener('click', function () {
      const isOpen = sidebar.classList.toggle('open');
      toggleBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });

    // Close drawer when clicking outside on mobile
    document.addEventListener('click', function (e) {
      if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
        sidebar.classList.remove('open');
        toggleBtn.setAttribute('aria-expanded', 'false');
      }
    });
  }

  /* ─── Form validation: require size/colour if variants exist ────────── */

  function initFormValidation() {
    const form = document.getElementById('add-to-cart-form');
    if (!form) return;

    const hasSizes   = document.querySelectorAll('.size-btn').length > 0;
    const hasColours = document.querySelectorAll('.colour-btn').length > 0;

    form.addEventListener('submit', function (e) {
      const variantInput = document.getElementById('selected-variant-id');
      if (!variantInput) return;

      if ((hasSizes || hasColours) && !variantInput.value) {
        e.preventDefault();
        let msg = 'Please select ';
        if (hasSizes && hasColours) {
          msg += 'a size and colour';
        } else if (hasSizes) {
          msg += 'a size';
        } else {
          msg += 'a colour';
        }
        msg += ' before adding to bag.';
        alert(msg);
      }
    });
  }

  /* ─── Bootstrap ─────────────────────────────────────────────────────── */

  document.addEventListener('DOMContentLoaded', function () {
    initSizePicker();
    initColourPicker();
    initQty();
    initFilterDrawer();
    initFormValidation();
  });

}());
