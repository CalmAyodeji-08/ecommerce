<?php

declare(strict_types=1);

class CartModel {

    /**
     * Get or create a cart for the current visitor.
     * Returns the cart ID.
     */
    public function getOrCreateCart(): int {
        $db = getDB();

        if (isLoggedIn()) {
            $userId = currentUserId();
            $stmt   = $db->prepare('SELECT id FROM carts WHERE user_id = ? LIMIT 1');
            $stmt->execute([$userId]);
            $cart = $stmt->fetch();
            if ($cart) return (int) $cart['id'];

            // Create a new cart for this user
            $db->prepare('INSERT INTO carts (user_id) VALUES (?)')->execute([$userId]);
            return (int) $db->lastInsertId();
        }

        // Guest cart — keyed by session
        if (empty($_SESSION['cart_session_id'])) {
            $_SESSION['cart_session_id'] = bin2hex(random_bytes(16));
        }
        $sessionId = $_SESSION['cart_session_id'];

        $stmt = $db->prepare('SELECT id FROM carts WHERE session_id = ? LIMIT 1');
        $stmt->execute([$sessionId]);
        $cart = $stmt->fetch();
        if ($cart) return (int) $cart['id'];

        $db->prepare('INSERT INTO carts (session_id) VALUES (?)')->execute([$sessionId]);
        return (int) $db->lastInsertId();
    }

    /**
     * Get all cart items with product and variant info.
     */
    public function getCartItems(int $cartId): array {
        $db   = getDB();
        $stmt = $db->prepare("
            SELECT ci.id, ci.quantity, ci.price_at_add, ci.variant_id,
                   p.id AS product_id, p.name, p.slug, p.price, p.sale_price,
                   p.total_stock, p.is_active,
                   pv.size, pv.colour, pv.stock AS variant_stock,
                   pi.image_path AS primary_image
            FROM cart_items ci
            JOIN products p ON p.id = ci.product_id
            LEFT JOIN product_variants pv ON pv.id = ci.variant_id
            LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_primary = 1
            WHERE ci.cart_id = ?
            ORDER BY ci.created_at ASC
        ");
        $stmt->execute([$cartId]);
        return $stmt->fetchAll();
    }

    /**
     * Add an item to the cart. Returns ['success' => bool, 'message' => string].
     */
    public function addItem(int $cartId, int $productId, ?int $variantId, int $quantity): array {
        $db = getDB();

        // Fetch product — must be active
        $pStmt = $db->prepare('SELECT id, name, price, sale_price, total_stock, is_active FROM products WHERE id = ? LIMIT 1');
        $pStmt->execute([$productId]);
        $product = $pStmt->fetch();

        if (!$product || !$product['is_active']) {
            return ['success' => false, 'message' => 'Product not available.'];
        }

        // Server-calculated price (never trust browser)
        $price = ($product['sale_price'] && $product['sale_price'] < $product['price'])
            ? (int) $product['sale_price']
            : (int) $product['price'];

        // Check variant stock
        $availableStock = (int) $product['total_stock'];
        if ($variantId) {
            $vStmt = $db->prepare('SELECT stock FROM product_variants WHERE id = ? AND product_id = ? LIMIT 1');
            $vStmt->execute([$variantId, $productId]);
            $variant = $vStmt->fetch();
            if (!$variant) return ['success' => false, 'message' => 'Selected variant not found.'];
            $availableStock = (int) $variant['stock'];
        }

        if ($availableStock <= 0) {
            return ['success' => false, 'message' => 'Sorry, this item is out of stock.'];
        }

        // Check if already in cart
        $cStmt = $db->prepare('SELECT id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ? AND (variant_id = ? OR (variant_id IS NULL AND ? IS NULL)) LIMIT 1');
        $cStmt->execute([$cartId, $productId, $variantId, $variantId]);
        $existing = $cStmt->fetch();

        if ($existing) {
            $newQty = min((int) $existing['quantity'] + $quantity, $availableStock);
            $db->prepare('UPDATE cart_items SET quantity = ?, price_at_add = ? WHERE id = ?')
               ->execute([$newQty, $price, $existing['id']]);
        } else {
            $quantity = min($quantity, $availableStock);
            $db->prepare('INSERT INTO cart_items (cart_id, product_id, variant_id, quantity, price_at_add) VALUES (?,?,?,?,?)')
               ->execute([$cartId, $productId, $variantId, $quantity, $price]);
        }

        return ['success' => true, 'message' => '"' . $product['name'] . '" added to your bag.'];
    }

    /**
     * Update quantity of a cart item.
     */
    public function updateItem(int $cartItemId, int $cartId, int $quantity): array {
        $db = getDB();

        // Verify ownership
        $stmt = $db->prepare('SELECT ci.id, ci.variant_id, ci.product_id FROM cart_items ci WHERE ci.id = ? AND ci.cart_id = ? LIMIT 1');
        $stmt->execute([$cartItemId, $cartId]);
        $item = $stmt->fetch();

        if (!$item) return ['success' => false, 'message' => 'Item not found.'];

        if ($quantity <= 0) {
            $this->removeItem($cartItemId, $cartId);
            return ['success' => true, 'message' => 'Item removed.'];
        }

        // Check stock
        $availableStock = 9999;
        if ($item['variant_id']) {
            $vStmt = $db->prepare('SELECT stock FROM product_variants WHERE id = ? LIMIT 1');
            $vStmt->execute([$item['variant_id']]);
            $v = $vStmt->fetch();
            if ($v) $availableStock = (int) $v['stock'];
        } else {
            $pStmt = $db->prepare('SELECT total_stock FROM products WHERE id = ? LIMIT 1');
            $pStmt->execute([$item['product_id']]);
            $p = $pStmt->fetch();
            if ($p) $availableStock = (int) $p['total_stock'];
        }

        $newQty = min($quantity, $availableStock);
        if ($newQty < 1) $newQty = 1;

        $db->prepare('UPDATE cart_items SET quantity = ? WHERE id = ? AND cart_id = ?')
           ->execute([$newQty, $cartItemId, $cartId]);

        return ['success' => true, 'message' => 'Quantity updated.'];
    }

    /**
     * Remove a cart item.
     */
    public function removeItem(int $cartItemId, int $cartId): bool {
        $db   = getDB();
        $stmt = $db->prepare('DELETE FROM cart_items WHERE id = ? AND cart_id = ?');
        $stmt->execute([$cartItemId, $cartId]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Calculate cart totals from items array.
     * Returns ['subtotal' => int (kobo), 'count' => int]
     */
    public function getCartTotals(array $items): array {
        $subtotal = 0;
        $count    = 0;
        foreach ($items as $item) {
            $subtotal += (int) $item['price_at_add'] * (int) $item['quantity'];
            $count    += (int) $item['quantity'];
        }
        return ['subtotal' => $subtotal, 'count' => $count];
    }

    /**
     * Update the session cart count for badge display.
     */
    public function updateSessionCount(int $cartId): void {
        $db   = getDB();
        $stmt = $db->prepare('SELECT COALESCE(SUM(quantity), 0) FROM cart_items WHERE cart_id = ?');
        $stmt->execute([$cartId]);
        $_SESSION['cart_count'] = (int) $stmt->fetchColumn();
    }

    /**
     * Merge a guest cart into a user cart (called on login).
     */
    public function mergeGuestCart(int $guestCartId, int $userCartId): void {
        $db    = getDB();
        $items = $this->getCartItems($guestCartId);

        foreach ($items as $item) {
            $this->addItem(
                $userCartId,
                (int) $item['product_id'],
                $item['variant_id'] ? (int) $item['variant_id'] : null,
                (int) $item['quantity']
            );
        }

        // Clean up guest cart
        $db->prepare('DELETE FROM cart_items WHERE cart_id = ?')->execute([$guestCartId]);
        $db->prepare('DELETE FROM carts WHERE id = ?')->execute([$guestCartId]);
        unset($_SESSION['cart_session_id']);
    }
}
