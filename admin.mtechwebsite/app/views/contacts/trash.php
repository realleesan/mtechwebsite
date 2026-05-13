<?php
// $contacts, $total, $currentPage, $totalPages
$totalPages  = $totalPages  ?? 1;
$currentPage = $currentPage ?? 1;
?>
<div class="page-header">
    <h4><i class="bi bi-trash me-2"></i>Thùng rác - Liên hệ</h4>
    <div class="d-flex gap-2">
        <a href="/contacts" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Quay lại
        </a>
    </div>
</div>

<div class="admin-table">
    <div class="p-3 border-bottom">
        <span class="text-muted small">Tổng: <strong><?= $total ?? 0 ?></strong> liên hệ đã xóa</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th style="width:50px">#</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>Điện thoại</th>
                    <th style="width:130px">Trạng thái</th>
                    <th style="width:140px">Ngày xóa</th>
                    <th style="width:180px">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($contacts)): foreach ($contacts as $c): ?>
                <tr>
                    <td class="text-muted small"><?= $c['id'] ?></td>
                    <td>
                        <div class="fw-medium"><?= htmlspecialchars($c['name'] ?? '') ?></div>
                        <?php if (!empty($c['subject'])): ?>
                            <small class="text-muted"><?= htmlspecialchars($c['subject']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td class="text-muted small"><?= htmlspecialchars($c['email'] ?? '—') ?></td>
                    <td class="text-muted small"><?= htmlspecialchars($c['phone'] ?? '—') ?></td>
                    <td>
                        <?php
                        $status = (int)($c['status'] ?? 0);
                        $badges = [
                            0 => '<span class="badge contact-badge-unread"><i class="bi bi-envelope me-1"></i>Chưa đọc</span>',
                            1 => '<span class="badge contact-badge-read"><i class="bi bi-envelope-open me-1"></i>Đã đọc</span>',
                            2 => '<span class="badge contact-badge-replied"><i class="bi bi-reply me-1"></i>Đã phản hồi</span>',
                        ];
                        echo $badges[$status] ?? '<span class="badge bg-secondary">—</span>';
                        ?>
                    </td>
                    <td class="text-muted small">
                        <?= isset($c['deleted_at']) ? date('d/m/Y H:i', strtotime($c['deleted_at'])) : '—' ?>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <form method="POST" action="/contacts/restore/<?= $c['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-success"
                                        data-confirm="Khôi phục liên hệ này?">
                                    <i class="bi bi-arrow-counterclockwise"></i> Khôi phục
                                </button>
                            </form>
                            <form method="POST" action="/contacts/hard-delete/<?= $c['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger btn-delete"
                                        data-confirm="XÓA VĨNH VIỄN liên hệ này? Không thể khôi phục!">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-3 d-block mb-2 opacity-50"></i>
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
