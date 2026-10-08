<?php

declare(strict_types=1);

/**
 * Application routes.
 * Add new routes here as phases are built.
 */

/** @var Router $router */

// ─── Phase 2 test ─────────────────────────────────────────────────────────────
$router->get('/', 'HomeController', 'index');

// ─── Phase 3: Landing / Home ───────────────────────────────────────────────────
// (HomeController handles this above)

// ─── Phase 4: Catalogue ────────────────────────────────────────────────────────
$router->get('/shop',                   'ShopController',    'index');
$router->get('/category/:slug',         'ShopController',    'category');
$router->get('/product/:slug',          'ProductController', 'show');
$router->get('/search',                 'ShopController',    'search');

// ─── Phase 5: Cart ─────────────────────────────────────────────────────────────
$router->get('/cart',                   'CartController',    'index');
$router->post('/cart/add',              'CartController',    'add');
$router->post('/cart/update',           'CartController',    'update');
$router->post('/cart/remove',           'CartController',    'remove');

// ─── Phase 6: Account ──────────────────────────────────────────────────────────
$router->get('/account',                'AccountController', 'dashboard');
$router->get('/account/login',          'AccountController', 'loginForm');
$router->post('/account/login',         'AccountController', 'login');
$router->get('/account/register',       'AccountController', 'registerForm');
$router->post('/account/register',      'AccountController', 'register');
$router->get('/account/logout',         'AccountController', 'logout');
$router->get('/account/forgot-password','AccountController', 'forgotForm');
$router->post('/account/forgot-password','AccountController','forgot');
$router->get('/account/reset-password', 'AccountController', 'resetForm');
$router->post('/account/reset-password','AccountController', 'reset');
$router->get('/account/addresses',      'AccountController', 'addresses');
$router->post('/account/addresses/add', 'AccountController', 'addAddress');
$router->post('/account/addresses/delete','AccountController','deleteAddress');
$router->get('/account/orders',         'AccountController', 'orders');
$router->get('/account/orders/:id',     'AccountController', 'orderDetail');
$router->get('/account/wishlist',       'AccountController', 'wishlist');
$router->post('/account/wishlist/toggle','AccountController','wishlistToggle');

// ─── Phase 7: Checkout ─────────────────────────────────────────────────────────
$router->get('/checkout',               'CheckoutController','index');
$router->post('/checkout',              'CheckoutController','process');
$router->get('/checkout/confirm/:id',   'CheckoutController','confirm');

// ─── Phase 8: Payments ─────────────────────────────────────────────────────────
$router->post('/payment/initiate',      'PaymentController', 'initiate');
$router->get('/payment/callback',       'PaymentController', 'callback');
$router->post('/payment/webhook/paystack','PaymentController','webhookPaystack');
$router->post('/payment/webhook/flutterwave','PaymentController','webhookFlutterwave');

// ─── Phase 9: Admin ────────────────────────────────────────────────────────────
$router->get('/admin',                  'AdminController',   'dashboard');
$router->get('/admin/login',            'AdminController',   'loginForm');
$router->post('/admin/login',           'AdminController',   'login');
$router->get('/admin/logout',           'AdminController',   'logout');
$router->get('/admin/products',         'AdminController',   'products');
$router->get('/admin/products/add',     'AdminController',   'addProductForm');
$router->post('/admin/products/add',    'AdminController',   'addProduct');
$router->get('/admin/products/edit/:id','AdminController',   'editProductForm');
$router->post('/admin/products/edit/:id','AdminController',  'editProduct');
$router->post('/admin/products/delete/:id','AdminController','deleteProduct');
$router->get('/admin/orders',           'AdminController',   'orders');
$router->get('/admin/orders/:id',       'AdminController',   'orderDetail');
$router->post('/admin/orders/:id/status','AdminController',  'updateOrderStatus');
$router->get('/admin/categories',       'AdminController',   'categories');
$router->post('/admin/categories/add',  'AdminController',   'addCategory');
$router->post('/admin/categories/edit/:id','AdminController','editCategory');
$router->get('/admin/delivery-zones',   'AdminController',   'deliveryZones');
$router->post('/admin/delivery-zones/update','AdminController','updateDeliveryZones');
$router->get('/admin/subscribers',      'AdminController',   'subscribers');
$router->get('/admin/settings',         'AdminController',   'settings');
$router->post('/admin/settings',        'AdminController',   'saveSettings');

// ─── Subscribe (landing page) ──────────────────────────────────────────────────
$router->post('/subscribe',             'SubscribeController','store');
