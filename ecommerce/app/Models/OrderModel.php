<?php

declare(strict_types=1);

class OrderModel {

    /**
     * Get delivery fee for a state (in kobo).
     * Returns 0 if not found.
     */
    public function getDeliveryFee(string $state): int {
        $db   = getDB();
        $stmt = $db->prepare('SELECT fee FROM delivery_zones WHERE state = ? LIMIT 1');
        $stmt->execute([$state]);
        $row = $stmt->fetch();
        return $row ? (int) $row['fee'] : 0;
    }

    /**
     * Create an order from cart items in a single transaction.
     * Returns the new order ID.
     *
     * $data keys:
     *   user_id (int|null), guest_email, guest_phone,
     *   first_name, last_name, phone,
     *   address_line1, address_line2, city, state,
     *   subtotal (kobo), delivery_fee (kobo), discount_amount (kobo), total (kobo),
     *   payment_method, notes
     *
     * $cartItems: result of CartModel::getCartItems()
     */
    public function createOrder(array $data, array $cartItems): int {
        $db = getDB();
        $db->beginTransaction();

        try {
            $orderNumber = generateOrderNumber();

            // Ensure unique order number
            while (true) {
                $check = $db->prepare('SELECT id FROM orders WHERE order_number = ? LIMIT 1');
                $check->execute([$orderNumber]);
                if (!$check->fetch()) break;
                $orderNumber = generateOrderNumber();
            }

            // Insert order
            $stmt = $db->prepare("
                INSERT INTO orders (
                    order_number, user_id, guest_email, guest_phone,
                    first_name, last_name, phone,
                    address_line1, address_line2, city, state,
                    subtotal, delivery_fee, discount_amount, total,
                    status, payment_method, notes
                ) VALUES (
                    ?, ?, ?, ?,
                    ?, ?, ?,
                    ?, ?, ?, ?,
                    ?, ?, ?, ?,
                    'pending_payment', ?, ?
                )
            ");

            $stmt->execute([
                $orderNumber,
                $data['user_id'] ?? null,
                $data['guest_email'] ?? null,
                $data['guest_phone'] ?? null,
                $data['first_name'],
                $data['last_name'],
                $data['phone'],
                $data['address_line1'],
                $data['address_line2'] ?? null,
                $data['city'],
                $data['state'],
                (int) $data['subtotal'],
                (int) $data['delivery_fee'],
                (int) ($data['discount_amount'] ?? 0),
                (int) $data['total'],
                $data['payment_method'] ?? null,
                $data['notes'] ?? null,
            ]);

            $orderId = (int) $db->lastInsertId();

            // Insert order items (snapshots — never reference live prices)
            $itemStmt = $db->prepare("
                INSERT INTO order_items (
                    order_id, product_id, variant_id,
                    product_name, variant_size, variant_colour,
                    quantity, unit_price, total_price
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            foreach ($cartItems as $item) {
                $unitPrice  = (int) $item['price_at_add'];
                $totalPrice = $unitPrice * (int) $item['quantity'];

                $itemStmt->execute([
                    $orderId,
                    (int) $item['product_id'],
                    $item['variant_id'] ? (int) $item['variant_id'] : null,
                    $item['name'],           // snapshot
                    $item['size']   ?? null, // snapshot
                    $item['colour'] ?? null, // snapshot
                    (int) $item['quantity'],
                    $unitPrice,
                    $totalPrice,
                ]);
            }

            $db->commit();
            return $orderId;

        } catch (Throwable $e) {
            $db->rollBack();
            error_log('Order creation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get an order by ID (no user restriction — used by payment callbacks).
     */
    public function getById(int $orderId): ?array {
        $db   = getDB();
        $stmt = $db->prepare('SELECT * FROM orders WHERE id = ? LIMIT 1');
        $stmt->execute([$orderId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Get order with items by order number.
     */
    public function getByOrderNumber(string $orderNumber): ?array {
        $db   = getDB();
        $stmt = $db->prepare('SELECT * FROM orders WHERE order_number = ? LIMIT 1');
        $stmt->execute([$orderNumber]);
        $order = $stmt->fetch();
        if (!$order) return null;

        $iStmt = $db->prepare('
            SELECT oi.*, p.slug AS product_slug
            FROM order_items oi
            LEFT JOIN products p ON p.id = oi.product_id
            WHERE oi.order_id = ?
            ORDER BY oi.id ASC
        ');
        $iStmt->execute([(int) $order['id']]);
        $order['items'] = $iStmt->fetchAll();

        return $order;
    }

    /**
     * Clear the cart after successful order creation.
     */
    public function clearCart(int $cartId): void {
        $db = getDB();
        $db->prepare('DELETE FROM cart_items WHERE cart_id = ?')->execute([$cartId]);
    }
}
