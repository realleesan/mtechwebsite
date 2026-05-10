<?php if (!isset($award) || empty($award)): ?>
    <?php header('Location: /awards'); exit; ?>
<?php endif; ?>

<div class="page-header">
    <h4><i class="bi bi-trophy me-2"></i>Chỉnh sửa giải thưởng</h4>
    <a href="/awards" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Quay lại</a>
</div>

<form method="POST" action="/awards/update/<?= $award['id'] ?>" enctype="multipart/form-data" id="awardForm">
    <div class="admin-form-card">
        <div class="row">

            <!-- Left: Fields -->
            <div class="col-md-8">

                <div class="mb-3">
                    <label for="name" class="form-label">Tên giải thưởng <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name"
                           value="<?= htmlspecialchars($award['name'] ?? '') ?>"
                           placeholder="Nhập tên giải thưởng hoặc chứng chỉ" required>
                </div>

                <div class="mb-3">
                    <label for="certificate" class="form-label">Đơn vị cấp</label>
                    <input type="text" class="form-control" id="certificate" name="certificate"
                           value="<?= htmlspecialchars($award['certificate'] ?? '') ?>"
                           placeholder="VD: Bộ Khoa học & Công nghệ, Bureau Veritas...">
                    <div class="form-text">Tổ chức hoặc cơ quan cấp giải thưởng/chứng chỉ</div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="sort_order" class="form-label">Thứ tự hiển thị</label>
                        <input type="number" class="form-control" id="sort_order" name="sort_order"
                               value="<?= (int)($award['sort_order'] ?? 0) ?>" min="0">
                        <div class="form-text">Số nhỏ hơn hiển thị trước</div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select class="form-select" id="status" name="status">
                        <option value="1" <?= ($award['status'] ?? 1) == 1 ? 'selected' : '' ?>>Hiển thị</option>
                        <option value="0" <?= ($award['status'] ?? 1) == 0 ? 'selected' : '' ?>>Ẩn</option>
                    </select>
                </div>

            </div>

            <!-- Right: Image Upload -->
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Ảnh giải thưởng / chứng chỉ</label>
                    <div class="award-upload-area" id="awardUploadArea">
                        <?php if (!empty($award['image'])): ?>
                            <img id="awardPreview" src="<?= htmlspecialchars($award['image']) ?>"
                                 alt="<?= htmlspecialchars($award['name'] ?? '') ?>"
                                 class="award-preview">
                            <div class="award-upload-placeholder d-none" id="awardPlaceholder">
                                <i class="bi bi-cloud-upload fs-2 mb-2 text-muted"></i>
                                <p class="mb-1 text-muted small">Click hoặc kéo thả ảnh vào đây</p>
                                <small class="text-muted">JPG, PNG, WEBP (tối đa 2MB)</small>
                            </div>
                        <?php else: ?>
                            <div class="award-upload-placeholder" id="awardPlaceholder">
                                <i class="bi bi-cloud-upload fs-2 mb-2 text-muted"></i>
                                <p class="mb-1 text-muted small">Click hoặc kéo thả ảnh vào đây</p>
                                <small class="text-muted">JPG, PNG, WEBP (tối đa 2MB)</small>
                            </div>
                            <img id="awardPreview" src="" alt="Preview" class="award-preview d-none">
                        <?php endif; ?>
                        <input type="file" id="image" name="image" accept="image/*" class="award-file-input">
                    </div>
                    <!-- Hidden input: báo server xóa ảnh khi không upload file mới -->
                    <input type="hidden" name="remove_image" id="removeImageFlag" value="0">
                    <div class="mt-2 <?= empty($award['image']) ? 'd-none' : '' ?>" id="awardPreviewActions">
                        <button type="button" class="btn btn-sm btn-outline-danger" id="removeImageBtn">
                            <i class="bi bi-x-lg me-1"></i>Xóa ảnh
                        </button>
                    </div>
                    <div class="form-text mt-1">Để trống nếu không muốn thay đổi ảnh</div>
                </div>
            </div>

        </div>

        <!-- Form Actions -->
        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
            <a href="/awards" class="btn btn-outline-secondary">
                <i class="bi bi-x-lg me-2"></i>Hủy
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-2"></i>Cập nhật giải thưởng
            </button>
        </div>
    </div>
</form>
