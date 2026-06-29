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

<!-- ========== Thống kê truy cập ========== -->
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="mb-0 fw-bold text-muted">
                        <i class="bi bi-graph-up me-2"></i>Thống kê truy cập
                    </h6>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-primary active" data-period="7days">7 ngày</button>
                        <button type="button" class="btn btn-outline-primary" data-period="month">1 tháng</button>
                        <button type="button" class="btn btn-outline-primary" data-period="year">1 năm</button>
                        <button type="button" class="btn btn-outline-primary" data-period="all">Tất cả</button>
                    </div>
                </div>
                
                <!-- Stats Cards -->
                <div class="row text-center mb-4">
                    <div class="col-4">
                        <div class="d-flex flex-column align-items-center">
                            <div class="stat-icon bg-info bg-opacity-10 text-info mb-2">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <div class="stat-number text-info" id="today-visits"><?= $access_stats['today']['visits'] ?? 0 ?></div>
                            <div class="text-muted small">Hôm nay</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="d-flex flex-column align-items-center">
                            <div class="stat-icon bg-warning bg-opacity-10 text-warning mb-2">
                                <i class="bi bi-calendar-week"></i>
                            </div>
                            <div class="stat-number text-warning" id="month-visits"><?= $access_stats['month']['visits'] ?? 0 ?></div>
                            <div class="text-muted small">Tháng này</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="d-flex flex-column align-items-center">
                            <div class="stat-icon bg-success bg-opacity-10 text-success mb-2">
                                <i class="bi bi-graph-up"></i>
                            </div>
                            <div class="stat-number text-success" id="total-visits"><?= $access_stats['total']['total'] ?? 0 ?></div>
                            <div class="text-muted small">Tổng truy cập</div>
                        </div>
                    </div>
                </div>
                
                <!-- Chart Container -->
                <div class="chart-container" style="position: relative; height: 300px;">
                    <canvas id="accessChart"></canvas>
                </div>
                
                <!-- Loading Spinner -->
                <div id="chart-loading" class="text-center py-4" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                    <div class="text-muted small mt-2">Đang tải dữ liệu...</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========== RECENT DATA ========== -->
<div class="row g-3 mb-4">

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
    <!-- Recent News -->
    <div class="col-12 col-lg-6">
        <div class="admin-table">
            <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-newspaper me-2 text-primary"></i>Tin tức mới nhất
                </h6>
                <a href="/blogs" class="btn btn-sm btn-outline-secondary">Xem tất cả</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Tiêu đề</th>
                            <th>Ngày đăng</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recentBlogs)): ?>
                            <?php foreach ($recentBlogs as $blog): ?>
                                <tr>
                                    <td>
                                        <a href="/blogs/edit?id=<?= $blog['id'] ?>" class="text-decoration-none">
                                            <?= htmlspecialchars(mb_substr($blog['title'] ?? '', 0, 40)) ?>
                                            <?= mb_strlen($blog['title'] ?? '') > 40 ? '...' : '' ?>
                                        </a>
                                    </td>
                                    <td class="text-muted small">
                                        <?= isset($blog['created_at'])
                                            ? date('d/m H:i', strtotime($blog['created_at']))
                                            : '' ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">Đã đăng</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">Chưa có tin tức nào</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Projects -->
    <div class="col-12 col-lg-6">
        <div class="admin-table">
            <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-building me-2 text-success"></i>Dự án mới nhất
                </h6>
                <a href="/projects" class="btn btn-sm btn-outline-secondary">Xem tất cả</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Tên dự án</th>
                            <th>Ngày tạo</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recentProjects)): ?>
                            <?php foreach ($recentProjects as $project): ?>
                                <tr>
                                    <td>
                                        <a href="/projects/edit?id=<?= $project['id'] ?>" class="text-decoration-none">
                                            <?= htmlspecialchars(mb_substr($project['title'] ?? '', 0, 40)) ?>
                                            <?= mb_strlen($project['title'] ?? '') > 40 ? '...' : '' ?>
                                        </a>
                                    </td>
                                    <td class="text-muted small">
                                        <?= isset($project['created_at'])
                                            ? date('d/m H:i', strtotime($project['created_at']))
                                            : '' ?>
                                    </td>
                                    <td>
                                        <?php
                                        $status = $project['status'] ?? 1;
                                        $badge = match($status) {
                                            1 => 'success',
                                            0 => 'secondary',
                                            2 => 'warning',
                                            default => 'secondary',
                                        };
                                        $label = match($status) {
                                            1 => 'Hoạt động',
                                            0 => 'Ẩn',
                                            2 => 'Nổi bật',
                                            default => 'Không rõ',
                                        };
                                        ?>
                                        <span class="badge bg-<?= $badge ?>"><?= $label ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">Chưa có dự án nào</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
