<?php
// $categories
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
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th style="width:60px">#</th>
                    <th>Tên danh mục</th>
                    <th>Slug</th>
                    <th>Trạng thái</th>
                    <th>Hiển thị menu</th>
                    <th style="width:100px">Thứ tự</th>
                    <th>Thời gian tạo</th>
                    <th style="width:120px">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td class="text-muted small"><?= $category['id'] ?></td>
                            <td>
                                <div class="fw-medium"><?= htmlspecialchars($category['name']) ?></div>
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
                            <td class="text-center">
                                <span class="badge bg-light text-dark"><?= (int)($category['sort_order'] ?? 0) ?></span>
                            </td>
                            <td class="text-muted small">
                                <?= isset($category['created_at']) ? date('d/m/Y H:i', strtotime($category['created_at'])) : '' ?>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="/blogs/categories/edit/<?= $category['id'] ?>"
                                       class="btn btn-sm btn-outline-primary" title="Chỉnh sửa">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="/blogs/categories/delete/<?= $category['id'] ?>" class="d-inline">
                                        <button type="submit" class="btn btn-sm btn-outline-danger btn-delete"
                                                data-confirm="Xóa danh mục này?" title="Xóa">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
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