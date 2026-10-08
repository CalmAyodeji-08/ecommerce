<?php

declare(strict_types=1);

class ProductModel {

    /**
     * Active featured products with primary image.
     */
    public function getFeatured(int $limit = 8): array {
        $db = getDB();
        $stmt = $db->prepare("
            SELECT p.id, p.name, p.slug, p.price, p.sale_price, p.total_stock,
                   pi.image_path AS primary_image, pi.alt_text AS image_alt
            FROM products p
            LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_primary = 1
            WHERE p.is_active = 1 AND p.is_featured = 1
            ORDER BY p.created_at DESC
            LIMIT :lim
        ");
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Paginated products for a category slug with optional filters and sort.
     *
     * @param array $filters  Keys: size (string), colour (string),
     *                              min_price (int kobo), max_price (int kobo)
     * @param string $sort    newest | price_asc | price_desc | name_asc
     */
    public function getByCategory(
        string $slug,
        array  $filters = [],
        string $sort    = 'newest',
        int    $page    = 1,
        int    $perPage = 12
    ): array {
        $db     = getDB();
        $offset = ($page - 1) * $perPage;

        [$where, $bindings] = $this->buildCategoryWhere($slug, $filters);
        $orderBy = $this->buildOrderBy($sort);

        $sql = "
            SELECT p.id, p.name, p.slug, p.price, p.sale_price, p.total_stock,
                   pi.image_path AS primary_image, pi.alt_text AS image_alt
            FROM products p
            LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_primary = 1
            {$where}
            {$orderBy}
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $db->prepare($sql);
        foreach ($bindings as $key => $value) {
            if (is_int($value)) {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value, PDO::PARAM_STR);
            }
        }
        $stmt->bindValue(':limit',  $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset,  PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Count products in a category with filters applied.
     */
    public function countByCategory(string $slug, array $filters = []): int {
        $db = getDB();
        [$where, $bindings] = $this->buildCategoryWhere($slug, $filters);

        $sql = "SELECT COUNT(DISTINCT p.id) FROM products p
                LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_primary = 1
                {$where}";

        $stmt = $db->prepare($sql);
        foreach ($bindings as $key => $value) {
            if (is_int($value)) {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value, PDO::PARAM_STR);
            }
        }
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    /**
     * All active products (no category filter), paginated — used by ShopController::index().
     */
    public function getAll(
        array  $filters = [],
        string $sort    = 'newest',
        int    $page    = 1,
        int    $perPage = 12
    ): array {
        $db     = getDB();
        $offset = ($page - 1) * $perPage;

        [$where, $bindings] = $this->buildGlobalWhere($filters);
        $orderBy = $this->buildOrderBy($sort);

        $sql = "
            SELECT p.id, p.name, p.slug, p.price, p.sale_price, p.total_stock,
                   pi.image_path AS primary_image, pi.alt_text AS image_alt
            FROM products p
            LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_primary = 1
            {$where}
            {$orderBy}
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $db->prepare($sql);
        foreach ($bindings as $key => $value) {
            if (is_int($value)) {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value, PDO::PARAM_STR);
            }
        }
        $stmt->bindValue(':limit',  $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset,  PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Count all active products with filters.
     */
    public function countAll(array $filters = []): int {
        $db = getDB();
        [$where, $bindings] = $this->buildGlobalWhere($filters);

        $sql = "SELECT COUNT(DISTINCT p.id) FROM products p
                LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_primary = 1
                {$where}";

        $stmt = $db->prepare($sql);
        foreach ($bindings as $key => $value) {
            if (is_int($value)) {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value, PDO::PARAM_STR);
            }
        }
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    /**
     * Get single product by slug, with category name.
     */
    public function getBySlug(string $slug): ?array {
        $db = getDB();
        $stmt = $db->prepare("
            SELECT p.id, p.name, p.slug, p.price, p.sale_price, p.total_stock,
                   p.description, p.sku, p.category_id,
                   c.name AS category_name, c.slug AS category_slug,
                   pi.image_path AS primary_image, pi.alt_text AS image_alt
            FROM products p
            LEFT JOIN categories c  ON c.id = p.category_id
            LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_primary = 1
            WHERE p.slug = :slug AND p.is_active = 1
            LIMIT 1
        ");
        $stmt->execute([':slug' => $slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * All images for a product, ordered by sort_order.
     */
    public function getImages(int $productId): array {
        $db = getDB();
        $stmt = $db->prepare("
            SELECT id, image_path, alt_text, is_primary, sort_order
            FROM product_images
            WHERE product_id = :product_id
            ORDER BY is_primary DESC, sort_order ASC
        ");
        $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * All variants for a product, grouped into sizes, colours, and raw rows.
     *
     * @return array{sizes: array, colours: array, variants: array}
     */
    public function getVariants(int $productId): array {
        $db = getDB();
        $stmt = $db->prepare("
            SELECT id, size, colour, stock, sku
            FROM product_variants
            WHERE product_id = :product_id
            ORDER BY size ASC, colour ASC
        ");
        $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
        $stmt->execute();
        $variants = $stmt->fetchAll();

        // Build unique sizes with aggregated stock
        $sizesMap = [];
        foreach ($variants as $v) {
            if ($v['size'] === null) continue;
            $s = $v['size'];
            if (!isset($sizesMap[$s])) {
                $sizesMap[$s] = ['size' => $s, 'stock' => 0];
            }
            $sizesMap[$s]['stock'] += (int) $v['stock'];
        }

        // Build unique colours
        $coloursMap = [];
        foreach ($variants as $v) {
            if ($v['colour'] === null) continue;
            $c = $v['colour'];
            if (!isset($coloursMap[$c])) {
                $coloursMap[$c] = ['colour' => $c];
            }
        }

        return [
            'sizes'    => array_values($sizesMap),
            'colours'  => array_values($coloursMap),
            'variants' => $variants,
        ];
    }

    /**
     * Full-text search on name and description, paginated.
     */
    public function search(string $query, int $page = 1, int $perPage = 12): array {
        $db     = getDB();
        $offset = ($page - 1) * $perPage;
        $like   = '%' . $query . '%';

        $stmt = $db->prepare("
            SELECT p.id, p.name, p.slug, p.price, p.sale_price, p.total_stock,
                   pi.image_path AS primary_image, pi.alt_text AS image_alt
            FROM products p
            LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_primary = 1
            WHERE p.is_active = 1
              AND (p.name LIKE :like1 OR p.description LIKE :like2)
            ORDER BY
                CASE WHEN p.name LIKE :like3 THEN 0 ELSE 1 END,
                p.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':like1',  $like,    PDO::PARAM_STR);
        $stmt->bindValue(':like2',  $like,    PDO::PARAM_STR);
        $stmt->bindValue(':like3',  $like,    PDO::PARAM_STR);
        $stmt->bindValue(':limit',  $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset,  PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Count search results.
     */
    public function countSearch(string $query): int {
        $db   = getDB();
        $like = '%' . $query . '%';

        $stmt = $db->prepare("
            SELECT COUNT(DISTINCT p.id)
            FROM products p
            WHERE p.is_active = 1
              AND (p.name LIKE :like1 OR p.description LIKE :like2)
        ");
        $stmt->bindValue(':like1', $like, PDO::PARAM_STR);
        $stmt->bindValue(':like2', $like, PDO::PARAM_STR);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    /**
     * Related products: same category, excluding current product.
     */
    public function getRelated(int $productId, int $categoryId, int $limit = 4): array {
        $db = getDB();
        $stmt = $db->prepare("
            SELECT p.id, p.name, p.slug, p.price, p.sale_price, p.total_stock,
                   pi.image_path AS primary_image, pi.alt_text AS image_alt
            FROM products p
            LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_primary = 1
            WHERE p.is_active = 1
              AND p.category_id = :category_id
              AND p.id != :product_id
            ORDER BY p.created_at DESC
            LIMIT :lim
        ");
        $stmt->bindValue(':category_id', $categoryId,  PDO::PARAM_INT);
        $stmt->bindValue(':product_id',  $productId,   PDO::PARAM_INT);
        $stmt->bindValue(':lim',         $limit,        PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ─── Private helpers ──────────────────────────────────────────────────────

    /**
     * Build WHERE + bindings for category-scoped queries.
     */
    private function buildCategoryWhere(string $slug, array $filters): array {
        $conditions = [
            'p.is_active = 1',
            'EXISTS (SELECT 1 FROM categories c WHERE c.id = p.category_id AND c.slug = :cat_slug AND c.is_active = 1)',
        ];
        $bindings = [':cat_slug' => $slug];

        $this->applyFilterConditions($filters, $conditions, $bindings);

        $where = 'WHERE ' . implode(' AND ', $conditions);
        return [$where, $bindings];
    }

    /**
     * Build WHERE + bindings for global (no-category) queries.
     */
    private function buildGlobalWhere(array $filters): array {
        $conditions = ['p.is_active = 1'];
        $bindings   = [];

        $this->applyFilterConditions($filters, $conditions, $bindings);

        $where = 'WHERE ' . implode(' AND ', $conditions);
        return [$where, $bindings];
    }

    /**
     * Append filter conditions and bindings in-place.
     */
    private function applyFilterConditions(array $filters, array &$conditions, array &$bindings): void {
        if (!empty($filters['size'])) {
            $conditions[] = 'EXISTS (
                SELECT 1 FROM product_variants pv
                WHERE pv.product_id = p.id AND pv.size = :filter_size AND pv.stock > 0
            )';
            $bindings[':filter_size'] = $filters['size'];
        }

        if (!empty($filters['colour'])) {
            $conditions[] = 'EXISTS (
                SELECT 1 FROM product_variants pv
                WHERE pv.product_id = p.id AND pv.colour = :filter_colour
            )';
            $bindings[':filter_colour'] = $filters['colour'];
        }

        if (isset($filters['min_price']) && $filters['min_price'] !== '') {
            $conditions[] = 'COALESCE(p.sale_price, p.price) >= :min_price';
            $bindings[':min_price'] = (int) $filters['min_price'];
        }

        if (isset($filters['max_price']) && $filters['max_price'] !== '') {
            $conditions[] = 'COALESCE(p.sale_price, p.price) <= :max_price';
            $bindings[':max_price'] = (int) $filters['max_price'];
        }
    }

    /**
     * Build ORDER BY clause from sort key.
     */
    private function buildOrderBy(string $sort): string {
        return match ($sort) {
            'price_asc'  => 'ORDER BY COALESCE(p.sale_price, p.price) ASC',
            'price_desc' => 'ORDER BY COALESCE(p.sale_price, p.price) DESC',
            'name_asc'   => 'ORDER BY p.name ASC',
            default      => 'ORDER BY p.created_at DESC', // newest
        };
    }
}
