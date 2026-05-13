<?php // $logos, $total, $currentPage, $totalPages
$totalPages  = $totalPages  ?? 1;
$currentPage = $currentPage ?? 1;
?>
<div class="page-header">
    <h4><i class="bi bi-trash me-2"></i>Thùng rác</h4>
    <div class="d-flex gap-2">
        <a href="/client-logos" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Quay lại</a>
    </div>
</div>

<div class="admin-table">
    <div class="p-3 border-bottom">
        <span class="text-muted small">Tổng: <strong><?= $total ?? 0 ?></strong> logo đối tác đã xóa</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle client-logos-trash-table">
            <thead>
                <tr>
                    <th style="width:60px">#</th>
                    <th style="width:80px">Logo</th>
                    <th>Tên đối tác</th>
                    <th>Link website</th>
                    <th>Ngày xóa</th>
                    <th style="width:180px">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($logos)): foreach ($logos as $logo): ?>
                <tr>
                    <td class="text-muted small"><?= $logo['id'] ?></td>
                    <td>
                        <div class="client-logo-thumb">
                            <?php if (!empty($logo['logo'])): ?>
                                <img src="<?= htmlspecialchars($logo['logo']) ?>"
                                     alt="<?= htmlspecialchars($logo['name'] ?? '') ?>"
                                     onerror="this.src='https://via.placeholder.com/80x40?text=Logo'">
                            <?php else: ?>
                                <div class="client-logo-thumb-empty">
                                    <i class="bi bi-image text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <span class="fw-medium"><?= htmlspecialchars($logo['name'] ?? '') ?></span>
                    </td>
                    <td>
                        <?php if (!empty($logo['url'])): ?>
                            <a href="<?= htmlspecialchars($logo['url']) ?>" target="_blank" rel="noopener"
                               class="text-decoration-none text-muted small">
                                <i class="bi bi-box-arrow-up-right me-1"></i>
                                <?= htmlspecialchars(parse_url($logo['url'], PHP_URL_HOST) ?: $logo['url']) ?>
                            </a>
                        <?php else: ?>
                            <span class="text-muted small">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="deleted-date">
                        <?= isset($logo['deleted_at']) ? date('d/m/Y H:i', strtotime($logo['deleted_at'])) : '' ?>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <form method="POST" action="/client-logos/restore/<?= $logo['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-success"
                                        data-confirm="Khôi phục logo đối tác này?">
                                    <i class="bi bi-arrow-counterclockwise"></i> Khôi phục
                                </button>
                            </form>
                            <form method="POST" action="/client-logos/hard-delete/<?= $logo['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                        data-confirm="XÓA VĨNH VIỄN logo đối tác này? Không thể khôi phục!">
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
