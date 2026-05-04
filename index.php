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
// NOTE: Output Buffering - Bật output buffering SỚM
// Phải bật trước mọi thứ để error handler có thể
// xóa output cũ và render trang 500 sạch sẽ
// ==========================================
if (ob_get_level() === 0) {
    ob_start();
}

// ==========================================
// NOTE: Global Error Handler - Bắt lỗi 500
// Đăng ký TRƯỚC KHI load bất kỳ thứ gì khác
// ==========================================

/**
 * Hàm hiển thị trang lỗi 500
 * Dùng chung cho cả exception handler và error handler
 */
function render500Page(string $logMessage = ''): void
{
    // Ghi log lỗi (không hiển thị ra user)
    if ($logMessage) {
        error_log('[500 Error] ' . $logMessage);
    }

    // Xóa toàn bộ output buffer đang có (tránh HTML dở dang)
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    // Trả HTTP status 500
    http_response_code(500);

    // Set các biến cần thiết cho master layout
    $page           = '500';
    $title          = 'Lỗi máy chủ - MTECHJSC';
    $content        = 'errors/500.php';
    $showPageHeader = false;
    $showCTA        = false;
    $showBreadcrumb = false;
    $hideHeader     = true;

    // Render layout
    include_once __DIR__ . '/app/views/_layout/master.php';
    exit;
}

/**
 * Exception Handler - Bắt tất cả Uncaught Exception & Error
 * Ví dụ: throw new Exception(), TypeError, RuntimeException...
 */
set_exception_handler(function (Throwable $e): void {
    render500Page(
        get_class($e) . ': ' . $e->getMessage() .
        ' in ' . $e->getFile() . ' on line ' . $e->getLine()
    );
});

/**
 * Error Handler - Bắt các lỗi PHP runtime
 * Ví dụ: E_ERROR, E_WARNING, E_NOTICE, E_PARSE...
 * Chuyển chúng thành ErrorException để exception handler xử lý
 */
set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline): bool {
    // Chỉ xử lý các lỗi nghiêm trọng thành trang 500
    // Bỏ qua E_NOTICE, E_DEPRECATED, E_STRICT (không phải lỗi server)
    $fatalErrors = E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR;
    if ($errno & $fatalErrors) {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
    // Trả false để PHP xử lý các lỗi nhẹ theo cách mặc định
    return false;
});

/**
 * Shutdown Handler - Bắt Fatal Error xảy ra khi PHP tắt
 * Ví dụ: memory exhausted, maximum execution time exceeded...
 * set_error_handler() không bắt được loại này
 */
register_shutdown_function(function (): void {
    $error = error_get_last();
    if ($error !== null) {
        $fatalTypes = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];
        if (in_array($error['type'], $fatalTypes, true)) {
            render500Page(
                'Fatal Error [' . $error['type'] . ']: ' . $error['message'] .
                ' in ' . $error['file'] . ' on line ' . $error['line']
            );
        }
    }
});

// ==========================================
// NOTE: Coming Soon Mode Check - Kiểm tra chế độ bảo trì
// ==========================================
// NOTE: Kiểm tra sớm để chặn toàn bộ website nếu đang bảo trì
require_once __DIR__ . '/core/database.php';
require_once __DIR__ . '/app/models/ComingsoonModel.php';

$comingsoonModel = new ComingsoonModel();
$isComingSoonActive = false;

try {
    $isComingSoonActive = $comingsoonModel->isComingSoonActive();
} catch (Exception $e) {
    // Nếu lỗi DB, mặc định tắt coming soon để website vẫn chạy
    $isComingSoonActive = false;
}

// NOTE: Kiểm tra nếu coming soon đang active
if ($isComingSoonActive) {
    // NOTE: Cho phép admin truy cập bình thường (có thể đăng nhập admin)
    // Bạn có thể thay đổi logic này theo cách xác thực admin của mình
    $isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    $isComingSoonPage = isset($_GET['page']) && $_GET['page'] === 'comingsoon';
    $isLoginPage = isset($_GET['page']) && in_array($_GET['page'], ['login', 'admin']);
    
    // Nếu không phải admin và không phải trang coming soon hoặc login
    if (!$isAdmin && !$isComingSoonPage && !$isLoginPage) {
        // Chuyển hướng đến trang coming soon
        // Trang coming soon tự chứa HTML hoàn chỉnh nên include trực tiếp
        include __DIR__ . '/app/views/comingsoon/comingsoon.php';
        exit;
    }
}

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

// ── Home contact form AJAX (same pattern as contact page) ──────────────────
if (isset($_GET['page']) && $_GET['page'] === 'home' &&
    isset($_GET['action']) && $_GET['action'] === 'contact-submit' &&
    $_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'app/views/home/home.php';
    exit;
}

// ── Teams question form AJAX ────────────────────────────────────────────────
// Cover cả page=teams (URL mới) và page=about (URL cũ có thể còn trong cache)
if (isset($_GET['action']) && $_GET['action'] === 'submit_question' &&
    isset($_GET['page']) && in_array($_GET['page'], ['teams', 'about']) &&
    $_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'app/views/about/teams.php';
    exit;
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
        // Load models và lấy dữ liệu cho trang chủ
        require_once 'app/models/CategoriesModel.php';
        require_once 'app/models/ProjectsModel.php';
        require_once 'app/models/ClientLogosModel.php';

        $categoriesModel    = new CategoriesModel();
        $projectsModel      = new ProjectsModel();
        $clientLogosModel   = new ClientLogosModel();

        $homeServices       = $categoriesModel->getHomeServices(6);
        $homeProjects       = $projectsModel->getHomeProjects(5);
        $clientLogos        = $clientLogosModel->getAllActive();
        // Lấy tối đa 3 blogs mới nhất cho trang home
        require_once 'app/models/BlogsModel.php';
        $blogsModel         = new BlogsModel();
        $homeBlogs          = $blogsModel->getHomeBlogs(3);

        $title          = 'Trang chủ - MTECH';
        $content        = 'app/views/home/home.php';
        $showPageHeader = false;
        $showCTA        = true;
        $showBreadcrumb = false;
        break;

    // --------------------------------------
    // NOTE: About Page - Trang giới thiệu
    // --------------------------------------
    case 'about':
        require_once 'app/models/ClientLogosModel.php';
        $clientLogosModel = new ClientLogosModel();
        $clientLogos      = $clientLogosModel->getAllActive();

        $title          = 'Giới thiệu - MTECHJSC';
        $content        = 'app/views/about/about.php';
        $showPageHeader = true;

        $showCTA        = true;
        $showBreadcrumb = true;
        break;
        
    // --------------------------------------
    // NOTE: Teams Page - Trang đội ngũ
    // --------------------------------------
    case 'teams':
        $title = 'Đội ngũ - MTECHJSC';
        $content = 'app/views/about/teams.php';
        $showPageHeader = true;

        $showCTA = false;
        $showBreadcrumb = true;
        break;
        
    // --------------------------------------
    // NOTE: Company History Page - Lịch sử công ty
    // --------------------------------------
    case 'company.history':
        $title = 'Lịch sử công ty - MTECHJSC';
        $content = 'app/views/about/company.history.php';
        $showPageHeader = true;
        $showCTA = true;
        $showBreadcrumb = true;
        break;
        
    // --------------------------------------
    // NOTE: Products Page - Trang sản phẩm/danh sách
    // --------------------------------------
    case 'products':
        $title = 'Sản phẩm - MTECHJSC';
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
        $title = 'Chi tiết sản phẩm - MTECHJSC';
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
        require_once 'app/models/BlogsModel.php';
        $blogsModel = new BlogsModel();

        $filterCatId  = isset($_GET['cat'])    ? (int) $_GET['cat']          : 0;
        $filterTag    = isset($_GET['tag'])     ? trim($_GET['tag'])          : '';
        $searchQuery  = isset($_GET['search'])  ? trim(urldecode($_GET['search'])) : '';
        $currentPage  = isset($_GET['p'])       ? max(1, (int) $_GET['p'])   : 1;
        $perPage      = 5;

        $blogsResult    = $blogsModel->getBlogs($currentPage, $perPage, $filterCatId, $filterTag, $searchQuery);
        $blogs          = $blogsResult['blogs'];
        $totalBlogs     = $blogsResult['total'];
        $blogCategories = $blogsModel->getAllBlogCategories();
        $recentBlogs    = $blogsModel->getRecentBlogs(4);
        $allTags        = $blogsModel->getAllTags();

        $title          = 'Blog - MTECHJSC';
        $content        = 'app/views/blogs/blogs.php';
        $showPageHeader = true;

        $showCTA        = false;
        $showBreadcrumb = true;
        break;
        
    // --------------------------------------
    // NOTE: Blog Details - Chi tiết blog
    // --------------------------------------
    case 'blog-details':
        $slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
        if (empty($slug)) {
            header('Location: ?page=blogs');
            exit;
        }

        require_once 'app/models/BlogsModel.php';
        $blogsModel = new BlogsModel();

        // Lấy chi tiết blog
        $blogDetail = $blogsModel->getBlogDetailsBySlug($slug);
        if (!$blogDetail) {
            $page    = '404';
            $title   = 'Không tìm thấy - MTECHJSC';
            $content = 'errors/404.php';
            $showPageHeader = false;
            $showCTA = false;
            $showBreadcrumb = false;
            $hideHeader = true;
            http_response_code(404);
            break;
        }

        // Tăng lượt xem
        $blogsModel->incrementViews($blogDetail['id']);

        // Lấy dữ liệu sidebar (giống trang blogs)
        $blogCategories = $blogsModel->getAllBlogCategories();
        $recentBlogs    = $blogsModel->getRecentBlogs(4);
        $allTags        = $blogsModel->getAllTags();
        
        // Lấy danh sách vị trí tuyển dụng đang mở (cho form ứng tuyển)
        $hiringPositions = $blogsModel->getAllHiringPositions();

        $title          = htmlspecialchars($blogDetail['title']) . ' - MTECHJSC';
        $content        = 'app/views/blogs/blog.details.php';
        $showPageHeader = true;

        $showCTA        = false;
        $showBreadcrumb = true;
        $breadcrumbs    = [
            ['title' => 'Blog',                                  'url' => '?page=blogs'],
            ['title' => htmlspecialchars($blogDetail['title']), 'url' => null],
        ];
        break;
        
    // --------------------------------------
    // NOTE: Search Page - Trang tìm kiếm toàn site
    // --------------------------------------
    case 'search':
        require_once 'app/models/SearchModel.php';
        $searchModel = new SearchModel();

        $searchQuery  = isset($_GET['q'])    ? trim(urldecode($_GET['q'])) : '';
        $searchType   = isset($_GET['type']) ? trim($_GET['type'])         : '';
        $currentPage  = isset($_GET['p'])    ? max(1, (int) $_GET['p'])   : 1;
        $perPage      = 10;

        // Chỉ chấp nhận type hợp lệ
        if (!in_array($searchType, ['blog', 'service', 'project', ''])) {
            $searchType = '';
        }

        if (!empty($searchQuery)) {
            $searchResult  = $searchModel->search($searchQuery, $currentPage, $perPage, $searchType);
            $searchResults = $searchResult['results'];
            $totalResults  = $searchResult['total'];
        } else {
            $searchResults = [];
            $totalResults  = 0;
        }

        $title          = 'Search Results - MTECHJSC';
        $content        = 'app/views/search/search.php';
        $showPageHeader = true;

        $showCTA        = false;
        $showBreadcrumb = true;
        break;

    // --------------------------------------
    // NOTE: Projects Page - Trang dự án
    // --------------------------------------
    case 'projects':
        $title = 'Dự án - MTECHJSC';
        $content = 'app/views/projects/projects.php';
        $showPageHeader = true;
      
        $showCTA = false;
        $showBreadcrumb = true;
        break;
        
    // --------------------------------------
    // NOTE: Project Details - Chi tiết dự án
    // --------------------------------------
    case 'project-details':
        $title = 'Chi tiết dự án - MTECHJSC';
        $content = 'app/views/projects/projects.details.php';
        $showPageHeader = true;
     
        $showCTA = false;
        $showBreadcrumb = true;
        // NOTE: Xử lý breadcrumb động theo ID dự án
        break;
        
    // --------------------------------------
    // NOTE: Services Page - Trang dịch vụ
    // --------------------------------------
    case 'services':
        $title = 'Dịch vụ - MTECHJSC';
        $content = 'app/views/services/services.php';
        $showPageHeader = true;
    
        $showCTA = false;
        $showBreadcrumb = true;
        break;
        
    // --------------------------------------
    // NOTE: Categories Page - Trang danh mục dịch vụ
    // --------------------------------------
    case 'categories':
        $title = 'Our Services - MTECHJSC';
        $content = 'app/views/categories/categories.php';
        $showPageHeader = true;
        $showCTA = false;
        $showBreadcrumb = true;
        break;

    // --------------------------------------
    // NOTE: Category Details - Chi tiết danh mục dịch vụ
    // --------------------------------------
    case 'categories-details':
        $slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
        if (empty($slug)) {
            header('Location: ?page=categories');
            exit;
        }
        require_once 'app/models/CategoriesModel.php';
        $categoriesModel  = new CategoriesModel();
        $categoryDetail   = $categoriesModel->getCategoryDetailBySlug($slug);
        $allCategories    = $categoriesModel->getAllCategories();
        if (!$categoryDetail) {
            $page    = '404';
            $title   = 'Không tìm thấy - MTECHJSC';
            $content = 'errors/404.php';
            $showPageHeader = false;
            $showCTA = false;
            $showBreadcrumb = false;
            $hideHeader = true;
            http_response_code(404);
            break;
        }
        $title          = htmlspecialchars($categoryDetail['name']) . ' - MTECHJSC';
        $content        = 'app/views/categories/categories_details.php';
        $showPageHeader = true;
    
        $showCTA        = false;
        $showBreadcrumb = true;
        $breadcrumbs    = [
            ['title' => 'Services',                              'url' => '?page=categories'],
            ['title' => htmlspecialchars($categoryDetail['name']), 'url' => null],
        ];
        break;
        
    // --------------------------------------
    // NOTE: News Page - Trang tin tức
    // --------------------------------------
    case 'news':
        $title = 'Tin tức - MTECHJSC';
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
        $title = 'Chi tiết tin tức - MTECHJSC';
        $content = 'app/views/news/details.php';
        $showPageHeader = false;
        $showCTA = false;
        $showBreadcrumb = true;
        // NOTE: Xử lý breadcrumb động theo ID tin tức
        break;
        
    // --------------------------------------
    // NOTE: Awards Page (Giải thưởng & Chứng chỉ)
    // --------------------------------------
    case 'awards':
        require_once 'app/models/AwardsModel.php';
        $awardsModel = new AwardsModel();
        $awards      = $awardsModel->getAllActive();

        $title          = 'Giải thưởng & Chứng chỉ - MTECH.JSC';
        $content        = 'app/views/about/awards.php';
        $showPageHeader = true;
      
        $showCTA        = false;
        $showBreadcrumb = true;
        break;

    // --------------------------------------
    // NOTE: Contact Page - Trang liên hệ
    // --------------------------------------
    case 'contact':
        $title = 'Liên hệ - MTECHJSC';
        $content = 'app/views/contact/contact.php';
        $showPageHeader = true;

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
        
        $title = 'Đăng nhập - MTECHJSC';
        $content = 'app/views/auth/login.php';
        break;
        
    case 'register':
        // NOTE: Tương tự login, xử lý register
        $title = 'Đăng ký - MTECHJSC';
        $content = 'app/views/auth/register.php';
        break;
        
    case 'forgot':
        // NOTE: Xử lý quên mật khẩu
        $title = 'Quên mật khẩu - MTECHJSC';
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
        
        $title = 'Tài khoản - MTECHJSC';
        $showPageHeader = false;
        $showCTA = false;
        $showBreadcrumb = true;
        
        switch($module) {
            case 'dashboard':
            default:
                $content = 'app/views/users/dashboard.php';
                $title = 'Tài khoản của tôi - MTECHJSC';
                break;
            case 'account':
                $content = 'app/views/users/account/index.php';
                $title = 'Thông tin tài khoản - MTECHJSC';
                break;
            case 'orders':
                $content = 'app/views/users/orders/index.php';
                $title = 'Đơn hàng - MTECHJSC';
                break;
            case 'cart':
                $content = 'app/views/users/cart/index.php';
                $title = 'Giỏ hàng - MTECHJSC';
                break;
            case 'wishlist':
                $content = 'app/views/users/wishlist/index.php';
                $title = 'Yêu thích - MTECHJSC';
                break;
        }
        break;
        
    // --------------------------------------
    // NOTE: Checkout/Payment - Thanh toán
    // --------------------------------------
    case 'checkout':
        $title = 'Thanh toán - MTECHJSC';
        $content = 'app/views/payment/checkout.php';
        $showPageHeader = false;
        $showCTA = false;
        $showBreadcrumb = true;
        // $breadcrumbs = generate_breadcrumb('checkout');
        break;
        
    case 'payment':
        $title = 'Thanh toán - MTECHJSC';
        $content = 'app/views/payment/payment.php';
        $showPageHeader = false;
        $showCTA = false;
        $showBreadcrumb = true;
        break;
        
    case 'payment_success':
        $title = 'Thanh toán thành công - MTECHJSC';
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
        
        $title = 'Admin Panel - MTECHJSC';
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
    // NOTE: Coming Soon Page - Trang bảo trì (có thể truy cập trực tiếp)
    // --------------------------------------
    case 'comingsoon':
        $title = 'Coming Soon - MTECHJSC';
        $content = 'app/views/comingsoon/comingsoon.php';
        $showPageHeader = false;
        $showCTA = false;
        $showBreadcrumb = false;
        // Trang coming soon tự có HTML đầy đủ nên không dùng master layout
        $useStandaloneLayout = true;
        break;
        
    // --------------------------------------
    // NOTE: 500 Page - Trang lỗi server
    // --------------------------------------
    case '500':
        $page = '500';
        $title = 'Lỗi máy chủ - MTECHJSC';
        $content = 'errors/500.php';
        $showPageHeader = false;
        $showCTA = false;
        $showBreadcrumb = false;
        $hideHeader = true;
        http_response_code(500);
        break;

    // --------------------------------------
    // NOTE: Job Application Submit - Xử lý nộp CV ứng tuyển
    // --------------------------------------
    case 'job-application-submit':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?page=blogs');
            exit;
        }

        require_once 'app/models/BlogsModel.php';
        require_once 'app/models/JobApplicationModel.php';
        
        $blogsModel = new BlogsModel();
        $jobAppModel = new JobApplicationModel();

        // Lấy dữ liệu từ form
        $blogId   = (int) ($_POST['blog_id'] ?? 0);
        $fullName = trim($_POST['full_name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $phone    = trim($_POST['phone'] ?? '');
        $position = trim($_POST['position'] ?? '');
        $message  = trim($_POST['message'] ?? '');
        $returnSlug = trim($_POST['return_slug'] ?? '');

        // Kiểm tra blog tồn tại và đang tuyển
        $blog = $blogsModel->getBlogById($blogId);
        if (!$blog || !$blogsModel->isHiringOpen($blog)) {
            $_SESSION['job_application_message'] = 'Vị trí này hiện không còn nhận hồ sơ.';
            $_SESSION['job_application_success'] = false;
            header('Location: ?page=blog-details&slug=' . urlencode($returnSlug));
            exit;
        }

        // Tạo đơn ứng tuyển
        $result = $jobAppModel->createApplication(
            $blogId, $fullName, $email, $phone, $position, 
            $_FILES['cv'] ?? [], $message
        );

        if ($result['success']) {
            // Gửi email thông báo (nếu có EmailNotificationService)
            if (file_exists('app/services/EmailNotificationService.php')) {
                require_once 'app/services/EmailNotificationService.php';
                if (class_exists('EmailNotificationService')) {
                    $emailService = new EmailNotificationService();
                    $emailService->sendJobApplicationNotification([
                        'application_id' => $result['id'],
                        'blog_id' => $blogId,
                        'position' => $position,
                        'full_name' => $fullName,
                        'email' => $email,
                        'phone' => $phone,
                        'cv_path' => $result['cv_path']
                    ]);
                    // Gửi email cảm ơn cho ứng viên
                    $emailService->sendThankYouEmail([
                        'full_name' => $fullName,
                        'email' => $email,
                        'position' => $position
                    ]);
                }
            }

            $_SESSION['job_application_message'] = 'CV ứng tuyển của bạn đã được gửi thành công! Chúng tôi sẽ liên hệ trong thời gian sớm nhất.';
            $_SESSION['job_application_success'] = true;
        } else {
            $_SESSION['job_application_message'] = $result['error'] ?? 'Có lỗi xảy ra. Vui lòng thử lại.';
            $_SESSION['job_application_success'] = false;
        }

        header('Location: ?page=blog-details&slug=' . urlencode($returnSlug));
        exit;

    // --------------------------------------
    // NOTE: 404 Page - Tường minh (từ .htaccess redirect hoặc link trực tiếp)
    // --------------------------------------
    case '404':
    // --------------------------------------
    // NOTE: Default - Mọi page không tồn tại → 404
    // --------------------------------------
    default:
        $page = '404';
        $title = 'Không tìm thấy trang - MTECHJSC';
        $content = 'errors/404.php';
        $showPageHeader = false;
        $showCTA = false;
        $showBreadcrumb = false;
        $hideHeader = true;
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
// Global assets được load trong master.php:
//   - assets/css/pusher.css  → Scroll to Top button styles
//   - assets/js/pusher.js    → Scroll to Top button logic
//   - app/views/_layout/pusher.php → Scroll to Top button HTML

if (isset($useStandaloneLayout) && $useStandaloneLayout) {
    // NOTE: Standalone layout - trang tự chứa HTML đầy đủ (coming soon)
    include_once $content;
} elseif (isset($useAdminLayout) && $useAdminLayout) {
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
