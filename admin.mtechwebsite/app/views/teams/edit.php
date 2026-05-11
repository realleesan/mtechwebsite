<?php if (!isset($team) || empty($team)): ?>
    <?php header('Location: /teams'); exit; ?>
<?php endif; ?>

<?php $hasImage = !empty($team['image']); ?>

<div class="page-header">
    <h4><i class="bi bi-people me-2"></i>Chỉnh sửa thành viên</h4>
    <a href="/teams" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Quay lại</a>
</div>

<form method="POST" action="/teams/update/<?= $team['id'] ?>" enctype="multipart/form-data" id="teamForm"
      data-has-image="<?= $hasImage ? '1' : '0' ?>">
    <div class="admin-form-card">
        <div class="row">

            <!-- Left: Fields -->
            <div class="col-md-8">

                <div class="mb-3">
                    <label for="name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name"
                           value="<?= htmlspecialchars($team['name'] ?? '') ?>"
                           placeholder="Nhập họ và tên thành viên" required>
                </div>

                <div class="mb-3">
                    <label for="position" class="form-label">Chức vụ <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="position" name="position"
                           value="<?= htmlspecialchars($team['position'] ?? '') ?>"
                           placeholder="VD: Giám đốc, Kỹ sư trưởng..." required>
                </div>

                <div class="mb-3">
                    <label for="bio" class="form-label">Giới thiệu ngắn</label>
                    <textarea class="form-control" id="bio" name="bio" rows="4"
                              placeholder="Mô tả ngắn về thành viên, kinh nghiệm, chuyên môn..."><?= htmlspecialchars($team['bio'] ?? '') ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="sort_order" class="form-label">Thứ tự hiển thị</label>
                        <input type="number" class="form-control" id="sort_order" name="sort_order"
                               value="<?= (int)($team['sort_order'] ?? 0) ?>" min="0">
                        <div class="form-text">Số nhỏ hơn hiển thị trước</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select class="form-select" id="status" name="status">
                            <option value="1" <?= ($team['status'] ?? 1) == 1 ? 'selected' : '' ?>>Hiển thị</option>
                            <option value="0" <?= ($team['status'] ?? 1) == 0 ? 'selected' : '' ?>>Ẩn</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="show_in_about"
                               name="show_in_about" value="1"
                               <?= ($team['show_in_about'] ?? 0) == 1 ? 'checked' : '' ?>>
                        <label class="form-check-label" for="show_in_about">
                            Hiển thị trên trang <strong>Giới thiệu</strong>
                        </label>
                    </div>
                    <div class="form-text">Tối đa 4 thành viên được hiển thị trên trang Giới thiệu</div>
                </div>

            </div>

            <!-- Right: Avatar Upload -->
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">
                        Ảnh đại diện
                        <?php if (!$hasImage): ?>
                            <span class="text-danger">*</span>
                        <?php endif; ?>
                    </label>
                    <div class="team-upload-area" id="teamUploadArea">
                        <?php if ($hasImage): ?>
                            <img id="teamPreview" src="<?= htmlspecialchars($team['image']) ?>"
                                 alt="<?= htmlspecialchars($team['name'] ?? '') ?>"
                                 class="team-preview">
                            <div class="team-upload-placeholder d-none" id="teamPlaceholder">
                                <i class="bi bi-person-circle fs-2 mb-2 text-muted"></i>
                                <p class="mb-1 text-muted small">Click hoặc kéo thả ảnh vào đây</p>
                                <small class="text-muted">JPG, PNG, WEBP (tối đa 2MB)</small>
                            </div>
                        <?php else: ?>
                            <div class="team-upload-placeholder" id="teamPlaceholder">
                                <i class="bi bi-person-circle fs-2 mb-2 text-muted"></i>
                                <p class="mb-1 text-muted small">Click hoặc kéo thả ảnh vào đây</p>
                                <small class="text-muted">JPG, PNG, WEBP (tối đa 2MB)</small>
                            </div>
                            <img id="teamPreview" src="" alt="Preview" class="team-preview d-none">
                        <?php endif; ?>
                        <input type="file" id="image_file" name="image_file" accept="image/*" class="team-file-input">
                    </div>
                    <!-- Hidden input: báo server xóa ảnh khi không upload file mới -->
                    <input type="hidden" name="remove_image" id="removeImageFlag" value="0">
                    <div id="imageError" class="text-danger small mt-1 d-none">
                        <i class="bi bi-exclamation-circle me-1"></i>Vui lòng tải lên ảnh đại diện
                    </div>
                    <div class="mt-2 <?= !$hasImage ? 'd-none' : '' ?>" id="teamPreviewActions">
                        <button type="button" class="btn btn-sm btn-outline-danger" id="removeImageBtn">
                            <i class="bi bi-x-lg me-1"></i>Xóa ảnh
                        </button>
                    </div>
                    <div class="form-text mt-1">
                        <?= $hasImage ? 'Để trống nếu không muốn thay đổi ảnh' : 'Ảnh chân dung (tỉ lệ vuông khuyến nghị)' ?>
                    </div>
                </div>
            </div>

        </div>

        <!-- Form Actions -->
        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
            <a href="/teams" class="btn btn-outline-secondary">
                <i class="bi bi-x-lg me-2"></i>Hủy
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-2"></i>Cập nhật thành viên
            </button>
        </div>
    </div>
</form>
