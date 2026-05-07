<?php
$admin = $admin ?? \AuthMiddleware::getAdmin();
?>
<header class="admin-topbar d-flex align-items-center justify-content-between px-4 py-2 border-bottom">

    <!-- Toggle Sidebar (mobile) -->
    <button class="btn btn-sm btn-outline-secondary d-lg-none" id="sidebarToggle">
        <i class="bi bi-list fs-5"></i>
    </button>

    <!-- Page Title -->
    <div class="topbar-title d-none d-lg-block">
        <h6 class="mb-0 text-muted"><?= htmlspecialchars($title ?? 'Admin Panel') ?></h6>
    </div>

    <!-- Right side -->
    <div class="topbar-right d-flex align-items-center gap-3">

        <!-- View website -->
        <a href="https://truongvinalogistics.com.vn" target="_blank"
           class="btn btn-sm btn-outline-primary d-none d-md-inline-flex align-items-center gap-1">
            <i class="bi bi-box-arrow-up-right"></i>
            <span>Xem website</span>
        </a>

        <!-- Admin dropdown -->
        <div class="dropdown">
            <button class="btn btn-sm btn-light dropdown-toggle d-flex align-items-center gap-2"
                    type="button" data-bs-toggle="dropdown">
                <div class="admin-avatar">
                    <i class="bi bi-person-circle fs-5"></i>
                </div>
                <span class="d-none d-md-inline">
                    <?= htmlspecialchars($admin['username'] ?? 'Admin') ?>
                </span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <span class="dropdown-item-text text-muted small">
                        <?= htmlspecialchars($admin['email'] ?? '') ?>
                    </span>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item" href="/settings">
                        <i class="bi bi-gear me-2"></i>Cài đặt
                    </a>
                </li>
                <li>
                    <a class="dropdown-item text-danger" href="/logout">
                        <i class="bi bi-box-arrow-left me-2"></i>Đăng xuất
                    </a>
                </li>
            </ul>
        </div>

    </div>

</header>
