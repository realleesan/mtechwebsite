<div class="page-header">
    <h4><i class="bi bi-plus-circle me-2"></i>Thêm Footer Link mới</h4>
    <div class="page-actions">
        <a href="/footer" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>Quay lại
        </a>
    </div>
</div>

<div class="admin-form-card">
    
    <form method="POST" action="/footer/store" class="admin-form">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="title" class="form-label">Tiêu đề liên kết <span class="text-danger">*</span></label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           class="form-control" 
                           value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"
                           required
                           placeholder="Ví dụ: Về chúng tôi">
                    <div class="form-text">Nhập tiêu đề hiển thị cho liên kết</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="url" class="form-label">URL liên kết <span class="text-danger">*</span></label>
                    <input type="url" 
                           id="url" 
                           name="url" 
                           class="form-control" 
                           value="<?= htmlspecialchars($_POST['url'] ?? '') ?>"
                           required
                           placeholder="https://example.com">
                    <div class="form-text">Nhập địa chỉ URL đầy đủ (bao gồm http:// hoặc https://)</div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="sort_order" class="form-label">Thứ tự hiển thị</label>
                    <input type="number" 
                           id="sort_order" 
                           name="sort_order" 
                           class="form-control" 
                           value="<?= $_POST['sort_order'] ?? 0 ?>"
                           min="0"
                           placeholder="0">
                    <div class="form-text">Số càng nhỏ, hiển thị càng lên trên. Mặc định: 0</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Trạng thái</label>
                    <div class="form-check">
                        <input type="checkbox" 
                               id="is_active" 
                               name="is_active" 
                               class="form-check-input" 
                               value="1"
                               <?= !isset($_POST['is_active']) || $_POST['is_active'] ? 'checked' : '' ?>>
                        <label for="is_active" class="form-check-label">
                            Hiển thị trên footer
                        </label>
                    </div>
                    <div class="form-text">Bỏ chọn để ẩn liên kết này khỏi footer</div>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i>Thêm liên kết
            </button>
            <a href="/footer" class="btn btn-secondary">
                <i class="bi bi-x-circle me-1"></i>Hủy
            </a>
        </div>
    </form>
</div>
