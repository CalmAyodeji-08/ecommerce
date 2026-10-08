<?php

declare(strict_types=1);

require_once APP_PATH . '/Models/ProductModel.php';

class ProductController {

    /**
     * GET /product/:slug — single product detail page.
     */
    public function show(array $params = []): void {
        $slug = $params['slug'] ?? '';

        $productModel = new ProductModel();
        $product      = $productModel->getBySlug($slug);

        if ($product === null) {
            http_response_code(404);
            require VIEW_PATH . '/errors/404.php';
            return;
        }

        $images  = $productModel->getImages((int) $product['id']);
        $variants = $productModel->getVariants((int) $product['id']);
        $related = $productModel->getRelated(
            (int) $product['id'],
            (int) $product['category_id']
        );

        // If no images returned, build a placeholder from the primary_image column
        if (empty($images) && !empty($product['primary_image'])) {
            $images = [[
                'id'         => 0,
                'image_path' => $product['primary_image'],
                'alt_text'   => $product['image_alt'] ?? $product['name'],
                'is_primary' => 1,
                'sort_order' => 0,
            ]];
        }

        $pageTitle       = e($product['name']) . ' — ' . APP_NAME;
        $metaDescription = !empty($product['description'])
            ? truncate(strip_tags($product['description']), 155)
            : $product['name'] . ' at AZFAT BEST BOUTIQUE.';
        $ogImage         = !empty($product['primary_image'])
            ? url('/images/products/' . $product['primary_image'])
            : '';

        // shop.js needs to be included
        $extraJs = ['js/shop.js'];

        ob_start();
        require VIEW_PATH . '/product/show.php';
        $content = ob_get_clean();

        require VIEW_PATH . '/layouts/main.php';
    }
}
