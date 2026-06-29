<div class="page-header">
    <h4><i class="bi bi-plus-circle me-2 text-primary"></i>Thêm Slide Hero mới</h4>
    <a href="/home-sliders" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Quay lại
    </a>
</div>

<form method="POST" action="/home-sliders/store"
      enctype="multipart/form-data" id="sliderForm">

    <div class="admin-form-card">
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="sort_order" class="form-label">Thứ tự hiển thị</label>
                <input type="number" class="form-control" id="sort_order" name="sort_order" value="0" min="0">
                <div class="form-text">Số nhỏ hơn hiển thị trước</div>
            </div>
            <div class="col-md-4 mb-3">
                <label for="status" class="form-label">Trạng thái</label>
                <select class="form-select" id="status" name="status">
                    <option value="1">Hiển thị</option>
                    <option value="0">Ẩn</option>
                </select>
            </div>
        </div>

        <hr class="my-4">

        <h5 class="fw-semibold mb-3">Ảnh Slide (bắt buộc đủ 3 ảnh)</h5>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Ảnh 1 <span class="text-danger">*</span></label>
                <div class="cat-upload-area" id="image_1UploadArea" data-target="image_1">
                    <div class="cat-upload-placeholder" id="image_1Placeholder">
                        <i class="bi bi-image fs-1 mb-2 text-muted"></i>
                        <p class="mb-1 text-muted">Click hoặc kéo thả ảnh vào đây</p>
                        <small class="text-muted">JPG, PNG, WEBP (tối đa 5MB)</small>
                    </div>
                    <img id="image_1Preview" src="" alt="Preview" class="cat-preview d-none">
                    <input type="file" id="image_1" name="image_1" accept="image/*" class="cat-file-input" required>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Ảnh 2 <span class="text-danger">*</span></label>
                <div class="cat-upload-area" id="image_2UploadArea" data-target="image_2">
                    <div class="cat-upload-placeholder" id="image_2Placeholder">
                        <i class="bi bi-image fs-1 mb-2 text-muted"></i>
                        <p class="mb-1 text-muted">Click hoặc kéo thả ảnh vào đây</p>
                        <small class="text-muted">JPG, PNG, WEBP (tối đa 5MB)</small>
                    </div>
                    <img id="image_2Preview" src="" alt="Preview" class="cat-preview d-none">
                    <input type="file" id="image_2" name="image_2" accept="image/*" class="cat-file-input" required>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Ảnh 3 <span class="text-danger">*</span></label>
                <div class="cat-upload-area" id="image_3UploadArea" data-target="image_3">
                    <div class="cat-upload-placeholder" id="image_3Placeholder">
                        <i class="bi bi-image fs-1 mb-2 text-muted"></i>
                        <p class="mb-1 text-muted">Click hoặc kéo thả ảnh vào đây</p>
                        <small class="text-muted">JPG, PNG, WEBP (tối đa 5MB)</small>
                    </div>
                    <img id="image_3Preview" src="" alt="Preview" class="cat-preview d-none">
                    <input type="file" id="image_3" name="image_3" accept="image/*" class="cat-file-input" required>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between mt-4 pt-3 border-top">
        <a href="/home-sliders" class="btn btn-outline-secondary">
            <i class="bi bi-x-lg me-2"></i>Hủy
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-lg me-2"></i>Lưu Slide
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
    const areas = ['image_1', 'image_2', 'image_3'];

    areas.forEach(id => {
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

    const form = document.getElementById('sliderForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const required = ['image_1', 'image_2', 'image_3'];
            let valid = true;
            required.forEach(id => {
                const input = document.getElementById(id);
                const preview = document.getElementById(id + 'Preview');
                const hasFile = input && input.files && input.files.length > 0;
                const hasPreview = preview && !preview.classList.contains('d-none') && preview.src && preview.src !== window.location.href;
                if (!hasFile && !hasPreview) {
                    valid = false;
                    const area = document.getElementById(id + 'UploadArea');
                    if (area) area.classList.add('cat-upload-error');
                } else {
                    const area = document.getElementById(id + 'UploadArea');
                    if (area) area.classList.remove('cat-upload-error');
                }
            });
            if (!valid) {
                e.preventDefault();
                alert('Vui lòng tải lên đủ 3 ảnh cho slide');
            }
        });
    }
})();
</script>
