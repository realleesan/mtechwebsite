<div class="page-header">
    <h4><i class="bi bi-layout-text-window me-2"></i>Quản lý Footer</h4>
    <div class="page-actions">
        <a href="/footer/trash" class="btn btn-warning">
            <i class="bi bi-trash me-1"></i>Thùng rác
            <?php 
            // Count trashed items (need to implement this in FooterModel)
            $trashedCount = 0; // $this->model->countTrashed();
            if ($trashedCount > 0): ?>
                <span class="badge bg-danger ms-1"><?= $trashedCount ?></span>
            <?php endif; ?>
        </a>
        <a href="/footer/add" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Thêm mục mới
        </a>
    </div>
</div>

<div class="admin-form-card">
    
    <?php if (!empty($footer)): ?>
        <!-- Cài đặt chung -->
        <div class="footer-section">
            <h5 class="section-title">
                <i class="bi bi-gear me-2"></i>Cài đặt chung
            </h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="info-item">
                        <label class="info-label">Tiêu đề Useful Links:</label>
                        <div class="info-value">
                            <?= htmlspecialchars($footer['settings']['useful_links_title'] ?? 'Useful Links') ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="section-actions">
                <a href="/footer/settings" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-pencil me-1"></i>Chỉnh sửa cài đặt
                </a>
            </div>
        </div>

        <!-- Useful Links -->
        <div class="footer-section">
            <h5 class="section-title">
                <i class="bi bi-link-45deg me-2"></i>Useful Links
                <span class="badge bg-primary ms-2"><?= count($footer['useful_links']) ?></span>
            </h5>
            <?php if (!empty($footer['useful_links'])): ?>
                <div class="links-grid">
                    <?php foreach ($footer['useful_links'] as $link): ?>
                        <div class="link-item">
                            <div class="link-info">
                                <div class="link-title">
                                    <?= htmlspecialchars($link['title']) ?>
                                    <?php if (!$link['is_active']): ?>
                                        <span class="badge bg-secondary ms-2">Đã ẩn</span>
                                    <?php else: ?>
                                        <span class="badge bg-success ms-2">Đang hiển thị</span>
                                    <?php endif; ?>
                                </div>
                                <div class="link-url"><?= htmlspecialchars($link['url']) ?></div>
                            </div>
                            <div class="link-actions">
                                <a href="/footer/edit/<?= $link['id'] ?>" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="/footer/delete/<?= $link['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" data-confirm="Xóa liên kết này?">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <p>Chưa có liên kết nào</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Social Icons -->
        <div class="footer-section">
            <h5 class="section-title">
                <i class="bi bi-share me-2"></i>Mạng xã hội
                <span class="badge bg-primary ms-2"><?= count($footer['social']) ?></span>
            </h5>
            <?php if (!empty($footer['social'])): ?>
                <div class="social-grid">
                    <?php foreach ($footer['social'] as $social): ?>
                        <div class="social-item">
                            <div class="social-info">
                                <div class="social-platform">
                                    <i class="bi bi-<?= strtolower($social['platform']) ?>"></i>
                                    <?= ucfirst($social['platform']) ?>
                                    <?php if (!$social['is_visible']): ?>
                                        <span class="badge bg-secondary ms-2">Đã ẩn</span>
                                    <?php else: ?>
                                        <span class="badge bg-success ms-2">Đang hiển thị</span>
                                    <?php endif; ?>
                                </div>
                                <div class="social-url"><?= htmlspecialchars($social['url'] ?? '') ?></div>
                            </div>
                            <div class="social-actions">
                                <a href="/footer/social/<?= strtolower($social['platform']) ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="bi bi-share"></i>
                    <p>Chưa có mạng xã hội nào</p>
                </div>
            <?php endif; ?>
            <div class="section-actions">
                <a href="/footer/social" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-gear me-1"></i>Quản lý mạng xã hội
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="bi bi-layout-text-window"></i>
            <p>Chưa có dữ liệu footer</p>
        </div>
    <?php endif; ?>
</div>
