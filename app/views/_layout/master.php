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
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- NOTE: Title - Thay đổi tiêu đề mặc định tại đây -->
    <title><?php echo isset($title) ? $title : 'Tiêu đề mặc định - Tên Website'; ?></title>
    
    <!-- NOTE: Favicon - Thêm đường dẫn favicon tại đây -->
    <link rel="icon" type="image/x-icon" href="<?php echo isset($favicon) ? $favicon : '/favicon.ico'; ?>">
    
    <!-- NOTE: Google Fonts/External Fonts - Thêm fonts tại đây -->
    <!-- Ví dụ:
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    -->
    
    <?php
    // Determine current page for conditional CSS/JS loading
    $currentPage = isset($_GET['page']) ? $_GET['page'] : 'home';
    ?>
    
    <!-- ========================================== -->
    <!-- NOTE: Core CSS Files - Thêm CSS chung tại đây -->
    <!-- ========================================== -->
    <!-- Ví dụ:
    <link rel="stylesheet" href="<?php echo css_url('header.css'); ?>">
    <link rel="stylesheet" href="<?php echo css_url('footer.css'); ?>">
    <link rel="stylesheet" href="<?php echo css_url('flash_messages.css'); ?>">
    -->
    
    <!-- ========================================== -->
    <!-- NOTE: Page-specific CSS - Thêm CSS theo từng trang -->
    <!-- ========================================== -->
    <?php
    switch($currentPage) {
        case 'home':
            // echo '<link rel="stylesheet" href="' . css_url('home.css') . '">';
            break;
        case 'about':
            // echo '<link rel="stylesheet" href="' . css_url('about.css') . '">';
            break;
        case 'contact':
            // echo '<link rel="stylesheet" href="' . css_url('contact.css') . '">';
            break;
        // NOTE: Thêm các case khác tại đây
        default:
            break;
    }
    ?>
    
    <!-- ========================================== -->
    <!-- NOTE: External CSS Libraries - Thêm thư viện CSS CDN tại đây -->
    <!-- ========================================== -->
    <!-- Ví dụ: Font Awesome, Bootstrap, v.v. -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> -->
    
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
    <?php 
    // Ví dụ: include_once __DIR__ . '/header.php';
    ?>
    
    <!-- ========================================== -->
    <!-- NOTE: Navigation/Menu - Include navigation tại đây nếu cần -->
    <!-- ========================================== -->
    <?php if (isset($showNav) && $showNav): ?>
        <?php // include_once __DIR__ . '/navigation.php'; ?>
    <?php endif; ?>
    
    <!-- ========================================== -->
    <!-- NOTE: Breadcrumb - Hiển thị đường dẫn breadcrumb -->
    <!-- ========================================== -->
    <?php if (isset($showBreadcrumb) && $showBreadcrumb && isset($breadcrumbs)): ?>
        <?php 
        // Ví dụ sử dụng: render_breadcrumb($breadcrumbs);
        // Hoặc include file: include_once __DIR__ . '/breadcrumb.php';
        ?>
    <?php endif; ?>
    
    <!-- ========================================== -->
    <!-- NOTE: Page Header - Banner/hero section cho trang -->
    <!-- ========================================== -->
    <?php if (isset($showPageHeader) && $showPageHeader): ?>
        <?php // include_once __DIR__ . '/pageheader.php'; ?>
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
    <main class="main-content">
        <?php 
        /**
         * Nội dung trang được truyền từ Controller qua biến $content
         * - $content có thể là HTML string hoặc đường dẫn file
         */
        if (isset($content)) {
            // Kiểm tra nếu là HTML content hoặc file path
            if (is_string($content) && (strpos($content, '<') !== false || strpos($content, '<?php') !== false)) {
                // It's HTML content - echo directly
                echo $content;
            } elseif (is_string($content) && file_exists($content)) {
                // It's a file path - include the file
                include $content;
            } else {
                echo "<div class='error'>Không tìm thấy nội dung trang.</div>";
            }
        } elseif (isset($viewFile) && file_exists($viewFile)) {
            // Alternative: include view file directly
            include $viewFile;
        } else {
            echo "<div class='error'>Không có nội dung để hiển thị.</div>";
        }
        ?>
    </main>
    
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
    <?php 
    // Ví dụ: include_once __DIR__ . '/footer.php';
    ?>
    
    <!-- ========================================== -->
    <!-- NOTE: Scroll to Top / Pusher Button -->
    <!-- ========================================== -->
    <?php if (isset($showScrollTop) && $showScrollTop): ?>
        <?php // include_once __DIR__ . '/pusher.php'; ?>
    <?php endif; ?>
    
    <!-- ========================================== -->
    <!-- NOTE: Core JavaScript Files - JS chung -->
    <!-- ========================================== -->
    <!-- Ví dụ:
    <script src="<?php echo js_url('header.js'); ?>"></script>
    <script src="<?php echo js_url('footer.js'); ?>"></script>
    <script src="<?php echo js_url('flash_messages.js'); ?>"></script>
    -->
    
    <!-- ========================================== -->
    <!-- NOTE: Page-specific JavaScript - JS theo trang -->
    <!-- ========================================== -->
    <?php
    switch($currentPage) {
        case 'home':
            // echo '<script src="' . js_url('home.js') . '"></script>';
            break;
        case 'about':
            // echo '<script src="' . js_url('about.js') . '"></script>';
            break;
        case 'contact':
            // echo '<script src="' . js_url('contact.js') . '"></script>';
            break;
        // NOTE: Thêm các case khác tại đây
        default:
            break;
    }
    ?>
    
    <!-- ========================================== -->
    <!-- NOTE: External JavaScript Libraries - CDN -->
    <!-- ========================================== -->
    <!-- Ví dụ: jQuery, Bootstrap, Chart.js, v.v. -->
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    
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
