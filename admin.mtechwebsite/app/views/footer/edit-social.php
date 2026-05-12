<?php if (!isset($socialLink) || empty($socialLink)): ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle me-2"></i>
        Không tìm thấy dữ liệu mạng xã hội. Vui lòng <a href="/footer/social">quay lại</a> và thử lại.
    </div>
<?php else: ?>
<div class="page-header">
    <h4><i class="bi bi-pencil me-2"></i>Chỉnh sửa <?= ucfirst($socialLink['platform'] ?? 'Unknown') ?></h4>
    <div class="page-actions">
        <a href="/footer/social" class="btn btn-secondary">
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

        <form method="POST" action="/footer/social/update" class="admin-form">
            <input type="hidden" name="platform" value="<?= htmlspecialchars($socialLink['platform'] ?? '') ?>">
            
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="url" class="form-label">
                            <i class="bi bi-link-45deg me-1"></i>URL <?= ucfirst($socialLink['platform'] ?? 'Unknown') ?>
                        </label>
                        <input 
                            type="url" 
                            class="form-control" 
                            id="url" 
                            name="url" 
                            value="<?= htmlspecialchars($socialLink['url'] ?? '') ?>"
                            placeholder="https://<?= strtolower($socialLink['platform'] ?? 'platform') ?>.com/your-page"
                        >
                        <div class="form-text">
                            Nhập URL đầy đủ của trang <?= ucfirst($socialLink['platform'] ?? 'Unknown') ?>. Để trống hoặc nhập "#" để ẩn liên kết.
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input 
                                type="checkbox" 
                                class="form-check-input" 
                                id="is_visible" 
                                name="is_visible" 
                                value="1"
                                <?= ($socialLink['is_visible'] ?? 0) ? 'checked' : '' ?>
                            >
                            <label class="form-check-label" for="is_visible">
                                <i class="bi bi-eye me-1"></i>Hiển thị trên footer
                            </label>
                            <div class="form-text">
                                Bật/tắt để hiển thị hoặc ẩn icon này trên footer trang người dùng.
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="preview-section">
                        <label class="form-label">Xem trước</label>
                        <div class="preview-box">
                            <div class="preview-icon">
                                <i class="bi bi-<?= strtolower($socialLink['platform'] ?? 'globe') ?>"></i>
                            </div>
                            <div class="preview-info">
                                <div class="preview-title"><?= ucfirst($socialLink['platform'] ?? 'Unknown') ?></div>
                                <div class="preview-url" id="preview-url">
                                    <?php if (!empty($socialLink['url']) && $socialLink['url'] !== '#'): ?>
                                        <?= htmlspecialchars($socialLink['url']) ?>
                                    <?php else: ?>
                                        <span class="text-muted">Chưa cấu hình</span>
                                    <?php endif; ?>
                                </div>
                                <div class="preview-status" id="preview-status">
                                    <span class="badge <?= ($socialLink['is_visible'] ?? 0) ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= ($socialLink['is_visible'] ?? 0) ? 'Đang hiển thị' : 'Đã ẩn' ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>Lưu thay đổi
                </button>
                <a href="/footer/social" class="btn btn-secondary">
                    <i class="bi bi-x-circle me-1"></i>Hủy
                </a>
                <?php if (!empty($socialLink['url']) && $socialLink['url'] !== '#'): ?>
                    <a href="<?= htmlspecialchars($socialLink['url']) ?>" target="_blank" class="btn btn-outline-info">
                        <i class="bi bi-box-arrow-up-right me-1"></i>Xem trang
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<style>
.preview-section {
    margin-top: 8px;
}

.preview-box {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.preview-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
}

.preview-info {
    flex: 1;
}

.preview-title {
    font-weight: 600;
    margin-bottom: 5px;
}

.preview-url {
    font-size: 12px;
    color: #6c757d;
    word-break: break-all;
    margin-bottom: 5px;
}

.preview-status {
    font-size: 12px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const urlInput = document.getElementById('url');
    const visibleCheckbox = document.getElementById('is_visible');
    const previewUrl = document.getElementById('preview-url');
    const previewStatus = document.getElementById('preview-status');
    
    function updatePreview() {
        const url = urlInput.value.trim();
        const isVisible = visibleCheckbox.checked;
        
        // Update URL preview
        if (url && url !== '#') {
            previewUrl.innerHTML = url;
        } else {
            previewUrl.innerHTML = '<span class="text-muted">Chưa cấu hình</span>';
        }
        
        // Update status preview
        const badgeClass = isVisible ? 'bg-success' : 'bg-secondary';
        const statusText = isVisible ? 'Đang hiển thị' : 'Đã ẩn';
        previewStatus.innerHTML = `<span class="badge ${badgeClass}">${statusText}</span>`;
    }
    
    urlInput.addEventListener('input', updatePreview);
    visibleCheckbox.addEventListener('change', updatePreview);
});
</script>
<?php endif; ?>
