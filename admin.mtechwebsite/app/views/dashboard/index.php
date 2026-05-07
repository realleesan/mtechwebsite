<?php
// $stats, $recentContacts, $recentJobApps được truyền từ DashboardController
?>

<!-- Page Header -->
<div class="page-header">
    <h4><i class="bi bi-speedometer2 me-2"></i>Dashboard</h4>
    <span class="text-muted small"><?= date('d/m/Y H:i') ?></span>
</div>

<!-- ========== STATS CARDS ========== -->
<div class="row g-3 mb-4">

    <!-- Blogs -->
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-newspaper"></i>
                </div>
                <div>
                    <div class="stat-number text-primary"><?= $stats['total_blogs'] ?? 0 ?></div>
                    <div class="text-muted small">Tin tức</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Projects -->
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-building"></i>
                </div>
                <div>
                    <div class="stat-number text-success"><?= $stats['total_projects'] ?? 0 ?></div>
                    <div class="text-muted small">Dự án</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contacts -->
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-envelope"></i>
                </div>
                <div>
                    <div class="stat-number text-warning"><?= $stats['total_contacts'] ?? 0 ?></div>
                    <div class="text-muted small">
                        Liên hệ
                        <?php if (!empty($stats['new_contacts'])): ?>
                            <span class="badge bg-danger"><?= $stats['new_contacts'] ?> mới</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Job Applications -->
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-file-person"></i>
                </div>
                <div>
                    <div class="stat-number text-info"><?= $stats['total_jobs'] ?? 0 ?></div>
                    <div class="text-muted small">
                        Ứng tuyển
                        <?php if (!empty($stats['new_jobs'])): ?>
                            <span class="badge bg-warning text-dark"><?= $stats['new_jobs'] ?> mới</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ========== RECENT DATA ========== -->
<div class="row g-3">

    <!-- Recent Contacts -->
    <div class="col-12 col-lg-6">
        <div class="admin-table">
            <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-envelope me-2 text-warning"></i>Liên hệ mới nhất
                </h6>
                <a href="/contacts" class="btn btn-sm btn-outline-secondary">Xem tất cả</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Tên</th>
                            <th>Email</th>
                            <th>Thời gian</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recentContacts)): ?>
                            <?php foreach ($recentContacts as $contact): ?>
                                <tr>
                                    <td><?= htmlspecialchars($contact['name'] ?? '') ?></td>
                                    <td class="text-muted small"><?= htmlspecialchars($contact['email'] ?? '') ?></td>
                                    <td class="text-muted small">
                                        <?= isset($contact['created_at'])
                                            ? date('d/m H:i', strtotime($contact['created_at']))
                                            : '' ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">Chưa có liên hệ nào</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Job Applications -->
    <div class="col-12 col-lg-6">
        <div class="admin-table">
            <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-file-person me-2 text-info"></i>Đơn ứng tuyển mới nhất
                </h6>
                <a href="/job-applications" class="btn btn-sm btn-outline-secondary">Xem tất cả</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Họ tên</th>
                            <th>Vị trí</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recentJobApps)): ?>
                            <?php foreach ($recentJobApps as $app): ?>
                                <tr>
                                    <td><?= htmlspecialchars($app['full_name'] ?? '') ?></td>
                                    <td class="text-muted small"><?= htmlspecialchars($app['position'] ?? '') ?></td>
                                    <td>
                                        <?php
                                        $status = $app['status'] ?? 'pending';
                                        $badge = match($status) {
                                            'pending'  => 'warning',
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                            default    => 'secondary',
                                        };
                                        $label = match($status) {
                                            'pending'  => 'Chờ duyệt',
                                            'approved' => 'Đã duyệt',
                                            'rejected' => 'Từ chối',
                                            default    => $status,
                                        };
                                        ?>
                                        <span class="badge bg-<?= $badge ?>"><?= $label ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">Chưa có đơn ứng tuyển nào</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
