<?php // $awards ?>
<div class="page-header">
    <h4><i class="bi bi-trophy me-2"></i>Quản lý Giải thưởng</h4>
    <div class="d-flex gap-2">
        <a href="/awards/trash" class="btn btn-warning">
            <i class="bi bi-trash me-1"></i>Thùng rác
        </a>
        <a href="/awards/create" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Thêm mới
        </a>
    </div>
</div>

<?php if (!empty($awards)): ?>
<div class="admin-table">
    <div class="p-3 border-bottom">
        <span class="text-muted small">Tổng: <strong><?= count($awards) ?></strong> giải thưởng</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle awards-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Ảnh</th>
                    <th>Tên giải thưởng</th>
                    <th>Đơn vị cấp</th>
                    <th class="award-col-sort text-center">Thứ tự</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($awards as $award): ?>
                <tr>
                    <td class="text-muted small"><?= $award['id'] ?></td>
                    <td>
                        <div class="award-thumb">
                            <?php if (!empty($award['image'])): ?>
                                <img src="<?= htmlspecialchars($award['image']) ?>"
                                     alt="<?= htmlspecialchars($award['name'] ?? '') ?>"
                                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                                <div class="award-thumb-empty" style="display:none">
                                    <i class="bi bi-trophy text-muted"></i>
                                </div>
                            <?php else: ?>
                                <div class="award-thumb-empty">
                                    <i class="bi bi-trophy text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <span class="fw-medium"><?= htmlspecialchars($award['name'] ?? '') ?></span>
                    </td>
                    <td class="text-muted small"><?= htmlspecialchars($award['certificate'] ?? '—') ?></td>
                    <td class="award-col-sort text-center text-muted small"><?= (int)($award['sort_order'] ?? 0) ?></td>
                    <td>
                        <?php if (($award['status'] ?? 1) == 1): ?>
                            <span class="badge bg-success">Hiển thị</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Ẩn</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="/awards/edit/<?= $award['id'] ?>"
                               class="btn btn-sm btn-outline-primary" title="Chỉnh sửa">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="/awards/delete/<?= $award['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger btn-delete"
                                        data-confirm="Chuyển giải thưởng này vào thùng rác?" title="Xóa">
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
    <i class="bi bi-trophy fs-1 d-block mb-3 opacity-50"></i>
    <p class="mb-3">Chưa có giải thưởng nào</p>
    <a href="/awards/create" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Thêm giải thưởng đầu tiên
    </a>
</div>
<?php endif; ?>
