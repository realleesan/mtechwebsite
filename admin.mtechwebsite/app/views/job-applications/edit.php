<?php
if (!isset($application) || empty($application)) {
    header('Location: /job-applications'); exit;
}

$statusOptions = [
    'pending'  => ['label' => 'Chờ duyệt', 'icon' => 'bi-hourglass-split', 'color' => 'warning'],
    'approved' => ['label' => 'Đã duyệt',  'icon' => 'bi-check-circle',    'color' => 'success'],
    'rejected' => ['label' => 'Từ chối',   'icon' => 'bi-x-circle',        'color' => 'danger'],
];
$currentStatus = $application['status'] ?? 'pending';
?>
<div class="page-header">
    <h4><i class="bi bi-pencil me-2"></i>Cập nhật trạng thái đơn #<?= $application['id'] ?></h4>
    <div class="d-flex gap-2">
        <a href="/job-applications/view/<?= $application['id'] ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-eye me-1"></i>Xem chi tiết
        </a>
        <a href="/job-applications" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Quay lại
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Form cập nhật -->
    <div class="col-lg-8">
        <form method="POST" action="/job-applications/update/<?= $application['id'] ?>" id="jobAppEditForm">
            <div class="admin-form-card">
                <h6 class="fw-semibold mb-3 pb-2 border-bottom">
                    <i class="bi bi-sliders me-2 text-primary"></i>Cập nhật trạng thái xét duyệt
                </h6>

                <!-- Trạng thái -->
                <div class="mb-4">
                    <label class="form-label fw-medium">Trạng thái đơn ứng tuyển</label>
                    <div class="job-status-options">
                        <label class="job-status-option <?= $currentStatus === 'pending'  ? 'active' : '' ?>" id="opt-pending">
                            <input type="radio" name="status" value="pending"
                                   <?= $currentStatus === 'pending'  ? 'checked' : '' ?> class="d-none">
                            <span class="badge job-badge-pending w-100 py-2">
                                <i class="bi bi-hourglass-split me-1"></i>Chờ duyệt
                            </span>
                        </label>
                        <label class="job-status-option <?= $currentStatus === 'approved' ? 'active' : '' ?>" id="opt-approved">
                            <input type="radio" name="status" value="approved"
                                   <?= $currentStatus === 'approved' ? 'checked' : '' ?> class="d-none">
                            <span class="badge job-badge-approved w-100 py-2">
                                <i class="bi bi-check-circle me-1"></i>Đã duyệt
                            </span>
                        </label>
                        <label class="job-status-option <?= $currentStatus === 'rejected' ? 'active' : '' ?>" id="opt-rejected">
                            <input type="radio" name="status" value="rejected"
                                   <?= $currentStatus === 'rejected' ? 'checked' : '' ?> class="d-none">
                            <span class="badge job-badge-rejected w-100 py-2">
                                <i class="bi bi-x-circle me-1"></i>Từ chối
                            </span>
                        </label>
                    </div>
                    <div class="form-text mt-2">
                        Khi chuyển sang <strong>Đã duyệt</strong> hoặc <strong>Từ chối</strong>, hệ thống sẽ tự động gửi email thông báo đến ứng viên.
                    </div>
                </div>

                <!-- Phản hồi từ nhà tuyển dụng -->
                <div class="mb-4">
                    <label for="employer_reply" class="form-label fw-medium">
                        <i class="bi bi-reply me-1 text-success"></i>Phản hồi từ nhà tuyển dụng
                        <span class="text-muted fw-normal small">(không bắt buộc)</span>
                    </label>
                    <textarea class="form-control" id="employer_reply" name="employer_reply"
                              rows="5"
                              placeholder="Nhập nội dung phản hồi gửi đến ứng viên...&#10;&#10;Ví dụ: Chúng tôi sẽ liên hệ để sắp xếp lịch phỏng vấn trong 3-5 ngày làm việc."><?= htmlspecialchars($application['employer_reply'] ?? '') ?></textarea>
                    <div class="form-text">
                        Nội dung này sẽ được gửi kèm trong email thông báo đến ứng viên (nếu có).
                    </div>
                </div>

                <!-- Ghi chú nội bộ -->
                <div class="mb-4">
                    <label for="admin_note" class="form-label fw-medium">
                        <i class="bi bi-sticky me-1 text-warning"></i>Ghi chú nội bộ
                        <span class="text-muted fw-normal small">(không hiển thị cho ứng viên)</span>
                    </label>
                    <textarea class="form-control" id="admin_note" name="admin_note"
                              rows="3"
                              placeholder="Ghi chú nội bộ về ứng viên này..."><?= htmlspecialchars($application['admin_note'] ?? '') ?></textarea>
                </div>

                <!-- Form Actions -->
                <div class="d-flex justify-content-between pt-3 border-top">
                    <a href="/job-applications" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg me-2"></i>Hủy
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-2"></i>Lưu thay đổi
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Thông tin ứng viên (readonly) -->
    <div class="col-lg-4">
        <div class="admin-form-card">
            <h6 class="fw-semibold mb-3 pb-2 border-bottom">
                <i class="bi bi-person me-2 text-primary"></i>Thông tin ứng viên
            </h6>

            <div class="contact-detail-item mb-3">
                <div class="contact-detail-label"><i class="bi bi-person-fill me-1"></i>Họ và tên</div>
                <div class="contact-detail-value fw-medium"><?= htmlspecialchars($application['full_name'] ?? '—') ?></div>
            </div>

            <div class="contact-detail-item mb-3">
                <div class="contact-detail-label"><i class="bi bi-briefcase-fill me-1"></i>Vị trí ứng tuyển</div>
                <div class="contact-detail-value"><?= htmlspecialchars($application['position'] ?? '—') ?></div>
            </div>

            <div class="contact-detail-item mb-3">
                <div class="contact-detail-label"><i class="bi bi-envelope-fill me-1"></i>Email</div>
                <div class="contact-detail-value">
                    <a href="mailto:<?= htmlspecialchars($application['email'] ?? '') ?>" class="text-decoration-none">
                        <?= htmlspecialchars($application['email'] ?? '—') ?>
                    </a>
                </div>
            </div>

            <?php if (!empty($application['phone'])): ?>
            <div class="contact-detail-item mb-3">
                <div class="contact-detail-label"><i class="bi bi-telephone-fill me-1"></i>Điện thoại</div>
                <div class="contact-detail-value">
                    <a href="tel:<?= htmlspecialchars($application['phone']) ?>" class="text-decoration-none">
                        <?= htmlspecialchars($application['phone']) ?>
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($application['cv_file'])): ?>
            <div class="contact-detail-item mb-3">
                <div class="contact-detail-label"><i class="bi bi-file-earmark-pdf-fill me-1 text-danger"></i>File CV</div>
                <div class="contact-detail-value">
                    <a href="/job-applications/download-cv/<?= $application['id'] ?>"
                       class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-download me-1"></i>Tải CV
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <div class="contact-detail-item mb-3">
                <div class="contact-detail-label"><i class="bi bi-clock me-1"></i>Thời gian nộp</div>
                <div class="contact-detail-value small">
                    <?= isset($application['created_at']) ? date('d/m/Y H:i', strtotime($application['created_at'])) : '—' ?>
                </div>
            </div>
        </div>
    </div>
</div>
