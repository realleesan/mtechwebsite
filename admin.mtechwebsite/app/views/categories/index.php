<?php
$categoryLevels = [];
if (!empty($categories)) {
    $childrenByParent = [];
    foreach ($categories as $categoryItem) {
        $parentKey = empty($categoryItem['parent_id']) ? 0 : (int)$categoryItem['parent_id'];
        $childrenByParent[$parentKey][] = (int)$categoryItem['id'];
    }

    $assignCategoryLevel = function ($parentId, $level) use (&$assignCategoryLevel, &$childrenByParent, &$categoryLevels) {
        foreach ($childrenByParent[(int)$parentId] ?? [] as $categoryId) {
            $categoryLevels[$categoryId] = $level;
            $assignCategoryLevel($categoryId, $level + 1);
        }
    };

    $assignCategoryLevel(0, 1);
}
?>
<div class="page-header">
    <h4><i class="bi bi-grid me-2"></i>Quản lý Dịch vụ</h4>
    <div class="d-flex gap-2">
        <a href="/categories/trash" class="btn btn-warning">
            <i class="bi bi-trash me-1"></i>Thùng rác
        </a>
        <a href="/categories/create" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Thêm mới
        </a>
    </div>
</div>

<?php if (!empty($categories)): ?>
<div class="admin-table">
    <div class="p-3 border-bottom">
        <span class="text-muted small">Tổng: <strong><?= count($categories) ?></strong> dịch vụ</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th style="width:60px">#</th>
                    <th>Tên dịch vụ</th>
                    <th style="width:90px">Cấp độ</th>
                    <th style="width:110px">Trạng thái</th>
                    <th style="width:120px">Ngày tạo</th>
                    <th style="width:120px">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                <tr>
                    <td class="text-muted small"><?= $cat['id'] ?></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <?php if (!empty($cat['image'])): ?>
                                <img src="<?= htmlspecialchars($cat['image']) ?>"
                                     alt="" width="50" height="50"
                                     style="object-fit:cover; border-radius:6px; flex-shrink:0;"
                                     onerror="this.style.display='none'">
                            <?php else: ?>
                                <div class="bg-light d-flex align-items-center justify-content-center"
                                     style="width:50px; height:50px; border-radius:6px; flex-shrink:0;">
                                    <i class="bi bi-grid text-muted"></i>
                                </div>
                            <?php endif; ?>
                            <div>
                                <div class="fw-medium"><?= htmlspecialchars($cat['name'] ?? '') ?></div>
                                <small class="text-muted"><?= htmlspecialchars($cat['slug'] ?? '') ?></small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">
                            Cấp <?= (int)($categoryLevels[(int)$cat['id']] ?? 1) ?>
                        </span>
                    </td>
                    <td>
                        <?php if (($cat['status'] ?? 1) == 1): ?>
                            <span class="badge bg-success">Hiển thị</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Ẩn</span>
                        <?php endif; ?>
                        <?php if (!empty($cat['show_in_footer'])): ?>
                            <span class="badge bg-info mt-1 d-block" style="width:fit-content">
                                <i class="bi bi-layout-text-window-reverse me-1"></i>Footer
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="text-muted small">
                        <?= !empty($cat['created_at']) ? date('d/m/Y', strtotime($cat['created_at'])) : '—' ?>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="/categories/edit/<?= $cat['id'] ?>"
                               class="btn btn-sm btn-outline-primary" title="Chỉnh sửa">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="/categories/delete/<?= $cat['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger btn-delete"
                                        data-confirm="Xóa dịch vụ này?" title="Xóa">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php else: ?>
<div class="text-center text-muted py-5">
    <i class="bi bi-grid fs-1 d-block mb-3 opacity-50"></i>
    <p class="mb-3">Chưa có dịch vụ nào</p>
    <a href="/categories/create" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Thêm dịch vụ đầu tiên
    </a>
</div>
<?php endif; ?>
