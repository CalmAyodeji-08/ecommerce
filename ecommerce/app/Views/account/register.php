<?php
/**
 * Register page — Phase 6
 */
?>

<div class="auth-card" style="max-width:480px;">
    <h1 class="auth-card__title">Create Account</h1>
    <p class="auth-card__subtitle">Join AZFAT BEST BOUTIQUE and start shopping.</p>

    <form method="POST" action="<?= url('/account/register') ?>" novalidate>
        <?= csrfField() ?>

        <!-- Name row -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);">
            <div class="form-group">
                <label class="form-label" for="first_name">First name</label>
                <input
                    type="text"
                    id="first_name"
                    name="first_name"
                    class="form-control"
                    autocomplete="given-name"
                    value="<?= e($_POST['first_name'] ?? '') ?>"
                    required
                    aria-required="true"
                >
            </div>
            <div class="form-group">
                <label class="form-label" for="last_name">Last name</label>
                <input
                    type="text"
                    id="last_name"
                    name="last_name"
                    class="form-control"
                    autocomplete="family-name"
                    value="<?= e($_POST['last_name'] ?? '') ?>"
                    required
                    aria-required="true"
                >
            </div>
        </div>

        <!-- Email -->
        <div class="form-group">
            <label class="form-label" for="email">Email address</label>
            <input
                type="email"
                id="email"
                name="email"
                class="form-control"
                autocomplete="email"
                value="<?= e($_POST['email'] ?? '') ?>"
                required
                aria-required="true"
            >
        </div>

        <!-- Phone (optional) -->
        <div class="form-group">
            <label class="form-label" for="phone">
                Phone number
                <span style="font-weight:400;color:var(--clr-grey-500);">(optional)</span>
            </label>
            <input
                type="tel"
                id="phone"
                name="phone"
                class="form-control"
                autocomplete="tel"
                value="<?= e($_POST['phone'] ?? '') ?>"
                placeholder="08012345678"
            >
        </div>

        <!-- Password -->
        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                class="form-control"
                autocomplete="new-password"
                minlength="8"
                required
                aria-required="true"
                aria-describedby="password-hint"
            >
            <span id="password-hint" style="font-size:var(--text-xs);color:var(--clr-grey-500);">
                Minimum 8 characters
            </span>
        </div>

        <!-- Confirm password -->
        <div class="form-group">
            <label class="form-label" for="confirm_password">Confirm password</label>
            <input
                type="password"
                id="confirm_password"
                name="confirm_password"
                class="form-control"
                autocomplete="new-password"
                minlength="8"
                required
                aria-required="true"
            >
        </div>

        <button type="submit" class="btn btn--primary btn--full" style="margin-top:var(--space-2);">
            Create Account
        </button>
    </form>

    <p class="auth-card__footer">
        Already have an account?
        <a href="<?= url('/account/login') ?>">Log in</a>
    </p>
</div>
