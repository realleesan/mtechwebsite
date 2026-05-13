<div class="page-header">
    <h4><i class="bi bi-share me-2"></i>Quản lý Mạng xã hội</h4>
    <div class="page-actions">
        <a href="/footer" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>Quay lại
        </a>
    </div>
</div>

<div class="admin-form-card">
    <div class="card-body">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php 
                echo htmlspecialchars($_SESSION['error']);
                unset($_SESSION['error']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php 
                echo htmlspecialchars($_SESSION['success']);
                unset($_SESSION['success']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="social-grid">
            <?php 
            if (!empty($socialLinks)) {
                foreach ($socialLinks as $social): 
            ?>
                <div class="social-item">
                    <div class="social-info">
                        <div class="social-platform">
                            <i class="bi bi-<?= strtolower($social['platform']) ?>"></i>
                            <?= ucfirst($social['platform']) ?>
                            <span class="badge <?= $social['is_visible'] ? 'bg-success' : 'bg-secondary' ?> ms-2">
                                <?= $social['is_visible'] ? 'Đang hiển thị' : 'Đã ẩn' ?>
                            </span>
                        </div>
                        <div class="social-url">
                            <?php if (!empty($social['url']) && $social['url'] !== '#'): ?>
                                <a href="<?= htmlspecialchars($social['url']) ?>" target="_blank">
                                    <?= htmlspecialchars($social['url']) ?>
                                    <i class="bi bi-box-arrow-up-right ms-1"></i>
                                </a>
                            <?php else: ?>
                                <span class="text-muted italic">Chưa cấu hình URL</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="social-actions">
                        <a href="/footer/social/<?= strtolower($social['platform']) ?>" class="btn btn-primary btn-sm">
                            <i class="bi bi-pencil me-1"></i>Sửa
                        </a>
                    </div>
                </div>
            <?php 
                endforeach; 
            } else {
                echo '<div class="alert alert-warning">Không có dữ liệu mạng xã hội.</div>';
            }
            ?>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <h6><i class="bi bi-lightning me-2"></i>Thao tác nhanh</h6>
            <div class="quick-action-buttons">
                <button type="button" class="btn btn-sm btn-success" onclick="toggleAllSocial(true)">
                    <i class="bi bi-eye me-1"></i>Hiển thị tất cả
                </button>
                <button type="button" class="btn btn-sm btn-warning" onclick="toggleAllSocial(false)">
                    <i class="bi bi-eye-slash me-1"></i>Ẩn tất cả
                </button>
                <button type="button" class="btn btn-sm btn-danger" onclick="clearAllSocialUrls()">
                    <i class="bi bi-trash me-1"></i>Xóa tất cả URL
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.quick-actions {
    background: var(--table-header-bg);
    border-radius: 8px;
    padding: 20px;
    border: 1px solid var(--border-color);
    margin-top: 2rem;
}

.quick-actions h6 {
    margin: 0 0 15px 0;
    color: var(--text-secondary);
    font-weight: 600;
}

.quick-action-buttons {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.social-url a {
    text-decoration: none;
    color: var(--primary);
}

.social-url a:hover {
    text-decoration: underline;
}

.italic {
    font-style: italic;
}
</style>

<script>
function toggleAllSocial(show) {
    if (!confirm(show ? 'Hiển thị tất cả mạng xã hội?' : 'Ẩn tất cả mạng xã hội?')) {
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/footer/social/bulk-toggle';
    
    const platformInput = document.createElement('input');
    platformInput.type = 'hidden';
    platformInput.name = 'platforms';
    platformInput.value = 'facebook,linkedin,twitter,google';
    
    const visibleInput = document.createElement('input');
    visibleInput.type = 'hidden';
    visibleInput.name = 'is_visible';
    visibleInput.value = show ? '1' : '0';
    
    form.appendChild(platformInput);
    form.appendChild(visibleInput);
    document.body.appendChild(form);
    form.submit();
}

function clearAllSocialUrls() {
    if (!confirm('Xóa tất cả URL mạng xã hội?')) {
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/footer/social/clear-urls';
    
    const platformInput = document.createElement('input');
    platformInput.type = 'hidden';
    platformInput.name = 'platforms';
    platformInput.value = 'facebook,linkedin,twitter,google';
    
    form.appendChild(platformInput);
    document.body.appendChild(form);
    form.submit();
}
</script>
