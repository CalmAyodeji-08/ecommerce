<?php

declare(strict_types=1);

class UserModel {

    // ─────────────────────────────────────────────────────────────────────────
    // User lookups
    // ─────────────────────────────────────────────────────────────────────────

    public function findByEmail(string $email): ?array {
        $db   = getDB();
        $stmt = $db->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findById(int $id): ?array {
        $db   = getDB();
        $stmt = $db->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Create / update
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Insert a new user and return the new ID.
     * Expects $data: first_name, last_name, email, password, phone (optional)
     */
    public function create(array $data): int {
        $db   = getDB();
        $hash = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);

        $stmt = $db->prepare('
            INSERT INTO users (first_name, last_name, email, phone, password_hash, role, is_active)
            VALUES (?, ?, ?, ?, ?, \'customer\', 1)
        ');
        $stmt->execute([
            trim($data['first_name']),
            trim($data['last_name']),
            strtolower(trim($data['email'])),
            isset($data['phone']) && $data['phone'] !== '' ? sanitisePhone($data['phone']) : null,
            $hash,
        ]);
        return (int) $db->lastInsertId();
    }

    public function updateLastLogin(int $id): void {
        $db = getDB();
        $db->prepare('UPDATE users SET last_login_at = NOW() WHERE id = ?')->execute([$id]);
    }

    /**
     * Update profile fields: first_name, last_name, phone.
     */
    public function updateProfile(int $id, array $data): void {
        $db = getDB();
        $db->prepare('
            UPDATE users SET first_name = ?, last_name = ?, phone = ? WHERE id = ?
        ')->execute([
            trim($data['first_name']),
            trim($data['last_name']),
            isset($data['phone']) && $data['phone'] !== '' ? sanitisePhone($data['phone']) : null,
            $id,
        ]);
    }

    public function updatePassword(int $id, string $newPassword): void {
        $db   = getDB();
        $hash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        $db->prepare('UPDATE users SET password_hash = ? WHERE id = ?')->execute([$hash, $id]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Password reset
    // ─────────────────────────────────────────────────────────────────────────

    public function setResetToken(int $id, string $token, string $expires): void {
        $db = getDB();
        $db->prepare('
            UPDATE users SET password_reset_token = ?, password_reset_expires = ? WHERE id = ?
        ')->execute([$token, $expires, $id]);
    }

    /**
     * Return user row only when the token exists and has not expired.
     */
    public function findByResetToken(string $token): ?array {
        $db   = getDB();
        $stmt = $db->prepare('
            SELECT * FROM users
            WHERE password_reset_token = ?
              AND password_reset_expires > NOW()
            LIMIT 1
        ');
        $stmt->execute([$token]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function clearResetToken(int $id): void {
        $db = getDB();
        $db->prepare('
            UPDATE users SET password_reset_token = NULL, password_reset_expires = NULL WHERE id = ?
        ')->execute([$id]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Wishlist
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Return all wishlist products for a user, including the primary image.
     */
    public function getWishlist(int $userId): array {
        $db   = getDB();
        $stmt = $db->prepare('
            SELECT p.id, p.name, p.slug, p.price, p.sale_price, p.total_stock,
                   pi.image_path AS primary_image,
                   pi.alt_text   AS image_alt
            FROM wishlists w
            JOIN products p  ON p.id = w.product_id
            LEFT JOIN product_images pi
                   ON pi.product_id = p.id AND pi.is_primary = 1
            WHERE w.user_id = ?
            ORDER BY w.created_at DESC
        ');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Toggle a product in a user\'s wishlist.
     * Returns \'added\' or \'removed\'.
     */
    public function toggleWishlist(int $userId, int $productId): string {
        $db   = getDB();
        $stmt = $db->prepare('SELECT id FROM wishlists WHERE user_id = ? AND product_id = ? LIMIT 1');
        $stmt->execute([$userId, $productId]);
        $existing = $stmt->fetch();

        if ($existing) {
            $db->prepare('DELETE FROM wishlists WHERE user_id = ? AND product_id = ?')
               ->execute([$userId, $productId]);
            return 'removed';
        }

        $db->prepare('INSERT INTO wishlists (user_id, product_id) VALUES (?, ?)')
           ->execute([$userId, $productId]);
        return 'added';
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Addresses
    // ─────────────────────────────────────────────────────────────────────────

    public function getAddresses(int $userId): array {
        $db   = getDB();
        $stmt = $db->prepare('
            SELECT * FROM addresses WHERE user_id = ? ORDER BY is_default DESC, created_at ASC
        ');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Add a new address and return its ID.
     * If it is the first address, mark it as default.
     */
    public function addAddress(int $userId, array $data): int {
        $db = getDB();

        // Auto-default if this is the user's first address
        $countStmt = $db->prepare('SELECT COUNT(*) FROM addresses WHERE user_id = ?');
        $countStmt->execute([$userId]);
        $isDefault = ((int) $countStmt->fetchColumn() === 0) ? 1 : 0;

        $stmt = $db->prepare('
            INSERT INTO addresses
                (user_id, first_name, last_name, phone, address_line1, address_line2, city, state, is_default)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            $userId,
            trim($data['first_name']),
            trim($data['last_name']),
            sanitisePhone($data['phone']),
            trim($data['address_line1']),
            isset($data['address_line2']) ? trim($data['address_line2']) : null,
            trim($data['city']),
            trim($data['state']),
            $isDefault,
        ]);
        return (int) $db->lastInsertId();
    }

    public function updateAddress(int $addressId, int $userId, array $data): void {
        $db = getDB();
        $db->prepare('
            UPDATE addresses
               SET first_name    = ?,
                   last_name     = ?,
                   phone         = ?,
                   address_line1 = ?,
                   address_line2 = ?,
                   city          = ?,
                   state         = ?
             WHERE id = ? AND user_id = ?
        ')->execute([
            trim($data['first_name']),
            trim($data['last_name']),
            sanitisePhone($data['phone']),
            trim($data['address_line1']),
            isset($data['address_line2']) ? trim($data['address_line2']) : null,
            trim($data['city']),
            trim($data['state']),
            $addressId,
            $userId,
        ]);
    }

    public function deleteAddress(int $addressId, int $userId): void {
        $db = getDB();
        $db->prepare('DELETE FROM addresses WHERE id = ? AND user_id = ?')
           ->execute([$addressId, $userId]);
    }

    /**
     * Set one address as default, clearing all others for this user.
     */
    public function setDefaultAddress(int $addressId, int $userId): void {
        $db = getDB();
        $db->prepare('UPDATE addresses SET is_default = 0 WHERE user_id = ?')->execute([$userId]);
        $db->prepare('UPDATE addresses SET is_default = 1 WHERE id = ? AND user_id = ?')
           ->execute([$addressId, $userId]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Orders
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Return all orders for a user with item count, newest first.
     */
    public function getOrders(int $userId): array {
        $db   = getDB();
        $stmt = $db->prepare('
            SELECT o.*,
                   COUNT(oi.id) AS item_count
            FROM orders o
            LEFT JOIN order_items oi ON oi.order_id = o.id
            WHERE o.user_id = ?
            GROUP BY o.id
            ORDER BY o.created_at DESC
        ');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Return a single order with its items — must belong to this user.
     */
    public function getOrderDetail(int $orderId, int $userId): ?array {
        $db = getDB();

        $oStmt = $db->prepare('SELECT * FROM orders WHERE id = ? AND user_id = ? LIMIT 1');
        $oStmt->execute([$orderId, $userId]);
        $order = $oStmt->fetch();
        if (!$order) return null;

        $iStmt = $db->prepare('
            SELECT oi.*,
                   p.slug AS product_slug
            FROM order_items oi
            LEFT JOIN products p ON p.id = oi.product_id
            WHERE oi.order_id = ?
            ORDER BY oi.id ASC
        ');
        $iStmt->execute([$orderId]);
        $order['items'] = $iStmt->fetchAll();

        return $order;
    }
}
