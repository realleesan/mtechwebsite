<?php // $teams ?>
<div class="page-header">
    <h4><i class="bi bi-people me-2"></i>Quản lý Đội ngũ</h4>
    <a href="/teams/create" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Thêm mới</a>
</div>
<div class="admin-table">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>#</th><th>Họ tên</th><th>Chức vụ</th><th style="width:120px">Thao tác</th></tr></thead>
            <tbody>
                <?php if (!empty($teams)): foreach ($teams as $t): ?>
                <tr>
                    <td class="text-muted small"><?= $t['id'] ?></td>
                    <td class="fw-medium"><?= htmlspecialchars($t['name'] ?? '') ?></td>
                    <td class="text-muted small"><?= htmlspecialchars($t['position'] ?? '') ?></td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="/teams/edit/<?= $t['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="/teams/delete/<?= $t['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger btn-delete" data-confirm="Xóa thành viên này?"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="4" class="text-center text-muted py-4"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Chưa có thành viên nào</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
