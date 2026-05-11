<div class="page-header">
    <h4><i class="bi bi-pencil me-2"></i>Chỉnh sửa Footer Link</h4>
    <div class="page-actions">
        <a href="/footer" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>Quay lại
        </a>
    </div>
</div>

<div class="admin-form-card">
    <div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>Chức năng đang phát triển.</div>
    
    <?php if (!empty($link)): ?>
        <form method="POST" action="/footer/update/<?= $link['id'] ?>" class="admin-form">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="title" class="form-label">Tiêu đề liên kết <span class="text-danger">*</span></label>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               class="form-control" 
                               value="<?= htmlspecialchars($link['title'] ?? '') ?>"
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
                               value="<?= htmlspecialchars($link['url'] ?? '') ?>"
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
                               value="<?= $link['sort_order'] ?? 0 ?>"
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
                                   <?= ($link['is_active'] ?? 1) ? 'checked' : '' ?>>
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
                    <i class="bi bi-check-circle me-1"></i>Cập nhật liên kết
                </button>
                <a href="/footer" class="btn btn-secondary">
                    <i class="bi bi-x-circle me-1"></i>Hủy
                </a>
            </div>
        </form>
    <?php else: ?>
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i>
            Không tìm thấy liên kết này.
        </div>
        <div class="form-actions">
            <a href="/footer" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i>Quay lại danh sách
            </a>
        </div>
    <?php endif; ?>
</div>
