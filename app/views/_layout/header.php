<?php
/**
 * Header Layout - Navigation Menu
 * Dự án: MTech Website
 */

// Lấy trang hiện tại để xác định active menu
$currentPage = $_GET['page'] ?? 'home';

// Lấy header settings động
require_once __DIR__ . '/../../models/HeaderModel.php';
$headerModel    = new HeaderModel();
$headerSettings = $headerModel->getSettingsWithFallback();

// Lấy projects hiển thị trong menu dropdown
require_once __DIR__ . '/../../models/ProjectsModel.php';
$projectsModel = new ProjectsModel();
$menuProjects  = $projectsModel->getMenuProjects(10);

// Lấy services hiển thị trong menu dropdown
require_once __DIR__ . '/../../models/CategoriesModel.php';
$categoriesModel = new CategoriesModel();
$menuServices    = $categoriesModel->getMenuServices(10);

// Lấy blog categories hiển thị trong menu dropdown
require_once __DIR__ . '/../../models/BlogsModel.php';
$blogsModel = new BlogsModel();
$menuBlogCategories = $blogsModel->getMenuBlogCategories(10);
?>

<header class="main_menu_area">

    <!-- Top Bar -->
    <div class="header_top_five">
        <div class="topbar_inner">
            <!-- Left: Call Us Today (thẳng hàng logo) -->
            <div class="topbar_left">
                <span class="topbar_phone">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:5px;opacity:0.8"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.6 1.22h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.8a16 16 0 0 0 6.29 6.29l.95-.95a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                    Call Us Today
                    <a href="tel:<?php echo htmlspecialchars($headerSettings['phone_href']); ?>"><?php echo htmlspecialchars($headerSettings['phone']); ?></a>
                </span>
            </div>

            <!-- Right: ISO + Translate + Download Button (thẳng hàng menu) -->
            <div class="topbar_right">
                <div class="topbar_iso"><?php echo htmlspecialchars($headerSettings['iso_text']); ?></div>
                <div class="topbar_divider"></div>

                <!-- Language Switcher (Elfsight) -->
                <div class="topbar_lang">
                    <div class="elfsight-app-0f1ebd7d-aee6-4ddc-b2ba-1eba802b9ca5" data-elfsight-app-lazy></div>
                </div>

                <div class="topbar_divider"></div>

                <!-- Nút tải Hồ Sơ Năng Lực -->
                <a href="<?php echo htmlspecialchars($headerSettings['profile_pdf_path']); ?>" class="btn_profile_download" download title="Tải Hồ Sơ Năng Lực">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    <?php echo htmlspecialchars($headerSettings['profile_pdf_label']); ?>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light menu_absolute">
        <!-- Logo -->
        <a class="navbar-brand" href="./">
            <img src="<?php echo htmlspecialchars($headerSettings['logo_path']); ?>" alt="<?php echo htmlspecialchars($headerSettings['logo_alt']); ?>" class="logo-img">
            <span class="logo-text">MTECH.JSC</span>
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
                    <a class="nav-link" href="./" title="Home">Home</a>
                </li>
                
                <!-- About -->
                <?php
                $aboutPages = ['about', 'company.history', 'teams', 'awards'];
                $isAboutActive = in_array($currentPage, $aboutPages);
                ?>
                <li class="nav-item submenu <?php echo $isAboutActive ? 'active' : ''; ?>">
                    <a class="nav-link" href="#" title="About" onclick="return false;">
                        About
                        <span class="caret-drop"></span>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li class="nav-item">
                            <a class="nav-link" href="/?page=about" title="About Us">About Us</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/lich-su-hinh-thanh-phat-trien" title="Lịch sử hình thành & phát triển">Lịch sử hình thành & phát triển</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/?page=teams" title="Teams">Teams</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/?page=awards" title="Awards">Awards</a>
                        </li>
                    </ul>
                </li>
                
                <!-- Services -->
                <li class="nav-item submenu <?php echo ($currentPage === 'categories') ? 'active' : ''; ?>">
                    <a class="nav-link" href="#" title="Services" onclick="return false;">
                        Services
                        <span class="caret-drop"></span>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li class="nav-item">
                            <a class="nav-link" href="/?page=categories" title="All Services">All Services</a>
                        </li>
                        <?php foreach ($menuServices as $service): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/?page=categories-details&slug=<?php echo urlencode($service['slug']); ?>" title="<?php echo htmlspecialchars($service['name']); ?>">
                                <?php echo htmlspecialchars($service['name']); ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                
                <!-- Projects -->
                <li class="nav-item submenu <?php echo ($currentPage === 'projects' || $currentPage === 'project-details') ? 'active' : ''; ?>">
                    <a class="nav-link" href="#" title="Dự án" onclick="return false;">
                        Dự án
                        <span class="caret-drop"></span>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li class="nav-item">
                            <a class="nav-link" href="/du-an" title="Tất cả dự án">Tất cả dự án</a>
                        </li>
                        <?php foreach ($menuProjects as $project): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/chi-tiet-du-an-<?php echo urlencode($project['slug']); ?>" title="<?php echo htmlspecialchars($project['title']); ?>">
                                <?php echo htmlspecialchars($project['title']); ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </li>

                <!-- Tuyển Dụng -->
                <li class="nav-item <?php echo (isset($_GET['cat']) && $_GET['cat'] == '7') ? 'active' : ''; ?>">
                    <a class="nav-link" href="/?page=blogs&cat=7" title="Tuyển Dụng">Tuyển Dụng</a>
                </li>

                <!-- Blog -->
                <li class="nav-item submenu <?php echo ($currentPage === 'blogs') ? 'active' : ''; ?>">
                    <a class="nav-link" href="#" title="Blog" onclick="return false;">
                        Blog
                        <span class="caret-drop"></span>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li class="nav-item">
                            <a class="nav-link" href="/?page=blogs" title="All Blogs">All Blogs</a>
                        </li>
                        <?php foreach ($menuBlogCategories as $category): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/?page=blogs&cat=<?php echo urlencode($category['id']); ?>" title="<?php echo htmlspecialchars($category['name']); ?>">
                                <?php echo htmlspecialchars($category['name']); ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                
                <!-- Contact -->
                <li class="nav-item <?php echo ($currentPage === 'contact') ? 'active' : ''; ?>">
                    <a class="nav-link" href="/lien-he" title="Liên hệ">Liên hệ</a>
                </li>

                <!-- Search Icon -->
                <li class="nav-item nav-search-btn">
                    <button class="search_toggle" aria-label="Open search" title="Search">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    </button>
                </li>
                
            </ul>
            
        </div>
    </nav>

    <!-- Search Overlay -->
    <div class="search_overlay" id="searchOverlay" role="dialog" aria-modal="true" aria-label="Search">
        <div class="search_overlay_box">
            <button class="search_overlay_close" id="searchClose" aria-label="Close search">&times;</button>
            <p class="search_overlay_label">SEARCH</p>
            <form method="get" action="./" class="search_overlay_inner">
                <input type="hidden" name="page" value="search">
                <input type="text" name="q" id="searchInput" class="search_overlay_input"
                       placeholder="Search for..." autocomplete="off"
                       value="">
                <button class="search_overlay_submit" type="submit" aria-label="Submit search">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </button>
            </form>
        </div>
    </div>

</header>
