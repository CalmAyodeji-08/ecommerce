<?php
/**
 * Reset password page — Phase 6
 * Variables: $token (string from controller)
 */
$token = $token ?? trim($_GET['token'] ?? '');
?>

<div class="auth-card">
    <h1 class="auth-card__title">Reset Password</h1>
    <p class="auth-card__subtitle">Choose a new password for your account.</p>

    <form method="POST" action="<?= url('/account/reset-password') ?>" novalidate>
        <?= csrfField() ?>
        <input type="hidden" name="token" value="<?= e($token) ?>">

        <div class="form-group">
            <label class="form-label" for="new_password">New password</label>
            <input
                type="password"
                id="new_password"
                name="new_password"
                class="form-control"
                autocomplete="new-password"
                minlength="8"
                required
                aria-required="true"
                aria-describedby="pw-hint"
            >
            <span id="pw-hint" style="font-size:var(--text-xs);color:var(--clr-grey-500);">
                Minimum 8 characters
            </span>
        </div>

        <div class="form-group">
            <label class="form-label" for="confirm_password">Confirm new password</label>
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
            Update Password
        </button>
    </form>

    <p class="auth-card__footer">
        <a href="<?= url('/account/login') ?>">← Back to log in</a>
    </p>
</div>
