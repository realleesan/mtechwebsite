<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Admin MTech') ?></title>

    <!-- NOTE: Favicon
         File: assets/icons/favicon.ico
         Dùng BASE_URL động để đúng cả localhost/subfolder lẫn hosting root
    -->
    <?php
    // Tính base URL động: hoạt động đúng cả localhost/subfolder và hosting root
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script   = $_SERVER['SCRIPT_NAME'] ?? '/index.php';          // /admin.mtechwebsite/index.php
    $basePath = rtrim(dirname($script), '/\\');                    // /admin.mtechwebsite  (hoặc '' nếu root)
    $baseUrl  = $protocol . '://' . $host . $basePath;            // http://localhost/admin.mtechwebsite
    ?>
    <link rel="icon" href="<?php echo $baseUrl; ?>/assets/icons/favicon.ico?v=1.1">

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <?php
    // Determine current page for conditional CSS/JS loading
    $currentPage = isset($page) ? $page : (isset($_GET['page']) ? $_GET['page'] : 'dashboard');

    // Auth pages: login, forgot-password, reset-password
    $authPages = ['login', 'forgot-password', 'reset-password'];
    $isAuthPage = in_array($currentPage, $authPages);
    ?>

    <!-- Admin CSS (chỉ load cho trang admin, không phải auth) -->
    <?php if (!$isAuthPage): ?>
    <link rel="stylesheet" href="/assets/css/admin.css">
    <?php endif; ?>
    <!-- ========================================== -->
    <!-- NOTE: Page-specific CSS - Thêm CSS theo từng trang -->
    <!-- ========================================== -->
    <?php
    switch($currentPage) {
        case 'dashboard':
            echo '<link rel="stylesheet" href="/assets/css/dashboard.css">';
            break;
        case 'projects':
            echo '<link rel="stylesheet" href="/assets/css/admin.projects.css">';
            break;
        case 'project-create':
        case 'project-edit':
            echo '<link rel="stylesheet" href="/assets/css/admin.projects.css">';
            break;

        case 'blogs':
        case 'blog.create':
        case 'blog.edit':
        case 'blog.view':
        case 'blog-categories':
        case 'blog.category.create':
        case 'blog.category.edit':
            echo '<link rel="stylesheet" href="/assets/css/admin.blogs.css">';
            echo '<link rel="stylesheet" href="/assets/css/image-editor.css">';
            break;

        case 'awards':
        case 'award.create':
        case 'award.edit':
        case 'award.trash':
            echo '<link rel="stylesheet" href="/assets/css/admin.awards.css">';
            break;
        case 'client.logos':
        case 'client.logo.create':
        case 'client.logo.edit':
        case 'client.logo.trash':
            echo '<link rel="stylesheet" href="/assets/css/admin.client.logos.css">';
            break;

        case 'footer':
        case 'footer-add':
        case 'footer-edit':
        case 'footer-trash':
        case 'footer-social':
        case 'footer-social-edit':
        case 'footer-settings':
            echo '<link rel="stylesheet" href="/assets/css/admin.footer.css">';
            break;

        case 'teams':
        case 'team.create':
        case 'team.edit':
            echo '<link rel="stylesheet" href="/assets/css/admin.teams.css">';
            break;
        case 'categories':
        case 'category.create':
        case 'category.edit':
            echo '<link rel="stylesheet" href="/assets/css/admin.blogs.css">';
            echo '<link rel="stylesheet" href="/assets/css/admin.categories.css">';
            break;
        case 'contacts':
        case 'contact.view':
        case 'contact.edit':
        case 'contact.trash':
            echo '<link rel="stylesheet" href="/assets/css/admin.contacts.css?v=1.1">';
            break;
        case 'job-applications':
        case 'job-application.view':
        case 'job-application.edit':
        case 'job-application.trash':
            echo '<link rel="stylesheet" href="' . $baseUrl . '/assets/css/admin.job-applications.css?v=1.1">';
            break;
        case 'login':
        case 'forgot-password':
        case 'reset-password':
            echo '<link rel="stylesheet" href="/assets/css/auth.css">';
            break;
        // Add more cases as needed
        default:
            break;
    }
    ?>
</head>

<body class="<?= $isAuthPage ? 'auth-page' : '' ?>">

<?php if (!$isAuthPage): ?>
<?php
// Track admin visits (chỉ cho trang admin, không phải auth)
require_once dirname(__DIR__, 2) . '/middleware/AccessMiddleware.php';
AccessMiddleware::trackVisit();
?>
<?php endif; ?>

<?php if ($isAuthPage): ?>

    <!-- ========== AUTH LAYOUT (không có sidebar/topbar) ========== -->
    <div class="auth-bg"></div>
    <div class="auth-overlay">
        <?php
        if (isset($content) && file_exists($content)) {
            include $content;
        }
        ?>
    </div>

<?php else: ?>

<div class="admin-wrapper d-flex">

    <!-- ========== SIDEBAR ========== -->
    <?php include __DIR__ . '/sidebar.php'; ?>

    <!-- ========== MAIN CONTENT ========== -->
    <div class="admin-main flex-grow-1 d-flex flex-column">

        <!-- Topbar -->
        <?php include __DIR__ . '/topbar.php'; ?>

        <!-- Page Content -->
        <main class="admin-content flex-grow-1 p-4">

            <!-- Flash Messages -->
            <?php if ($success = ($GLOBALS['_SESSION']['success'] ?? null)): unset($_SESSION['success']); ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($success) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($error = ($_SESSION['error'] ?? null)): unset($_SESSION['error']); ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- View Content -->
            <?php
            /**
             * Nội dung trang được truyền từ Controller qua biến $content
             * - $content có thể là HTML string hoặc đường dẫn file
             */
            if (isset($content)) {
                if (file_exists($content)) {
                    include $content;
                } else {
                    echo "<div class='alert alert-warning'>Không tìm thấy nội dung trang.</div>";
                }
            } else {
                echo "<div class='alert alert-info'>Chưa có nội dung được tải.</div>";
            }
            ?>

        </main>

        <!-- Footer -->
        <footer class="admin-footer text-center text-muted py-3 border-top">
            <small>© <?= date('Y') ?> MTECH.JSC Admin Panel</small>
        </footer>

    </div><!-- /.admin-main -->

</div><!-- /.admin-wrapper -->

<?php endif; ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php if (!$isAuthPage): ?>
<!-- Admin JS (chỉ load cho trang admin) -->
<script src="/assets/js/admin.js"></script>
<?php endif; ?>

<!-- ========================================== -->
<!-- NOTE: Page-specific JavaScript - JS theo trang -->
<!-- ========================================== -->
<?php
switch($currentPage) {
    case 'dashboard':
        echo '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';
        echo '<script src="/assets/js/dashboard.js"></script>';
        break;
    case 'projects':
        echo '<script src="/assets/js/admin.projects.js"></script>';
        break;
    case 'project-create':
    case 'project-edit':
        echo '<script src="/assets/js/admin.projects.js"></script>';
        break;

    case 'blogs':
    case 'blog.create':
    case 'blog.edit':
    case 'blog.view':
    case 'blog-categories':
    case 'blog.category.create':
    case 'blog.category.edit':
        echo '<script src="/assets/js/admin.blogs.js"></script>';
        echo '<script src="/assets/js/image-editor.js"></script>';
        break;

    case 'awards':
    case 'award.create':
    case 'award.edit':
    case 'award.trash':
        echo '<script src="/assets/js/admin.awards.js"></script>';
        break;
    case 'client.logos':
    case 'client.logo.create':
    case 'client.logo.edit':
    case 'client.logo.trash':
        echo '<script src="/assets/js/admin.client.logos.js"></script>';
        break;

    case 'footer':
    case 'footer-add':
    case 'footer-edit':
    case 'footer-trash':
    case 'footer-social':
    case 'footer-social-edit':
    case 'footer-settings':
        echo '<script src="/assets/js/admin.footer.js"></script>';
        break;

    case 'teams':
    case 'team.create':
    case 'team.edit':
        echo '<script src="/assets/js/admin.teams.js"></script>';
        break;
    case 'categories':
    case 'category.create':
    case 'category.edit':
        echo '<script src="/assets/js/admin.blogs.js"></script>';
        echo '<script src="/assets/js/admin.categories.js"></script>';
        break;
    case 'contacts':
    case 'contact.view':
    case 'contact.edit':
    case 'contact.trash':
        echo '<script src="/assets/js/admin.contacts.js"></script>';
        break;
    case 'job-applications':
    case 'job-application.view':
    case 'job-application.edit':
    case 'job-application.trash':
        echo '<script src="/assets/js/admin.job-applications.js"></script>';
        break;
    // Auth pages không cần JS riêng
    case 'login':
    case 'forgot-password':
    case 'reset-password':
        break;
    // Add more cases as needed
    default:
        break;
}
?>

</body>
</html>
