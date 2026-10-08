<?php
/**
 * Forgot password page — Phase 6
 */
?>

<div class="auth-card">
    <h1 class="auth-card__title">Forgot Password?</h1>
    <p class="auth-card__subtitle">Enter your email and we'll send you a reset link.</p>

    <form method="POST" action="<?= url('/account/forgot-password') ?>" novalidate>
        <?= csrfField() ?>

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

        <button type="submit" class="btn btn--primary btn--full" style="margin-top:var(--space-2);">
            Send Reset Link
        </button>
    </form>

    <p class="auth-card__footer">
        Remembered it?
        <a href="<?= url('/account/login') ?>">Log in</a>
    </p>
</div>
