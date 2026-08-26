<?php
if (!isset($application) || empty($application)) {
    header('Location: /job-applications'); exit;
}

$statusMap = [
    'pending'  => ['label' => 'Chờ duyệt', 'badge' => 'bg-warning text-dark', 'icon' => 'bi-hourglass-split'],
    'approved' => ['label' => 'Đã duyệt',  'badge' => 'bg-success',           'icon' => 'bi-check-circle'],
    'rejected' => ['label' => 'Từ chối',   'badge' => 'bg-danger',            'icon' => 'bi-x-circle'],
];
$status     = $application['status'] ?? 'pending';
$statusInfo = $statusMap[$status] ?? $statusMap['pending'];
?>
<div class="page-header">
    <h4><i class="bi bi-file-person me-2"></i>Chi tiết đơn ứng tuyển #<?= $application['id'] ?></h4>
    <div class="d-flex gap-2">
        <a href="/job-applications/edit/<?= $application['id'] ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-pencil me-1"></i>Cập nhật trạng thái
        </a>
        <a href="/job-applications" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Quay lại
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Thông tin chính -->
    <div class="col-lg-8">
        <div class="admin-form-card">
            <h6 class="fw-semibold mb-3 pb-2 border-bottom">
                <i class="bi bi-person me-2 text-primary"></i>Thông tin ứng viên
            </h6>
            <div class="row g-3">
                <div class="col-sm-6">
                    <div class="contact-detail-item">
                        <div class="contact-detail-label"><i class="bi bi-person-fill me-1"></i>Họ và tên</div>
                        <div class="contact-detail-value fw-medium"><?= htmlspecialchars($application['full_name'] ?? '—') ?></div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="contact-detail-item">
                        <div class="contact-detail-label"><i class="bi bi-briefcase-fill me-1"></i>Vị trí ứng tuyển</div>
                        <div class="contact-detail-value"><?= htmlspecialchars($application['position'] ?? '—') ?></div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="contact-detail-item">
                        <div class="contact-detail-label"><i class="bi bi-envelope-fill me-1"></i>Email</div>
                        <div class="contact-detail-value">
                            <a href="mailto:<?= htmlspecialchars($application['email'] ?? '') ?>" class="text-decoration-none">
                                <?= htmlspecialchars($application['email'] ?? '—') ?>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="contact-detail-item">
                        <div class="contact-detail-label"><i class="bi bi-telephone-fill me-1"></i>Điện thoại</div>
                        <div class="contact-detail-value">
                            <?php if (!empty($application['phone'])): ?>
                                <a href="tel:<?= htmlspecialchars($application['phone']) ?>" class="text-decoration-none">
                                    <?= htmlspecialchars($application['phone']) ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- File CV -->
            <?php if (!empty($application['cv_file'])): ?>
            <div class="mt-3 pt-3 border-top">
                <div class="contact-detail-label mb-2">
                    <i class="bi bi-file-earmark-pdf-fill me-1 text-danger"></i>File CV đính kèm
                </div>
                <a href="/job-applications/download-cv/<?= $application['id'] ?>"
                   class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-download me-1"></i>Tải xuống CV
                </a>
            </div>
            <?php endif; ?>

            <!-- Thư xin việc / Nội dung -->
            <?php if (!empty($application['cover_letter'])): ?>
            <div class="mt-3 pt-3 border-top">
                <div class="contact-detail-label mb-2">
                    <i class="bi bi-card-text me-1"></i>Thư xin việc / Nội dung
                </div>
                <div class="contact-message-box">
                    <?= nl2br(htmlspecialchars($application['cover_letter'])) ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Phản hồi từ nhà tuyển dụng -->
            <?php if (!empty($application['employer_reply'])): ?>
            <div class="mt-3 pt-3 border-top">
                <div class="contact-detail-label mb-2">
                    <i class="bi bi-reply-fill me-1 text-success"></i>Phản hồi từ nhà tuyển dụng
                </div>
                <div class="contact-reply-box reply-employer">
                    <?= nl2br(htmlspecialchars($application['employer_reply'])) ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Ghi chú nội bộ -->
            <?php if (!empty($application['admin_note'])): ?>
            <div class="mt-3 pt-3 border-top">
                <div class="contact-detail-label mb-2">
                    <i class="bi bi-sticky-fill me-1 text-warning"></i>Ghi chú nội bộ
                </div>
                <div class="contact-reply-box">
                    <?= nl2br(htmlspecialchars($application['admin_note'])) ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Thông tin hệ thống -->
    <div class="col-lg-4">
        <div class="admin-form-card">
            <h6 class="fw-semibold mb-3 pb-2 border-bottom">
                <i class="bi bi-info-circle me-2 text-primary"></i>Thông tin hệ thống
            </h6>

            <div class="contact-detail-item mb-3">
                <div class="contact-detail-label">Trạng thái xét duyệt</div>
                <div class="mt-1">
                    <span class="badge <?= $statusInfo['badge'] ?>">
                        <i class="bi <?= $statusInfo['icon'] ?> me-1"></i><?= $statusInfo['label'] ?>
                    </span>
                </div>
            </div>

            <div class="contact-detail-item mb-3">
                <div class="contact-detail-label"><i class="bi bi-clock me-1"></i>Thời gian nộp đơn</div>
                <div class="contact-detail-value small">
                    <?= isset($application['created_at']) ? date('d/m/Y H:i:s', strtotime($application['created_at'])) : '—' ?>
                </div>
            </div>

            <div class="contact-detail-item mb-3">
                <div class="contact-detail-label"><i class="bi bi-arrow-clockwise me-1"></i>Cập nhật lần cuối</div>
                <div class="contact-detail-value small">
                    <?= isset($application['updated_at']) ? date('d/m/Y H:i:s', strtotime($application['updated_at'])) : '—' ?>
                </div>
            </div>

            <?php if (!empty($application['ip_address'])): ?>
            <div class="contact-detail-item mb-3">
                <div class="contact-detail-label"><i class="bi bi-globe me-1"></i>Địa chỉ IP</div>
                <div class="contact-detail-value font-monospace small">
                    <?= htmlspecialchars($application['ip_address']) ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Thao tác nhanh -->
        <div class="admin-form-card mt-3">
            <h6 class="fw-semibold mb-3 pb-2 border-bottom">
                <i class="bi bi-lightning me-2 text-warning"></i>Thao tác nhanh
            </h6>
            <div class="d-grid gap-2">
                <a href="/job-applications/edit/<?= $application['id'] ?>" class="btn btn-primary btn-sm">
                    <i class="bi bi-pencil me-2"></i>Cập nhật trạng thái
                </a>
                <form method="POST" action="/job-applications/delete/<?= $application['id'] ?>">
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100 btn-delete"
                            data-confirm="Xóa đơn ứng tuyển này?">
                        <i class="bi bi-trash me-2"></i>Xóa đơn
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
