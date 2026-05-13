<?php
if (!isset($contact) || empty($contact)) {
    header('Location: /contacts'); exit;
}

$statusMap = [
    0 => 'Chưa đọc',
    1 => 'Đã đọc',
    2 => 'Đã phản hồi',
];
$currentStatus = (int)($contact['status'] ?? 0);
?>
<div class="page-header">
    <h4><i class="bi bi-pencil me-2"></i>Chỉnh sửa liên hệ #<?= $contact['id'] ?></h4>
    <div class="d-flex gap-2">
        <a href="/contacts/view/<?= $contact['id'] ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-eye me-1"></i>Xem chi tiết
        </a>
        <a href="/contacts" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Quay lại
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Form chỉnh sửa -->
    <div class="col-lg-8">
        <form method="POST" action="/contacts/update/<?= $contact['id'] ?>" id="contactEditForm">
            <div class="admin-form-card">
                <h6 class="fw-semibold mb-3 pb-2 border-bottom">
                    <i class="bi bi-sliders me-2 text-primary"></i>Cập nhật trạng thái & Phản hồi
                </h6>

                <!-- Trạng thái -->
                <div class="mb-4">
                    <label class="form-label fw-medium">Trạng thái liên hệ</label>
                    <div class="contact-status-options">
                        <label class="contact-status-option <?= $currentStatus === 0 ? 'active' : '' ?>" id="opt-0">
                            <input type="radio" name="status" value="0"
                                   <?= $currentStatus === 0 ? 'checked' : '' ?> class="d-none">
                            <span class="badge contact-badge-unread w-100 py-2">
                                <i class="bi bi-envelope me-1"></i>Chưa đọc
                            </span>
                        </label>
                        <label class="contact-status-option <?= $currentStatus === 1 ? 'active' : '' ?>" id="opt-1">
                            <input type="radio" name="status" value="1"
                                   <?= $currentStatus === 1 ? 'checked' : '' ?> class="d-none">
                            <span class="badge contact-badge-read w-100 py-2">
                                <i class="bi bi-envelope-open me-1"></i>Đã đọc
                            </span>
                        </label>
                        <label class="contact-status-option <?= $currentStatus === 2 ? 'active' : '' ?>" id="opt-2">
                            <input type="radio" name="status" value="2"
                                   <?= $currentStatus === 2 ? 'checked' : '' ?> class="d-none">
                            <span class="badge contact-badge-replied w-100 py-2">
                                <i class="bi bi-reply me-1"></i>Đã phản hồi
                            </span>
                        </label>
                    </div>
                    <div class="form-text">Khi nhập nội dung phản hồi và lưu, trạng thái sẽ tự động chuyển thành <strong>Đã phản hồi</strong>.</div>
                </div>

                <!-- Phản hồi admin -->
                <div class="mb-4">
                    <label for="admin_reply" class="form-label fw-medium">
                        <i class="bi bi-reply me-1 text-success"></i>Nội dung phản hồi
                    </label>
                    <textarea class="form-control" id="admin_reply" name="admin_reply"
                              rows="6"
                              placeholder="Nhập nội dung phản hồi cho khách hàng...&#10;&#10;Lưu ý: Khi có nội dung phản hồi, trạng thái sẽ tự động chuyển thành 'Đã phản hồi'."><?= htmlspecialchars($contact['admin_reply'] ?? '') ?></textarea>
                    <div class="form-text">
                        Nội dung này dùng để ghi chú nội bộ hoặc lưu lại phản hồi đã gửi cho khách.
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="d-flex justify-content-between pt-3 border-top">
                    <a href="/contacts" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg me-2"></i>Hủy
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-2"></i>Lưu thay đổi
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Thông tin người gửi (readonly) -->
    <div class="col-lg-4">
        <div class="admin-form-card">
            <h6 class="fw-semibold mb-3 pb-2 border-bottom">
                <i class="bi bi-person me-2 text-primary"></i>Thông tin người gửi
            </h6>

            <div class="contact-detail-item mb-3">
                <div class="contact-detail-label"><i class="bi bi-person-fill me-1"></i>Họ và tên</div>
                <div class="contact-detail-value fw-medium"><?= htmlspecialchars($contact['name'] ?? '—') ?></div>
            </div>

            <div class="contact-detail-item mb-3">
                <div class="contact-detail-label"><i class="bi bi-envelope-fill me-1"></i>Email</div>
                <div class="contact-detail-value">
                    <a href="mailto:<?= htmlspecialchars($contact['email'] ?? '') ?>" class="text-decoration-none">
                        <?= htmlspecialchars($contact['email'] ?? '—') ?>
                    </a>
                </div>
            </div>

            <div class="contact-detail-item mb-3">
                <div class="contact-detail-label"><i class="bi bi-telephone-fill me-1"></i>Điện thoại</div>
                <div class="contact-detail-value">
                    <?php if (!empty($contact['phone'])): ?>
                        <a href="tel:<?= htmlspecialchars($contact['phone']) ?>" class="text-decoration-none">
                            <?= htmlspecialchars($contact['phone']) ?>
                        </a>
                    <?php else: ?>
                        <span class="text-muted">—</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="contact-detail-item mb-3">
                <div class="contact-detail-label"><i class="bi bi-chat-text-fill me-1"></i>Chủ đề</div>
                <div class="contact-detail-value"><?= htmlspecialchars($contact['subject'] ?? '—') ?></div>
            </div>

            <div class="contact-detail-item mb-3">
                <div class="contact-detail-label"><i class="bi bi-clock me-1"></i>Thời gian gửi</div>
                <div class="contact-detail-value small">
                    <?= isset($contact['created_at']) ? date('d/m/Y H:i', strtotime($contact['created_at'])) : '—' ?>
                </div>
            </div>

            <div class="contact-detail-label mb-2"><i class="bi bi-card-text me-1"></i>Nội dung</div>
            <div class="contact-message-box small">
                <?= nl2br(htmlspecialchars($contact['message'] ?? '')) ?>
            </div>
        </div>
    </div>
</div>
