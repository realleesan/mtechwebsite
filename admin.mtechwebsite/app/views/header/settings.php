<?php
/**
 * @var array $header Header settings data with keys: logo_path, logo_alt, phone, phone_href, iso_text, profile_pdf_label, profile_pdf_path
 * @var string $title Page title
 * @var string $page Current page identifier
 * @var array $admin Admin user data
 */
?>
<div class="page-header">
    <h4><i class="bi bi-gear me-2"></i>Cài đặt Header</h4>
    <div class="page-actions">
        <a href="/header" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>Quay lại
        </a>
    </div>
</div>

<div class="admin-form-card">
    <div class="card-body">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                <?php 
                echo htmlspecialchars($_SESSION['success']); 
                unset($_SESSION['success']); 
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <?php 
                echo htmlspecialchars($_SESSION['error']); 
                unset($_SESSION['error']); 
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="/header/settings/update" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-4">
                        <label for="logo" class="form-label fw-bold">Logo Website</label>
                        <div class="mb-3">
                            <img id="logo-preview-img" src="/<?= htmlspecialchars($header['logo_path']) ?>" class="img-thumbnail mb-2" style="max-height: 100px; background: #0d1117;">
                            <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label for="logo_alt" class="form-label">Mô tả Logo (Alt text)</label>
                            <input type="text" class="form-control" id="logo_alt" name="logo_alt" value="<?= htmlspecialchars($header['logo_alt']) ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Thông tin liên hệ & ISO</label>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại hiển thị</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($header['phone']) ?>">
                        </div>
                        <div class="mb-3">
                            <label for="phone_href" class="form-label">Số điện thoại (Link gọi)</label>
                            <input type="text" class="form-control" id="phone_href" name="phone_href" value="<?= htmlspecialchars($header['phone_href']) ?>">
                        </div>
                        <div class="mb-3">
                            <label for="iso_text" class="form-label">Chứng chỉ ISO (Text)</label>
                            <input type="text" class="form-control" id="iso_text" name="iso_text" value="<?= htmlspecialchars($header['iso_text']) ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions border-top pt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Lưu cài đặt
                </button>
                <a href="/header" class="btn btn-secondary">
                    <i class="bi bi-x-circle me-1"></i>Hủy
                </a>
            </div>
        </form>
    </div>
</div>
