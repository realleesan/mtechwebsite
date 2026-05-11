<?php // $teams, $total, $currentPage, $totalPages
$totalPages  = $totalPages  ?? 1;
$currentPage = $currentPage ?? 1;
?>
<div class="page-header">
    <h4><i class="bi bi-trash me-2"></i>Thùng rác</h4>
    <div class="d-flex gap-2">
        <a href="/teams" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Quay lại</a>
    </div>
</div>

<div class="admin-table">
    <div class="p-3 border-bottom">
        <span class="text-muted small">Tổng: <strong><?= $total ?? 0 ?></strong> thành viên đã xóa</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Họ tên</th>
                    <th>Chức vụ</th>
                    <th>Ngày xóa</th>
                    <th style="width:180px">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($teams)): foreach ($teams as $t): ?>
                <tr>
                    <td class="text-muted small"><?= $t['id'] ?></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <?php if (!empty($t['image'])): ?>
                                <img src="<?= htmlspecialchars($t['image']) ?>"
                                     width="40" height="40"
                                     style="object-fit:cover;border-radius:50%"
                                     onerror="this.style.display='none'">
                            <?php endif; ?>
                            <div>
                                <div class="fw-medium"><?= htmlspecialchars($t['name'] ?? '') ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="text-muted small"><?= htmlspecialchars($t['position'] ?? '—') ?></td>
                    <td class="text-muted small">
                        <?= isset($t['deleted_at']) ? date('d/m/Y H:i', strtotime($t['deleted_at'])) : '' ?>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <form method="POST" action="/teams/restore/<?= $t['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-success"
                                        data-confirm="Khôi phục thành viên này?">
                                    <i class="bi bi-arrow-counterclockwise"></i> Khôi phục
                                </button>
                            </form>
                            <form method="POST" action="/teams/hard-delete/<?= $t['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger btn-delete"
                                        data-confirm="XÓA VĨNH VIỄN thành viên này? Không thể khôi phục!">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                        Thùng rác trống
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($totalPages > 1): ?>
<nav aria-label="Page navigation" class="mt-3">
    <ul class="pagination justify-content-center pagination-sm">
        <?php if ($currentPage > 1): ?>
        <li class="page-item">
            <a class="page-link" href="?page=<?= $currentPage - 1 ?>"><i class="bi bi-chevron-left"></i></a>
        </li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
        </li>
        <?php endfor; ?>

        <?php if ($currentPage < $totalPages): ?>
        <li class="page-item">
            <a class="page-link" href="?page=<?= $currentPage + 1 ?>"><i class="bi bi-chevron-right"></i></a>
        </li>
        <?php endif; ?>
    </ul>
</nav>
<?php endif; ?>
