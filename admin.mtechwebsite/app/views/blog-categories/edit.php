<?php
// View chỉ có HTML, tất cả logic được xử lý trong controller
// $categories - danh sách danh mục đã được flatten từ controller
// $category - danh mục hiện tại
// $showParentSelector - boolean để quyết định hiển thị parent selector
?>

<div class="page-header">
    <h4><i class="bi bi-tags me-2"></i>Chỉnh sửa danh mục tin tức</h4>
    <a href="/blogs/categories" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Quay lại</a>
</div>

<form method="POST" action="/blogs/categories/update/<?= $category['id'] ?>" onsubmit="return validateCategoryForm()">
    <div class="admin-form-card">
        <div class="row">
            <div class="col-md-8">
                <?php if ($showParentSelector): ?>
                <div class="mb-4">
                    <label for="parent_id" class="form-label fw-semibold">Danh mục cha <span class="text-muted">(tùy chọn)</span></label>
                    <select class="form-select form-select-lg category-parent-select" id="parent_id" name="parent_id">
                        <option value="" data-level="0">Danh mục gốc</option>
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= htmlspecialchars($cat['id']) ?>" 
                                    data-level="<?= htmlspecialchars($cat['level'] ?? 1) ?>"
                                    <?= ($category['parent_id'] == $cat['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['display_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <div class="form-text">Chọn danh mục cha để tạo quan hệ cha-con</div>
                </div>
                <?php else: ?>
                <div class="mb-4 p-3 bg-light rounded border">
                    <div class="text-muted small">
                        <i class="bi bi-info-circle me-2"></i><strong>Danh mục gốc (Cấp 1)</strong> không có danh mục cha
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Tên danh mục <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-lg" id="name" name="name" value="<?= htmlspecialchars($category['name'] ?? '') ?>" required placeholder="Nhập tên danh mục...">
                </div>
                
                <div class="mb-3">
                    <label for="slug" class="form-label fw-semibold">Slug <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="slug" name="slug" value="<?= htmlspecialchars($category['slug'] ?? '') ?>" required placeholder="slug-danh-muc">
                    <div class="form-text"><i class="bi bi-info-circle me-1"></i>URL thân thiện, sẽ tự động tạo từ tên danh mục</div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card border-0 bg-light p-3 mb-3">
                    <div class="card-body p-0">
                        <h6 class="card-title text-muted mb-3">Cài đặt</h6>
                        
                        <div class="mb-3">
                            <label for="status" class="form-label small">Trạng thái</label>
                            <select class="form-select form-select-sm" id="status" name="status">
                                <option value="1" <?= ($category['status'] == 1) ? 'selected' : '' ?>>✓ Kích hoạt</option>
                                <option value="0" <?= ($category['status'] == 0) ? 'selected' : '' ?>>✗ Vô hiệu hóa</option>
                            </select>
                        </div>
                        
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="show_in_menu" name="show_in_menu" value="1" 
                                <?= ($category['show_in_menu'] == 1) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="show_in_menu">
                                <small>Hiển thị trong menu</small>
                            </label>
                            <div class="form-text small"><i class="bi bi-info-circle me-1"></i>Hiển thị trong dropdown tin tức ở header</div>
                        </div>
                    </div>
                </div>
                
                <div class="card border-0 bg-warning bg-opacity-10 p-3 category-level-card">
                    <div class="card-body p-0">
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            <strong>Cấp độ hiện tại:</strong> 
                            <span class="badge bg-warning text-dark">Cấp <?= htmlspecialchars($category['level'] ?? 1) ?></span>
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
            <a href="/blogs/categories" class="btn btn-outline-secondary">
                <i class="bi bi-x-lg me-2"></i>Hủy
            </a>
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="bi bi-check-lg me-2"></i>Cập nhật danh mục
            </button>
        </div>
    </div>
</form>