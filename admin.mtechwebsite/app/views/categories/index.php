<?php // $categories ?>
<div class="page-header">
    <h4><i class="bi bi-grid me-2"></i>Quản lý Dịch vụ</h4>
    <a href="/categories/create" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Thêm mới</a>
</div>
<div class="admin-table">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>#</th><th>Tên dịch vụ</th><th>Slug</th><th style="width:120px">Thao tác</th></tr></thead>
            <tbody>
                <?php if (!empty($categories)): foreach ($categories as $cat): ?>
                <tr>
                    <td class="text-muted small"><?= $cat['id'] ?></td>
                    <td class="fw-medium"><?= htmlspecialchars($cat['name'] ?? '') ?></td>
                    <td class="text-muted small"><?= htmlspecialchars($cat['slug'] ?? '') ?></td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="/categories/edit/<?= $cat['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="/categories/delete/<?= $cat['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger btn-delete" data-confirm="Xóa dịch vụ này?"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="4" class="text-center text-muted py-4"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Chưa có dịch vụ nào</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
