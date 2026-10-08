<?php
/**
 * Login page — Phase 6
 */
?>

<div class="auth-card">
    <h1 class="auth-card__title">Log In</h1>
    <p class="auth-card__subtitle">
        <?php if (!empty($_GET['redirect'])): ?>
            Sign in to view this product and start shopping.
        <?php else: ?>
            Welcome back — enter your details to continue.
        <?php endif; ?>
    </p>

    <form method="POST" action="<?= url('/account/login') ?>" novalidate>
        <?= csrfField() ?>

        <!-- Preserve redirect destination -->
        <?php if (!empty($_GET['redirect'])): ?>
        <input type="hidden" name="redirect" value="<?= e($_GET['redirect']) ?>">
        <?php endif; ?>

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

        <!-- Password -->
        <div class="form-group">
            <label class="form-label" for="password">
                Password
                <a href="<?= url('/account/forgot-password') ?>"
                   style="float:right;font-size:var(--text-sm);font-weight:400;color:var(--clr-accent);">
                    Forgot password?
                </a>
            </label>
            <input
                type="password"
                id="password"
                name="password"
                class="form-control"
                autocomplete="current-password"
                required
                aria-required="true"
            >
        </div>

        <button type="submit" class="btn btn--primary btn--full" style="margin-top:var(--space-2);">
            Log In
        </button>
    </form>

    <p class="auth-card__footer">
        Don't have an account?
        <a href="<?= url('/account/register') ?>">Create one</a>
    </p>
</div>
