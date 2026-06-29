<div class="page-header">
    <h4><i class="bi bi-pencil-square me-2 text-primary"></i>Chỉnh sửa Slide Hero</h4>
    <a href="/home-sliders" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Quay lại
    </a>
</div>

<form method="POST" action="/home-sliders/update/<?= $slide['id'] ?>"
      enctype="multipart/form-data" id="sliderForm">

    <div class="admin-form-card">
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="sort_order" class="form-label">Thứ tự hiển thị</label>
                <input type="number" class="form-control" id="sort_order" name="sort_order"
                       value="<?= (int)($slide['sort_order'] ?? 0) ?>" min="0">
                <div class="form-text">Số nhỏ hơn hiển thị trước</div>
            </div>
            <div class="col-md-4 mb-3">
                <label for="status" class="form-label">Trạng thái</label>
                <select class="form-select" id="status" name="status">
                    <option value="1" <?= ($slide['status'] ?? 1) == 1 ? 'selected' : '' ?>>Hiển thị</option>
                    <option value="0" <?= ($slide['status'] ?? 1) == 0 ? 'selected' : '' ?>>Ẩn</option>
                </select>
            </div>
        </div>

        <hr class="my-4">

        <h5 class="fw-semibold mb-3">Ảnh Slide</h5>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Ảnh 1</label>
                <div class="cat-upload-area" id="image_1UploadArea" data-target="image_1">
                    <?php if (!empty($slide['image_1'])): ?>
                        <img id="image_1Preview" src="<?= htmlspecialchars($slide['image_1']) ?>"
                             alt="Ảnh hiện tại" class="cat-preview"
                             onerror="this.classList.add('d-none');this.src='';document.getElementById('image_1Placeholder').classList.remove('d-none');">
                        <div class="cat-upload-placeholder d-none" id="image_1Placeholder">
                            <i class="bi bi-image fs-1 mb-2 text-muted"></i>
                            <p class="mb-1 text-muted">Click hoặc kéo thả ảnh mới</p>
                            <small class="text-muted">JPG, PNG, WEBP (tối đa 5MB)</small>
                        </div>
                    <?php else: ?>
                        <div class="cat-upload-placeholder" id="image_1Placeholder">
                            <i class="bi bi-image fs-1 mb-2 text-muted"></i>
                            <p class="mb-1 text-muted">Click hoặc kéo thả ảnh vào đây</p>
                            <small class="text-muted">JPG, PNG, WEBP (tối đa 5MB)</small>
                        </div>
                        <img id="image_1Preview" src="" alt="Preview" class="cat-preview d-none">
                    <?php endif; ?>
                    <input type="file" id="image_1" name="image_1" accept="image/*" class="cat-file-input">
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Ảnh 2</label>
                <div class="cat-upload-area" id="image_2UploadArea" data-target="image_2">
                    <?php if (!empty($slide['image_2'])): ?>
                        <img id="image_2Preview" src="<?= htmlspecialchars($slide['image_2']) ?>"
                             alt="Ảnh hiện tại" class="cat-preview"
                             onerror="this.classList.add('d-none');this.src='';document.getElementById('image_2Placeholder').classList.remove('d-none');">
                        <div class="cat-upload-placeholder d-none" id="image_2Placeholder">
                            <i class="bi bi-image fs-1 mb-2 text-muted"></i>
                            <p class="mb-1 text-muted">Click hoặc kéo thả ảnh mới</p>
                            <small class="text-muted">JPG, PNG, WEBP (tối đa 5MB)</small>
                        </div>
                    <?php else: ?>
                        <div class="cat-upload-placeholder" id="image_2Placeholder">
                            <i class="bi bi-image fs-1 mb-2 text-muted"></i>
                            <p class="mb-1 text-muted">Click hoặc kéo thả ảnh vào đây</p>
                            <small class="text-muted">JPG, PNG, WEBP (tối đa 5MB)</small>
                        </div>
                        <img id="image_2Preview" src="" alt="Preview" class="cat-preview d-none">
                    <?php endif; ?>
                    <input type="file" id="image_2" name="image_2" accept="image/*" class="cat-file-input">
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Ảnh 3</label>
                <div class="cat-upload-area" id="image_3UploadArea" data-target="image_3">
                    <?php if (!empty($slide['image_3'])): ?>
                        <img id="image_3Preview" src="<?= htmlspecialchars($slide['image_3']) ?>"
                             alt="Ảnh hiện tại" class="cat-preview"
                             onerror="this.classList.add('d-none');this.src='';document.getElementById('image_3Placeholder').classList.remove('d-none');">
                        <div class="cat-upload-placeholder d-none" id="image_3Placeholder">
                            <i class="bi bi-image fs-1 mb-2 text-muted"></i>
                            <p class="mb-1 text-muted">Click hoặc kéo thả ảnh mới</p>
                            <small class="text-muted">JPG, PNG, WEBP (tối đa 5MB)</small>
                        </div>
                    <?php else: ?>
                        <div class="cat-upload-placeholder" id="image_3Placeholder">
                            <i class="bi bi-image fs-1 mb-2 text-muted"></i>
                            <p class="mb-1 text-muted">Click hoặc kéo thả ảnh vào đây</p>
                            <small class="text-muted">JPG, PNG, WEBP (tối đa 5MB)</small>
                        </div>
                        <img id="image_3Preview" src="" alt="Preview" class="cat-preview d-none">
                    <?php endif; ?>
                    <input type="file" id="image_3" name="image_3" accept="image/*" class="cat-file-input">
                </div>
            </div>
        </div>
        <small class="text-muted">Để trống nếu không muốn thay đổi ảnh</small>
    </div>

    <div class="d-flex justify-content-between mt-4 pt-3 border-top">
        <a href="/home-sliders" class="btn btn-outline-secondary">
            <i class="bi bi-x-lg me-2"></i>Hủy
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-lg me-2"></i>Cập nhật Slide
        </button>
    </div>
</form>

<style>
.cat-upload-area {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    position: relative;
    min-height: 160px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.cat-upload-area:hover {
    border-color: #0d6efd;
    background: #f8f9fa;
}
.cat-upload-area.dragover {
    border-color: #0d6efd;
    background: #e9ecef;
}
.cat-upload-placeholder {
    text-align: center;
}
.cat-preview {
    max-width: 100%;
    max-height: 160px;
    object-fit: cover;
    border-radius: 6px;
    position: absolute;
    inset: 0;
    margin: auto;
    width: auto;
    height: auto;
}
.cat-file-input {
    position: absolute;
    inset: 0;
    opacity: 0;
    cursor: pointer;
    z-index: 2;
}
.hidden-on-preview {
    opacity: 0;
    pointer-events: none;
}
.cat-upload-error {
    border-color: #dc3545 !important;
    background: #fff5f5 !important;
}
</style>

<script>
(function() {
    const MAX_SIZE = 5 * 1024 * 1024;
    ['image_1', 'image_2', 'image_3'].forEach(id => {
        const area = document.getElementById(id + 'UploadArea');
        if (!area) return;

        const fileInput = document.getElementById(id);
        const preview   = document.getElementById(id + 'Preview');
        const placeholder = document.getElementById(id + 'Placeholder');

        if (!fileInput || !preview || !placeholder) return;

        area.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            area.classList.add('dragover');
        });
        area.addEventListener('dragleave', function(e) {
            e.stopPropagation();
            area.classList.remove('dragover');
        });
        area.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            area.classList.remove('dragover');
            const file = e.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) {
                const dt = new DataTransfer();
                dt.items.add(file);
                fileInput.files = dt.files;
                showPreview(file);
            }
        });

        fileInput.addEventListener('change', function() {
            if (this.files[0]) showPreview(this.files[0]);
        });

        function showPreview(file) {
            if (file.size > MAX_SIZE) {
                alert('Ảnh quá lớn. Tối đa 5MB.');
                fileInput.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
                placeholder.classList.add('d-none');
                fileInput.classList.add('hidden-on-preview');
            };
            reader.readAsDataURL(file);
        }
    });
})();
</script>
