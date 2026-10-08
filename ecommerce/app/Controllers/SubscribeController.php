<?php

declare(strict_types=1);

class SubscribeController {

    public function store(array $params = []): void {
        if (!verifyCsrf()) {
            flash('error', 'Invalid request. Please try again.');
            redirect(url('/'));
        }

        $email = trim(post('email', ''));

        // Server-side validation
        if (empty($email)) {
            flash('error', 'Please enter your email address.');
            redirect(url('/#signup-heading'));
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Please enter a valid email address.');
            redirect(url('/#signup-heading'));
        }

        $email = strtolower($email);

        try {
            $db   = getDB();
            $stmt = $db->prepare('SELECT id FROM subscribers WHERE email = ?');
            $stmt->execute([$email]);

            if ($stmt->fetch()) {
                flash('info', 'You\'re already on the list! We\'ll notify you first.');
            } else {
                $insert = $db->prepare('INSERT INTO subscribers (email) VALUES (?)');
                $insert->execute([$email]);
                flash('success', 'You\'re in! We\'ll notify you about new drops and deals.');
            }
        } catch (Throwable $e) {
            error_log('Subscribe error: ' . $e->getMessage());
            flash('error', 'Something went wrong. Please try again.');
        }

        redirect(url('/#signup-heading'));
    }
}
