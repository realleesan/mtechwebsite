<?php // $awards, $total, $currentPage, $totalPages
$totalPages  = $totalPages  ?? 1;
$currentPage = $currentPage ?? 1;
?>
<div class="page-header">
    <h4><i class="bi bi-trash me-2"></i>Thùng rác</h4>
    <div class="d-flex gap-2">
        <a href="/awards" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Quay lại</a>
    </div>
</div>

<div class="admin-table">
    <div class="p-3 border-bottom">
        <span class="text-muted small">Tổng: <strong><?= $total ?? 0 ?></strong> giải thưởng đã xóa</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle awards-trash-table">
            <thead>
                <tr>
                    <th style="width:60px">#</th>
                    <th style="width:80px">Ảnh</th>
                    <th>Tên giải thưởng</th>
                    <th>Đơn vị cấp</th>
                    <th>Ngày xóa</th>
                    <th style="width:180px">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($awards)): foreach ($awards as $award): ?>
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
                    <td class="deleted-date">
                        <?= isset($award['deleted_at']) ? date('d/m/Y H:i', strtotime($award['deleted_at'])) : '' ?>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <form method="POST" action="/awards/restore/<?= $award['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-success"
                                        data-confirm="Khôi phục giải thưởng này?">
                                    <i class="bi bi-arrow-counterclockwise"></i> Khôi phục
                                </button>
                            </form>
                            <form method="POST" action="/awards/hard-delete/<?= $award['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                        data-confirm="XÓA VĨNH VIỄN giải thưởng này? Không thể khôi phục!">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
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
