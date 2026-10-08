<?php
$whatsapp  = setting('whatsapp_number', '2348000000000');
$instagram = setting('instagram_handle', 'azfatbestboutique');
$year      = date('Y');
?>
<footer class="site-footer" role="contentinfo">
    <div class="container">
        <div class="site-footer__grid">

            <!-- Brand -->
            <div class="site-footer__brand">
                <div class="site-footer__logo">AZFAT <span>BEST BOUTIQUE</span></div>
                <p>Premium streetwear crafted for everyday life. Quality fabrics, timeless fits.</p>
                <div class="site-footer__social">
                    <a href="https://wa.me/<?= e($whatsapp) ?>" target="_blank" rel="noopener" aria-label="WhatsApp">
                        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/>
                        </svg>
                    </a>
                    <a href="https://instagram.com/<?= e($instagram) ?>" target="_blank" rel="noopener" aria-label="Instagram">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                            <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Shop links -->
            <div class="site-footer__col">
                <h4>Shop</h4>
                <ul>
                    <li><a href="<?= url('/category/tees') ?>">Tees</a></li>
                    <li><a href="<?= url('/category/hoodies') ?>">Hoodies</a></li>
                    <li><a href="<?= url('/category/caps') ?>">Caps</a></li>
                    <li><a href="<?= url('/category/joggers') ?>">Joggers</a></li>
                    <li><a href="<?= url('/category/sets') ?>">Sets</a></li>
                    <li><a href="<?= url('/shop') ?>">All Products</a></li>
                </ul>
            </div>

            <!-- Help links -->
            <div class="site-footer__col">
                <h4>Help</h4>
                <ul>
                    <li><a href="<?= url('/#faq') ?>">FAQ</a></li>
                    <li><a href="<?= url('/#delivery') ?>">Delivery Info</a></li>
                    <li><a href="<?= url('/#returns') ?>">Returns Policy</a></li>
                    <li><a href="https://wa.me/<?= e($whatsapp) ?>" target="_blank" rel="noopener">WhatsApp Us</a></li>
                </ul>
            </div>

            <!-- Account links -->
            <div class="site-footer__col">
                <h4>Account</h4>
                <ul>
                    <?php if (isLoggedIn()): ?>
                    <li><a href="<?= url('/account') ?>">My Account</a></li>
                    <li><a href="<?= url('/account/orders') ?>">Order History</a></li>
                    <li><a href="<?= url('/account/wishlist') ?>">Wishlist</a></li>
                    <li><a href="<?= url('/account/logout') ?>">Log Out</a></li>
                    <?php else: ?>
                    <li><a href="<?= url('/account/login') ?>">Log In</a></li>
                    <li><a href="<?= url('/account/register') ?>">Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>

        </div>

        <!-- Bottom bar -->
        <div class="site-footer__bottom">
            <span>&copy; <?= $year ?> <?= e(APP_NAME) ?>. All rights reserved.</span>
            <span>Secure payments by Paystack &amp; Flutterwave</span>
        </div>
    </div>
</footer>
