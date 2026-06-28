<?php
// $categories

function buildCategoryHierarchy($categories) {
    $hierarchy = [];
    $indexed = [];
    
    // First, index all categories
    foreach ($categories as $cat) {
        $indexed[$cat['id']] = $cat;
        $indexed[$cat['id']]['children'] = [];
    }
    
    // Then build parent-child relationships
    foreach ($indexed as $id => &$cat) {
        if ($cat['parent_id'] && isset($indexed[$cat['parent_id']])) {
            $indexed[$cat['parent_id']]['children'][] = &$cat;
        } else {
            $hierarchy[] = &$cat;
        }
    }
    
    return $hierarchy;
}

/**
 * Render categories with hierarchy indentation
 */
function renderCategoryRows($categories, $depth = 0) {
    foreach ($categories as $category) {
        $hasChildren = !empty($category['children']);
        $level = (int)($category['level'] ?? ($depth + 1));
        $levelLabel = $level == 1 ? 'Cấp 1' : "Cấp $level";
        $levelBadgeClass = $level == 1 ? 'bg-primary' : 'bg-info';
        
        // Tính indent dựa trên LEVEL từ database, không phải $depth
        // Level 1 = 0px, Level 2 = 20px, Level 3 = 40px, etc.
        $indentPixels = ($level - 1) * 20;
        
        // Determine row classes
        $rowClass = 'category-row';
        if ($level > 1) {
            $rowClass .= ' child-row';
        }
        if ($hasChildren && $level > 1) {
            $rowClass .= ' sub-parent-row';
        }
        ?>
        <tr data-category-id="<?= $category['id'] ?>" data-depth="<?= $depth ?>" data-level="<?= $level ?>" class="<?= $rowClass ?>">
            <td class="text-muted small"><?= $category['id'] ?></td>
            <td>
                    <div class="d-flex align-items-center">
                    <?php if ($hasChildren): ?>
                        <button type="button" class="btn btn-sm btn-link text-secondary p-0 me-2 chevron-toggle" onclick="toggleBlogCategoryChildren(<?= $category['id'] ?>)" title="Mở rộng/Thu gọn">
                            <i class="bi bi-plus" id="chevron-<?= $category['id'] ?>"></i>
                        </button>
                    <?php else: ?>
                        <span class="d-inline-block me-2" style="width: 18px;"></span>
                    <?php endif; ?>
                    <div class="category-name-wrapper" style="margin-left: <?= $indentPixels ?>px;">
                        <span class="fw-medium"><?= htmlspecialchars($category['name']) ?></span>
                    </div>
                </div>
            </td>
            <td>
                <span class="badge <?= $levelBadgeClass ?>"><?= $levelLabel ?></span>
            </td>
            <td>
                <code class="text-muted small"><?= htmlspecialchars($category['slug']) ?></code>
            </td>
            <td>
                <?php if ($category['status'] == 1): ?>
                    <span class="badge bg-success">Kích hoạt</span>
                <?php else: ?>
                    <span class="badge bg-secondary">Vô hiệu hóa</span>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($category['show_in_menu'] == 1): ?>
                    <span class="badge bg-info">Hiển thị</span>
                <?php else: ?>
                    <span class="badge bg-light text-dark">Ẩn</span>
                <?php endif; ?>
            </td>
            <td class="text-muted small">
                <?= isset($category['created_at']) ? date('d/m/Y H:i', strtotime($category['created_at'])) : '' ?>
            </td>
            <td>
                <div class="d-flex gap-1">
                    <?php if ($level >= 1): ?>
                        <a href="/blogs/categories/create?parent_id=<?= $category['id'] ?>"
                           class="btn btn-sm btn-outline-success" title="Thêm danh mục con">
                            <i class="bi bi-plus-circle"></i>
                        </a>
                    <?php endif; ?>
                    <a href="/blogs/categories/edit/<?= $category['id'] ?>"
                       class="btn btn-sm btn-outline-primary" title="Chỉnh sửa">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <?php if (empty($category['children'])): ?>
                        <form method="POST" action="/blogs/categories/delete/<?= $category['id'] ?>" class="d-inline">
                            <button type="submit" class="btn btn-sm btn-outline-danger btn-delete"
                                    data-confirm="Xóa danh mục này?" title="Xóa">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    <?php else: ?>
                        <button type="button" class="btn btn-sm btn-outline-secondary" 
                                title="Không thể xóa danh mục có con" disabled>
                            <i class="bi bi-lock"></i>
                        </button>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
        <?php
        if ($hasChildren) {
            renderCategoryRows($category['children'], $depth + 1);
        }
    }
}

$categoryHierarchy = buildCategoryHierarchy($categories ?? []);
?>

<!-- Page Header -->
<div class="page-header">
    <h4><i class="bi bi-tags me-2"></i>Quản lý Danh mục Tin tức</h4>
    <div class="d-flex gap-2">
        <a href="/blogs" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Quay lại tin tức
        </a>
        <a href="/blogs/categories/create" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Thêm danh mục
        </a>
    </div>
</div>

<!-- Table -->
<div class="admin-table">
    <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
        <span class="text-muted small">Tổng: <strong><?= count($categories ?? []) ?></strong> danh mục</span>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="expandAllBlogCategories()">
                <i class="bi bi-arrows-expand me-1"></i>Mở rộng tất cả
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="collapseAllBlogCategories()">
                <i class="bi bi-arrows-collapse me-1"></i>Thu gọn tất cả
            </button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th style="width:60px">#</th>
                    <th>Tên danh mục & Phân cấp</th>
                    <th style="width:80px">Cấp độ</th>
                    <th>Slug</th>
                    <th>Trạng thái</th>
                    <th>Hiển thị menu</th>
                    <th>Thời gian tạo</th>
                    <th style="width:120px">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($categoryHierarchy)): ?>
                    <?php renderCategoryRows($categoryHierarchy); ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                            Chưa có danh mục nào
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>