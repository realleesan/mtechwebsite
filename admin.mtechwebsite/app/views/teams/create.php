<div class="page-header">
    <h4><i class="bi bi-people me-2"></i>Thêm thành viên mới</h4>
    <a href="/teams" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Quay lại</a>
</div>

<form method="POST" action="/teams/store" enctype="multipart/form-data" id="teamForm">
    <div class="admin-form-card">
        <div class="row">

            <!-- Left: Fields -->
            <div class="col-md-8">

                <div class="mb-3">
                    <label for="name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name"
                           placeholder="Nhập họ và tên thành viên" required>
                </div>

                <div class="mb-3">
                    <label for="position" class="form-label">Chức vụ <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="position" name="position"
                           placeholder="VD: Giám đốc, Kỹ sư trưởng..." required>
                </div>

                <div class="mb-3">
                    <label for="bio" class="form-label">Giới thiệu ngắn</label>
                    <textarea class="form-control" id="bio" name="bio" rows="4"
                              placeholder="Mô tả ngắn về thành viên, kinh nghiệm, chuyên môn..."></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="sort_order" class="form-label">Thứ tự hiển thị</label>
                        <input type="number" class="form-control" id="sort_order" name="sort_order"
                               value="0" min="0">
                        <div class="form-text">Số nhỏ hơn hiển thị trước</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select class="form-select" id="status" name="status">
                            <option value="1">Hiển thị</option>
                            <option value="0">Ẩn</option>
                        </select>
                    </div>
                </div>

            </div>

            <!-- Right: Avatar Upload -->
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Ảnh đại diện</label>
                    <div class="team-upload-area" id="teamUploadArea">
                        <div class="team-upload-placeholder" id="teamPlaceholder">
                            <i class="bi bi-person-circle fs-2 mb-2 text-muted"></i>
                            <p class="mb-1 text-muted small">Click hoặc kéo thả ảnh vào đây</p>
                            <small class="text-muted">JPG, PNG, WEBP (tối đa 2MB)</small>
                        </div>
                        <img id="teamPreview" src="" alt="Preview" class="team-preview d-none">
                        <input type="file" id="image_file" name="image_file" accept="image/*" class="team-file-input">
                    </div>
                    <div class="mt-2 d-none" id="teamPreviewActions">
                        <button type="button" class="btn btn-sm btn-outline-danger" id="removeImageBtn">
                            <i class="bi bi-x-lg me-1"></i>Xóa ảnh
                        </button>
                    </div>
                    <div class="form-text mt-1">Ảnh chân dung (tỉ lệ vuông khuyến nghị)</div>
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Hoặc nhập URL ảnh</label>
                    <input type="text" class="form-control" id="image" name="image"
                           placeholder="https://example.com/avatar.jpg">
                    <div class="form-text">Dùng nếu ảnh đã có sẵn trên server</div>
                </div>
            </div>

        </div>

        <!-- Form Actions -->
        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
            <a href="/teams" class="btn btn-outline-secondary">
                <i class="bi bi-x-lg me-2"></i>Hủy
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-2"></i>Lưu thành viên
            </button>
        </div>
    </div>
</form>
