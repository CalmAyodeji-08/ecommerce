-- =============================================================================
-- AZFAT BEST BOUTIQUE — Product Images Seed (Fixed URLs)
-- =============================================================================

USE azfat_boutique;

DELETE FROM product_images;

INSERT INTO `product_images` (`product_id`, `image_path`, `alt_text`, `is_primary`, `sort_order`) VALUES

-- ── TEES ──────────────────────────────────────────────────────────────────────

-- 1: Classic White Oversized Tee
(1, 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=600&q=80&auto=format&fit=crop', 'Classic White Oversized Tee', 1, 1),
(1, 'https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?w=600&q=80&auto=format&fit=crop', 'Classic White Oversized Tee back view', 0, 2),

-- 2: Classic Black Oversized Tee
(2, 'https://images.unsplash.com/photo-1503341504253-dff4815485f1?w=600&q=80&auto=format&fit=crop', 'Classic Black Oversized Tee', 1, 1),
(2, 'https://images.unsplash.com/photo-1576566588028-4147f3842f27?w=600&q=80&auto=format&fit=crop', 'Classic Black Oversized Tee side view', 0, 2),

-- 3: Vintage Wash Graphic Tee
(3, 'https://images.unsplash.com/photo-1529374255404-311a2a4f1fd9?w=600&q=80&auto=format&fit=crop', 'Vintage Wash Graphic Tee', 1, 1),

-- 4: Navy Essential Tee
(4, 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=600&q=80&auto=format&fit=crop', 'Navy Essential Tee', 1, 1),

-- 5: Burgundy Polo Tee
(5, 'https://images.unsplash.com/photo-1586790170083-2f9ceadc732d?w=600&q=80&auto=format&fit=crop', 'Burgundy Polo Tee', 1, 1),

-- 6: Camel Pocket Tee
(6, 'https://images.unsplash.com/photo-1523381210434-271e8be1f52b?w=600&q=80&auto=format&fit=crop', 'Camel Pocket Tee', 1, 1),

-- ── HOODIES ───────────────────────────────────────────────────────────────────

-- 7: Black Premium Hoodie
(7, 'https://images.unsplash.com/photo-1620799140188-3b2a02fd9a77?w=600&q=80&auto=format&fit=crop', 'Black Premium Hoodie', 1, 1),
(7, 'https://images.unsplash.com/photo-1509942774463-acf339cf87d5?w=600&q=80&auto=format&fit=crop', 'Black Premium Hoodie back', 0, 2),

-- 8: Off-White Fleece Hoodie
(8, 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=600&q=80&auto=format&fit=crop', 'Off-White Fleece Hoodie', 1, 1),

-- 9: Navy Zip Hoodie
(9, 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?w=600&q=80&auto=format&fit=crop', 'Navy Zip Hoodie', 1, 1),

-- 10: Olive Pullover Hoodie
(10, 'https://images.unsplash.com/photo-1542272604-787c3835535d?w=600&q=80&auto=format&fit=crop', 'Olive Pullover Hoodie', 1, 1),

-- 11: Burgundy Oversized Hoodie
(11, 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=600&q=80&auto=format&fit=crop', 'Burgundy Oversized Hoodie', 1, 1),

-- ── CAPS ──────────────────────────────────────────────────────────────────────

-- 12: Classic Black Dad Cap
(12, 'https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=600&q=80&auto=format&fit=crop', 'Classic Black Dad Cap', 1, 1),

-- 13: White Embroidered Cap
(13, 'https://images.unsplash.com/photo-1521369909029-2afed882baee?w=600&q=80&auto=format&fit=crop', 'White Embroidered Cap', 1, 1),

-- 14: Navy Snapback
(14, 'https://images.unsplash.com/photo-1534215754734-18e55d13e346?w=600&q=80&auto=format&fit=crop', 'Navy Snapback', 1, 1),

-- 15: Olive Bucket Hat
(15, 'https://images.unsplash.com/photo-1620231150904-a647c1988c1b?w=600&q=80&auto=format&fit=crop', 'Olive Bucket Hat', 1, 1),

-- ── JOGGERS ───────────────────────────────────────────────────────────────────

-- 16: Black Slim Joggers
(16, 'https://images.unsplash.com/photo-1552902865-b72c031ac5ea?w=600&q=80&auto=format&fit=crop', 'Black Slim Joggers', 1, 1),
(16, 'https://images.unsplash.com/photo-1539109136881-3be0616acf4b?w=600&q=80&auto=format&fit=crop', 'Black Slim Joggers side', 0, 2),

-- 17: Navy Cargo Joggers
(17, 'https://images.unsplash.com/photo-1473966968600-fa801b869a1a?w=600&q=80&auto=format&fit=crop', 'Navy Cargo Joggers', 1, 1),

-- 18: Olive Tapered Joggers
(18, 'https://images.unsplash.com/photo-1560769629-975ec94e6a86?w=600&q=80&auto=format&fit=crop', 'Olive Tapered Joggers', 1, 1),

-- ── SETS ──────────────────────────────────────────────────────────────────────

-- 19: Black Tee & Jogger Set
(19, 'https://images.unsplash.com/photo-1441984904996-e0b6ba687e04?w=600&q=80&auto=format&fit=crop', 'Black Tee and Jogger Set', 1, 1),
(19, 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=600&q=80&auto=format&fit=crop', 'Black Tee and Jogger Set detail', 0, 2),

-- 20: Navy Hoodie & Jogger Set
(20, 'https://images.unsplash.com/photo-1483985988355-763728e1935b?w=600&q=80&auto=format&fit=crop', 'Navy Hoodie and Jogger Set', 1, 1),
(20, 'https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=600&q=80&auto=format&fit=crop', 'Navy Hoodie and Jogger Set styled', 0, 2);

SELECT CONCAT('Images inserted: ', COUNT(*), ' rows') AS result FROM product_images;
