<?php

declare(strict_types=1);

class HomeController {

    /**
     * GET / — redirect straight to the shop.
     * Landing page is preserved at app/Views/home/landing-page.php.bak
     * and can be restored anytime by renaming it back to index.php.
     */
    public function index(array $params = []): void {
        redirect(url('/shop'));
    }
}
