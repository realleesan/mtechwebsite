<?php
// $applications, $total, $pageNum, $totalPages, $statusFilter
$totalPages = max(1, (int)($totalPages ?? 1));
$pageNum    = max(1, (int)($pageNum    ?? 1));
$total      = (int)($total ?? 0);
$perPage    = 15;

if ($totalPages <= 1 && $total > $perPage) {
    $totalPages = (int)ceil($total / $perPage);
}

if (!function_exists('jobAppStatusBadge')) {
    function jobAppStatusBadge($status) {
        return match($status) {
            'approved' => '<span class="badge job-badge-approved">Đã duyệt</span>',
            'rejected' => '<span class="badge job-badge-rejected">Từ chối</span>',
            default    => '<span class="badge job-badge-pending">Chờ duyệt</span>',
        };
    }
}
?>
<div class="page-header">
    <h4><i class="bi bi-file-person me-2"></i>Quản lý Đơn ứng tuyển</h4>
    <div class="d-flex gap-2">
        <a href="/job-applications/trash" class="btn btn-warning">
            <i class="bi bi-trash me-1"></i>Thùng rác
        </a>
    </div>
</div>

<!-- Filter bar -->
<div class="card mb-3 border-0 shadow-sm">
    <div class="card-body py-2">
        <form method="GET" action="/job-applications" class="d-flex align-items-center gap-2 flex-wrap">
            <select class="form-select form-select-sm" name="status_filter" style="max-width:200px">
                <option value="">Tất cả trạng thái</option>
                <option value="pending"  <?= isset($statusFilter) && $statusFilter === 'pending'  ? 'selected' : '' ?>>Chờ duyệt</option>
                <option value="approved" <?= isset($statusFilter) && $statusFilter === 'approved' ? 'selected' : '' ?>>Đã duyệt</option>
                <option value="rejected" <?= isset($statusFilter) && $statusFilter === 'rejected' ? 'selected' : '' ?>>Từ chối</option>
            </select>
            <button type="submit" class="btn btn-sm btn-primary">Lọc</button>
            <?php if (isset($statusFilter) && $statusFilter !== ''): ?>
                <a href="/job-applications" class="btn btn-sm btn-outline-secondary">Reset</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="admin-table">
    <div class="p-3 border-bottom d-flex align-items-center gap-3">
        <span class="text-muted small">Tổng: <strong><?= $total ?></strong> đơn ứng tuyển
            <?php if ($totalPages > 1): ?>
                &nbsp;·&nbsp; Trang <?= $pageNum ?>/<?= $totalPages ?>
            <?php endif; ?>
        </span>
        <?php if (!empty($pendingCount) && $pendingCount > 0): ?>
            <span class="badge job-badge-pending"><?= $pendingCount ?> chờ duyệt</span>
        <?php endif; ?>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th style="width:50px">#</th>
                    <th>Họ tên</th>
                    <th>Vị trí ứng tuyển</th>
                    <th>Email</th>
                    <th style="width:120px">Trạng thái</th>
                    <th style="width:130px">Thời gian nộp</th>
                    <th style="width:120px">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($applications)): foreach ($applications as $app): ?>
                <tr>
                    <td class="text-muted small"><?= $app['id'] ?></td>
                    <td>
                        <span class="fw-medium"><?= htmlspecialchars($app['full_name'] ?? '') ?></span>
                    </td>
                    <td class="text-muted small"><?= htmlspecialchars($app['position'] ?? '—') ?></td>
                    <td class="text-muted small">
                        <a href="mailto:<?= htmlspecialchars($app['email'] ?? '') ?>" class="text-decoration-none">
                            <?= htmlspecialchars($app['email'] ?? '—') ?>
                        </a>
                    </td>
                    <td><?= jobAppStatusBadge($app['status'] ?? 'pending') ?></td>
                    <td class="text-muted small">
                        <?= isset($app['created_at']) ? date('d/m/Y H:i', strtotime($app['created_at'])) : '—' ?>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="/job-applications/view/<?= $app['id'] ?>"
                               class="btn btn-sm btn-outline-primary" title="Xem chi tiết">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="/job-applications/edit/<?= $app['id'] ?>"
                               class="btn btn-sm btn-outline-secondary" title="Chỉnh sửa trạng thái">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="/job-applications/delete/<?= $app['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger btn-delete"
                                        data-confirm="Xóa đơn ứng tuyển này?" title="Xóa">
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
                        Chưa có đơn ứng tuyển nào
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

        <?php
        $queryBase = [];
        if (isset($statusFilter) && $statusFilter !== '') {
            $queryBase['status_filter'] = $statusFilter;
        }
        ?>

        <?php if ($pageNum > 1): ?>
        <li class="page-item">
            <a class="page-link" href="/job-applications?<?= http_build_query(array_merge($queryBase, ['page' => $pageNum - 1])) ?>">
                <i class="bi bi-chevron-left"></i>
            </a>
        </li>
        <?php else: ?>
        <li class="page-item disabled">
            <span class="page-link"><i class="bi bi-chevron-left"></i></span>
        </li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?= $i === $pageNum ? 'active' : '' ?>">
            <a class="page-link" href="/job-applications?<?= http_build_query(array_merge($queryBase, ['page' => $i])) ?>">
                <?= $i ?>
            </a>
        </li>
        <?php endfor; ?>

        <?php if ($pageNum < $totalPages): ?>
        <li class="page-item">
            <a class="page-link" href="/job-applications?<?= http_build_query(array_merge($queryBase, ['page' => $pageNum + 1])) ?>">
                <i class="bi bi-chevron-right"></i>
            </a>
        </li>
        <?php else: ?>
        <li class="page-item disabled">
            <span class="page-link"><i class="bi bi-chevron-right"></i></span>
        </li>
        <?php endif; ?>

    </ul>
</nav>
<?php endif; ?>
