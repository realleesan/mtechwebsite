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
                <div class="social-card">
                    <div class="social-header">
                        <div class="social-icon">
                            <i class="bi bi-<?= strtolower($social['platform']) ?>"></i>
                        </div>
                        <div class="social-info">
                            <h5 class="social-title"><?= ucfirst($social['platform']) ?></h5>
                            <span class="social-status <?= $social['is_visible'] ? 'status-active' : 'status-inactive' ?>">
                                <?= $social['is_visible'] ? 'Đang hiển thị' : 'Đã ẩn' ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="social-content">
                        <div class="form-group">
                            <label class="form-label">URL:</label>
                            <div class="url-display">
                                <?php if (!empty($social['url']) && $social['url'] !== '#'): ?>
                                    <a href="<?= htmlspecialchars($social['url']) ?>" target="_blank" class="url-link">
                                        <?= htmlspecialchars($social['url']) ?>
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="url-empty">Chưa cấu hình</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Trạng thái:</label>
                            <div class="status-toggle">
                                <span class="toggle-indicator <?= $social['is_visible'] ? 'active' : 'inactive' ?>">
                                    <i class="bi bi-<?= $social['is_visible'] ? 'eye' : 'eye-slash' ?>"></i>
                                </span>
                                <span class="toggle-text">
                                    <?= $social['is_visible'] ? 'Hiển thị trên footer' : 'Ẩn trên footer' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="social-actions">
                        <a href="/footer/social/<?= strtolower($social['platform']) ?>" class="btn btn-primary btn-sm">
                            <i class="bi bi-pencil me-1"></i>Chỉnh sửa
                        </a>
                        <?php if (!empty($social['url']) && $social['url'] !== '#'): ?>
                            <a href="<?= htmlspecialchars($social['url']) ?>" target="_blank" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-box-arrow-up-right me-1"></i>Xem
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php 
            endforeach; 
        } else {
            echo '<div class="alert alert-warning">Không có dữ liệu mạng xã hội. Vui lòng kiểm tra cơ sở dữ liệu.</div>';
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
.social-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.social-card {
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 12px;
    padding: 20px;
    transition: all 0.3s ease;
}

.social-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.social-header {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}

.social-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
    margin-right: 15px;
}

.social-info {
    flex: 1;
}

.social-title {
    margin: 0 0 5px 0;
    font-size: 18px;
    font-weight: 600;
    color: #333;
}

.social-status {
    font-size: 12px;
    padding: 4px 8px;
    border-radius: 12px;
    font-weight: 500;
}

.status-active {
    background: #d4edda;
    color: #155724;
}

.status-inactive {
    background: #f8d7da;
    color: #721c24;
}

.social-content {
    margin-bottom: 20px;
}

.url-display {
    font-family: 'Courier New', monospace;
    font-size: 13px;
}

.url-link {
    color: #007bff;
    text-decoration: none;
    word-break: break-all;
}

.url-link:hover {
    text-decoration: underline;
}

.url-empty {
    color: #6c757d;
    font-style: italic;
}

.status-toggle {
    display: flex;
    align-items: center;
    gap: 10px;
}

.toggle-indicator {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
}

.toggle-indicator.active {
    background: #28a745;
    color: white;
}

.toggle-indicator.inactive {
    background: #6c757d;
    color: white;
}

.toggle-text {
    font-size: 14px;
    color: #6c757d;
}

.social-actions {
    display: flex;
    gap: 10px;
}

.quick-actions {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    border: 1px solid #dee2e6;
}

.quick-actions h6 {
    margin: 0 0 15px 0;
    color: #495057;
    font-weight: 600;
}

.quick-action-buttons {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
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
