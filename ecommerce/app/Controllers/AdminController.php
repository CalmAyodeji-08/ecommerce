<?php
declare(strict_types=1);

class AdminController {

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function render(string $view, array $data = []): void {
        extract($data, EXTR_SKIP);
        ob_start();
        require VIEW_PATH . '/admin/' . $view . '.php';
        $content = ob_get_clean();
        require VIEW_PATH . '/admin/layouts/main.php';
    }

    private function statusBadge(string $status): string {
        $map = [
            'pending_payment' => 'yellow',
            'paid'            => 'blue',
            'processing'      => 'blue',
            'shipped'         => 'purple',
            'delivered'       => 'green',
            'cancelled'       => 'red',
            'failed'          => 'red',
        ];
        $colour = $map[$status] ?? 'grey';
        $label  = ucwords(str_replace('_', ' ', $status));
        return '<span class="admin-badge admin-badge--' . $colour . '">' . e($label) . '</span>';
    }

    // ── Auth ─────────────────────────────────────────────────────────────────

    public function loginForm(array $params = []): void {
        if (isAdmin()) { redirect(url('/admin')); }
        $adminTitle = 'Admin Login';
        // Login uses its own standalone layout (no sidebar)
        ob_start();
        require VIEW_PATH . '/admin/login.php';
        $content = ob_get_clean();
        // Output directly — login.php contains its own full HTML
        echo $content;
        exit;
    }

    public function login(array $params = []): void {
        if (!verifyCsrf()) { flash('error', 'Invalid request.'); redirect(url('/admin/login')); }

        $email    = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) {
            flash('error', 'Email and password required.');
            redirect(url('/admin/login'));
        }

        $db   = getDB();
        $stmt = $db->prepare('SELECT * FROM users WHERE email = ? AND role = ? AND is_active = 1 LIMIT 1');
        $stmt->execute([$email, 'admin']);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, (string)$user['password_hash'])) {
            flash('error', 'Invalid credentials.');
            redirect(url('/admin/login'));
        }

        $_SESSION['user_id']   = (int)$user['id'];
        $_SESSION['user_role'] = 'admin';
        $_SESSION['user_name'] = $user['first_name'];

        flash('success', 'Welcome back, ' . $user['first_name'] . '!');
        redirect(url('/admin'));
    }

    public function logout(array $params = []): void {
        session_unset(); session_destroy();
        redirect(url('/admin/login'));
    }

    // ── Dashboard ────────────────────────────────────────────────────────────

    public function dashboard(array $params = []): void {
        requireAdmin();
        $db = getDB();

        $today = date('Y-m-d');

        $ordersToday = (int)$db->query("SELECT COUNT(*) FROM orders WHERE DATE(created_at)='$today'")->fetchColumn();
        $salesToday  = (int)$db->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status NOT IN ('cancelled','failed') AND DATE(created_at)='$today'")->fetchColumn();
        $ordersAll   = (int)$db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
        $salesAll    = (int)$db->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status NOT IN ('cancelled','failed')")->fetchColumn();

        $lowStock = $db->query("SELECT id,name,total_stock FROM products WHERE total_stock <= 5 AND is_active=1 ORDER BY total_stock ASC LIMIT 10")->fetchAll();

        $recentOrders = $db->query("SELECT o.*,
            CONCAT(o.first_name,' ',o.last_name) AS customer_name
            FROM orders o ORDER BY o.created_at DESC LIMIT 10")->fetchAll();

        $this->render('dashboard', [
            'adminTitle'   => 'Dashboard',
            'activeNav'    => 'dashboard',
            'ordersToday'  => $ordersToday,
            'salesToday'   => $salesToday,
            'ordersAll'    => $ordersAll,
            'salesAll'     => $salesAll,
            'lowStock'     => $lowStock,
            'recentOrders' => $recentOrders,
        ]);
    }

    // ── Products ─────────────────────────────────────────────────────────────

    public function products(array $params = []): void {
        requireAdmin();
        $db      = getDB();
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $search  = trim($_GET['search'] ?? '');
        $offset  = ($page - 1) * $perPage;

        $where  = $search ? "WHERE p.name LIKE :s OR p.sku LIKE :s2" : "";
        $bind   = $search ? [':s' => "%$search%", ':s2' => "%$search%"] : [];

        $total = (int)$db->prepare("SELECT COUNT(*) FROM products p $where")->execute($bind) ? 0 : 0;
        $cStmt = $db->prepare("SELECT COUNT(*) FROM products p $where");
        $cStmt->execute($bind);
        $total = (int)$cStmt->fetchColumn();

        $stmt = $db->prepare("SELECT p.*,c.name AS category_name
            FROM products p LEFT JOIN categories c ON c.id=p.category_id
            $where ORDER BY p.created_at DESC LIMIT :lim OFFSET :off");
        foreach ($bind as $k => $v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset,  PDO::PARAM_INT);
        $stmt->execute();
        $products = $stmt->fetchAll();

        $this->render('products/index', [
            'adminTitle' => 'Products',
            'activeNav'  => 'products',
            'products'   => $products,
            'search'     => $search,
            'pagination' => ['current'=>$page,'total_pages'=>max(1,(int)ceil($total/$perPage)),'total_items'=>$total,'per_page'=>$perPage],
        ]);
    }

    public function addProductForm(array $params = []): void {
        requireAdmin();
        $categories = getDB()->query("SELECT id,name FROM categories WHERE is_active=1 ORDER BY sort_order")->fetchAll();
        $this->render('products/add', ['adminTitle'=>'Add Product','activeNav'=>'products','categories'=>$categories]);
    }

    public function addProduct(array $params = []): void {
        requireAdmin();
        if (!verifyCsrf()) { flash('error','Invalid request.'); redirect(url('/admin/products/add')); }

        $name        = trim($_POST['name'] ?? '');
        $categoryId  = (int)($_POST['category_id'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $priceNaira  = (float)($_POST['price'] ?? 0);
        $saleNaira   = $_POST['sale_price'] !== '' ? (float)($_POST['sale_price'] ?? 0) : null;
        $sku         = trim($_POST['sku'] ?? '') ?: null;
        $isActive    = isset($_POST['is_active']) ? 1 : 0;
        $isFeatured  = isset($_POST['is_featured']) ? 1 : 0;

        if (!$name || !$categoryId || $priceNaira <= 0) {
            flash('error','Name, category and price are required.');
            redirect(url('/admin/products/add'));
        }

        $price     = (int)round($priceNaira * 100);
        $salePrice = $saleNaira !== null ? (int)round($saleNaira * 100) : null;
        $slug      = slugify($name);

        $db = getDB();

        // Ensure unique slug
        $base = $slug; $i = 1;
        while ($db->prepare('SELECT id FROM products WHERE slug=?')->execute([$slug]) && $db->prepare('SELECT id FROM products WHERE slug=?')->execute([$slug]) && (function() use ($db,$slug){ $s=$db->prepare('SELECT id FROM products WHERE slug=?');$s->execute([$slug]);return $s->fetchColumn();})()){
            $slug = $base . '-' . $i++;
        }

        $stmt = $db->prepare("INSERT INTO products (category_id,name,slug,description,price,sale_price,sku,is_active,is_featured) VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$categoryId,$name,$slug,$description,$price,$salePrice,$sku,$isActive,$isFeatured]);
        $productId = (int)$db->lastInsertId();

        // Image upload
        if (!empty($_FILES['image']['name'])) {
            $this->handleImageUpload($productId, $_FILES['image'], true);
        }

        flash('success', '"' . $name . '" added successfully.');
        redirect(url('/admin/products'));
    }

    public function editProductForm(array $params = []): void {
        requireAdmin();
        $id      = (int)($params['id'] ?? 0);
        $db      = getDB();
        $product = $db->prepare('SELECT * FROM products WHERE id=? LIMIT 1')->execute([$id]) ? null : null;
        $pStmt   = $db->prepare('SELECT * FROM products WHERE id=? LIMIT 1');
        $pStmt->execute([$id]);
        $product = $pStmt->fetch();
        if (!$product) { http_response_code(404); require VIEW_PATH.'/errors/404.php'; return; }

        $images     = $db->prepare('SELECT * FROM product_images WHERE product_id=? ORDER BY is_primary DESC,sort_order ASC')->execute([$id]) ? [] : [];
        $iStmt      = $db->prepare('SELECT * FROM product_images WHERE product_id=? ORDER BY is_primary DESC,sort_order ASC');
        $iStmt->execute([$id]);
        $images     = $iStmt->fetchAll();

        $vStmt = $db->prepare('SELECT * FROM product_variants WHERE product_id=? ORDER BY size,colour');
        $vStmt->execute([$id]);
        $variants = $vStmt->fetchAll();

        $categories = $db->query('SELECT id,name FROM categories WHERE is_active=1 ORDER BY sort_order')->fetchAll();

        $this->render('products/edit', [
            'adminTitle' => 'Edit: ' . $product['name'],
            'activeNav'  => 'products',
            'product'    => $product,
            'images'     => $images,
            'variants'   => $variants,
            'categories' => $categories,
        ]);
    }

    public function editProduct(array $params = []): void {
        requireAdmin();
        $id = (int)($params['id'] ?? 0);
        if (!verifyCsrf()) { flash('error','Invalid request.'); redirect(url('/admin/products/edit/'.$id)); }

        $db      = getDB();
        $pStmt   = $db->prepare('SELECT id FROM products WHERE id=? LIMIT 1');
        $pStmt->execute([$id]);
        if (!$pStmt->fetch()) { flash('error','Product not found.'); redirect(url('/admin/products')); }

        $name        = trim($_POST['name'] ?? '');
        $categoryId  = (int)($_POST['category_id'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $priceNaira  = (float)($_POST['price'] ?? 0);
        $saleStr     = trim($_POST['sale_price'] ?? '');
        $saleNaira   = $saleStr !== '' ? (float)$saleStr : null;
        $sku         = trim($_POST['sku'] ?? '') ?: null;
        $isActive    = isset($_POST['is_active'])   ? 1 : 0;
        $isFeatured  = isset($_POST['is_featured']) ? 1 : 0;

        $price     = (int)round($priceNaira * 100);
        $salePrice = $saleNaira !== null ? (int)round($saleNaira * 100) : null;

        $db->prepare("UPDATE products SET category_id=?,name=?,description=?,price=?,sale_price=?,sku=?,is_active=?,is_featured=? WHERE id=?")
           ->execute([$categoryId,$name,$description,$price,$salePrice,$sku,$isActive,$isFeatured,$id]);

        if (!empty($_FILES['image']['name'])) {
            $this->handleImageUpload($id, $_FILES['image'], false);
        }

        // Handle variant stock updates
        if (!empty($_POST['variant_stock']) && is_array($_POST['variant_stock'])) {
            foreach ($_POST['variant_stock'] as $variantId => $stock) {
                $db->prepare('UPDATE product_variants SET stock=? WHERE id=? AND product_id=?')
                   ->execute([(int)$stock, (int)$variantId, $id]);
            }
            // Update total_stock
            $db->prepare('UPDATE products SET total_stock=(SELECT COALESCE(SUM(stock),0) FROM product_variants WHERE product_id=?) WHERE id=?')
               ->execute([$id,$id]);
        }

        flash('success', 'Product updated.');
        redirect(url('/admin/products'));
    }

    public function deleteProduct(array $params = []): void {
        requireAdmin();
        $id = (int)($params['id'] ?? 0);
        if (!verifyCsrf()) { flash('error','Invalid request.'); redirect(url('/admin/products')); }
        getDB()->prepare('UPDATE products SET is_active=0 WHERE id=?')->execute([$id]);
        flash('success','Product hidden from shop.');
        redirect(url('/admin/products'));
    }

    // ── Orders ───────────────────────────────────────────────────────────────

    public function orders(array $params = []): void {
        requireAdmin();
        $db      = getDB();
        $page    = max(1,(int)($_GET['page'] ?? 1));
        $perPage = 20;
        $offset  = ($page-1)*$perPage;
        $status  = trim($_GET['status'] ?? '');
        $search  = trim($_GET['search'] ?? '');

        $conditions = []; $bind = [];
        if ($status) { $conditions[] = 'o.status=:status'; $bind[':status']=$status; }
        if ($search)  { $conditions[] = '(o.order_number LIKE :s OR o.first_name LIKE :s2 OR o.last_name LIKE :s3)'; $bind[':s']="%$search%";$bind[':s2']="%$search%";$bind[':s3']="%$search%"; }
        $where = $conditions ? 'WHERE '.implode(' AND ',$conditions) : '';

        $cStmt = $db->prepare("SELECT COUNT(*) FROM orders o $where");
        $cStmt->execute($bind);
        $total = (int)$cStmt->fetchColumn();

        $stmt = $db->prepare("SELECT o.*, CONCAT(o.first_name,' ',o.last_name) AS customer_name FROM orders o $where ORDER BY o.created_at DESC LIMIT :lim OFFSET :off");
        foreach ($bind as $k=>$v) $stmt->bindValue($k,$v);
        $stmt->bindValue(':lim',$perPage,PDO::PARAM_INT);
        $stmt->bindValue(':off',$offset,PDO::PARAM_INT);
        $stmt->execute();
        $orders = $stmt->fetchAll();

        $this->render('orders/index', [
            'adminTitle'  => 'Orders',
            'activeNav'   => 'orders',
            'orders'      => $orders,
            'status'      => $status,
            'search'      => $search,
            'pagination'  => ['current'=>$page,'total_pages'=>max(1,(int)ceil($total/$perPage)),'total_items'=>$total,'per_page'=>$perPage],
            'statusBadge' => [$this,'statusBadge'],
        ]);
    }

    public function orderDetail(array $params = []): void {
        requireAdmin();
        $id = (int)($params['id'] ?? 0);
        $db = getDB();
        $oStmt = $db->prepare('SELECT * FROM orders WHERE id=? LIMIT 1');
        $oStmt->execute([$id]);
        $order = $oStmt->fetch();
        if (!$order) { http_response_code(404); require VIEW_PATH.'/errors/404.php'; return; }

        $iStmt = $db->prepare('SELECT oi.*,p.slug AS product_slug FROM order_items oi LEFT JOIN products p ON p.id=oi.product_id WHERE oi.order_id=? ORDER BY oi.id');
        $iStmt->execute([$id]);
        $items = $iStmt->fetchAll();

        $this->render('orders/detail', [
            'adminTitle'  => 'Order ' . $order['order_number'],
            'activeNav'   => 'orders',
            'order'       => $order,
            'items'       => $items,
            'statusBadge' => [$this,'statusBadge'],
        ]);
    }

    public function updateOrderStatus(array $params = []): void {
        requireAdmin();
        $id = (int)($params['id'] ?? 0);
        if (!verifyCsrf()) { flash('error','Invalid request.'); redirect(url('/admin/orders/'.$id)); }

        $allowed = ['pending_payment','paid','processing','shipped','delivered','cancelled','failed'];
        $status  = $_POST['status'] ?? '';
        if (!in_array($status, $allowed, true)) { flash('error','Invalid status.'); redirect(url('/admin/orders/'.$id)); }

        getDB()->prepare('UPDATE orders SET status=? WHERE id=?')->execute([$status,$id]);
        flash('success','Order status updated to "'.ucwords(str_replace('_',' ',$status)).'".');
        redirect(url('/admin/orders/'.$id));
    }

    // ── Categories ───────────────────────────────────────────────────────────

    public function categories(array $params = []): void {
        requireAdmin();
        $categories = getDB()->query('SELECT * FROM categories ORDER BY sort_order,id')->fetchAll();
        $this->render('categories/index', ['adminTitle'=>'Categories','activeNav'=>'categories','categories'=>$categories]);
    }

    public function addCategory(array $params = []): void {
        requireAdmin();
        if (!verifyCsrf()) { flash('error','Invalid request.'); redirect(url('/admin/categories')); }
        $name = trim($_POST['name'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        $sort = (int)($_POST['sort_order'] ?? 0);
        if (!$name) { flash('error','Name required.'); redirect(url('/admin/categories')); }
        $slug = slugify($name);
        $db   = getDB();
        $base = $slug; $i = 1;
        while ((function() use ($db,$slug){ $s=$db->prepare('SELECT id FROM categories WHERE slug=?');$s->execute([$slug]);return $s->fetchColumn();})()) {
            $slug = $base.'-'.$i++;
        }
        $db->prepare('INSERT INTO categories (name,slug,description,sort_order,is_active) VALUES (?,?,?,?,1)')->execute([$name,$slug,$desc,$sort]);
        flash('success','"'.$name.'" category added.');
        redirect(url('/admin/categories'));
    }

    public function editCategory(array $params = []): void {
        requireAdmin();
        $id = (int)($params['id'] ?? 0);
        if (!verifyCsrf()) { flash('error','Invalid request.'); redirect(url('/admin/categories')); }
        $name     = trim($_POST['name'] ?? '');
        $desc     = trim($_POST['description'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $sort     = (int)($_POST['sort_order'] ?? 0);
        getDB()->prepare('UPDATE categories SET name=?,description=?,is_active=?,sort_order=? WHERE id=?')->execute([$name,$desc,$isActive,$sort,$id]);
        flash('success','Category updated.');
        redirect(url('/admin/categories'));
    }

    // ── Delivery zones ────────────────────────────────────────────────────────

    public function deliveryZones(array $params = []): void {
        requireAdmin();
        $zones = getDB()->query('SELECT * FROM delivery_zones ORDER BY state')->fetchAll();
        $this->render('delivery-zones/index', ['adminTitle'=>'Delivery Zones','activeNav'=>'delivery','zones'=>$zones]);
    }

    public function updateDeliveryZones(array $params = []): void {
        requireAdmin();
        if (!verifyCsrf()) { flash('error','Invalid request.'); redirect(url('/admin/delivery-zones')); }
        $fees = $_POST['fees'] ?? [];
        $db   = getDB();
        $stmt = $db->prepare('UPDATE delivery_zones SET fee=? WHERE state=?');
        foreach ($fees as $state => $naira) {
            $kobo = (int)round((float)$naira * 100);
            $stmt->execute([$kobo, $state]);
        }
        flash('success','Delivery fees updated.');
        redirect(url('/admin/delivery-zones'));
    }

    // ── Subscribers ───────────────────────────────────────────────────────────

    public function subscribers(array $params = []): void {
        requireAdmin();
        $subs = getDB()->query('SELECT * FROM subscribers ORDER BY created_at DESC')->fetchAll();
        $this->render('subscribers/index', ['adminTitle'=>'Subscribers','activeNav'=>'subscribers','subscribers'=>$subs]);
    }

    // ── Settings ─────────────────────────────────────────────────────────────

    public function settings(array $params = []): void {
        requireAdmin();
        $rows     = getDB()->query('SELECT setting_key,setting_value FROM settings ORDER BY setting_key')->fetchAll();
        $settings = array_column($rows,'setting_value','setting_key');
        $this->render('settings/index', ['adminTitle'=>'Settings','activeNav'=>'settings','settings'=>$settings]);
    }

    public function saveSettings(array $params = []): void {
        requireAdmin();
        if (!verifyCsrf()) { flash('error','Invalid request.'); redirect(url('/admin/settings')); }
        $db   = getDB();
        $stmt = $db->prepare('INSERT INTO settings (setting_key,setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)');
        foreach ($_POST as $key => $value) {
            if ($key === 'csrf_token') continue;
            $stmt->execute([trim($key), trim($value)]);
        }
        flash('success','Settings saved.');
        redirect(url('/admin/settings'));
    }

    // ── Image upload helper ───────────────────────────────────────────────────

    private function handleImageUpload(int $productId, array $file, bool $isPrimary): void {
        $allowed = ['image/jpeg','image/jpg','image/png','image/webp','image/gif'];
        if (!in_array($file['type'], $allowed, true)) return;
        if ($file['size'] > 5 * 1024 * 1024) return;

        $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = uniqid('prod_') . '.' . $ext;
        $dest     = ROOT_PATH . '/public/images/products/' . $filename;

        if (!is_dir(dirname($dest))) mkdir(dirname($dest), 0755, true);
        if (!move_uploaded_file($file['tmp_name'], $dest)) return;

        $db = getDB();
        if ($isPrimary) {
            $db->prepare('UPDATE product_images SET is_primary=0 WHERE product_id=?')->execute([$productId]);
        }
        $db->prepare('INSERT INTO product_images (product_id,image_path,alt_text,is_primary,sort_order) VALUES (?,?,?,?,0)')
           ->execute([$productId, $filename, '', $isPrimary ? 1 : 0]);

        // Update primary_image reference
        $db->prepare('UPDATE products SET total_stock=(SELECT COALESCE(SUM(stock),0) FROM product_variants WHERE product_id=?) WHERE id=?')
           ->execute([$productId,$productId]);
    }
}
