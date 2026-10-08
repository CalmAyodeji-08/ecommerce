<?php

declare(strict_types=1);

require_once APP_PATH . '/Models/CartModel.php';

class CartController {

    private CartModel $cart;

    public function __construct() {
        $this->cart = new CartModel();
    }

    /**
     * GET /cart
     */
    public function index(array $params = []): void {
        $cartId    = $this->cart->getOrCreateCart();
        $cartItems = $this->cart->getCartItems($cartId);
        $totals    = $this->cart->getCartTotals($cartItems);

        // Delivery zones for estimate dropdown
        $db           = getDB();
        $deliveryZones = $db->query('SELECT state, fee FROM delivery_zones ORDER BY state ASC')->fetchAll();

        $pageTitle = 'Your Bag — ' . APP_NAME;
        $extraJs   = ['js/cart.js'];

        ob_start();
        require VIEW_PATH . '/cart/index.php';
        $content = ob_get_clean();

        require VIEW_PATH . '/layouts/main.php';
    }

    /**
     * POST /cart/add
     */
    public function add(array $params = []): void {
        if (!verifyCsrf()) {
            $this->respond(false, 'Invalid request.', 0);
            return;
        }

        $productId = (int) ($_POST['product_id'] ?? 0);
        $variantId = isset($_POST['variant_id']) && $_POST['variant_id'] !== ''
            ? (int) $_POST['variant_id']
            : null;
        $quantity  = max(1, min(20, (int) ($_POST['quantity'] ?? 1)));

        if ($productId <= 0) {
            $this->respond(false, 'Invalid product.', cartCount());
            return;
        }

        $cartId = $this->cart->getOrCreateCart();
        $result = $this->cart->addItem($cartId, $productId, $variantId, $quantity);
        $this->cart->updateSessionCount($cartId);

        $this->respond($result['success'], $result['message'], cartCount());
    }

    /**
     * POST /cart/update
     */
    public function update(array $params = []): void {
        if (!verifyCsrf()) {
            $this->respond(false, 'Invalid request.', 0);
            return;
        }

        $cartItemId = (int) ($_POST['cart_item_id'] ?? 0);
        $quantity   = (int) ($_POST['quantity'] ?? 1);

        $cartId = $this->cart->getOrCreateCart();
        $result = $this->cart->updateItem($cartItemId, $cartId, $quantity);
        $this->cart->updateSessionCount($cartId);

        $this->respond($result['success'], $result['message'], cartCount());
    }

    /**
     * POST /cart/remove
     */
    public function remove(array $params = []): void {
        if (!verifyCsrf()) {
            $this->respond(false, 'Invalid request.', 0);
            return;
        }

        $cartItemId = (int) ($_POST['cart_item_id'] ?? 0);
        $cartId     = $this->cart->getOrCreateCart();
        $success    = $this->cart->removeItem($cartItemId, $cartId);
        $this->cart->updateSessionCount($cartId);

        $this->respond($success, $success ? 'Item removed.' : 'Item not found.', cartCount());
    }

    /**
     * Respond: JSON for AJAX, flash+redirect for normal POST.
     */
    private function respond(bool $success, string $message, int $count): void {
        $isAjax = ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => $success, 'count' => $count, 'message' => $message]);
            exit;
        }

        flash($success ? 'success' : 'error', $message);
        redirect(url('/cart'));
    }
}
