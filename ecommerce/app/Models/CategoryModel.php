<?php

declare(strict_types=1);

class CategoryModel {

    /**
     * Get all active categories ordered by sort_order.
     */
    public function getAll(): array {
        $db = getDB();
        $stmt = $db->prepare("
            SELECT id, name, slug, description, image, sort_order
            FROM categories
            WHERE is_active = 1
            ORDER BY sort_order ASC, name ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get a single category by slug.
     */
    public function getBySlug(string $slug): ?array {
        $db = getDB();
        $stmt = $db->prepare("
            SELECT id, name, slug, description, image, sort_order
            FROM categories
            WHERE slug = :slug AND is_active = 1
            LIMIT 1
        ");
        $stmt->execute([':slug' => $slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
