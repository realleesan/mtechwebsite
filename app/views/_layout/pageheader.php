<?php
/**
 * Page Header - Banner với tiêu đề trang và breadcrumb
 * Theo cấu trúc Wokrate template
 */

// Tự động lấy tiêu đề nếu không được truyền vào
if (!isset($pageTitle)) {
    $pageTitles = [
        'about'           => 'Giới thiệu',
        'awards'          => 'Giải thưởng & Chứng chỉ',
        'services'        => 'Dịch vụ',
        'projects'        => 'Dự án',
        'project-details' => 'Chi tiết dự án',
        'blogs'           => 'Tin tức',
        'blog-details'    => 'Chi tiết tin tức',
        'contact'         => 'Liên hệ',
        'company.history' => 'Lịch sử hình thành & phát triển',
        'teams'           => 'Đội ngũ',
    ];
    $currentPage = $_GET['page'] ?? 'home';
    
    // Nếu là trang blogs với cat=7 (tuyển dụng)
    if ($currentPage === 'blogs' && isset($_GET['cat']) && $_GET['cat'] == '7') {
        $pageTitle = 'Tuyển dụng';
    }
    // Nếu là trang blogs với danh mục (cat_slug)
    elseif ($currentPage === 'blogs' && isset($_GET['cat_slug']) && !empty($categoryName)) {
        $pageTitle = htmlspecialchars($categoryName);
    } else {
        $pageTitle = $pageTitles[$currentPage] ?? ucfirst($currentPage);
    }
}

// Tính base URL động
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
$script   = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
$basePath = rtrim(dirname($script), '/\\');
$baseUrl  = $protocol . '://' . $host . $basePath;
$bannerImage = $baseUrl . '/assets/images/header_banner.png';
?>

<section class="banner_area" style="background-image: 
    -webkit-gradient(linear, left top, left bottom, from(rgba(0, 0, 0, 0.5))),
    url('<?php echo htmlspecialchars($bannerImage); ?>');
    background-image:
    -webkit-linear-gradient(rgba(0, 0, 0, 0.5)),
    url('<?php echo htmlspecialchars($bannerImage); ?>');
    background-image:
    -o-linear-gradient(rgba(0, 0, 0, 0.5)),
    url('<?php echo htmlspecialchars($bannerImage); ?>');
    background-image:
    linear-gradient(rgba(0, 0, 0, 0.5)),
    url('<?php echo htmlspecialchars($bannerImage); ?>');">
    <div class="container">
        <div class="banner_content text-center">

            <h2 class="page_title"><?php echo htmlspecialchars($pageTitle); ?></h2>

            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="./">Trang chủ</a></li>

                <?php if (isset($breadcrumbs) && is_array($breadcrumbs)): ?>
                    <?php foreach ($breadcrumbs as $crumb): ?>
                        <?php if (!empty($crumb['url'])): ?>
                            <li class="breadcrumb-item">
                                <a href="<?php echo htmlspecialchars($crumb['url']); ?>">
                                    <?php echo htmlspecialchars($crumb['title']); ?>
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="breadcrumb-item active" aria-current="page">
                                <?php echo htmlspecialchars($crumb['title']); ?>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="breadcrumb-item active" aria-current="page">
                        <?php echo htmlspecialchars($pageTitle); ?>
                    </li>
                <?php endif; ?>
            </ol>

        </div>
    </div>
</section>
