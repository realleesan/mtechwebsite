<?php
/**
 * Index.php - Entry Point
 * 
 * NOTE: Đây là file entry point chính của hệ thống MVC
 * - Xử lý routing, load config, khởi tạo session
 * - Định nghĩa các route cho từng trang trong switch-case
 */

// ==========================================
// NOTE: Security Constant - Định nghĩa hằng số bảo mật
// ==========================================
// define('APP_INIT', true);

// ==========================================
// NOTE: Session - Khởi tạo session
// ==========================================
session_start();

// ==========================================
// NOTE: Error Reporting - Cấu hình hiển thị lỗi
// ==========================================
// Development mode:
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);

// Production mode:
// error_reporting(0);
// ini_set('display_errors', 0);

// ==========================================
// NOTE: Error/Exception Handlers - Xử lý lỗi tùy chỉnh
// ==========================================
// set_error_handler(function($errno, $errstr, $errfile, $errline) {
//     error_log("PHP Error [$errno]: $errstr in $errfile on line $errline");
//     return false;
// });

// set_exception_handler(function($e) {
//     error_log('Uncaught exception: ' . $e->getMessage());
//     // NOTE: Thêm xử lý hiển thị lỗi cho user
//     echo '<h1>Lỗi hệ thống</h1>';
//     echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
//     exit;
// });

// ==========================================
// NOTE: Load Configuration - Load file config
// ==========================================
$base_dir = __DIR__;
// $config = require_once $base_dir . '/config/config.php';

// ==========================================
// NOTE: Set error reporting based on config
// ==========================================
// if ($config['app']['debug']) {
//     error_reporting(E_ALL);
//     ini_set('display_errors', 1);
// } else {
//     error_reporting(0);
//     ini_set('display_errors', 0);
// }

// ==========================================
// NOTE: Include Core Files - Load các file core
// ==========================================
// require_once $base_dir . '/core/security.php';
// require_once $base_dir . '/core/functions.php';
// require_once $base_dir . '/core/database.php';
// require_once $base_dir . '/app/middleware/AuthMiddleware.php';

// ==========================================
// NOTE: Initialize Services - Khởi tạo services
// ==========================================
// require_once $base_dir . '/core/view_init.php';
// init_url_builder();

// ==========================================
// NOTE: Output Buffering - Bật output buffering
// ==========================================
if (ob_get_level() === 0) {
    ob_start();
}

// ==========================================
// NOTE: Load Breadcrumb Config
// ==========================================
require_once 'app/views/_layout/breadcrumb.php';

// ==========================================
// NOTE: Handle AJAX Form Submission (before routing)
// ==========================================
if (isset($_GET['page']) && $_GET['page'] === 'contact' && 
    isset($_GET['action']) && $_GET['action'] === 'submit' &&
    $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Form submission - output JSON directly, no layout
    require_once 'app/views/contact/contact.php';
    // Script ends in contact.php after outputting JSON
}

// ==========================================
// NOTE: Get Current Page - Lấy trang hiện tại từ URL
// ==========================================
$page = $_GET['page'] ?? 'home';

// ==========================================
// NOTE: Default Service - Service mặc định cho các trang public
// ==========================================
// $currentService = $publicService ?? null;

// ==========================================
// NOTE: Routing - Định nghĩa các route cho từng trang
// ==========================================
switch($page) {
    
    // --------------------------------------
    // NOTE: Home Page - Trang chủ
    // --------------------------------------
    case 'home':
        $title = 'Trang chủ - Tên Website';
        $content = 'app/views/home/home.php';
        $showPageHeader = false;
        $showCTA = true;
        $showBreadcrumb = false;
        // $currentService = $publicService ?? $currentService;
        break;
        
    // --------------------------------------
    // NOTE: About Page - Trang giới thiệu
    // --------------------------------------
    case 'about':
        $title = 'Giới thiệu - Tên Website';
        $content = 'app/views/about/about.php';
        $showPageHeader = true;
        $pageTitle = 'About Us';
        $showCTA = true;
        $showBreadcrumb = true;
        break;
        
    // --------------------------------------
    // NOTE: Teams Page - Trang đội ngũ
    // --------------------------------------
    case 'teams':
        $title = 'Đội ngũ - Tên Website';
        $content = 'app/views/about/teams.php';
        $showPageHeader = true;
        $pageTitle = 'Our Team';
        $showCTA = false;
        $showBreadcrumb = true;
        break;
        
    // --------------------------------------
    // NOTE: Company History Page - Lịch sử công ty
    // --------------------------------------
    case 'company.history':
        $title = 'Lịch sử công ty - Tên Website';
        $content = 'app/views/about/company.history.php';
        $showPageHeader = true;
        $showCTA = true;
        $showBreadcrumb = true;
        break;
        
    // --------------------------------------
    // NOTE: Products Page - Trang sản phẩm/danh sách
    // --------------------------------------
    case 'products':
        $title = 'Sản phẩm - Tên Website';
        $content = 'app/views/products/products.php';
        $showPageHeader = true;
        $showCTA = false;
        $showBreadcrumb = true;
        // $breadcrumbs = generate_breadcrumb('products');
        break;
        
    // --------------------------------------
    // NOTE: Product Details - Chi tiết sản phẩm
    // --------------------------------------
    case 'details':
        $title = 'Chi tiết sản phẩm - Tên Website';
        $content = 'app/views/products/details.php';
        $showPageHeader = false;
        $showCTA = false;
        $showBreadcrumb = true;
        // NOTE: Xử lý breadcrumb động theo ID sản phẩm
        // if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        //     $breadcrumbs = get_product_breadcrumb($_GET['id']);
        // }
        break;
        
    // --------------------------------------
    // NOTE: Blogs Page - Trang tin tức/blog
    // --------------------------------------
    case 'blogs':
        $title = 'Blog - Tên Website';
        $content = 'app/views/blogs/blogs.php';
        $showPageHeader = true;
        $pageTitle = 'Blog';
        $showCTA = false;
        $showBreadcrumb = true;
        break;
        
    // --------------------------------------
    // NOTE: Blog Details - Chi tiết blog
    // --------------------------------------
    case 'blog-details':
        $title = 'Chi tiết blog - Tên Website';
        $content = 'app/views/blogs/details.php';
        $showPageHeader = false;
        $showCTA = false;
        $showBreadcrumb = true;
        // NOTE: Xử lý breadcrumb động theo ID blog
        break;
        
    // --------------------------------------
    // NOTE: Projects Page - Trang dự án
    // --------------------------------------
    case 'projects':
        $title = 'Dự án - Tên Website';
        $content = 'app/views/projects/projects.php';
        $showPageHeader = true;
        $pageTitle = 'Projects';
        $showCTA = false;
        $showBreadcrumb = true;
        break;
        
    // --------------------------------------
    // NOTE: Project Details - Chi tiết dự án
    // --------------------------------------
    case 'project-details':
        $title = 'Chi tiết dự án - Tên Website';
        $content = 'app/views/projects/projects.details.php';
        $showPageHeader = true;
        $pageTitle = 'Project Details';
        $showCTA = false;
        $showBreadcrumb = true;
        // NOTE: Xử lý breadcrumb động theo ID dự án
        break;
        
    // --------------------------------------
    // NOTE: Services Page - Trang dịch vụ
    // --------------------------------------
    case 'services':
        $title = 'Dịch vụ - Tên Website';
        $content = 'app/views/services/services.php';
        $showPageHeader = true;
        $pageTitle = 'Services';
        $showCTA = false;
        $showBreadcrumb = true;
        break;
        
    // --------------------------------------
    // NOTE: Categories Page - Trang danh mục dịch vụ
    // --------------------------------------
    case 'categories':
        $title = 'Our Services - Tên Website';
        $content = 'app/views/categories/categories.php';
        $showPageHeader = false; // Banner được xử lý trong view
        $showCTA = false;
        $showBreadcrumb = false; // Breadcrumb được xử lý trong view
        break;
        
    // --------------------------------------
    // NOTE: News Page - Trang tin tức
    // --------------------------------------
    case 'news':
        $title = 'Tin tức - Tên Website';
        $content = 'app/views/news/news.php';
        $showPageHeader = true;
        $showCTA = false;
        $showBreadcrumb = true;
        // $breadcrumbs = generate_breadcrumb('news');
        break;
        
    // --------------------------------------
    // NOTE: News Details - Chi tiết tin tức
    // --------------------------------------
    case 'news-details':
        $title = 'Chi tiết tin tức - Tên Website';
        $content = 'app/views/news/details.php';
        $showPageHeader = false;
        $showCTA = false;
        $showBreadcrumb = true;
        // NOTE: Xử lý breadcrumb động theo ID tin tức
        break;
        
    // --------------------------------------
    // NOTE: Contact Page - Trang liên hệ
    // --------------------------------------
    case 'contact':
        $title = 'Liên hệ - Tên Website';
        $content = 'app/views/contact/contact.php';
        $showPageHeader = true;
        $pageTitle = 'Contact Us';
        $showCTA = false;
        $showBreadcrumb = true;
        break;
        
    // --------------------------------------
    // NOTE: Authentication Routes - Đăng nhập/Đăng ký
    // --------------------------------------
    case 'login':
        // $action = $_GET['action'] ?? '';
        // if ($action === 'process' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        //     require_once 'app/controllers/AuthController.php';
        //     $authController = new AuthController();
        //     $authController->processLogin();
        //     exit;
        // }
        // require_once 'app/controllers/AuthController.php';
        // $authController = new AuthController();
        // $authController->login();
        // exit;
        
        $title = 'Đăng nhập - Tên Website';
        $content = 'app/views/auth/login.php';
        break;
        
    case 'register':
        // NOTE: Tương tự login, xử lý register
        $title = 'Đăng ký - Tên Website';
        $content = 'app/views/auth/register.php';
        break;
        
    case 'forgot':
        // NOTE: Xử lý quên mật khẩu
        $title = 'Quên mật khẩu - Tên Website';
        $content = 'app/views/auth/forgot.php';
        break;
        
    case 'logout':
        // require_once 'app/controllers/AuthController.php';
        // $authController = new AuthController();
        // $authController->logout();
        // exit;
        break;
        
    // --------------------------------------
    // NOTE: User Dashboard - Trang tài khoản người dùng
    // --------------------------------------
    case 'users':
        $module = $_GET['module'] ?? 'dashboard';
        $action = $_GET['action'] ?? 'index';
        
        $title = 'Tài khoản - Tên Website';
        $showPageHeader = false;
        $showCTA = false;
        $showBreadcrumb = true;
        
        switch($module) {
            case 'dashboard':
            default:
                $content = 'app/views/users/dashboard.php';
                $title = 'Tài khoản của tôi - Tên Website';
                break;
            case 'account':
                $content = 'app/views/users/account/index.php';
                $title = 'Thông tin tài khoản - Tên Website';
                break;
            case 'orders':
                $content = 'app/views/users/orders/index.php';
                $title = 'Đơn hàng - Tên Website';
                break;
            case 'cart':
                $content = 'app/views/users/cart/index.php';
                $title = 'Giỏ hàng - Tên Website';
                break;
            case 'wishlist':
                $content = 'app/views/users/wishlist/index.php';
                $title = 'Yêu thích - Tên Website';
                break;
        }
        break;
        
    // --------------------------------------
    // NOTE: Checkout/Payment - Thanh toán
    // --------------------------------------
    case 'checkout':
        $title = 'Thanh toán - Tên Website';
        $content = 'app/views/payment/checkout.php';
        $showPageHeader = false;
        $showCTA = false;
        $showBreadcrumb = true;
        // $breadcrumbs = generate_breadcrumb('checkout');
        break;
        
    case 'payment':
        $title = 'Thanh toán - Tên Website';
        $content = 'app/views/payment/payment.php';
        $showPageHeader = false;
        $showCTA = false;
        $showBreadcrumb = true;
        break;
        
    case 'payment_success':
        $title = 'Thanh toán thành công - Tên Website';
        $content = 'app/views/payment/success.php';
        $showPageHeader = false;
        $showCTA = false;
        $showBreadcrumb = true;
        break;
        
    // --------------------------------------
    // NOTE: Admin Panel - Quản trị (nếu có)
    // --------------------------------------
    case 'admin':
        $module = $_GET['module'] ?? 'dashboard';
        $action = $_GET['action'] ?? 'index';
        
        // NOTE: Kiểm tra quyền admin
        // $authMiddleware = new AuthMiddleware();
        // if (!$authMiddleware->requireAdmin()) {
        //     header('Location: ?page=users');
        //     exit;
        // }
        
        $title = 'Admin Panel - Tên Website';
        $useAdminLayout = true; // Flag để sử dụng admin layout
        
        // NOTE: Routing cho admin modules
        switch($module) {
            case 'dashboard':
                $content = 'app/views/admin/dashboard.php';
                break;
            case 'products':
                $content = 'app/views/admin/products/index.php';
                break;
            case 'categories':
                $content = 'app/views/admin/categories/index.php';
                break;
            case 'users':
                $content = 'app/views/admin/users/index.php';
                break;
            case 'orders':
                $content = 'app/views/admin/orders/index.php';
                break;
            default:
                $content = 'app/views/admin/dashboard.php';
                break;
        }
        break;
        
    // --------------------------------------
    // NOTE: 404 Page - Trang không tìm thấy
    // --------------------------------------
    default:
        $title = 'Không tìm thấy trang - Tên Website';
        $content = 'errors/404.php';
        $showPageHeader = false;
        $showCTA = false;
        $showBreadcrumb = false;
        http_response_code(404);
        break;
}

// ==========================================
// NOTE: Auto-resolve breadcrumbs từ config tập trung
// Chỉ set nếu chưa được gán thủ công trong switch
// ==========================================
if (!isset($breadcrumbs) && ($showBreadcrumb ?? false)) {
    $breadcrumbs = get_breadcrumbs($page);
}

// ==========================================
// NOTE: Include Master Layout - Load layout chính
// ==========================================
// Các biến truyền sang layout: $title, $content, $breadcrumbs, $showPageHeader, v.v.

if (isset($useAdminLayout) && $useAdminLayout) {
    // NOTE: Sử dụng admin layout
    // include_once 'app/views/_layout/admin_master.php';
} else {
    // NOTE: Sử dụng layout thường
    include_once 'app/views/_layout/master.php';
}

// ==========================================
// NOTE: Flush Output Buffer - Xuất output buffer
// ==========================================
if (ob_get_level() > 0) {
    ob_end_flush();
}
