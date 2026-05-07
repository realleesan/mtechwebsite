<?php
// Xác định trang hiện tại để highlight menu
$currentPage = $page ?? '';
?>
<aside class="admin-sidebar d-flex flex-column" id="adminSidebar">

    <!-- Logo -->
    <div class="sidebar-logo d-flex align-items-center px-3 py-4">
        <img src="/assets/images/logo.png" alt="MTech" height="36" onerror="this.style.display='none'">
        <span class="ms-2 fw-bold text-white fs-5">MTech Admin</span>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav flex-grow-1 px-2">
        <ul class="nav flex-column gap-1">

            <!-- Dashboard -->
            <li class="nav-item">
                <a href="/dashboard" class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="sidebar-divider"><small>NỘI DUNG</small></li>

            <!-- Blogs -->
            <li class="nav-item">
                <a href="/blogs" class="nav-link <?= $currentPage === 'blogs' ? 'active' : '' ?>">
                    <i class="bi bi-newspaper"></i>
                    <span>Tin tức / Tuyển dụng</span>
                </a>
            </li>

            <!-- Projects -->
            <li class="nav-item">
                <a href="/projects" class="nav-link <?= $currentPage === 'projects' ? 'active' : '' ?>">
                    <i class="bi bi-building"></i>
                    <span>Dự án</span>
                </a>
            </li>

            <!-- Categories -->
            <li class="nav-item">
                <a href="/categories" class="nav-link <?= $currentPage === 'categories' ? 'active' : '' ?>">
                    <i class="bi bi-grid"></i>
                    <span>Dịch vụ</span>
                </a>
            </li>

            <!-- Teams -->
            <li class="nav-item">
                <a href="/teams" class="nav-link <?= $currentPage === 'teams' ? 'active' : '' ?>">
                    <i class="bi bi-people"></i>
                    <span>Đội ngũ</span>
                </a>
            </li>

            <!-- Awards -->
            <li class="nav-item">
                <a href="/awards" class="nav-link <?= $currentPage === 'awards' ? 'active' : '' ?>">
                    <i class="bi bi-trophy"></i>
                    <span>Giải thưởng</span>
                </a>
            </li>

            <!-- Client Logos -->
            <li class="nav-item">
                <a href="/client-logos" class="nav-link <?= $currentPage === 'client-logos' ? 'active' : '' ?>">
                    <i class="bi bi-images"></i>
                    <span>Logo đối tác</span>
                </a>
            </li>

            <li class="sidebar-divider"><small>LIÊN HỆ & TUYỂN DỤNG</small></li>

            <!-- Contacts -->
            <li class="nav-item">
                <a href="/contacts" class="nav-link <?= $currentPage === 'contacts' ? 'active' : '' ?>">
                    <i class="bi bi-envelope"></i>
                    <span>Liên hệ</span>
                    <?php if (!empty($newContactsCount) && $newContactsCount > 0): ?>
                        <span class="badge bg-danger ms-auto"><?= $newContactsCount ?></span>
                    <?php endif; ?>
                </a>
            </li>

            <!-- Job Applications -->
            <li class="nav-item">
                <a href="/job-applications" class="nav-link <?= $currentPage === 'job-applications' ? 'active' : '' ?>">
                    <i class="bi bi-file-person"></i>
                    <span>Đơn ứng tuyển</span>
                    <?php if (!empty($newJobsCount) && $newJobsCount > 0): ?>
                        <span class="badge bg-warning ms-auto"><?= $newJobsCount ?></span>
                    <?php endif; ?>
                </a>
            </li>

            <li class="sidebar-divider"><small>CẤU HÌNH</small></li>

            <!-- Header -->
            <li class="nav-item">
                <a href="/header" class="nav-link <?= $currentPage === 'header' ? 'active' : '' ?>">
                    <i class="bi bi-layout-text-window-reverse"></i>
                    <span>Header</span>
                </a>
            </li>

            <!-- Footer -->
            <li class="nav-item">
                <a href="/footer" class="nav-link <?= $currentPage === 'footer' ? 'active' : '' ?>">
                    <i class="bi bi-layout-text-window"></i>
                    <span>Footer</span>
                </a>
            </li>

            <!-- Settings -->
            <li class="nav-item">
                <a href="/settings" class="nav-link <?= $currentPage === 'settings' ? 'active' : '' ?>">
                    <i class="bi bi-gear"></i>
                    <span>Cài đặt</span>
                </a>
            </li>

        </ul>
    </nav>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer px-3 py-3 border-top border-secondary">
        <a href="/logout" class="nav-link text-danger d-flex align-items-center gap-2">
            <i class="bi bi-box-arrow-left"></i>
            <span>Đăng xuất</span>
        </a>
    </div>

</aside>
