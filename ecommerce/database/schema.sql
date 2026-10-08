-- =============================================================================
-- AZFAT BEST BOUTIQUE — Database Schema
-- Engine: InnoDB | Charset: utf8mb4_unicode_ci
-- Prices stored as integers in kobo (₦1 = 100 kobo)
-- Safe to re-run: all tables dropped and recreated in FK-safe order
-- =============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------------------------------
-- 1. settings
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `setting_key`   VARCHAR(100)  NOT NULL,
    `setting_value` TEXT          NULL,
    `created_at`    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_settings_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 2. categories
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(100)  NOT NULL,
    `slug`        VARCHAR(100)  NOT NULL,
    `description` TEXT          NULL,
    `image`       VARCHAR(255)  NULL,
    `is_active`   TINYINT(1)    NOT NULL DEFAULT 1,
    `sort_order`  INT           NOT NULL DEFAULT 0,
    `created_at`  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_categories_slug` (`slug`),
    KEY `idx_categories_is_active` (`is_active`),
    KEY `idx_categories_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 3. users
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id`                     INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `first_name`             VARCHAR(100)  NOT NULL,
    `last_name`              VARCHAR(100)  NOT NULL,
    `email`                  VARCHAR(191)  NOT NULL,
    `phone`                  VARCHAR(20)   NULL,
    `password_hash`          VARCHAR(255)  NULL COMMENT 'NULL for guest accounts',
    `role`                   ENUM('customer','admin') NOT NULL DEFAULT 'customer',
    `is_active`              TINYINT(1)    NOT NULL DEFAULT 1,
    `email_verified_at`      TIMESTAMP     NULL,
    `password_reset_token`   VARCHAR(100)  NULL,
    `password_reset_expires` TIMESTAMP     NULL,
    `last_login_at`          TIMESTAMP     NULL,
    `created_at`             TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`             TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_users_email` (`email`),
    KEY `idx_users_role` (`role`),
    KEY `idx_users_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 4. addresses
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `addresses`;
CREATE TABLE `addresses` (
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`      INT UNSIGNED NOT NULL,
    `first_name`   VARCHAR(100) NOT NULL,
    `last_name`    VARCHAR(100) NOT NULL,
    `phone`        VARCHAR(20)  NOT NULL,
    `address_line1` VARCHAR(255) NOT NULL,
    `address_line2` VARCHAR(255) NULL,
    `city`         VARCHAR(100) NOT NULL,
    `state`        VARCHAR(100) NOT NULL,
    `is_default`   TINYINT(1)   NOT NULL DEFAULT 0,
    `created_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_addresses_user_id` (`user_id`),
    CONSTRAINT `fk_addresses_user_id`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 5. products
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
    `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `category_id` INT UNSIGNED  NOT NULL,
    `name`        VARCHAR(255)  NOT NULL,
    `slug`        VARCHAR(255)  NOT NULL,
    `description` TEXT          NULL,
    `price`       INT           NOT NULL COMMENT 'Price in kobo',
    `sale_price`  INT           NULL     COMMENT 'Sale price in kobo; NULL = no active sale',
    `sku`         VARCHAR(100)  NULL,
    `is_active`   TINYINT(1)    NOT NULL DEFAULT 1,
    `is_featured` TINYINT(1)    NOT NULL DEFAULT 0,
    `total_stock` INT           NOT NULL DEFAULT 0,
    `created_at`  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_products_slug` (`slug`),
    UNIQUE KEY `uq_products_sku`  (`sku`),
    KEY `idx_products_category_id` (`category_id`),
    KEY `idx_products_is_active`   (`is_active`),
    KEY `idx_products_is_featured` (`is_featured`),
    CONSTRAINT `fk_products_category_id`
        FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 6. product_images
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `product_images`;
CREATE TABLE `product_images` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `product_id`  INT UNSIGNED NOT NULL,
    `image_path`  VARCHAR(255) NOT NULL,
    `alt_text`    VARCHAR(255) NULL,
    `is_primary`  TINYINT(1)   NOT NULL DEFAULT 0,
    `sort_order`  INT          NOT NULL DEFAULT 0,
    `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_product_images_product_id` (`product_id`),
    KEY `idx_product_images_is_primary` (`is_primary`),
    CONSTRAINT `fk_product_images_product_id`
        FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 7. product_variants
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `product_variants`;
CREATE TABLE `product_variants` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `product_id` INT UNSIGNED NOT NULL,
    `size`       VARCHAR(20)  NULL,
    `colour`     VARCHAR(50)  NULL,
    `stock`      INT          NOT NULL DEFAULT 0,
    `sku`        VARCHAR(100) NULL,
    `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_product_variants_product_id` (`product_id`),
    CONSTRAINT `fk_product_variants_product_id`
        FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 8. carts
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `carts`;
CREATE TABLE `carts` (
    `id`         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `user_id`    INT UNSIGNED  NULL,
    `session_id` VARCHAR(191)  NULL,
    `created_at` TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_carts_user_id`    (`user_id`),
    KEY `idx_carts_session_id` (`session_id`),
    CONSTRAINT `fk_carts_user_id`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 9. cart_items
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `cart_items`;
CREATE TABLE `cart_items` (
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `cart_id`      INT UNSIGNED NOT NULL,
    `product_id`   INT UNSIGNED NOT NULL,
    `variant_id`   INT UNSIGNED NULL,
    `quantity`     INT          NOT NULL DEFAULT 1,
    `price_at_add` INT          NOT NULL COMMENT 'Server-set price in kobo at time of adding to cart',
    `created_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_cart_items_cart_id`    (`cart_id`),
    KEY `idx_cart_items_product_id` (`product_id`),
    KEY `idx_cart_items_variant_id` (`variant_id`),
    CONSTRAINT `fk_cart_items_cart_id`
        FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `fk_cart_items_product_id`
        FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `fk_cart_items_variant_id`
        FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 10. wishlists
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `wishlists`;
CREATE TABLE `wishlists` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`    INT UNSIGNED NOT NULL,
    `product_id` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_wishlists_user_product` (`user_id`, `product_id`),
    KEY `idx_wishlists_product_id` (`product_id`),
    CONSTRAINT `fk_wishlists_user_id`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `fk_wishlists_product_id`
        FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 11. delivery_zones
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `delivery_zones`;
CREATE TABLE `delivery_zones` (
    `id`        INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `zone_name` VARCHAR(100) NOT NULL,
    `state`     VARCHAR(100) NOT NULL,
    `fee`       INT          NOT NULL COMMENT 'Delivery fee in kobo',
    `created_at` TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_delivery_zones_state` (`state`),
    KEY `idx_delivery_zones_state` (`state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 12. orders
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
    `id`                INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `order_number`      VARCHAR(50)   NOT NULL,
    `user_id`           INT UNSIGNED  NULL,
    `guest_email`       VARCHAR(191)  NULL,
    `guest_phone`       VARCHAR(20)   NULL,
    `first_name`        VARCHAR(100)  NOT NULL,
    `last_name`         VARCHAR(100)  NOT NULL,
    `phone`             VARCHAR(20)   NOT NULL,
    `address_line1`     VARCHAR(255)  NOT NULL,
    `address_line2`     VARCHAR(255)  NULL,
    `city`              VARCHAR(100)  NOT NULL,
    `state`             VARCHAR(100)  NOT NULL,
    `subtotal`          INT           NOT NULL COMMENT 'In kobo',
    `delivery_fee`      INT           NOT NULL DEFAULT 0 COMMENT 'In kobo',
    `discount_amount`   INT           NOT NULL DEFAULT 0 COMMENT 'In kobo',
    `total`             INT           NOT NULL COMMENT 'In kobo',
    `status`            ENUM(
                            'pending_payment',
                            'paid',
                            'processing',
                            'shipped',
                            'delivered',
                            'cancelled',
                            'failed'
                        ) NOT NULL DEFAULT 'pending_payment',
    `payment_method`    VARCHAR(50)   NULL,
    `payment_reference` VARCHAR(191)  NULL,
    `notes`             TEXT          NULL,
    `created_at`        TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_orders_order_number`      (`order_number`),
    UNIQUE KEY `uq_orders_payment_reference` (`payment_reference`),
    KEY `idx_orders_user_id`           (`user_id`),
    KEY `idx_orders_status`            (`status`),
    KEY `idx_orders_payment_reference` (`payment_reference`),
    CONSTRAINT `fk_orders_user_id`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 13. order_items
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
    `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `order_id`       INT UNSIGNED NOT NULL,
    `product_id`     INT UNSIGNED NULL,
    `variant_id`     INT UNSIGNED NULL,
    `product_name`   VARCHAR(255) NOT NULL COMMENT 'Snapshot of product name at order time',
    `variant_size`   VARCHAR(20)  NULL     COMMENT 'Snapshot of variant size at order time',
    `variant_colour` VARCHAR(50)  NULL     COMMENT 'Snapshot of variant colour at order time',
    `quantity`       INT          NOT NULL,
    `unit_price`     INT          NOT NULL COMMENT 'Price per unit in kobo at order time (snapshot)',
    `total_price`    INT          NOT NULL COMMENT 'quantity * unit_price in kobo',
    `created_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_order_items_order_id`   (`order_id`),
    KEY `idx_order_items_product_id` (`product_id`),
    CONSTRAINT `fk_order_items_order_id`
        FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `fk_order_items_product_id`
        FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
        ON DELETE SET NULL,
    CONSTRAINT `fk_order_items_variant_id`
        FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 14. payments
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
    `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `order_id`          INT UNSIGNED NOT NULL,
    `gateway`           ENUM('paystack','flutterwave','pay_on_delivery','bank_transfer') NOT NULL,
    `reference`         VARCHAR(191) NOT NULL,
    `gateway_reference` VARCHAR(191) NULL,
    `amount`            INT          NOT NULL COMMENT 'In kobo',
    `currency`          VARCHAR(10)  NOT NULL DEFAULT 'NGN',
    `status`            ENUM('pending','success','failed','abandoned') NOT NULL DEFAULT 'pending',
    `gateway_response`  JSON         NULL,
    `created_at`        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_payments_reference` (`reference`),
    KEY `idx_payments_order_id`  (`order_id`),
    KEY `idx_payments_reference` (`reference`),
    KEY `idx_payments_status`    (`status`),
    CONSTRAINT `fk_payments_order_id`
        FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 15. subscribers
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `subscribers`;
CREATE TABLE `subscribers` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `email`      VARCHAR(191) NOT NULL,
    `is_active`  TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_subscribers_email` (`email`),
    KEY `idx_subscribers_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
