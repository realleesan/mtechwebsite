<?php
// $contacts, $total, $currentPage, $totalPages, $trashedCount
$totalPages  = $totalPages  ?? 1;
$currentPage = $currentPage ?? 1;

if (!function_exists('contactStatusBadge')) {
    function contactStatusBadge($status) {
        return match((int)$status) {
            0 => '<span class="badge contact-badge-unread"><i class="bi bi-envelope me-1"></i>Chưa đọc</span>',
            1 => '<span class="badge contact-badge-read"><i class="bi bi-envelope-open me-1"></i>Đã đọc</span>',
            2 => '<span class="badge contact-badge-replied"><i class="bi bi-reply me-1"></i>Đã phản hồi</span>',
            default => '<span class="badge bg-secondary">Không xác định</span>',
        };
    }
}
?>
<div class="page-header">
    <h4><i class="bi bi-envelope me-2"></i>Quản lý Liên hệ</h4>
    <div class="d-flex gap-2">
        <a href="/contacts/trash" class="btn btn-warning">
            <i class="bi bi-trash me-1"></i>Thùng rác
        </a>
    </div>
</div>

<!-- Filter bar: chỉ lọc theo trạng thái -->
<div class="card mb-3 border-0 shadow-sm">
    <div class="card-body py-2">
        <form method="GET" action="/contacts" class="d-flex align-items-center gap-2 flex-wrap">
            <select class="form-select form-select-sm" name="status_filter" style="max-width:200px">
                <option value="">Tất cả trạng thái</option>
                <option value="0" <?= isset($statusFilter) && $statusFilter === '0' ? 'selected' : '' ?>>Chưa đọc</option>
                <option value="1" <?= isset($statusFilter) && $statusFilter === '1' ? 'selected' : '' ?>>Đã đọc</option>
                <option value="2" <?= isset($statusFilter) && $statusFilter === '2' ? 'selected' : '' ?>>Đã phản hồi</option>
            </select>
            <button type="submit" class="btn btn-sm btn-primary">Lọc</button>
            <?php if (isset($statusFilter) && $statusFilter !== ''): ?>
                <a href="/contacts" class="btn btn-sm btn-outline-secondary">Reset</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="admin-table">
    <div class="p-3 border-bottom d-flex align-items-center gap-3">
        <span class="text-muted small">Tổng: <strong><?= $total ?? 0 ?></strong> liên hệ</span>
        <?php if (!empty($unreadCount) && $unreadCount > 0): ?>
            <span class="badge contact-badge-unread"><i class="bi bi-envelope me-1"></i><?= $unreadCount ?> chưa đọc</span>
        <?php endif; ?>
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
                    <th style="width:130px">Thời gian</th>
                    <th style="width:120px">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($contacts)): foreach ($contacts as $c): ?>
                <tr class="<?= (int)($c['status'] ?? 0) === 0 ? 'contact-row-unread' : '' ?>">
                    <td class="text-muted small"><?= $c['id'] ?></td>
                    <td>
                        <span class="fw-medium <?= (int)($c['status'] ?? 0) === 0 ? 'text-dark' : '' ?>">
                            <?= htmlspecialchars($c['name'] ?? '') ?>
                        </span>
                        <?php if (!empty($c['subject'])): ?>
                            <div class="text-muted small text-truncate" style="max-width:200px">
                                <?= htmlspecialchars($c['subject']) ?>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td class="text-muted small"><?= htmlspecialchars($c['email'] ?? '') ?></td>
                    <td class="text-muted small"><?= htmlspecialchars($c['phone'] ?? '—') ?></td>
                    <td><?= contactStatusBadge($c['status'] ?? 0) ?></td>
                    <td class="text-muted small">
                        <?= isset($c['created_at']) ? date('d/m/Y H:i', strtotime($c['created_at'])) : '' ?>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="/contacts/view/<?= $c['id'] ?>"
                               class="btn btn-sm btn-outline-primary" title="Xem chi tiết">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="/contacts/edit/<?= $c['id'] ?>"
                               class="btn btn-sm btn-outline-secondary" title="Chỉnh sửa">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="/contacts/delete/<?= $c['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger btn-delete"
                                        data-confirm="Xóa liên hệ này?" title="Xóa">
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
                        Chưa có liên hệ nào
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
            <a class="page-link" href="?page=<?= $currentPage - 1 ?><?= isset($statusFilter) && $statusFilter !== '' ? '&status_filter='.urlencode($statusFilter) : '' ?>">
                <i class="bi bi-chevron-left"></i>
            </a>
        </li>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
            <a class="page-link" href="?page=<?= $i ?><?= isset($statusFilter) && $statusFilter !== '' ? '&status_filter='.urlencode($statusFilter) : '' ?>"><?= $i ?></a>
        </li>
        <?php endfor; ?>
        <?php if ($currentPage < $totalPages): ?>
        <li class="page-item">
            <a class="page-link" href="?page=<?= $currentPage + 1 ?><?= isset($statusFilter) && $statusFilter !== '' ? '&status_filter='.urlencode($statusFilter) : '' ?>">
                <i class="bi bi-chevron-right"></i>
            </a>
        </li>
        <?php endif; ?>
    </ul>
</nav>
<?php endif; ?>
