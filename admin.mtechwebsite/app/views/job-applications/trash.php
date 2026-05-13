<?php
// $applications, $total, $currentPage, $totalPages
$totalPages  = $totalPages  ?? 1;
$currentPage = $currentPage ?? 1;

$statusMap = [
    'pending'   => ['label' => 'Chờ duyệt',    'badge' => 'bg-warning text-dark', 'icon' => 'bi-hourglass-split'],
    'reviewing' => ['label' => 'Đang xem xét', 'badge' => 'bg-info text-dark',    'icon' => 'bi-eye'],
    'approved'  => ['label' => 'Đã duyệt',     'badge' => 'bg-success',           'icon' => 'bi-check-circle'],
    'rejected'  => ['label' => 'Từ chối',      'badge' => 'bg-danger',            'icon' => 'bi-x-circle'],
];
?>
<div class="page-header">
    <h4><i class="bi bi-trash me-2"></i>Thùng rác - Đơn ứng tuyển</h4>
    <div class="d-flex gap-2">
        <a href="/job-applications" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Quay lại
        </a>
    </div>
</div>

<div class="admin-table">
    <div class="p-3 border-bottom">
        <span class="text-muted small">Tổng: <strong><?= $total ?? 0 ?></strong> đơn đã xóa</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th style="width:50px">#</th>
                    <th>Họ tên</th>
                    <th>Vị trí ứng tuyển</th>
                    <th>Email</th>
                    <th style="width:140px">Trạng thái</th>
                    <th style="width:140px">Ngày xóa</th>
                    <th style="width:180px">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($applications)): foreach ($applications as $app): ?>
                <?php
                    $s    = $app['status'] ?? 'pending';
                    $info = $statusMap[$s] ?? $statusMap['pending'];
                ?>
                <tr>
                    <td class="text-muted small"><?= $app['id'] ?></td>
                    <td>
                        <div class="fw-medium"><?= htmlspecialchars($app['full_name'] ?? '') ?></div>
                    </td>
                    <td class="text-muted small"><?= htmlspecialchars($app['position'] ?? '—') ?></td>
                    <td class="text-muted small"><?= htmlspecialchars($app['email'] ?? '—') ?></td>
                    <td>
                        <span class="badge <?= $info['badge'] ?>">
                            <i class="bi <?= $info['icon'] ?> me-1"></i><?= $info['label'] ?>
                        </span>
                    </td>
                    <td class="text-muted small">
                        <?= isset($app['deleted_at']) ? date('d/m/Y H:i', strtotime($app['deleted_at'])) : '—' ?>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <form method="POST" action="/job-applications/restore/<?= $app['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-success"
                                        data-confirm="Khôi phục đơn ứng tuyển này?">
                                    <i class="bi bi-arrow-counterclockwise"></i> Khôi phục
                                </button>
                            </form>
                            <form method="POST" action="/job-applications/hard-delete/<?= $app['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger btn-delete"
                                        data-confirm="XÓA VĨNH VIỄN đơn này? Không thể khôi phục!">
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
