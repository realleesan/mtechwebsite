<?php // $teams, $trashedCount ?>
<div class="page-header">
    <h4><i class="bi bi-people me-2"></i>Quản lý Đội ngũ</h4>
    <div class="d-flex gap-2">
        <a href="/teams/trash" class="btn btn-trash-custom">
            <i class="bi bi-trash me-1"></i>Thùng rác
        </a>
        <a href="/teams/create" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Thêm mới
        </a>
    </div>
</div>

<?php if (!empty($teams)): ?>
<div class="admin-table">
    <div class="p-3 border-bottom">
        <span class="text-muted small">Tổng: <strong><?= count($teams) ?></strong> thành viên</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th style="width:60px">#</th>
                    <th style="width:220px">Họ tên</th>
                    <th>Chức vụ</th>
                    <th style="width:80px">Thứ tự</th>
                    <th style="width:110px">Trạng thái</th>
                    <th style="width:130px">Ngày tạo</th>
                    <th style="width:120px">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($teams as $t): ?>
                <tr>
                    <td class="text-muted small"><?= $t['id'] ?></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="team-thumb">
                                <?php if (!empty($t['image'])): ?>
                                    <img src="<?= htmlspecialchars($t['image']) ?>"
                                         alt="<?= htmlspecialchars($t['name'] ?? '') ?>"
                                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                                    <div class="team-thumb-empty" style="display:none">
                                        <i class="bi bi-person text-muted"></i>
                                    </div>
                                <?php else: ?>
                                    <div class="team-thumb-empty">
                                        <i class="bi bi-person text-muted"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <span class="fw-medium"><?= htmlspecialchars($t['name'] ?? '') ?></span>
                        </div>
                    </td>
                    <td class="text-muted small"><?= htmlspecialchars($t['position'] ?? '—') ?></td>
                    <td class="text-center text-muted small"><?= (int)($t['sort_order'] ?? 0) ?></td>
                    <td>
                        <?php if (($t['status'] ?? 1) == 1): ?>
                            <span class="badge bg-success">Hiển thị</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Ẩn</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-muted small">
                        <?= !empty($t['created_at']) ? date('d/m/Y', strtotime($t['created_at'])) : '—' ?>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="/teams/edit/<?= $t['id'] ?>"
                               class="btn btn-sm btn-outline-primary" title="Chỉnh sửa">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="/teams/delete/<?= $t['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger btn-delete"
                                        data-confirm="Xóa thành viên này?" title="Xóa">
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
    <i class="bi bi-people fs-1 d-block mb-3 opacity-50"></i>
    <p class="mb-3">Chưa có thành viên nào</p>
    <a href="/teams/create" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Thêm thành viên đầu tiên
    </a>
</div>
<?php endif; ?>
