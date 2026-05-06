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
// NOTE: Helpers - Load helper functions
// ==========================================
require_once __DIR__ . '/core/helpers.php';

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
// NOTE: Router - Thay thế switch-case bằng Router class
// ==========================================
require_once __DIR__ . '/core/router.php';
$router = new Router();
$router->dispatch();

// ==========================================
// NOTE: Router đã xử lý render view, không cần layout ở đây
// ==========================================
// Router::dispatch() đã tự gọi controller->view() và render layout
// Không cần include layout nữa để tránh duplicate

// ==========================================
// NOTE: Flush Output Buffer - Xuất output buffer
// ==========================================
if (ob_get_level() > 0) {
    ob_end_flush();
}
