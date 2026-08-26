<?php
if (!isset($contact) || empty($contact)) {
    header('Location: /contacts'); exit;
}

$statusMap = [
    0 => ['label' => 'Chưa đọc',    'badge' => 'contact-badge-unread',  'icon' => 'bi-envelope'],
    1 => ['label' => 'Đã đọc',      'badge' => 'contact-badge-read',    'icon' => 'bi-envelope-open'],
    2 => ['label' => 'Đã phản hồi', 'badge' => 'contact-badge-replied', 'icon' => 'bi-reply'],
];
$statusInfo = $statusMap[(int)($contact['status'] ?? 0)] ?? $statusMap[0];
?>
<div class="page-header">
    <h4><i class="bi bi-envelope-open me-2"></i>Chi tiết liên hệ #<?= $contact['id'] ?></h4>
    <div class="d-flex gap-2">
        <a href="/contacts/edit/<?= $contact['id'] ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-pencil me-1"></i>Chỉnh sửa
        </a>
        <a href="/contacts" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Quay lại
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Thông tin người gửi -->
    <div class="col-lg-8">
        <div class="admin-form-card">
            <h6 class="fw-semibold mb-3 pb-2 border-bottom">
                <i class="bi bi-person me-2 text-primary"></i>Thông tin người gửi
            </h6>
            <div class="row g-3">
                <div class="col-sm-6">
                    <div class="contact-detail-item">
                        <div class="contact-detail-label"><i class="bi bi-person-fill me-1"></i>Họ và tên</div>
                        <div class="contact-detail-value fw-medium"><?= htmlspecialchars($contact['name'] ?? '—') ?></div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="contact-detail-item">
                        <div class="contact-detail-label"><i class="bi bi-envelope-fill me-1"></i>Email</div>
                        <div class="contact-detail-value">
                            <a href="mailto:<?= htmlspecialchars($contact['email'] ?? '') ?>" class="text-decoration-none">
                                <?= htmlspecialchars($contact['email'] ?? '—') ?>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="contact-detail-item">
                        <div class="contact-detail-label"><i class="bi bi-telephone-fill me-1"></i>Số điện thoại</div>
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
                </div>
                <div class="col-sm-6">
                    <div class="contact-detail-item">
                        <div class="contact-detail-label"><i class="bi bi-chat-text-fill me-1"></i>Chủ đề</div>
                        <div class="contact-detail-value"><?= htmlspecialchars($contact['subject'] ?? '—') ?></div>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <div class="contact-detail-label mb-2"><i class="bi bi-card-text me-1"></i>Nội dung tin nhắn</div>
                <div class="contact-message-box">
                    <?= nl2br(htmlspecialchars($contact['message'] ?? '')) ?>
                </div>
            </div>

            <?php if (!empty($contact['admin_reply'])): ?>
            <div class="mt-3">
                <div class="contact-detail-label mb-2"><i class="bi bi-reply-fill me-1 text-success"></i>Phản hồi của Admin</div>
                <div class="contact-reply-box">
                    <?= nl2br(htmlspecialchars($contact['admin_reply'])) ?>
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
                <div class="contact-detail-label">Trạng thái</div>
                <div class="mt-1">
                    <span class="badge <?= $statusInfo['badge'] ?>">
                        <i class="bi <?= $statusInfo['icon'] ?> me-1"></i><?= $statusInfo['label'] ?>
                    </span>
                </div>
            </div>

            <div class="contact-detail-item mb-3">
                <div class="contact-detail-label"><i class="bi bi-clock me-1"></i>Thời gian gửi</div>
                <div class="contact-detail-value">
                    <?= isset($contact['created_at']) ? date('d/m/Y H:i:s', strtotime($contact['created_at'])) : '—' ?>
                </div>
            </div>

            <div class="contact-detail-item mb-3">
                <div class="contact-detail-label"><i class="bi bi-arrow-clockwise me-1"></i>Cập nhật lần cuối</div>
                <div class="contact-detail-value">
                    <?= isset($contact['updated_at']) ? date('d/m/Y H:i:s', strtotime($contact['updated_at'])) : '—' ?>
                </div>
            </div>

            <div class="contact-detail-item mb-3">
                <div class="contact-detail-label"><i class="bi bi-globe me-1"></i>Địa chỉ IP</div>
                <div class="contact-detail-value font-monospace small">
                    <?= htmlspecialchars($contact['ip_address'] ?? '—') ?>
                </div>
            </div>

            <?php if (!empty($contact['user_agent'])): ?>
            <div class="contact-detail-item">
                <div class="contact-detail-label"><i class="bi bi-browser-chrome me-1"></i>User Agent</div>
                <div class="contact-detail-value small text-muted" style="word-break:break-all;font-size:0.75rem">
                    <?= htmlspecialchars($contact['user_agent']) ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Quick actions -->
        <div class="admin-form-card mt-3">
            <h6 class="fw-semibold mb-3 pb-2 border-bottom">
                <i class="bi bi-lightning me-2 text-warning"></i>Thao tác nhanh
            </h6>
            <div class="d-grid gap-2">
                <a href="/contacts/edit/<?= $contact['id'] ?>" class="btn btn-primary btn-sm">
                    <i class="bi bi-pencil me-2"></i>Chỉnh sửa / Phản hồi
                </a>
                <form method="POST" action="/contacts/delete/<?= $contact['id'] ?>">
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100 btn-delete"
                            data-confirm="Xóa liên hệ này?">
                        <i class="bi bi-trash me-2"></i>Xóa liên hệ
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

