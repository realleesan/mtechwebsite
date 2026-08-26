<div class="page-header">
    <h4><i class="bi bi-images me-2"></i>Thêm logo đối tác mới</h4>
    <a href="/client-logos" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Quay lại</a>
</div>

<form method="POST" action="/client-logos/store" enctype="multipart/form-data" id="clientLogoForm">
    <div class="admin-form-card">
        <div class="row">

            <!-- Left: Fields -->
            <div class="col-md-8">

                <div class="mb-3">
                    <label for="name" class="form-label">Tên đối tác <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name"
                           placeholder="Nhập tên đối tác" required>
                </div>

                <div class="mb-3">
                    <label for="url" class="form-label">Link website</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                        <input type="url" class="form-control" id="url" name="url"
                               placeholder="https://example.com">
                    </div>
                    <div class="form-text">Để trống nếu không có website</div>
                </div>

                <div class="mb-3">
                    <label for="sort_order" class="form-label">Thứ tự hiển thị</label>
                    <input type="number" class="form-control" id="sort_order" name="sort_order"
                           value="0" min="0">
                    <div class="form-text">Số nhỏ hơn hiển thị trước</div>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select class="form-select" id="status" name="status">
                        <option value="1">Hiển thị</option>
                        <option value="0">Ẩn</option>
                    </select>
                </div>

            </div>

            <!-- Right: Logo Upload -->
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Ảnh logo <span class="text-danger">*</span></label>
                    <div class="client-logo-upload-area" id="logoUploadArea">
                        <div class="client-logo-upload-placeholder" id="logoPlaceholder">
                            <i class="bi bi-cloud-upload fs-2 mb-2 text-muted"></i>
                            <p class="mb-1 text-muted small">Click hoặc kéo thả ảnh vào đây</p>
                            <small class="text-muted">JPG, PNG, SVG, WEBP (tối đa 2MB)</small>
                        </div>
                        <img id="logoPreview" src="" alt="Preview" class="client-logo-preview d-none">
                        <input type="file" id="logo" name="logo" accept="image/*" class="client-logo-file-input" required>
                    </div>
                    <div class="mt-2 d-none" id="logoPreviewActions">
                        <button type="button" class="btn btn-sm btn-outline-danger" id="removeLogoBtn">
                            <i class="bi bi-x-lg me-1"></i>Xóa ảnh
                        </button>
                    </div>
                </div>
            </div>

        </div>

        <!-- Form Actions -->
        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
            <a href="/client-logos" class="btn btn-outline-secondary">
                <i class="bi bi-x-lg me-2"></i>Hủy
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-2"></i>Lưu logo
            </button>
        </div>
    </div>
</form>
