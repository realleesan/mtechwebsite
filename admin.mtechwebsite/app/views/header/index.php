<?php
/**
 * @var array $header Header settings data with keys: logo_path, logo_alt, phone, phone_href, iso_text, profile_pdf_label, profile_pdf_path
 * @var string $title Page title
 * @var string $page Current page identifier
 * @var array $admin Admin user data
 */
?>
<div class="page-header">
    <h4><i class="bi bi-layout-text-window-reverse me-2"></i>Quản lý Header</h4>
</div>

<div class="row">
    <div class="col-lg-12">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                <?php 
                echo htmlspecialchars($_SESSION['success']); 
                unset($_SESSION['success']); 
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <?php 
                echo htmlspecialchars($_SESSION['error']); 
                unset($_SESSION['error']); 
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Cài đặt chung -->
        <div class="admin-form-card">
            <h5 class="section-title">
                <i class="bi bi-gear me-2"></i>Cài đặt chung
            </h5>
            <div class="row align-items-center">
                <div class="col-md-3">
                    <div class="logo-preview-container" style="background: #0d1117; padding: 10px; border-radius: 4px;">
                        <img src="/<?= htmlspecialchars(ltrim($header['logo_path'], '/')) ?>" alt="Logo Preview" class="preview-logo">
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="header-info-grid">
                        <div class="info-box">
                            <label>Số điện thoại</label>
                            <span><?= htmlspecialchars($header['phone']) ?></span>
                        </div>
                        <div class="info-box">
                            <label>ISO Text</label>
                            <span><?= htmlspecialchars($header['iso_text']) ?></span>
                        </div>
                        <div class="info-box">
                            <label>Hồ sơ năng lực</label>
                            <span class="file-info-badge">
                                <i class="bi bi-file-earmark-pdf me-1"></i>
                                <?= htmlspecialchars($header['profile_pdf_label']) ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="section-actions">
                <a href="/header/settings" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-pencil me-1"></i>Chỉnh sửa cài đặt chung
                </a>
                <a href="/header/profile" class="btn btn-sm btn-outline-info">
                    <i class="bi bi-pencil me-1"></i>Chỉnh sửa hồ sơ năng lực
                </a>
            </div>
        </div>
    </div>
</div>
