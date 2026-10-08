-- =============================================================================
-- AZFAT BEST BOUTIQUE — Seed Data
-- Run AFTER schema.sql
-- Safe to re-run: uses INSERT IGNORE or ON DUPLICATE KEY UPDATE throughout
-- Prices in kobo (₦1 = 100 kobo)
-- =============================================================================

USE azfat_boutique;

SET FOREIGN_KEY_CHECKS = 0;

-- =============================================================================
-- 1. SETTINGS
-- =============================================================================
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
    ('shop_name',               'AZFAT BEST BOUTIQUE'),
    ('free_delivery_threshold', '5000000'),
    ('currency',                'NGN'),
    ('currency_symbol',         '₦'),
    ('whatsapp_number',         '2348000000000'),
    ('instagram_handle',        'azfatbestboutique'),
    ('announcement_bar_text',   'Free delivery on orders above ₦50,000 🚚'),
    ('promo_end_date',          '2025-12-31')
ON DUPLICATE KEY UPDATE
    `setting_value` = VALUES(`setting_value`),
    `updated_at`    = CURRENT_TIMESTAMP;

-- =============================================================================
-- 2. CATEGORIES
-- =============================================================================
INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `is_active`, `sort_order`) VALUES
    (1, 'Tees',    'tees',    'Premium oversized and fitted t-shirts for everyday wear.',    1, 1),
    (2, 'Hoodies', 'hoodies', 'Heavy-weight fleece and pullover hoodies built to last.',     1, 2),
    (3, 'Caps',    'caps',    'Dad caps, snapbacks, and bucket hats to top off any look.',   1, 3),
    (4, 'Joggers', 'joggers', 'Slim, cargo, and tapered joggers for comfort and style.',     1, 4),
    (5, 'Sets',    'sets',    'Matching two-piece sets — tee & jogger or hoodie & jogger.',  1, 5)
ON DUPLICATE KEY UPDATE
    `name`        = VALUES(`name`),
    `slug`        = VALUES(`slug`),
    `description` = VALUES(`description`),
    `is_active`   = VALUES(`is_active`),
    `sort_order`  = VALUES(`sort_order`),
    `updated_at`  = CURRENT_TIMESTAMP;

-- =============================================================================
-- 3. DELIVERY ZONES — all 36 states + FCT
-- =============================================================================
INSERT INTO `delivery_zones` (`zone_name`, `state`, `fee`) VALUES
    -- Lagos
    ('Lagos Zone',          'Lagos',        150000),
    -- South West (excl. Lagos)
    ('South West Zone',     'Ogun',         200000),
    ('South West Zone',     'Oyo',          200000),
    ('South West Zone',     'Osun',         200000),
    ('South West Zone',     'Ondo',         200000),
    ('South West Zone',     'Ekiti',        200000),
    -- South South
    ('South South Zone',    'Rivers',       250000),
    ('South South Zone',    'Delta',        250000),
    ('South South Zone',    'Edo',          250000),
    ('South South Zone',    'Bayelsa',      250000),
    ('South South Zone',    'Akwa Ibom',    250000),
    ('South South Zone',    'Cross River',  250000),
    -- South East
    ('South East Zone',     'Anambra',      250000),
    ('South East Zone',     'Imo',          250000),
    ('South East Zone',     'Enugu',        250000),
    ('South East Zone',     'Ebonyi',       250000),
    ('South East Zone',     'Abia',         250000),
    -- North Central + FCT
    ('North Central Zone',  'FCT',          300000),
    ('North Central Zone',  'Kogi',         300000),
    ('North Central Zone',  'Benue',        300000),
    ('North Central Zone',  'Nassarawa',    300000),
    ('North Central Zone',  'Plateau',      300000),
    ('North Central Zone',  'Niger',        300000),
    ('North Central Zone',  'Kwara',        300000),
    -- North West
    ('North West Zone',     'Kano',         350000),
    ('North West Zone',     'Kaduna',       350000),
    ('North West Zone',     'Katsina',      350000),
    ('North West Zone',     'Sokoto',       350000),
    ('North West Zone',     'Kebbi',        350000),
    ('North West Zone',     'Zamfara',      350000),
    ('North West Zone',     'Jigawa',       350000),
    -- North East
    ('North East Zone',     'Borno',        400000),
    ('North East Zone',     'Yobe',         400000),
    ('North East Zone',     'Gombe',        400000),
    ('North East Zone',     'Adamawa',      400000),
    ('North East Zone',     'Taraba',       400000),
    ('North East Zone',     'Bauchi',       400000)
ON DUPLICATE KEY UPDATE
    `zone_name`  = VALUES(`zone_name`),
    `fee`        = VALUES(`fee`),
    `updated_at` = CURRENT_TIMESTAMP;


-- =============================================================================
-- 4. PRODUCTS (20 products)
-- =============================================================================
-- price and sale_price are in kobo
-- is_featured: 1=yes, 0=no  |  Products 1,2,7,12,16,19 are featured
-- =============================================================================
INSERT INTO `products`
    (`id`, `category_id`, `name`, `slug`, `description`, `price`, `sale_price`, `sku`, `is_active`, `is_featured`, `total_stock`)
VALUES
    -- ── TEES (category_id = 1) ──────────────────────────────────────────────
    (1,  1, 'Classic White Oversized Tee',
         'classic-white-oversized-tee',
         'A wardrobe staple. Premium 220gsm cotton in a relaxed oversized fit. Pre-washed for softness.',
         850000, NULL, 'TEE-WHT-OVR', 1, 1, 0),

    (2,  1, 'Classic Black Oversized Tee',
         'classic-black-oversized-tee',
         'The black twin. Same premium 220gsm cotton, same oversized silhouette — just darker.',
         850000, NULL, 'TEE-BLK-OVR', 1, 1, 0),

    (3,  1, 'Vintage Wash Graphic Tee',
         'vintage-wash-graphic-tee',
         'Acid-washed olive tee with a vintage graphic print. Slightly boxy fit with a raw hem.',
         1200000, 950000, 'TEE-OLV-GFX', 1, 0, 0),

    (4,  1, 'Navy Essential Tee',
         'navy-essential-tee',
         'Clean, minimal, and versatile. A navy essential that pairs with everything in the wardrobe.',
         850000, NULL, 'TEE-NVY-ESS', 1, 0, 0),

    (5,  1, 'Burgundy Polo Tee',
         'burgundy-polo-tee',
         'Elevated polo collar tee in rich burgundy. Smart-casual look without the effort.',
         1100000, NULL, 'TEE-BRG-PLO', 1, 0, 0),

    (6,  1, 'Camel Pocket Tee',
         'camel-pocket-tee',
         'Warm camel tone with a single chest pocket detail. 100% ring-spun cotton.',
         950000, NULL, 'TEE-CAM-PKT', 1, 0, 0),

    -- ── HOODIES (category_id = 2) ────────────────────────────────────────────
    (7,  2, 'Black Premium Hoodie',
         'black-premium-hoodie',
         '400gsm fleece pullover hoodie. Double-lined hood, kangaroo pocket, ribbed cuffs and hem.',
         2800000, NULL, 'HOD-BLK-PRM', 1, 1, 0),

    (8,  2, 'Off-White Fleece Hoodie',
         'off-white-fleece-hoodie',
         'Heavyweight off-white hoodie with a relaxed fit. Currently on sale — limited stock.',
         2800000, 2200000, 'HOD-WHT-FLC', 1, 0, 0),

    (9,  2, 'Navy Zip Hoodie',
         'navy-zip-hoodie',
         'Full-zip navy hoodie with a brushed interior. Two side pockets, adjustable drawstring.',
         3200000, NULL, 'HOD-NVY-ZIP', 1, 0, 0),

    (10, 2, 'Olive Pullover Hoodie',
         'olive-pullover-hoodie',
         'Earthy olive pullover in a medium-weight fleece. Minimal branding, maximum comfort.',
         2600000, NULL, 'HOD-OLV-PUL', 1, 0, 0),

    (11, 2, 'Burgundy Oversized Hoodie',
         'burgundy-oversized-hoodie',
         'Drop-shoulder oversized fit in deep burgundy. Perfect layering piece for cooler evenings.',
         2900000, NULL, 'HOD-BRG-OVR', 1, 0, 0),

    -- ── CAPS (category_id = 3) ───────────────────────────────────────────────
    (12, 3, 'Classic Black Dad Cap',
         'classic-black-dad-cap',
         'Six-panel unstructured dad cap in all black. Adjustable brass buckle strap.',
         650000, NULL, 'CAP-BLK-DAD', 1, 1, 0),

    (13, 3, 'White Embroidered Cap',
         'white-embroidered-cap',
         'Clean white dad cap with a subtle embroidered logo on the front panel.',
         700000, NULL, 'CAP-WHT-EMB', 1, 0, 0),

    (14, 3, 'Navy Snapback',
         'navy-snapback',
         'Flat-brim structured snapback in navy. Adjustable snap closure, one size fits most.',
         750000, NULL, 'CAP-NVY-SNP', 1, 0, 0),

    (15, 3, 'Olive Bucket Hat',
         'olive-bucket-hat',
         'Reversible olive bucket hat in 100% cotton twill. Wide brim, packable design.',
         800000, NULL, 'CAP-OLV-BCK', 1, 0, 0),

    -- ── JOGGERS (category_id = 4) ────────────────────────────────────────────
    (16, 4, 'Black Slim Joggers',
         'black-slim-joggers',
         'Tapered slim-fit joggers in brushed cotton. Elastic waistband with drawstring, zip pockets.',
         1800000, NULL, 'JOG-BLK-SLM', 1, 1, 0),

    (17, 4, 'Navy Cargo Joggers',
         'navy-cargo-joggers',
         'Utility cargo joggers with side zip pockets and leg pockets. Relaxed fit in navy.',
         2200000, NULL, 'JOG-NVY-CGO', 1, 0, 0),

    (18, 4, 'Olive Tapered Joggers',
         'olive-tapered-joggers',
         'Clean tapered joggers in olive. Minimal design with a cuffed hem and deep pockets.',
         1900000, NULL, 'JOG-OLV-TAP', 1, 0, 0),

    -- ── SETS (category_id = 5) ───────────────────────────────────────────────
    (19, 5, 'Black Tee & Jogger Set',
         'black-tee-jogger-set',
         'Matching black oversized tee and slim jogger combo. Sold as a set — save vs buying separately.',
         2500000, 2100000, 'SET-BLK-TJS', 1, 1, 0),

    (20, 5, 'Navy Hoodie & Jogger Set',
         'navy-hoodie-jogger-set',
         'Co-ord navy hoodie and cargo jogger set. A complete look, straight out of the bag.',
         3500000, NULL, 'SET-NVY-HJS', 1, 0, 0)

ON DUPLICATE KEY UPDATE
    `category_id` = VALUES(`category_id`),
    `name`        = VALUES(`name`),
    `slug`        = VALUES(`slug`),
    `description` = VALUES(`description`),
    `price`       = VALUES(`price`),
    `sale_price`  = VALUES(`sale_price`),
    `sku`         = VALUES(`sku`),
    `is_active`   = VALUES(`is_active`),
    `is_featured` = VALUES(`is_featured`),
    `updated_at`  = CURRENT_TIMESTAMP;


-- =============================================================================
-- 5. PRODUCT VARIANTS
-- =============================================================================
-- Each row: (product_id, size, colour, stock, sku)
-- Caps use 'One Size'
-- =============================================================================
INSERT INTO `product_variants` (`product_id`, `size`, `colour`, `stock`, `sku`) VALUES
    -- Product 1: Classic White Oversized Tee — XS/S/M/L/XL/XXL, White, 10 each
    (1, 'XS',  'White', 10, 'TEE-WHT-OVR-XS'),
    (1, 'S',   'White', 10, 'TEE-WHT-OVR-S'),
    (1, 'M',   'White', 10, 'TEE-WHT-OVR-M'),
    (1, 'L',   'White', 10, 'TEE-WHT-OVR-L'),
    (1, 'XL',  'White', 10, 'TEE-WHT-OVR-XL'),
    (1, 'XXL', 'White', 10, 'TEE-WHT-OVR-XXL'),

    -- Product 2: Classic Black Oversized Tee — XS/S/M/L/XL/XXL, Black, 10 each
    (2, 'XS',  'Black', 10, 'TEE-BLK-OVR-XS'),
    (2, 'S',   'Black', 10, 'TEE-BLK-OVR-S'),
    (2, 'M',   'Black', 10, 'TEE-BLK-OVR-M'),
    (2, 'L',   'Black', 10, 'TEE-BLK-OVR-L'),
    (2, 'XL',  'Black', 10, 'TEE-BLK-OVR-XL'),
    (2, 'XXL', 'Black', 10, 'TEE-BLK-OVR-XXL'),

    -- Product 3: Vintage Wash Graphic Tee — S/M/L/XL, Olive, 8 each
    (3, 'S',  'Olive', 8, 'TEE-OLV-GFX-S'),
    (3, 'M',  'Olive', 8, 'TEE-OLV-GFX-M'),
    (3, 'L',  'Olive', 8, 'TEE-OLV-GFX-L'),
    (3, 'XL', 'Olive', 8, 'TEE-OLV-GFX-XL'),

    -- Product 4: Navy Essential Tee — XS/S/M/L/XL/XXL, Navy, 12 each
    (4, 'XS',  'Navy', 12, 'TEE-NVY-ESS-XS'),
    (4, 'S',   'Navy', 12, 'TEE-NVY-ESS-S'),
    (4, 'M',   'Navy', 12, 'TEE-NVY-ESS-M'),
    (4, 'L',   'Navy', 12, 'TEE-NVY-ESS-L'),
    (4, 'XL',  'Navy', 12, 'TEE-NVY-ESS-XL'),
    (4, 'XXL', 'Navy', 12, 'TEE-NVY-ESS-XXL'),

    -- Product 5: Burgundy Polo Tee — S/M/L/XL/XXL, Burgundy, 8 each
    (5, 'S',   'Burgundy', 8, 'TEE-BRG-PLO-S'),
    (5, 'M',   'Burgundy', 8, 'TEE-BRG-PLO-M'),
    (5, 'L',   'Burgundy', 8, 'TEE-BRG-PLO-L'),
    (5, 'XL',  'Burgundy', 8, 'TEE-BRG-PLO-XL'),
    (5, 'XXL', 'Burgundy', 8, 'TEE-BRG-PLO-XXL'),

    -- Product 6: Camel Pocket Tee — XS/S/M/L/XL, Camel, 10 each
    (6, 'XS', 'Camel', 10, 'TEE-CAM-PKT-XS'),
    (6, 'S',  'Camel', 10, 'TEE-CAM-PKT-S'),
    (6, 'M',  'Camel', 10, 'TEE-CAM-PKT-M'),
    (6, 'L',  'Camel', 10, 'TEE-CAM-PKT-L'),
    (6, 'XL', 'Camel', 10, 'TEE-CAM-PKT-XL'),

    -- Product 7: Black Premium Hoodie — S/M/L/XL/XXL, Black, 6 each
    (7, 'S',   'Black', 6, 'HOD-BLK-PRM-S'),
    (7, 'M',   'Black', 6, 'HOD-BLK-PRM-M'),
    (7, 'L',   'Black', 6, 'HOD-BLK-PRM-L'),
    (7, 'XL',  'Black', 6, 'HOD-BLK-PRM-XL'),
    (7, 'XXL', 'Black', 6, 'HOD-BLK-PRM-XXL'),

    -- Product 8: Off-White Fleece Hoodie — S/M/L/XL, White, 6 each
    (8, 'S',  'White', 6, 'HOD-WHT-FLC-S'),
    (8, 'M',  'White', 6, 'HOD-WHT-FLC-M'),
    (8, 'L',  'White', 6, 'HOD-WHT-FLC-L'),
    (8, 'XL', 'White', 6, 'HOD-WHT-FLC-XL'),

    -- Product 9: Navy Zip Hoodie — S/M/L/XL/XXL, Navy, 5 each
    (9, 'S',   'Navy', 5, 'HOD-NVY-ZIP-S'),
    (9, 'M',   'Navy', 5, 'HOD-NVY-ZIP-M'),
    (9, 'L',   'Navy', 5, 'HOD-NVY-ZIP-L'),
    (9, 'XL',  'Navy', 5, 'HOD-NVY-ZIP-XL'),
    (9, 'XXL', 'Navy', 5, 'HOD-NVY-ZIP-XXL'),

    -- Product 10: Olive Pullover Hoodie — S/M/L/XL, Olive, 7 each
    (10, 'S',  'Olive', 7, 'HOD-OLV-PUL-S'),
    (10, 'M',  'Olive', 7, 'HOD-OLV-PUL-M'),
    (10, 'L',  'Olive', 7, 'HOD-OLV-PUL-L'),
    (10, 'XL', 'Olive', 7, 'HOD-OLV-PUL-XL'),

    -- Product 11: Burgundy Oversized Hoodie — S/M/L/XL/XXL, Burgundy, 5 each
    (11, 'S',   'Burgundy', 5, 'HOD-BRG-OVR-S'),
    (11, 'M',   'Burgundy', 5, 'HOD-BRG-OVR-M'),
    (11, 'L',   'Burgundy', 5, 'HOD-BRG-OVR-L'),
    (11, 'XL',  'Burgundy', 5, 'HOD-BRG-OVR-XL'),
    (11, 'XXL', 'Burgundy', 5, 'HOD-BRG-OVR-XXL'),

    -- Product 12: Classic Black Dad Cap — One Size, Black, 20
    (12, 'One Size', 'Black', 20, 'CAP-BLK-DAD-OS'),

    -- Product 13: White Embroidered Cap — One Size, White, 20
    (13, 'One Size', 'White', 20, 'CAP-WHT-EMB-OS'),

    -- Product 14: Navy Snapback — One Size, Navy, 15
    (14, 'One Size', 'Navy', 15, 'CAP-NVY-SNP-OS'),

    -- Product 15: Olive Bucket Hat — One Size, Olive, 15
    (15, 'One Size', 'Olive', 15, 'CAP-OLV-BCK-OS'),

    -- Product 16: Black Slim Joggers — XS/S/M/L/XL/XXL, Black, 8 each
    (16, 'XS',  'Black', 8, 'JOG-BLK-SLM-XS'),
    (16, 'S',   'Black', 8, 'JOG-BLK-SLM-S'),
    (16, 'M',   'Black', 8, 'JOG-BLK-SLM-M'),
    (16, 'L',   'Black', 8, 'JOG-BLK-SLM-L'),
    (16, 'XL',  'Black', 8, 'JOG-BLK-SLM-XL'),
    (16, 'XXL', 'Black', 8, 'JOG-BLK-SLM-XXL'),

    -- Product 17: Navy Cargo Joggers — S/M/L/XL/XXL, Navy, 6 each
    (17, 'S',   'Navy', 6, 'JOG-NVY-CGO-S'),
    (17, 'M',   'Navy', 6, 'JOG-NVY-CGO-M'),
    (17, 'L',   'Navy', 6, 'JOG-NVY-CGO-L'),
    (17, 'XL',  'Navy', 6, 'JOG-NVY-CGO-XL'),
    (17, 'XXL', 'Navy', 6, 'JOG-NVY-CGO-XXL'),

    -- Product 18: Olive Tapered Joggers — S/M/L/XL, Olive, 7 each
    (18, 'S',  'Olive', 7, 'JOG-OLV-TAP-S'),
    (18, 'M',  'Olive', 7, 'JOG-OLV-TAP-M'),
    (18, 'L',  'Olive', 7, 'JOG-OLV-TAP-L'),
    (18, 'XL', 'Olive', 7, 'JOG-OLV-TAP-XL'),

    -- Product 19: Black Tee & Jogger Set — S/M/L/XL, Black, 5 each
    (19, 'S',  'Black', 5, 'SET-BLK-TJS-S'),
    (19, 'M',  'Black', 5, 'SET-BLK-TJS-M'),
    (19, 'L',  'Black', 5, 'SET-BLK-TJS-L'),
    (19, 'XL', 'Black', 5, 'SET-BLK-TJS-XL'),

    -- Product 20: Navy Hoodie & Jogger Set — S/M/L/XL, Navy, 4 each
    (20, 'S',  'Navy', 4, 'SET-NVY-HJS-S'),
    (20, 'M',  'Navy', 4, 'SET-NVY-HJS-M'),
    (20, 'L',  'Navy', 4, 'SET-NVY-HJS-L'),
    (20, 'XL', 'Navy', 4, 'SET-NVY-HJS-XL')

ON DUPLICATE KEY UPDATE
    `stock`      = VALUES(`stock`),
    `updated_at` = CURRENT_TIMESTAMP;


-- =============================================================================
-- 6. UPDATE total_stock ON PRODUCTS FROM VARIANTS
-- =============================================================================
UPDATE `products`
SET `total_stock` = (
    SELECT COALESCE(SUM(`stock`), 0)
    FROM `product_variants`
    WHERE `product_variants`.`product_id` = `products`.`id`
);

-- =============================================================================
-- 7. ADMIN USER
-- =============================================================================
-- password_hash is a placeholder — admin must reset on first login
INSERT INTO `users`
    (`first_name`, `last_name`, `email`, `password_hash`, `role`, `is_active`)
VALUES
    ('Admin', 'AZFAT', 'admin@azfatbest.com', '$2y$12$placeholder_replace_on_setup', 'admin', 1)
ON DUPLICATE KEY UPDATE
    `first_name`    = VALUES(`first_name`),
    `last_name`     = VALUES(`last_name`),
    `password_hash` = VALUES(`password_hash`),
    `role`          = VALUES(`role`),
    `is_active`     = VALUES(`is_active`),
    `updated_at`    = CURRENT_TIMESTAMP;

SET FOREIGN_KEY_CHECKS = 1;
