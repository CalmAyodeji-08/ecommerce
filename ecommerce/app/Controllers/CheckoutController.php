<?php

declare(strict_types=1);

require_once APP_PATH . '/Models/CartModel.php';
require_once APP_PATH . '/Models/OrderModel.php';
require_once APP_PATH . '/Models/UserModel.php';

class CheckoutController {

    private CartModel  $cartModel;
    private OrderModel $orderModel;
    private UserModel  $userModel;

    public function __construct() {
        $this->cartModel  = new CartModel();
        $this->orderModel = new OrderModel();
        $this->userModel  = new UserModel();
    }

    /**
     * GET /checkout — show the checkout form.
     */
    public function index(array $params = []): void {
        // Must be logged in
        if (!isLoggedIn()) {
            flash('info', 'Please log in or create an account to checkout.');
            redirect(url('/account/login') . '?redirect=' . urlencode(url('/checkout')));
        }

        $cartId    = $this->cartModel->getOrCreateCart();
        $cartItems = $this->cartModel->getCartItems($cartId);

        // Empty cart — redirect back
        if (empty($cartItems)) {
            flash('error', 'Your bag is empty. Add some items first.');
            redirect(url('/shop'));
        }

        $totals    = $this->cartModel->getCartTotals($cartItems);
        $subtotal  = $totals['subtotal'];

        // Saved addresses for logged-in users
        $addresses = [];
        $user      = null;
        if (isLoggedIn()) {
            $user      = $this->userModel->findById((int) currentUserId());
            $addresses = $this->userModel->getAddresses((int) currentUserId());
        }

        // All delivery zones
        $db            = getDB();
        $deliveryZones = $db->query('SELECT state, fee FROM delivery_zones ORDER BY state ASC')->fetchAll();

        $states = [
            'Abia','Adamawa','Akwa Ibom','Anambra','Bauchi','Bayelsa','Benue','Borno',
            'Cross River','Delta','Ebonyi','Edo','Ekiti','Enugu','FCT','Gombe','Imo',
            'Jigawa','Kaduna','Kano','Katsina','Kebbi','Kogi','Kwara','Lagos','Nassarawa',
            'Niger','Ogun','Ondo','Osun','Oyo','Plateau','Rivers','Sokoto','Taraba',
            'Yobe','Zamfara',
        ];

        $pageTitle = 'Checkout — ' . APP_NAME;
        $extraJs   = ['js/checkout.js'];

        ob_start();
        require VIEW_PATH . '/checkout/index.php';
        $content = ob_get_clean();

        require VIEW_PATH . '/layouts/main.php';
    }

    /**
     * POST /checkout — process the order.
     */
    public function process(array $params = []): void {
        if (!isLoggedIn()) {
            redirect(url('/account/login') . '?redirect=' . urlencode(url('/checkout')));
        }

        if (!verifyCsrf()) {
            flash('error', 'Invalid request. Please try again.');
            redirect(url('/checkout'));
        }

        $cartId    = $this->cartModel->getOrCreateCart();
        $cartItems = $this->cartModel->getCartItems($cartId);

        if (empty($cartItems)) {
            flash('error', 'Your bag is empty.');
            redirect(url('/shop'));
        }

        // ── Collect + validate delivery details ───────────────────────────
        $firstName    = trim($_POST['first_name']    ?? '');
        $lastName     = trim($_POST['last_name']     ?? '');
        $email        = strtolower(trim($_POST['email'] ?? ''));
        $phone        = trim($_POST['phone']         ?? '');
        $addressLine1 = trim($_POST['address_line1'] ?? '');
        $addressLine2 = trim($_POST['address_line2'] ?? '');
        $city         = trim($_POST['city']          ?? '');
        $state        = trim($_POST['state']         ?? '');
        $notes        = trim($_POST['notes']         ?? '');

        $errors = [];
        if ($firstName    === '') $errors[] = 'First name is required.';
        if ($lastName     === '') $errors[] = 'Last name is required.';
        if ($email        === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'A valid email address is required.';
        }
        if ($phone        === '') $errors[] = 'Phone number is required.';
        if ($addressLine1 === '') $errors[] = 'Street address is required.';
        if ($city         === '') $errors[] = 'City is required.';
        if ($state        === '') $errors[] = 'State is required.';

        if (!empty($errors)) {
            foreach ($errors as $err) flash('error', $err);
            redirect(url('/checkout'));
        }

        // ── Calculate totals server-side ──────────────────────────────────
        $totals   = $this->cartModel->getCartTotals($cartItems);
        $subtotal = $totals['subtotal'];

        // Delivery fee from DB — never trust browser
        $deliveryFee = $this->orderModel->getDeliveryFee($state);
        if ($subtotal >= freeDeliveryThreshold()) {
            $deliveryFee = 0;
        }

        $total = $subtotal + $deliveryFee;

        // ── Save address if requested ──────────────────────────────────────
        if (isLoggedIn() && !empty($_POST['save_address']) && empty($_POST['saved_address_id'])) {
            try {
                $this->userModel->addAddress((int) currentUserId(), [
                    'first_name'    => $firstName,
                    'last_name'     => $lastName,
                    'phone'         => $phone,
                    'address_line1' => $addressLine1,
                    'address_line2' => $addressLine2,
                    'city'          => $city,
                    'state'         => $state,
                ]);
            } catch (Throwable $e) {
                error_log('Failed to save address: ' . $e->getMessage());
                // Non-fatal — order still proceeds
            }
        }

        // ── Create order ──────────────────────────────────────────────────
        try {
            $orderId = $this->orderModel->createOrder([
                'user_id'        => isLoggedIn() ? currentUserId() : null,
                'guest_email'    => isLoggedIn() ? null : $email,
                'guest_phone'    => isLoggedIn() ? null : sanitisePhone($phone),
                'first_name'     => $firstName,
                'last_name'      => $lastName,
                'phone'          => sanitisePhone($phone),
                'address_line1'  => $addressLine1,
                'address_line2'  => $addressLine2 ?: null,
                'city'           => $city,
                'state'          => $state,
                'subtotal'       => $subtotal,
                'delivery_fee'   => $deliveryFee,
                'discount_amount'=> 0,
                'total'          => $total,
                'payment_method' => 'paystack',
                'notes'          => $notes ?: null,
            ], $cartItems);

        } catch (Throwable $e) {
            flash('error', 'Something went wrong placing your order. Please try again.');
            redirect(url('/checkout'));
        }

        // ── Clear cart ────────────────────────────────────────────────────
        $this->orderModel->clearCart($cartId);
        $this->cartModel->updateSessionCount($cartId);

        // Store order ID in session for payment phase
        $_SESSION['pending_order_id'] = $orderId;

        // Go to order confirmation / payment page
        redirect(url('/checkout/confirm/' . $orderId));
    }

    /**
     * GET /checkout/confirm/:id — order summary before payment.
     */
    public function confirm(array $params = []): void {
        if (!isLoggedIn()) {
            redirect(url('/account/login'));
        }

        $orderId = (int) ($params['id'] ?? 0);
        $order   = $this->orderModel->getById($orderId);

        // Security: only the owner can view their order confirmation
        if (!$order || (int) $order['user_id'] !== (int) currentUserId()) {
            http_response_code(404);
            require VIEW_PATH . '/errors/404.php';
            return;
        }

        $orderFull = $this->orderModel->getByOrderNumber($order['order_number']);

        $pageTitle = 'Order Confirmed — ' . APP_NAME;

        ob_start();
        require VIEW_PATH . '/checkout/confirm.php';
        $content = ob_get_clean();

        require VIEW_PATH . '/layouts/main.php';
    }
}
