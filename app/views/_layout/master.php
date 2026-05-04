<?php
/**
 * Master Layout Template
 * 
 * NOTE: Đây là file layout chính của hệ thống MVC
 * - Các biến được truyền từ Controller sẽ sử dụng ở đây
 * - Thêm CSS/JS cụ thể vào các khu vực được đánh dấu NOTE
 */

// Set charset headers for proper UTF-8 encoding
header('Content-Type: text/html; charset=UTF-8');
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- NOTE: Title - Thay đổi tiêu đề mặc định tại đây -->
    <title><?php echo isset($title) ? $title : 'Tiêu đề mặc định - Tên Website'; ?></title>
    
    <!-- NOTE: Favicon
         File: assets/icons/favicon.ico
         Dùng BASE_URL động để đúng cả localhost/subfolder lẫn hosting root
    -->
    <?php
    // Tính base URL động: hoạt động đúng cả localhost/subfolder và hosting root
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script   = $_SERVER['SCRIPT_NAME'] ?? '/index.php';          // /mtechwebsite/index.php
    $basePath = rtrim(dirname($script), '/\\');                    // /mtechwebsite  (hoặc '' nếu root)
    $baseUrl  = $protocol . '://' . $host . $basePath;            // http://localhost/mtechwebsite
    ?>
    <link rel="icon" href="<?php echo $baseUrl; ?>/assets/icons/favicon.ico?v=1.1">
    
    <!-- NOTE: Google Fonts/External Fonts - Thêm fonts tại đây -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <!-- Ví dụ:
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    -->
    
    <?php
    // Determine current page for conditional CSS/JS loading
    // Ưu tiên dùng $page từ index.php (đã xử lý 404/500 override)
    // thay vì $_GET['page'] để CSS/JS load đúng cho trang lỗi
    $currentPage = isset($page) ? $page : (isset($_GET['page']) ? $_GET['page'] : 'home');
    ?>
    
    <!-- Elfsight Platform Script -->
    <script src="https://static.elfsight.com/platform/platform.js" async></script>
    <!-- Ẩn Information Panel (chỉ hiện khi đăng nhập Elfsight) khỏi làm vỡ layout -->
    <style>.eapps-widget-toolbar { display: none !important; }</style>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <!-- ========================================== -->
    <!-- NOTE: External CSS Libraries - Thêm thư viện CSS CDN tại đây -->
    <!-- ========================================== -->
    <!-- Bootstrap Grid -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <!-- ========================================== -->
    <!-- NOTE: Core CSS Files - Thêm CSS chung tại đây -->
    <!-- ========================================== -->
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/pageheader.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/pusher.css">
    <link rel="stylesheet" href="assets/css/client.logos.css">
    
    <!-- ========================================== -->
    <!-- NOTE: Page-specific CSS - Thêm CSS theo từng trang -->
    <!-- ========================================== -->
    <?php
    switch($currentPage) {
        case 'home':
            echo '<link rel="stylesheet" href="assets/css/home.css">';
            echo '<link rel="stylesheet" href="assets/css/awards.css">';
            break;
        case 'about':
            echo '<link rel="stylesheet" href="assets/css/about.css">';
            break;
        case 'contact':
            echo '<link rel="stylesheet" href="assets/css/contact.css">';
            break;
        case 'company.history':
            echo '<link rel="stylesheet" href="assets/css/company.history.css">';
            break;
        case 'teams':
            echo '<link rel="stylesheet" href="assets/css/teams.css">';
            break;
        case 'projects':
            echo '<link rel="stylesheet" href="assets/css/projects.css">';
            break;
        case 'project-details':
            echo '<link rel="stylesheet" href="assets/css/projects.details.css">';
            break;
        case 'categories':
            echo '<link rel="stylesheet" href="assets/css/categories.css">';
            break;
        case 'categories-details':
            echo '<link rel="stylesheet" href="assets/css/categories.details.css">';
            break;
        case 'comingsoon':
            echo '<link rel="stylesheet" href="assets/css/comingsoon.css">';
            break;
        case 'blogs':
        case 'search':
            echo '<link rel="stylesheet" href="assets/css/blogs.css">';
            break;
        case 'blog-details':
            echo '<link rel="stylesheet" href="assets/css/blog.details.css">';
            break;
        case 'awards':
            echo '<link rel="stylesheet" href="assets/css/awards.css">';
            break;
        case '404':
            echo '<link rel="stylesheet" href="assets/css/errors.css">';
            break;
        case '500':
            echo '<link rel="stylesheet" href="assets/css/errors.css">';
            break;
        // NOTE: Thêm các case khác tại đây
        default:
            break;
    }
    ?>
    
    <!-- ========================================== -->
    <!-- NOTE: Additional CSS - CSS động từ Controller -->
    <!-- ========================================== -->
    <?php if (isset($additionalCSS) && is_array($additionalCSS)): ?>
        <?php foreach ($additionalCSS as $css): ?>
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>

<!-- NOTE: Class theo trang - có thể thêm class CSS tùy chỉnh -->
<body class="<?php echo isset($bodyClass) ? $bodyClass : $currentPage . '-page'; ?>">
    
    <!-- ========================================== -->
    <!-- NOTE: Header - Include file header tại đây -->
    <!-- ========================================== -->
    <?php if (!isset($hideHeader) || !$hideHeader): ?>
        <?php include_once __DIR__ . '/header.php'; ?>
    <?php endif; ?>
    
    <!-- ========================================== -->
    <!-- NOTE: Navigation/Menu - Include navigation tại đây nếu cần -->
    <!-- ========================================== -->
    <?php if (isset($showNav) && $showNav): ?>
        <?php // include_once __DIR__ . '/navigation.php'; ?>
    <?php endif; ?>
    
    <!-- ========================================== -->
    <!-- NOTE: Breadcrumb - được render bên trong pageheader.php -->
    <!-- ========================================== -->
    
    <!-- ========================================== -->
    <!-- NOTE: Page Header (if needed) -->
    <!-- ========================================== -->
    <?php if (isset($showPageHeader) && $showPageHeader): ?>
        <?php include_once __DIR__ . '/pageheader.php'; ?>
    <?php endif; ?>
    
    <!-- ========================================== -->
    <!-- NOTE: Flash Messages - Thông báo từ Session -->
    <!-- ========================================== -->
    <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="flash-message flash-success">
            <i class="fas fa-check-circle"></i>
            <?php echo htmlspecialchars($_SESSION['flash_success']); ?>
            <button class="flash-close" onclick="this.parentElement.style.display='none'">&times;</button>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="flash-message flash-error">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo htmlspecialchars($_SESSION['flash_error']); ?>
            <button class="flash-close" onclick="this.parentElement.style.display='none'">&times;</button>
        </div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['flash_warning'])): ?>
        <div class="flash-message flash-warning">
            <i class="fas fa-exclamation-triangle"></i>
            <?php echo htmlspecialchars($_SESSION['flash_warning']); ?>
            <button class="flash-close" onclick="this.parentElement.style.display='none'">&times;</button>
        </div>
        <?php unset($_SESSION['flash_warning']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['flash_info'])): ?>
        <div class="flash-message flash-info">
            <i class="fas fa-info-circle"></i>
            <?php echo htmlspecialchars($_SESSION['flash_info']); ?>
            <button class="flash-close" onclick="this.parentElement.style.display='none'">&times;</button>
        </div>
        <?php unset($_SESSION['flash_info']); ?>
    <?php endif; ?>
    
    <!-- ========================================== -->
    <!-- NOTE: Main Content - Nội dung chính từ Controller -->
    <!-- ========================================== -->
    <?php
    // Trang 404/500 cần full viewport, không dùng main wrapper
    $isErrorPage     = (isset($page) && in_array($page, ['404', '500']));
    // Blog sidebar layout: blogs và blog-details dùng 2-column layout (col-9 + col-3)
    $showBlogSidebar = isset($showBlogSidebar) && $showBlogSidebar;
    $blogSectionClass = ($currentPage === 'blog-details') ? 'blog_details_area sec_gap' : 'blog_area';
    ?>
    
    <?php if (!$isErrorPage): ?>
    <main class="main-content">
    <?php endif; ?>

        <?php if ($showBlogSidebar): ?>
        <!-- Blog 2-column layout: content (col-9) + sidebar (col-3) -->
        <section class="<?php echo $blogSectionClass; ?>">
            <div class="container">
                <div class="row">
                    <div class="col-lg-9">
        <?php endif; ?>

        <?php
        /**
         * Nội dung trang được truyền từ Controller qua biến $content
         * - $content có thể là HTML string hoặc đường dẫn file
         */
        if (isset($content)) {
            if (is_string($content) && (strpos($content, '<') !== false || strpos($content, '<?php') !== false)) {
                echo $content;
            } elseif (is_string($content) && file_exists($content)) {
                include $content;
            } else {
                echo "<div class='error'>Không tìm thấy nội dung trang.</div>";
            }
        } elseif (isset($viewFile) && file_exists($viewFile)) {
            include $viewFile;
        } else {
            echo "<div class='error'>Không có nội dung để hiển thị.</div>";
        }
        ?>

        <?php if ($showBlogSidebar): ?>
                    </div><!-- /.col-lg-9 -->
                    <div class="col-lg-3">
                        <?php include __DIR__ . '/blog_sidebar.php'; ?>
                    </div><!-- /.col-lg-3 -->
                </div><!-- /.row -->
            </div><!-- /.container -->
        </section>
        <?php endif; ?>
    
    <?php if (!$isErrorPage): ?>
    </main>
    <?php endif; ?>
    
    <!-- ========================================== -->
    <!-- NOTE: Sidebar - Sidebar nếu layout có sidebar -->
    <!-- ========================================== -->
    <?php if (isset($showSidebar) && $showSidebar): ?>
        <aside class="sidebar">
            <?php // include_once __DIR__ . '/sidebar.php'; ?>
        </aside>
    <?php endif; ?>
    
    <!-- ========================================== -->
    <!-- NOTE: CTA Section - Call to Action nếu cần -->
    <!-- ========================================== -->
    <?php if (isset($showCTA) && $showCTA): ?>
        <?php // include_once __DIR__ . '/cta.php'; ?>
    <?php endif; ?>
    
    <!-- ========================================== -->
    <!-- NOTE: Footer - Include file footer tại đây -->
    <!-- ========================================== -->
    <?php include_once __DIR__ . '/footer.php'; ?>
    
    <!-- ========================================== -->
    <!-- NOTE: Scroll to Top / Pusher Button -->
    <!-- ========================================== -->
    <?php include_once __DIR__ . '/pusher.php'; ?>
    
    <!-- ========================================== -->
    <!-- NOTE: Core JavaScript Files - JS chung -->
    <!-- ========================================== -->
    <?php if (!isset($hideHeader) || !$hideHeader): ?>
        <script src="assets/js/header.js"></script>
    <?php endif; ?>
    <script src="assets/js/footer.js"></script>
    <script src="assets/js/pusher.js"></script>
    <script src="assets/js/client.logos.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Ví dụ các file JS khác:
    -->
    
    <!-- ========================================== -->
    <!-- NOTE: Page-specific JavaScript - JS theo trang -->
    <!-- ========================================== -->
    <?php
    switch($currentPage) {
        case 'home':
            echo '<script src="assets/js/home.js"></script>';
            echo '<script src="assets/js/awards.js"></script>';
            echo '<script src="assets/js/projects.carousel.js"></script>';
            break;
        case 'about':
            echo '<script src="assets/js/about.js"></script>';
            break;
        case 'contact':
            echo '<script src="assets/js/contact.js"></script>';
            break;
        case 'company.history':
            echo '<script src="assets/js/company.history.js"></script>';
            break;
        case 'teams':
            echo '<script src="assets/js/teams.js"></script>';
            break;
        case 'projects':
            echo '<script src="assets/js/projects.js"></script>';
            break;
        case 'project-details':
            echo '<script src="assets/js/projects.details.js"></script>';
            break;
        case 'categories':
            echo '<script src="assets/js/categories.js"></script>';
            break;
        case 'categories-details':
            echo '<script src="assets/js/categories.details.js"></script>';
            break;
        case 'comingsoon':
            echo '<script src="assets/js/comingsoon.js"></script>';
            break;
        case 'blogs':
            echo '<script src="assets/js/blogs.js"></script>';
            break;
        case 'blog-details':
            echo '<script src="assets/js/blog.details.js"></script>';
            break;
        case 'awards':
            echo '<script src="assets/js/awards.js"></script>';
            break;
        case '404':
            echo '<script src="assets/js/errors.js"></script>';
            break;
        case '500':
            echo '<script src="assets/js/errors.js"></script>';
            break;
        // NOTE: Thêm các case khác tại đây
        default:
            break;
    }
    ?>
    
    <!-- ========================================== -->
    <!-- NOTE: External JavaScript Libraries - CDN -->
    <!-- ========================================== -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
    
    <!-- ========================================== -->
    <!-- NOTE: Additional JS - JavaScript động từ Controller -->
    <!-- ========================================== -->
    <?php if (isset($additionalJS) && is_array($additionalJS)): ?>
        <?php foreach ($additionalJS as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- ========================================== -->
    <!-- NOTE: Inline JavaScript - JS inline từ Controller -->
    <!-- ========================================== -->
    <?php if (isset($inlineJS)): ?>
        <script>
            <?php echo $inlineJS; ?>
        </script>
    <?php endif; ?>
</body>
</html>
