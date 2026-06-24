<div class="page-header">
    <h4><i class="bi bi-tags me-2"></i>Thêm danh mục tin tức</h4>
    <a href="/blogs/categories" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Quay lại</a>
</div>

<form method="POST" action="/blogs/categories/store" onsubmit="return validateCategoryForm()">
    <div class="admin-form-card">
        <div class="row">
            <div class="col-md-8">
                <div class="mb-3">
                    <label for="parent_id" class="form-label">Danh mục cha (tùy chọn)</label>
                    <select class="form-select" id="parent_id" name="parent_id">
                        <option value="">-- Danh mục gốc --</option>
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= htmlspecialchars($cat['id']) ?>">
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <div class="form-text">Chọn danh mục cha nếu đây là danh mục con</div>
                </div>
                
                <div class="mb-3">
                    <label for="name" class="form-label">Tên danh mục <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                
                <div class="mb-3">
                    <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="slug" name="slug" required>
                    <div class="form-text">URL thân thiện, sẽ tự động tạo từ tên danh mục</div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select class="form-select" id="status" name="status">
                        <option value="1">Kích hoạt</option>
                        <option value="0">Vô hiệu hóa</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="sort_order" class="form-label">Thứ tự hiển thị</label>
                    <input type="number" class="form-control" id="sort_order" name="sort_order" value="0" min="0">
                    <div class="form-text">Số nhỏ hơn hiển thị trước (từ trái sang phải, từ trên xuống dưới)</div>
                </div>
                
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="show_in_menu" name="show_in_menu" value="1">
                        <label class="form-check-label" for="show_in_menu">Hiển thị trong menu</label>
                        <div class="form-text">Hiển thị trong dropdown tin tức ở header</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
            <a href="/blogs/categories" class="btn btn-outline-secondary">
                <i class="bi bi-x-lg me-2"></i>Hủy
            </a>
            <div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-2"></i>Lưu danh mục
                </button>
            </div>
        </div>
    </div>
</form>