<div class="page-header">
    <h4><i class="bi bi-gear me-2"></i>Cài đặt Footer</h4>
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

        <form method="POST" action="/footer/settings/update" class="admin-form">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="useful_links_title" class="form-label required">
                            <i class="bi bi-link-45deg me-1"></i>Tiêu đề Useful Links
                        </label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="useful_links_title" 
                            name="useful_links_title" 
                            value="<?= htmlspecialchars($settings['useful_links_title'] ?? 'Useful Links') ?>"
                            placeholder="Nhập tiêu đề cho phần liên kết hữu ích"
                            required
                        >
                        <div class="form-text">
                            Tiêu đề này sẽ hiển thị ở phần Useful Links của footer trên trang người dùng.
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Xem trước</label>
                        <div class="preview-box">
                            <h5 class="preview-title">
                                <i class="bi bi-link-45deg me-1"></i>
                                <span id="preview-title"><?= htmlspecialchars($settings['useful_links_title'] ?? 'Useful Links') ?></span>
                            </h5>
                            <ul class="preview-links">
                                <li><a href="#">Ví dụ liên kết 1</a></li>
                                <li><a href="#">Ví dụ liên kết 2</a></li>
                                <li><a href="#">Ví dụ liên kết 3</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>Lưu cài đặt
                </button>
                <a href="/footer" class="btn btn-secondary">
                    <i class="bi bi-x-circle me-1"></i>Hủy
                </a>
            </div>
        </form>
    </div>
</div>

<style>
.preview-box {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    margin-top: 8px;
}

.preview-title {
    color: #333;
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 10px;
    border-bottom: 2px solid #007bff;
    padding-bottom: 5px;
}

.preview-links {
    list-style: none;
    padding: 0;
    margin: 0;
}

.preview-links li {
    margin-bottom: 5px;
}

.preview-links a {
    color: #6c757d;
    text-decoration: none;
    font-size: 14px;
}

.preview-links a:hover {
    color: #007bff;
    text-decoration: underline;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const titleInput = document.getElementById('useful_links_title');
    const previewTitle = document.getElementById('preview-title');
    
    titleInput.addEventListener('input', function() {
        const value = this.value.trim();
        previewTitle.textContent = value || 'Useful Links';
    });
});
</script>
