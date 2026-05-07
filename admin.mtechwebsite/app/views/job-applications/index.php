<?php // $applications, $total, $currentPage, $totalPages ?>
<div class="page-header">
    <h4><i class="bi bi-file-person me-2"></i>Quản lý Đơn ứng tuyển</h4>
</div>
<div class="admin-table">
    <div class="p-3 border-bottom"><span class="text-muted small">Tổng: <strong><?= $total ?? 0 ?></strong> đơn</span></div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>#</th><th>Họ tên</th><th>Vị trí</th><th>Email</th><th>Trạng thái</th><th>Ngày nộp</th><th style="width:80px">Xem</th></tr></thead>
            <tbody>
                <?php if (!empty($applications)): foreach ($applications as $app): ?>
                <?php
                    $status = $app['status'] ?? 'pending';
                    $badge  = match($status) { 'approved' => 'success', 'rejected' => 'danger', default => 'warning' };
                    $label  = match($status) { 'approved' => 'Đã duyệt', 'rejected' => 'Từ chối', default => 'Chờ duyệt' };
                ?>
                <tr>
                    <td class="text-muted small"><?= $app['id'] ?></td>
                    <td class="fw-medium"><?= htmlspecialchars($app['full_name'] ?? '') ?></td>
                    <td class="text-muted small"><?= htmlspecialchars($app['position'] ?? '') ?></td>
                    <td class="text-muted small"><?= htmlspecialchars($app['email'] ?? '') ?></td>
                    <td><span class="badge bg-<?= $badge ?>"><?= $label ?></span></td>
                    <td class="text-muted small"><?= isset($app['created_at']) ? date('d/m/Y', strtotime($app['created_at'])) : '' ?></td>
                    <td><a href="/job-applications/view/<?= $app['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a></td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="7" class="text-center text-muted py-4"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Chưa có đơn ứng tuyển nào</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
