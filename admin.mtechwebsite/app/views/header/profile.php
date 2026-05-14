<?php
/**
 * @var array $header Header settings data with keys: logo_path, logo_alt, phone, phone_href, iso_text, profile_pdf_label, profile_pdf_path
 * @var string $title Page title
 * @var string $page Current page identifier
 * @var array $admin Admin user data
 */
?>
<div class="page-header">
    <h4><i class="bi bi-file-earmark-pdf me-2"></i>Quản lý Hồ sơ năng lực</h4>
    <div class="page-actions">
        <a href="/header" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>Quay lại
        </a>
    </div>
</div>

<div class="admin-form-card">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i><?= $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i><?= $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form action="/header/profile/update" method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-8">
                <div class="mb-4">
                    <label for="profile_pdf_label" class="form-label fw-bold">Nhãn nút tải xuống</label>
                    <input type="text" class="form-control" id="profile_pdf_label" name="profile_pdf_label" value="<?= htmlspecialchars($header['profile_pdf_label']) ?>" placeholder="Ví dụ: Tải Hồ Sơ Năng Lực">
                    <div class="form-text">Đây là văn bản sẽ hiển thị trên nút tải xuống ở Header.</div>
                </div>
                
                <div class="mb-4">
                    <label for="profile_pdf" class="form-label fw-bold">Tải lên file PDF mới</label>
                    <input type="file" class="form-control" id="profile_pdf" name="profile_pdf" accept=".pdf">
                    <div class="form-text mt-2">
                        <i class="bi bi-info-circle me-1"></i> File hiện tại: 
                        <a href="/<?= htmlspecialchars($header['profile_pdf_path']) ?>" target="_blank" class="text-primary fw-500">
                            <?= basename($header['profile_pdf_path']) ?>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="alert alert-light border">
                    <h6 class="fw-bold mb-2"><i class="bi bi-lightbulb me-2"></i>Hướng dẫn</h6>
                    <ul class="small mb-0 ps-3">
                        <li>Chỉ chấp nhận định dạng file <strong>.pdf</strong>.</li>
                        <li>Dung lượng file không nên quá 10MB để đảm bảo tốc độ tải trang.</li>
                        <li>Tên file nên viết liền không dấu để tránh lỗi hiển thị.</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="form-actions border-top pt-3">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-cloud-arrow-up me-1"></i>Cập nhật Hồ sơ năng lực
            </button>
        </div>
    </form>
</div>
