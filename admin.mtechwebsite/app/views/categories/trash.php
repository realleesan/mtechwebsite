<?php // $categories, $total, $currentPage, $totalPages
$totalPages  = $totalPages  ?? 1;
$currentPage = $currentPage ?? 1;
?>
<div class="page-header">
    <h4><i class="bi bi-trash me-2"></i>Thùng rác</h4>
    <div class="d-flex gap-2">
        <a href="/categories" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Quay lại</a>
    </div>
</div>

<div class="admin-table">
    <div class="p-3 border-bottom">
        <span class="text-muted small">Tổng: <strong><?= $total ?? 0 ?></strong> lĩnh vực đã xóa</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên lĩnh vực</th>
                    <th>Ngày xóa</th>
                    <th style="width:180px">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($categories)): foreach ($categories as $cat): ?>
                <tr>
                    <td class="text-muted small"><?= $cat['id'] ?></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <?php if (!empty($cat['image'])): ?>
                                <img src="<?= htmlspecialchars($cat['image']) ?>"
                                     width="40" height="40"
                                     style="object-fit:cover;border-radius:6px"
                                     onerror="this.style.display='none'">
                            <?php endif; ?>
                            <div>
                                <div class="fw-medium"><?= htmlspecialchars($cat['name'] ?? '') ?></div>
                                <small class="text-muted"><?= htmlspecialchars($cat['slug'] ?? '') ?></small>
                            </div>
                        </div>
                    </td>
                    <td class="text-muted small">
                        <?= isset($cat['deleted_at']) ? date('d/m/Y H:i', strtotime($cat['deleted_at'])) : '' ?>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <form method="POST" action="/categories/restore/<?= $cat['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-success"
                                        data-confirm="Khôi phục lĩnh vực này?">
                                    <i class="bi bi-arrow-counterclockwise"></i> Khôi phục
                                </button>
                            </form>
                            <form method="POST" action="/categories/hard-delete/<?= $cat['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                        data-confirm="XÓA VĨNH VIỄN lĩnh vực này? Không thể khôi phục!">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">
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
