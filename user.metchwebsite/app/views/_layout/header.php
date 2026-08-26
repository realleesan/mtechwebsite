<?php

/**
 * Header Layout - Mega Menu Navigation
 * Dự án: MTech Website
 * 
 * Hỗ trợ Mega Menu đa cấp sử dụng dữ liệu phân cấp từ FilterConfigService.
 * Fallback về dữ liệu gốc nếu chưa có cấu hình filter_config.
 */

// Lấy trang hiện tại để xác định active menu
$currentPage = $page ?? $_GET['page'] ?? 'home';

// Lấy header settings động
require_once __DIR__ . '/../../models/HeaderModel.php';
$headerModel    = new HeaderModel();
$headerSettings = $headerModel->getSettingsWithFallback();

// Lấy projects hiển thị trong menu dropdown
require_once __DIR__ . '/../../models/ProjectsModel.php';
$projectsModel = new ProjectsModel();
$menuProjects  = $projectsModel->getMenuProjects(10);

// Lấy services cho menu
require_once __DIR__ . '/../../models/CategoriesModel.php';
$categoriesModel = new CategoriesModel();
$allServices     = $categoriesModel->getAllCategories();

// Lấy blog categories cho menu
require_once __DIR__ . '/../../models/BlogsModel.php';
$blogsModel         = new BlogsModel();
$menuBlogCategories = $blogsModel->getAllBlogCategoriesFlat(50);
$menuBlogCategoriesHierarchy = $blogsModel->getMenuBlogCategories(50);

// --- Mega Menu: Dựng cây phân cấp ---
$servicesTree = [];
$blogCategoriesTree = [];
try {
    require_once __DIR__ . '/../../services/FilterConfigService.php';
    $filterService = new FilterConfigService();

    $servicesConfig = $filterService->getConfig('services');
    if (!empty($servicesConfig)) {
        $servicesTree = $filterService->getFilteredMenuTree('services', $allServices);
    } else {
        $servicesTree = $categoriesModel->buildTree($allServices);
    }

    $blogCategoriesConfig = $filterService->getConfig('blog_categories');
    if (!empty($blogCategoriesConfig)) {
        $filteredBlogTree = $filterService->getFilteredMenuTree('blog_categories', $menuBlogCategories);
        $blogCategoriesTree = !empty($filteredBlogTree) ? $filteredBlogTree : $menuBlogCategoriesHierarchy;
    } else {
        $blogCategoriesTree = $menuBlogCategoriesHierarchy;
    }
} catch (Exception $e) {
    error_log('Header FilterConfigService error: ' . $e->getMessage());
    $servicesTree = $categoriesModel->buildTree($allServices);
    $blogCategoriesTree = $menuBlogCategoriesHierarchy;
}

/**
 * Hàm render đệ quy submenu đa cấp cho Services Dropdown - Đồng bộ với Blog Categories
 * Support cấp n (không giới hạn) - Tương tự như Blog Categories
 *
 * @param array $items Mảng cây con
 * @param int $depth Cấp hiện tại (0 = dropdown cấp 1, 1+ = nested levels)
 * @param string $urlPrefix Tiền tố URL
 */
function renderDropdownMenuItems(array $items, int $depth = 0, string $urlPrefix = '/linh-vuc-'): string
{
    if (empty($items)) return '';

    $html = '';
    foreach ($items as $item) {
        $name     = htmlspecialchars($item['name']);
        $slug     = urlencode($item['slug']);
        $hasChild = !empty($item['children']);
        $projects = $item['projects'] ?? [];

        if ($hasChild) {
            // Category có con - thêm class submenu + caret-drop cho TẤT CẢ cấp
            // Đồng bộ với Blog Categories
            $html .= '<li class="nav-item submenu" data-depth="' . $depth . '">';
            $html .= '<a class="nav-link" href="' . $urlPrefix . $slug . '" title="' . $name . '">';
            $html .= mb_strtoupper($name, 'UTF-8');
            $html .= '<span class="caret-drop"></span>';
            $html .= '</a>';
            $html .= '<ul class="dropdown-menu" role="menu">';

            // Render các category con recursively - không giới hạn cấp
            $html .= renderDropdownMenuItems($item['children'], $depth + 1, $urlPrefix);

            // Thêm dự án vào cuối submenu (nếu có)
            if (!empty($projects)) {
                foreach ($projects as $project) {
                    $projectTitle = htmlspecialchars($project['title']);
                    $projectSlug = urlencode($project['slug']);
                    $html .= '<li class="nav-item project-item">';
                    $html .= '<a href="/chi-tiet-du-an-' . $projectSlug . '" title="' . $projectTitle . '" class="project-link">';
                    $html .= mb_strtoupper($projectTitle, 'UTF-8');
                    $html .= '</a>';
                    $html .= '</li>';
                }
            }

            $html .= '</ul>';
            $html .= '</li>';
        } elseif (!empty($projects)) {
            // Mục lá có dự án - render như submenu item
            $html .= '<li class="nav-item submenu">';
            $html .= '<a class="nav-link" href="' . $urlPrefix . $slug . '" title="' . $name . '">';
            $html .= mb_strtoupper($name, 'UTF-8');
            $html .= '<span class="caret-drop"></span>';
            $html .= '</a>';
            $html .= '<ul class="dropdown-menu" role="menu">';
            foreach ($projects as $project) {
                $projectTitle = htmlspecialchars($project['title']);
                $projectSlug = urlencode($project['slug']);
                $html .= '<li class="nav-item project-item">';
                $html .= '<a href="/chi-tiet-du-an-' . $projectSlug . '" title="' . $projectTitle . '" class="project-link">';
                $html .= mb_strtoupper($projectTitle, 'UTF-8');
                $html .= '</a>';
                $html .= '</li>';
            }
            $html .= '</ul>';
            $html .= '</li>';
        } else {
            // Mục lá (không có con, không có dự án) - link trực tiếp
            $html .= '<li class="nav-item">';
            $html .= '<a class="nav-link" href="' . $urlPrefix . $slug . '" title="' . $name . '">';
            $html .= mb_strtoupper($name, 'UTF-8');
            $html .= '</a>';
            $html .= '</li>';
        }
    }
    return $html;
}

?>

<header class="main_menu_area">

    <!-- Main Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light menu_absolute">

        <!-- Logo + MTECH - Moved from Topbar -->
        <a class="navbar_logo" href="./">
            <img src="/assets/images/logo_mtech.png" alt="<?php echo htmlspecialchars($headerSettings['logo_alt'] ?? 'MTechJSC'); ?>" class="navbar_logo_img">
            <span class="navbar_logo_text">MTECH</span>
        </a>

        <!-- Hamburger Menu Button for Mobile -->
        <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="menu_toggle">
                <span class="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
                <span class="hamburger-cross">
                    <span></span>
                    <span></span>
                </span>
            </span>
        </button>

        <!-- Navigation Menu -->
        <div class="collapse navbar-collapse" id="navbarSupportedContent">

            <!-- Close Button (Mobile only) -->
            <button class="nav-close-btn" aria-label="Close menu">
                <span class="nav-close-arrow">&#8592;</span>
                <span class="nav-close-text">Back</span>
            </button>

            <ul class="navbar-nav menu">

                <!-- Home -->
                <li class="nav-item <?php echo ($currentPage === 'home') ? 'active' : ''; ?>">
                    <a class="nav-link" href="./" title="Home">TRANG CHỦ</a>
                </li>

                <!-- About -->
                <?php
                $aboutPages = ['about', 'company.history', 'teams', 'awards', 'clients'];
                $isAboutActive = in_array($currentPage, $aboutPages);
                ?>
                <li class="nav-item submenu <?php echo $isAboutActive ? 'active' : ''; ?>">
                    <a class="nav-link" href="javascript:void(0);" title="Thư ngỏ">
                        GIỚI THIỆU
                        <span class="caret-drop"></span>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li>
                            <a class="nav-link" href="/ve-chung-toi" title="Thư ngỏ">VỀ CHÚNG TÔI</a>
                        </li>
                        <li>
                            <a class="nav-link" href="/lich-su-hinh-thanh-phat-trien" title="Lịch sử hình thành & phát triển">LỊCH SỬ HÌNH THÀNH & PHÁT TRIỂN</a>
                        </li>
                        <li>
                            <a class="nav-link" href="/co-cau-to-chuc" title="Cơ cấu tổ chức">CƠ CẤU TỔ CHỨC</a>
                        </li>
                        <li>
                            <a class="nav-link" href="/chung-chi-nang-luc" title="Chứng chỉ năng lực">CHỨNG CHỈ NĂNG LỰC</a>
                        </li>
                        <li>
                            <a class="nav-link" href="/danh-sach-khach-hang" title="Danh sách khách hàng">DANH SÁCH KHÁCH HÀNG</a>
                        </li>
                    </ul>
                </li>

                <!-- Services (Dropdown Menu chỉ hiển thị lĩnh vực cấp 1) -->
                <?php
                $level1Services = array_filter(is_array($allServices ?? null) ? $allServices : [], function ($cat) {
                    return empty($cat['parent_id']) || (int)$cat['parent_id'] === 0;
                });
                ?>
                <li class="nav-item submenu services-dropdown <?php echo ($currentPage === 'categories' || $currentPage === 'categories-details') ? 'active' : ''; ?>">
                    <a class="nav-link" href="javascript:void(0);" title="Lĩnh vực hoạt động">
                        LĨNH VỰC HOẠT ĐỘNG
                        <span class="caret-drop"></span>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li class="nav-item all-categories-item">
                            <a class="nav-link" href="/linh-vuc" title="Tất cả lĩnh vực">TẤT CẢ LĨNH VỰC</a>
                        </li>
                        <?php foreach ($level1Services as $service): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/linh-vuc-<?php echo urlencode($service['slug']); ?>" title="<?php echo htmlspecialchars($service['name']); ?>">
                                    <?php echo mb_strtoupper(htmlspecialchars($service['name']), 'UTF-8'); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>

                <!-- Projects (Direct Link) -->
                <li class="nav-item <?php echo ($currentPage === 'projects' || $currentPage === 'project-details') ? 'active' : ''; ?>">
                    <a class="nav-link" href="/du-an" title="Dự án">DỰ ÁN</a>
                </li>

                <!-- Blog with Hierarchical Categories -->
                <?php
                $isBlogActive = ($currentPage === 'blogs' && !(isset($_GET['cat']) && $_GET['cat'] == '7')) ||
                    ($currentPage === 'blog-details');

                // Function to render blog category hierarchy recursively (supports n-levels)
                function renderBlogCategoryMenu($categories, $depth = 0)
                {
                    if (empty($categories)) return '';

                    $html = '';
                    foreach ($categories as $category) {
                        $hasChildren = !empty($category['children']);
                        $categoryUrl = '/tin-tuc-' . urlencode($category['slug']);

                        if ($hasChildren) {
                            // Category có con - thêm class submenu + caret-drop cho TẤT CẢ cấp
                            // data-depth cho debugging/styling nếu cần
                            $html .= '<li class="nav-item submenu" data-depth="' . $depth . '">';
                            $html .= '<a class="nav-link" href="' . $categoryUrl . '" title="' . htmlspecialchars($category['name']) . '">';
                            $html .= mb_strtoupper(htmlspecialchars($category['name']), 'UTF-8');
                            $html .= '<span class="caret-drop"></span>';
                            $html .= '</a>';
                            $html .= '<ul class="dropdown-menu" role="menu">';

                            // Render các category con recursively - không giới hạn cấp
                            $html .= renderBlogCategoryMenu($category['children'], $depth + 1);

                            $html .= '</ul>';
                            $html .= '</li>';
                        } else {
                            // Category lá (không có con) - link trực tiếp
                            $html .= '<li class="nav-item">';
                            $html .= '<a class="nav-link" href="' . $categoryUrl . '" title="' . htmlspecialchars($category['name']) . '">';
                            $html .= mb_strtoupper(htmlspecialchars($category['name']), 'UTF-8');
                            $html .= '</a>';
                            $html .= '</li>';
                        }
                    }
                    return $html;
                }
                ?>
                <li class="nav-item submenu <?php echo $isBlogActive ? 'active' : ''; ?>">
                    <a class="nav-link" href="javascript:void(0);" title="Blog">
                        TIN TỨC - THƯ VIỆN
                        <span class="caret-drop"></span>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li class="nav-item">
                            <a class="nav-link" href="/tin-tuc" title="Tất cả tin tức">TẤT CẢ TIN TỨC</a>
                        </li>
                        <?php echo renderBlogCategoryMenu($blogCategoriesTree); ?>
                    </ul>
                </li>

                <!-- Contact -->
                <li class="nav-item <?php echo ($currentPage === 'contact') ? 'active' : ''; ?>">
                    <a class="nav-link" href="/lien-he" title="Liên hệ">LIÊN HỆ</a>
                </li>

                <!-- Search Icon -->
                <li class="nav-item nav-search-btn">
                    <button class="search_toggle" aria-label="Open search" title="Search">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                    </button>
                </li>

                <!-- Profile Button - Static File ho-so-nang-luc.pdf -->
                <li class="nav-item nav-profile-btn">
                    <a href="/assets/files/ho-so-nang-luc.pdf" class="btn_profile_download_nav" download="ho-so-nang-luc.pdf" title="Tải Profile">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                            <polyline points="7 10 12 15 17 10" />
                            <line x1="12" y1="15" x2="12" y2="3" />
                        </svg>
                        <span>Profile</span>
                    </a>
                </li>

                <!-- Language Switcher (Elfsight) - Moved from topbar -->
                <li class="nav-item nav-lang-item">
                    <div class="nav_lang">
                        <div class="elfsight-app-c8ccbe90-5ee0-4fcc-a0ab-0bd32c144dd7" data-elfsight-app-lazy></div>
                    </div>
                </li>

            </ul>

        </div>
    </nav>

    <!-- Search Overlay -->
    <div class="search_overlay" id="searchOverlay" role="dialog" aria-modal="true" aria-label="Tìm kiếm">
        <div class="search_overlay_box">
            <button class="search_overlay_close" id="searchClose" aria-label="Đóng tìm kiếm">&times;</button>
            <p class="search_overlay_label">TÌM KIẾM</p>
            <form class="search_overlay_inner" id="searchOverlayForm">
                <input type="text" id="searchInput" class="search_overlay_input"
                    placeholder="Tìm kiếm cho..." autocomplete="off"
                    value="">
                <button class="search_overlay_submit" type="submit" aria-label="Gửi tìm kiếm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                </button>
            </form>
        </div>
    </div>

</header>