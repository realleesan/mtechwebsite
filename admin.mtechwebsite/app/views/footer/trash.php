<?php // $links, $pagination
$totalPages = ($pagination['total'] ?? 1);
$currentPage = ($pagination['current'] ?? 1);
$totalItems = ($pagination['total_items'] ?? 0);
?>
<div class="page-header">
    <h4><i class="bi bi-trash me-2"></i>Thùng rác</h4>
    <div class="d-flex gap-2">
        <a href="/footer" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Quay lại</a>
    </div>
</div>

<div class="admin-table">
    <div class="p-3 border-bottom"><span class="text-muted small">Tổng: <strong><?= $totalItems ?></strong> liên kết đã xóa</span></div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tiêu đề</th>
                    <th>URL</th>
                    <th>Ngày xóa</th>
                    <th style="width:180px">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($links)): foreach ($links as $link): ?>
                <tr>
                    <td class="text-muted small"><?= $link['id'] ?></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div>
                                <div class="fw-medium"><?= htmlspecialchars($link['title'] ?? '') ?></div>
                                <?php if (!$link['is_active']): ?>
                                    <small class="text-muted">Đã ẩn</small>
                                <?php else: ?>
                                    <small class="text-muted">Đang hiển thị</small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td>
                        <a href="<?= htmlspecialchars($link['url']) ?>" target="_blank" class="text-decoration-none text-muted">
                            <?= htmlspecialchars($link['url']) ?>
                        </a>
                    </td>
                    <td class="text-muted small">
                        <?= isset($link['deleted_at']) ? date('d/m/Y H:i', strtotime($link['deleted_at'])) : '' ?>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <form method="POST" action="/footer/restore/<?= $link['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-success" data-confirm="Khôi phục liên kết này?">
                                    <i class="bi bi-arrow-counterclockwise"></i> Khôi phục
                                </button>
                            </form>
                            <form method="POST" action="/footer/hard-delete/<?= $link['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger" data-confirm="XÓA VĨNH VIỄN liên kết này? Không thể khôi phục!">
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
