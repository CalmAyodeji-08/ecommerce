<?php

declare(strict_types=1);

require_once APP_PATH . '/Models/UserModel.php';
require_once APP_PATH . '/Models/CartModel.php';

class AccountController {

    private UserModel $users;
    private CartModel $cart;

    public function __construct() {
        $this->users = new UserModel();
        $this->cart  = new CartModel();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Dashboard  GET /account
    // ─────────────────────────────────────────────────────────────────────────

    public function dashboard(array $params = []): void {
        requireLogin();

        $user         = $this->users->findById((int) currentUserId());
        $allOrders    = $this->users->getOrders((int) currentUserId());
        $recentOrders = array_slice($allOrders, 0, 3);

        $pageTitle = 'My Account — ' . APP_NAME;

        ob_start();
        require VIEW_PATH . '/account/dashboard.php';
        $content = ob_get_clean();

        require VIEW_PATH . '/layouts/main.php';
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Login  GET /account/login
    // ─────────────────────────────────────────────────────────────────────────

    public function loginForm(array $params = []): void {
        if (isLoggedIn()) {
            // If already logged in, go to redirect param or account
            $redirectTo = $_GET['redirect'] ?? '';
            redirect($redirectTo ?: url('/account'));
        }

        $pageTitle = 'Log In — ' . APP_NAME;

        ob_start();
        require VIEW_PATH . '/account/login.php';
        $content = ob_get_clean();

        require VIEW_PATH . '/layouts/main.php';
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Login  POST /account/login
    // ─────────────────────────────────────────────────────────────────────────

    public function login(array $params = []): void {
        if (!verifyCsrf()) {
            flash('error', 'Invalid request. Please try again.');
            redirect(url('/account/login'));
        }

        // ── Rate limiting ─────────────────────────────────────────────────
        $now       = time();
        $attempts  = (int) ($_SESSION['login_attempts']    ?? 0);
        $lastAttempt = (int) ($_SESSION['login_last_attempt'] ?? 0);
        $windowSec = 15 * 60; // 15 minutes

        // Reset counter if window has expired
        if ($lastAttempt > 0 && ($now - $lastAttempt) > $windowSec) {
            $attempts = 0;
            $_SESSION['login_attempts'] = 0;
        }

        if ($attempts >= 5) {
            $remaining = $windowSec - ($now - $lastAttempt);
            $mins      = (int) ceil($remaining / 60);
            flash('error', "Too many failed attempts. Please wait {$mins} minute(s) before trying again.");
            redirect(url('/account/login'));
        }
        // ──────────────────────────────────────────────────────────────────

        $email    = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            flash('error', 'Email and password are required.');
            redirect(url('/account/login'));
        }

        $user = $this->users->findByEmail($email);

        if (!$user || !password_verify($password, (string) $user['password_hash'])) {
            $_SESSION['login_attempts']    = $attempts + 1;
            $_SESSION['login_last_attempt'] = $now;
            flash('error', 'Invalid email or password.');
            redirect(url('/account/login'));
        }

        if (!(bool) $user['is_active']) {
            flash('error', 'Your account has been deactivated. Please contact support.');
            redirect(url('/account/login'));
        }

        // ── Successful login ──────────────────────────────────────────────
        // Reset rate-limit counters
        unset($_SESSION['login_attempts'], $_SESSION['login_last_attempt']);

        $this->users->updateLastLogin((int) $user['id']);

        // Capture guest cart before session is repopulated
        $guestCartId = null;
        if (!empty($_SESSION['cart_session_id'])) {
            $guestCartId = $this->cart->getOrCreateCart();
        }

        $_SESSION['user_id']   = (int) $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['first_name'];

        // Merge guest cart into user cart
        if ($guestCartId !== null) {
            $userCartId = $this->cart->getOrCreateCart();
            if ($guestCartId !== $userCartId) {
                $this->cart->mergeGuestCart($guestCartId, $userCartId);
            }
            $this->cart->updateSessionCount($userCartId);
        } else {
            // Sync badge for already-existing user cart
            $userCartId = $this->cart->getOrCreateCart();
            $this->cart->updateSessionCount($userCartId);
        }

        flash('success', 'Welcome back, ' . e($user['first_name']) . '!');

        // Redirect back to where they were going, or account dashboard
        $redirectTo = $_POST['redirect'] ?? '';
        // Only allow relative URLs to prevent open redirect
        if ($redirectTo && str_starts_with($redirectTo, APP_URL)) {
            redirect($redirectTo);
        }
        redirect(url('/account'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Register  GET /account/register
    // ─────────────────────────────────────────────────────────────────────────

    public function registerForm(array $params = []): void {
        if (isLoggedIn()) {
            redirect(url('/account'));
        }

        $pageTitle = 'Create Account — ' . APP_NAME;

        ob_start();
        require VIEW_PATH . '/account/register.php';
        $content = ob_get_clean();

        require VIEW_PATH . '/layouts/main.php';
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Register  POST /account/register
    // ─────────────────────────────────────────────────────────────────────────

    public function register(array $params = []): void {
        if (!verifyCsrf()) {
            flash('error', 'Invalid request. Please try again.');
            redirect(url('/account/register'));
        }

        $firstName       = trim($_POST['first_name']       ?? '');
        $lastName        = trim($_POST['last_name']        ?? '');
        $email           = strtolower(trim($_POST['email'] ?? ''));
        $phone           = trim($_POST['phone']            ?? '');
        $password        = $_POST['password']              ?? '';
        $confirmPassword = $_POST['confirm_password']      ?? '';

        $errors = [];

        if ($firstName === '') $errors[] = 'First name is required.';
        if ($lastName  === '') $errors[] = 'Last name is required.';
        if ($email     === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'A valid email address is required.';
        }
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters.';
        }
        if ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match.';
        }

        if (!empty($errors)) {
            foreach ($errors as $err) flash('error', $err);
            redirect(url('/account/register'));
        }

        // Check email not taken
        if ($this->users->findByEmail($email)) {
            flash('error', 'An account with this email already exists. Please log in.');
            redirect(url('/account/register'));
        }

        // Capture guest cart before changing session
        $guestCartId = null;
        if (!empty($_SESSION['cart_session_id'])) {
            $guestCartId = $this->cart->getOrCreateCart();
        }

        $userId = $this->users->create([
            'first_name' => $firstName,
            'last_name'  => $lastName,
            'email'      => $email,
            'phone'      => $phone,
            'password'   => $password,
        ]);

        // Auto-login
        $_SESSION['user_id']   = $userId;
        $_SESSION['user_role'] = 'customer';
        $_SESSION['user_name'] = $firstName;

        // Merge guest cart
        if ($guestCartId !== null) {
            $userCartId = $this->cart->getOrCreateCart();
            if ($guestCartId !== $userCartId) {
                $this->cart->mergeGuestCart($guestCartId, $userCartId);
            }
            $this->cart->updateSessionCount($userCartId);
        }

        flash('success', 'Welcome to ' . APP_NAME . ', ' . $firstName . '! Your account is ready.');
        redirect(url('/account'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Logout  GET /account/logout
    // ─────────────────────────────────────────────────────────────────────────

    public function logout(array $params = []): void {
        session_unset();
        session_destroy();
        redirect(url('/'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Forgot password  GET /account/forgot-password
    // ─────────────────────────────────────────────────────────────────────────

    public function forgotForm(array $params = []): void {
        $pageTitle = 'Forgot Password — ' . APP_NAME;

        ob_start();
        require VIEW_PATH . '/account/forgot.php';
        $content = ob_get_clean();

        require VIEW_PATH . '/layouts/main.php';
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Forgot password  POST /account/forgot-password
    // ─────────────────────────────────────────────────────────────────────────

    public function forgot(array $params = []): void {
        if (!verifyCsrf()) {
            flash('error', 'Invalid request. Please try again.');
            redirect(url('/account/forgot-password'));
        }

        $email = strtolower(trim($_POST['email'] ?? ''));

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Please enter a valid email address.');
            redirect(url('/account/forgot-password'));
        }

        $user = $this->users->findByEmail($email);

        if ($user) {
            $token   = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hour
            $this->users->setResetToken((int) $user['id'], $token, $expires);

            $resetLink = url('/account/reset-password') . '?token=' . urlencode($token);

            // Phase 8 will add real email — for now expose the link in dev
            $appEnv = defined('APP_ENV') ? APP_ENV : (getenv('APP_ENV') ?: 'local');
            if ($appEnv === 'local' || $appEnv === 'development') {
                flash('info', 'Reset link (dev only — email coming in Phase 8): ' . $resetLink);
            }
        }

        // Always show the same message to prevent email enumeration
        flash('success', 'If that email exists in our system, a reset link has been sent.');
        redirect(url('/account/login'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Reset password  GET /account/reset-password
    // ─────────────────────────────────────────────────────────────────────────

    public function resetForm(array $params = []): void {
        $token = trim($_GET['token'] ?? '');

        if ($token === '') {
            flash('error', 'Invalid or missing reset token.');
            redirect(url('/account/forgot-password'));
        }

        $user = $this->users->findByResetToken($token);
        if (!$user) {
            flash('error', 'This reset link has expired or is invalid. Please request a new one.');
            redirect(url('/account/forgot-password'));
        }

        $pageTitle = 'Reset Password — ' . APP_NAME;

        ob_start();
        require VIEW_PATH . '/account/reset.php';
        $content = ob_get_clean();

        require VIEW_PATH . '/layouts/main.php';
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Reset password  POST /account/reset-password
    // ─────────────────────────────────────────────────────────────────────────

    public function reset(array $params = []): void {
        if (!verifyCsrf()) {
            flash('error', 'Invalid request. Please try again.');
            redirect(url('/account/forgot-password'));
        }

        $token           = trim($_POST['token']            ?? '');
        $password        = $_POST['new_password']          ?? '';
        $confirmPassword = $_POST['confirm_password']      ?? '';

        if ($token === '') {
            flash('error', 'Invalid reset token.');
            redirect(url('/account/forgot-password'));
        }

        $user = $this->users->findByResetToken($token);
        if (!$user) {
            flash('error', 'This reset link has expired or is invalid. Please request a new one.');
            redirect(url('/account/forgot-password'));
        }

        if (strlen($password) < 8) {
            flash('error', 'Password must be at least 8 characters.');
            redirect(url('/account/reset-password') . '?token=' . urlencode($token));
        }

        if ($password !== $confirmPassword) {
            flash('error', 'Passwords do not match.');
            redirect(url('/account/reset-password') . '?token=' . urlencode($token));
        }

        $this->users->updatePassword((int) $user['id'], $password);
        $this->users->clearResetToken((int) $user['id']);

        flash('success', 'Your password has been updated. You can now log in.');
        redirect(url('/account/login'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Addresses  GET /account/addresses
    // ─────────────────────────────────────────────────────────────────────────

    public function addresses(array $params = []): void {
        requireLogin();

        $user      = $this->users->findById((int) currentUserId());
        $addresses = $this->users->getAddresses((int) currentUserId());
        $states    = $this->nigerianStates();

        $pageTitle = 'My Addresses — ' . APP_NAME;

        ob_start();
        require VIEW_PATH . '/account/addresses.php';
        $content = ob_get_clean();

        require VIEW_PATH . '/layouts/main.php';
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Add address  POST /account/addresses/add
    // Also handles "set_default" sub-action from addresses view.
    // ─────────────────────────────────────────────────────────────────────────

    public function addAddress(array $params = []): void {
        requireLogin();

        if (!verifyCsrf()) {
            flash('error', 'Invalid request.');
            redirect(url('/account/addresses'));
        }

        // Sub-action: set an existing address as default
        if (($_POST['action'] ?? '') === 'set_default') {
            $addressId = (int) ($_POST['set_default_id'] ?? 0);
            if ($addressId > 0) {
                $this->users->setDefaultAddress($addressId, (int) currentUserId());
                flash('success', 'Default address updated.');
            }
            redirect(url('/account/addresses'));
        }

        // Normal add-address flow
        $data = [
            'first_name'    => trim($_POST['first_name']    ?? ''),
            'last_name'     => trim($_POST['last_name']     ?? ''),
            'phone'         => trim($_POST['phone']         ?? ''),
            'address_line1' => trim($_POST['address_line1'] ?? ''),
            'address_line2' => trim($_POST['address_line2'] ?? ''),
            'city'          => trim($_POST['city']          ?? ''),
            'state'         => trim($_POST['state']         ?? ''),
        ];

        $errors = [];
        if ($data['first_name']    === '') $errors[] = 'First name is required.';
        if ($data['last_name']     === '') $errors[] = 'Last name is required.';
        if ($data['phone']         === '') $errors[] = 'Phone number is required.';
        if ($data['address_line1'] === '') $errors[] = 'Address is required.';
        if ($data['city']          === '') $errors[] = 'City is required.';
        if ($data['state']         === '') $errors[] = 'State is required.';

        if (!empty($errors)) {
            foreach ($errors as $err) flash('error', $err);
            redirect(url('/account/addresses'));
        }

        $newId = $this->users->addAddress((int) currentUserId(), $data);

        // Override default if user explicitly ticked the checkbox
        if (!empty($_POST['set_default'])) {
            $this->users->setDefaultAddress($newId, (int) currentUserId());
        }

        flash('success', 'Address added successfully.');
        redirect(url('/account/addresses'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Delete address  POST /account/addresses/delete
    // ─────────────────────────────────────────────────────────────────────────

    public function deleteAddress(array $params = []): void {
        requireLogin();

        if (!verifyCsrf()) {
            flash('error', 'Invalid request.');
            redirect(url('/account/addresses'));
        }

        $addressId = (int) ($_POST['address_id'] ?? 0);
        if ($addressId > 0) {
            $this->users->deleteAddress($addressId, (int) currentUserId());
            flash('success', 'Address removed.');
        }

        redirect(url('/account/addresses'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Orders  GET /account/orders
    // ─────────────────────────────────────────────────────────────────────────

    public function orders(array $params = []): void {
        requireLogin();

        $user   = $this->users->findById((int) currentUserId());
        $orders = $this->users->getOrders((int) currentUserId());

        $pageTitle = 'My Orders — ' . APP_NAME;

        ob_start();
        require VIEW_PATH . '/account/orders.php';
        $content = ob_get_clean();

        require VIEW_PATH . '/layouts/main.php';
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Order detail  GET /account/orders/:id
    // ─────────────────────────────────────────────────────────────────────────

    public function orderDetail(array $params = []): void {
        requireLogin();

        $orderId = (int) ($params['id'] ?? 0);
        $result  = $this->users->getOrderDetail($orderId, (int) currentUserId());

        if (!$result) {
            http_response_code(404);
            require VIEW_PATH . '/errors/404.php';
            return;
        }

        $user  = $this->users->findById((int) currentUserId());
        $order = $result;
        $items = $result['items'] ?? [];

        $pageTitle = 'Order ' . e($order['order_number']) . ' — ' . APP_NAME;

        ob_start();
        require VIEW_PATH . '/account/order-detail.php';
        $content = ob_get_clean();

        require VIEW_PATH . '/layouts/main.php';
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Wishlist  GET /account/wishlist
    // ─────────────────────────────────────────────────────────────────────────

    public function wishlist(array $params = []): void {
        requireLogin();

        $user          = $this->users->findById((int) currentUserId());
        $wishlistItems = $this->users->getWishlist((int) currentUserId());

        $pageTitle = 'My Wishlist — ' . APP_NAME;

        ob_start();
        require VIEW_PATH . '/account/wishlist.php';
        $content = ob_get_clean();

        require VIEW_PATH . '/layouts/main.php';
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Wishlist toggle  POST /account/wishlist/toggle
    // ─────────────────────────────────────────────────────────────────────────

    public function wishlistToggle(array $params = []): void {
        $isAjax = ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';

        if (!isLoggedIn()) {
            if ($isAjax) {
                header('Content-Type: application/json');
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Please log in to use your wishlist.']);
                exit;
            }
            flash('error', 'Please log in to use your wishlist.');
            redirect(url('/account/login'));
        }

        if (!verifyCsrf()) {
            if ($isAjax) {
                header('Content-Type: application/json');
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Invalid request.']);
                exit;
            }
            flash('error', 'Invalid request.');
            redirect(url('/account/wishlist'));
        }

        $productId = (int) ($_POST['product_id'] ?? 0);
        if ($productId <= 0) {
            if ($isAjax) {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid product.']);
                exit;
            }
            flash('error', 'Invalid product.');
            redirect(url('/account/wishlist'));
        }

        $action = $this->users->toggleWishlist((int) currentUserId(), $productId);

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'action' => $action]);
            exit;
        }

        flash('success', $action === 'added' ? 'Added to your wishlist.' : 'Removed from your wishlist.');
        redirect(url('/account/wishlist'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helper
    // ─────────────────────────────────────────────────────────────────────────

    private function nigerianStates(): array {
        return [
            'Abia','Adamawa','Akwa Ibom','Anambra','Bauchi','Bayelsa','Benue','Borno',
            'Cross River','Delta','Ebonyi','Edo','Ekiti','Enugu','FCT','Gombe','Imo',
            'Jigawa','Kaduna','Kano','Katsina','Kebbi','Kogi','Kwara','Lagos','Nassarawa',
            'Niger','Ogun','Ondo','Osun','Oyo','Plateau','Rivers','Sokoto','Taraba',
            'Yobe','Zamfara',
        ];
    }
}
