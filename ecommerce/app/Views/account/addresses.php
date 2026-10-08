<?php
/**
 * Addresses page — Phase 6
 * Variables: $user (array), $addresses (array), $states (array)
 */
?>

<div style="display:flex;align-items:center;gap:var(--space-3);margin-bottom:var(--space-6);">
    <a href="<?= url('/account') ?>" style="font-size:var(--text-sm);color:var(--clr-grey-600);display:flex;align-items:center;gap:4px;">← My Account</a>
</div>

<h1 class="page-title" style="margin-bottom:var(--space-6);">My Addresses</h1>

        <!-- Saved addresses -->
        <?php if (empty($addresses)): ?>
        <p style="color:var(--clr-grey-600);margin-bottom:var(--space-6);">
            You haven't saved any addresses yet. Add one below.
        </p>
        <?php else: ?>
        <div style="display:grid;gap:var(--space-4);margin-bottom:var(--space-8);">
            <?php foreach ($addresses as $addr): ?>
            <div style="background:var(--clr-white);border:1px solid <?= $addr['is_default'] ? 'var(--clr-accent)' : 'var(--clr-grey-200)' ?>;border-radius:var(--radius-md);padding:var(--space-5);">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:var(--space-3);">

                    <!-- Address details -->
                    <div>
                        <?php if ($addr['is_default']): ?>
                        <span style="display:inline-block;background:var(--clr-accent-light);color:var(--clr-accent);font-size:var(--text-xs);font-weight:600;padding:2px 8px;border-radius:999px;margin-bottom:var(--space-2);">
                            Default
                        </span>
                        <?php endif; ?>
                        <p style="font-weight:600;margin:0 0 var(--space-1);">
                            <?= e($addr['first_name']) ?> <?= e($addr['last_name']) ?>
                        </p>
                        <p style="color:var(--clr-grey-700);margin:0 0 2px;"><?= e($addr['address_line1']) ?></p>
                        <?php if ($addr['address_line2']): ?>
                        <p style="color:var(--clr-grey-700);margin:0 0 2px;"><?= e($addr['address_line2']) ?></p>
                        <?php endif; ?>
                        <p style="color:var(--clr-grey-700);margin:0 0 2px;">
                            <?= e($addr['city']) ?>, <?= e($addr['state']) ?>
                        </p>
                        <p style="color:var(--clr-grey-600);font-size:var(--text-sm);margin:0;">
                            <?= e($addr['phone']) ?>
                        </p>
                    </div>

                    <!-- Actions -->
                    <div style="display:flex;flex-direction:column;gap:var(--space-2);align-items:flex-end;">
                        <?php if (!$addr['is_default']): ?>
                        <!-- Set as default -->
                        <form method="POST" action="<?= url('/account/addresses/add') ?>">
                            <?= csrfField() ?>
                            <input type="hidden" name="set_default_id" value="<?= (int) $addr['id'] ?>">
                            <input type="hidden" name="action" value="set_default">
                            <button type="submit" class="btn btn--ghost btn--sm"
                                    style="font-size:var(--text-xs);">
                                Set as Default
                            </button>
                        </form>
                        <?php endif; ?>

                        <!-- Delete -->
                        <form method="POST" action="<?= url('/account/addresses/delete') ?>"
                              onsubmit="return confirm('Remove this address?')">
                            <?= csrfField() ?>
                            <input type="hidden" name="address_id" value="<?= (int) $addr['id'] ?>">
                            <button type="submit" class="btn btn--ghost btn--sm"
                                    style="color:var(--clr-error);font-size:var(--text-xs);"
                                    aria-label="Remove address">
                                Remove
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- ── Add New Address ── -->
        <section>
            <h2 style="font-size:var(--text-lg);font-weight:700;margin-bottom:var(--space-5);">
                Add New Address
            </h2>

            <form method="POST" action="<?= url('/account/addresses/add') ?>" novalidate
                  style="background:var(--clr-white);border:1px solid var(--clr-grey-200);border-radius:var(--radius-md);padding:var(--space-6);">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="add">

                <!-- Name row -->
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);">
                    <div class="form-group">
                        <label class="form-label" for="addr_first_name">First name</label>
                        <input type="text" id="addr_first_name" name="first_name"
                               class="form-control" autocomplete="given-name" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="addr_last_name">Last name</label>
                        <input type="text" id="addr_last_name" name="last_name"
                               class="form-control" autocomplete="family-name" required>
                    </div>
                </div>

                <!-- Phone -->
                <div class="form-group">
                    <label class="form-label" for="addr_phone">Phone number</label>
                    <input type="tel" id="addr_phone" name="phone"
                           class="form-control" autocomplete="tel"
                           placeholder="08012345678" required>
                </div>

                <!-- Address line 1 -->
                <div class="form-group">
                    <label class="form-label" for="addr_line1">Street address</label>
                    <input type="text" id="addr_line1" name="address_line1"
                           class="form-control" autocomplete="address-line1" required>
                </div>

                <!-- Address line 2 -->
                <div class="form-group">
                    <label class="form-label" for="addr_line2">
                        Apartment, suite, etc.
                        <span style="font-weight:400;color:var(--clr-grey-500);">(optional)</span>
                    </label>
                    <input type="text" id="addr_line2" name="address_line2"
                           class="form-control" autocomplete="address-line2">
                </div>

                <!-- City + State -->
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);">
                    <div class="form-group">
                        <label class="form-label" for="addr_city">City / Town</label>
                        <input type="text" id="addr_city" name="city"
                               class="form-control" autocomplete="address-level2" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="addr_state">State</label>
                        <select id="addr_state" name="state" class="form-control" required
                                aria-required="true">
                            <option value="">— Select state —</option>
                            <?php foreach ($states as $state): ?>
                            <option value="<?= e($state) ?>"><?= e($state) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Set as default -->
                <div class="form-group" style="display:flex;align-items:center;gap:var(--space-2);">
                    <input type="checkbox" id="set_default" name="set_default" value="1"
                           style="width:auto;flex-shrink:0;">
                    <label for="set_default"
                           style="font-size:var(--text-sm);margin:0;cursor:pointer;">
                        Set as my default address
                    </label>
                </div>

                <button type="submit" class="btn btn--primary">
                    Save Address
                </button>
            </form>
        </section>
