<?php

declare(strict_types=1);

require_once APP_PATH . '/Models/ProductModel.php';
require_once APP_PATH . '/Models/CategoryModel.php';

class ShopController {

    /**
     * GET /shop — all products, filterable.
     */
    public function index(array $params = []): void {
        $productModel  = new ProductModel();
        $categoryModel = new CategoryModel();

        $page    = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 12;
        $sort    = $this->sanitiseSort($_GET['sort'] ?? 'newest');
        $filters = $this->extractFilters($_GET);

        $totalItems  = $productModel->countAll($filters);
        $totalPages  = max(1, (int) ceil($totalItems / $perPage));
        $page        = min($page, $totalPages);
        $products    = $productModel->getAll($filters, $sort, $page, $perPage);
        $categories  = $categoryModel->getAll();

        $pagination = [
            'current'     => $page,
            'total_pages' => $totalPages,
            'total_items' => $totalItems,
            'per_page'    => $perPage,
        ];

        $pageTitle = 'All Products — ' . APP_NAME;

        ob_start();
        require VIEW_PATH . '/shop/index.php';
        $content = ob_get_clean();

        require VIEW_PATH . '/layouts/main.php';
    }

    /**
     * GET /category/:slug — products filtered by category.
     */
    public function category(array $params = []): void {
        $slug = $params['slug'] ?? '';

        $categoryModel = new CategoryModel();
        $category      = $categoryModel->getBySlug($slug);

        if ($category === null) {
            http_response_code(404);
            require VIEW_PATH . '/errors/404.php';
            return;
        }

        $productModel = new ProductModel();

        $page    = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 12;
        $sort    = $this->sanitiseSort($_GET['sort'] ?? 'newest');
        $filters = $this->extractFilters($_GET);

        $totalItems = $productModel->countByCategory($slug, $filters);
        $totalPages = max(1, (int) ceil($totalItems / $perPage));
        $page       = min($page, $totalPages);
        $products   = $productModel->getByCategory($slug, $filters, $sort, $page, $perPage);

        $pagination = [
            'current'     => $page,
            'total_pages' => $totalPages,
            'total_items' => $totalItems,
            'per_page'    => $perPage,
        ];

        $pageTitle = e($category['name']) . ' — ' . APP_NAME;

        ob_start();
        require VIEW_PATH . '/shop/category.php';
        $content = ob_get_clean();

        require VIEW_PATH . '/layouts/main.php';
    }

    /**
     * GET /search?q=... — search products.
     */
    public function search(array $params = []): void {
        $query = trim($_GET['q'] ?? '');

        $productModel = new ProductModel();
        $page    = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 12;
        $products   = [];
        $totalItems = 0;
        $totalPages = 1;

        if ($query !== '') {
            $totalItems = $productModel->countSearch($query);
            $totalPages = max(1, (int) ceil($totalItems / $perPage));
            $page       = min($page, $totalPages);
            $products   = $productModel->search($query, $page, $perPage);
        }

        $pagination = [
            'current'     => $page,
            'total_pages' => $totalPages,
            'total_items' => $totalItems,
            'per_page'    => $perPage,
        ];

        $pageTitle = $query ? 'Search: ' . e($query) . ' — ' . APP_NAME : 'Search — ' . APP_NAME;

        ob_start();
        require VIEW_PATH . '/shop/search.php';
        $content = ob_get_clean();

        require VIEW_PATH . '/layouts/main.php';
    }

    // ─── Private helpers ──────────────────────────────────────────────────────

    /**
     * Whitelist the sort parameter.
     */
    private function sanitiseSort(string $sort): string {
        $allowed = ['newest', 'price_asc', 'price_desc', 'name_asc'];
        return in_array($sort, $allowed, true) ? $sort : 'newest';
    }

    /**
     * Extract and sanitise filter parameters from a query array (e.g. $_GET).
     */
    private function extractFilters(array $input): array {
        $filters = [];

        if (!empty($input['size'])) {
            $filters['size'] = trim((string) $input['size']);
        }

        if (!empty($input['colour'])) {
            $filters['colour'] = trim((string) $input['colour']);
        }

        // User inputs are in naira; model expects kobo (multiply by 100)
        if (isset($input['min_price']) && $input['min_price'] !== '') {
            $filters['min_price'] = (int) $input['min_price'] * 100;
        }

        if (isset($input['max_price']) && $input['max_price'] !== '') {
            $filters['max_price'] = (int) $input['max_price'] * 100;
        }

        return $filters;
    }
}
