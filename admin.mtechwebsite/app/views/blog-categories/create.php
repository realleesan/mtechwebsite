<?php
// $categories - danh sách danh mục (hierarchy format)
// $_GET['parent_id'] - parent_id từ URL (khi click "Thêm danh mục con")
// Hàm flatten hierarchy để loop dễ hơn
function flattenCategories($categories, &$result = [], $prefix = '') {
    foreach ($categories as $cat) {
        $cat['display_name'] = $prefix . $cat['name'];
        $result[] = $cat;
        if (!empty($cat['children'])) {
            flattenCategories($cat['children'], $result, $prefix . '— ');
        }
    }
    return $result;
}

$flatCategories = flattenCategories($categories);
$parentId = isset($_GET['parent_id']) ? (int)$_GET['parent_id'] : null;
$parentInfo = null;

// Tìm parent từ danh sách
if ($parentId) {
    foreach ($flatCategories as $cat) {
        if ($cat['id'] == $parentId) {
            $parentInfo = $cat;
            break;
        }
    }
}
?>

<div class="page-header">
    <h4><i class="bi bi-tags me-2"></i>Thêm danh mục tin tức</h4>
    <a href="/blogs/categories" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Quay lại</a>
</div>

<?php if ($parentInfo): ?>
<div class="alert alert-info" role="alert">
    <i class="bi bi-info-circle me-2"></i><strong>Tạo danh mục con của:</strong> <code><?= htmlspecialchars($parentInfo['display_name']) ?></code>
</div>
<?php endif; ?>

<form method="POST" action="/blogs/categories/store" onsubmit="return validateCategoryForm()">
    <input type="hidden" name="parent_id" value="<?= $parentId ?? '' ?>">
    <div class="admin-form-card">
        <div class="row">
            <div class="col-md-8">
                <?php if ($parentInfo): ?>
                <div class="mb-3">
                    <label class="form-label">Danh mục cha</label>
                    <div class="form-control" style="background-color: #f8f9fa;">
                        <i class="bi bi-tag me-2"></i><strong><?= htmlspecialchars($parentInfo['display_name']) ?></strong>
                        <small class="text-muted d-block">(Cấp <?= $parentInfo['level'] ?? 1 ?>)</small>
                    </div>
                    <div class="form-text">Danh mục con sẽ được tạo ở cấp <?= ($parentInfo['level'] ?? 1) + 1 ?></div>
                </div>
                <?php else: ?>
                <div class="mb-3">
                    <label for="parent_id" class="form-label">Danh mục cha (tùy chọn)</label>
                    <select class="form-select" id="parent_id" name="parent_id">
                        <option value="">-- Danh mục gốc (Cấp 1) --</option>
                        <?php if (!empty($flatCategories)): ?>
                            <?php foreach ($flatCategories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat['id']) ?>">
                                <?= htmlspecialchars($cat['display_name']) ?> (Cấp <?= $cat['level'] ?? 1 ?>)
                            </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <div class="form-text">Chọn danh mục cha nếu đây là danh mục con</div>
                </div>
                <?php endif; ?>

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
