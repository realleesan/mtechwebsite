/**
 * Admin Teams - JavaScript
 */

document.addEventListener('DOMContentLoaded', function () {

    const uploadArea     = document.getElementById('teamUploadArea');
    const fileInput      = document.getElementById('image_file');
    const preview        = document.getElementById('teamPreview');
    const placeholder    = document.getElementById('teamPlaceholder');
    const previewActions = document.getElementById('teamPreviewActions');
    const removeBtn      = document.getElementById('removeImageBtn');

    if (!uploadArea || !fileInput) return;

    // ---- Show preview when file selected ----
    fileInput.addEventListener('change', function () {
        const file = this.files[0];
        if (file) showPreview(file);
    });

    // ---- Drag & Drop ----
    uploadArea.addEventListener('dragover', function (e) {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', function () {
        uploadArea.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', function (e) {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        const file = e.dataTransfer.files[0];
        if (file && file.type.startsWith('image/')) {
            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;
            showPreview(file);
        }
    });

    // ---- Remove image ----
    if (removeBtn) {
        removeBtn.addEventListener('click', function () {
            fileInput.value = '';
            preview.src = '';
            preview.classList.add('d-none');
            if (placeholder) placeholder.classList.remove('d-none');
            if (previewActions) previewActions.classList.add('d-none');
            const flag = document.getElementById('removeImageFlag');
            if (flag) flag.value = '1';
        });
    }

    // ---- Helper: show preview ----
    function showPreview(file) {
        const maxSize = 2 * 1024 * 1024; // 2MB
        if (file.size > maxSize) {
            alert('Ảnh quá lớn. Vui lòng chọn ảnh nhỏ hơn 2MB.');
            fileInput.value = '';
            return;
        }
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
            if (placeholder) placeholder.classList.add('d-none');
            if (previewActions) previewActions.classList.remove('d-none');
            // Reset flag xóa nếu có
            const flag = document.getElementById('removeImageFlag');
            if (flag) flag.value = '0';
            // Ẩn thông báo lỗi ảnh nếu đang hiện
            const imgErr = document.getElementById('imageError');
            if (imgErr) imgErr.classList.add('d-none');
            uploadArea.classList.remove('upload-error');
        };
        reader.readAsDataURL(file);
    }

    // ---- Form validation ----
    const form = document.getElementById('teamForm');
    if (!form) return;

    const isCreatePage = form.action.includes('/teams/store');
    // data-has-image="1" nếu bản ghi đã có ảnh, "0" nếu chưa
    let currentlyHasImage = form.dataset.hasImage === '1';

    // Khi bấm "Xóa ảnh" → đánh dấu là không còn ảnh nữa
    if (removeBtn) {
        removeBtn.addEventListener('click', function () {
            currentlyHasImage = false;
            // Hiện label * bắt buộc nếu cần
            const imgErr = document.getElementById('imageError');
            if (imgErr) imgErr.classList.add('d-none');
            uploadArea.classList.remove('upload-error');
        });
    }

    form.addEventListener('submit', function (e) {
        let valid = true;

        // Validate Họ tên
        const nameInput = document.getElementById('name');
        if (nameInput && nameInput.value.trim() === '') {
            nameInput.classList.add('is-invalid');
            valid = false;
        } else if (nameInput) {
            nameInput.classList.remove('is-invalid');
        }

        // Validate Chức vụ
        const positionInput = document.getElementById('position');
        if (positionInput && positionInput.value.trim() === '') {
            positionInput.classList.add('is-invalid');
            valid = false;
        } else if (positionInput) {
            positionInput.classList.remove('is-invalid');
        }

        // Validate ảnh:
        // - Trang create: luôn bắt buộc upload file
        // - Trang edit: bắt buộc nếu bản ghi chưa có ảnh (hoặc vừa bấm xóa ảnh)
        const hasFile = fileInput.files && fileInput.files.length > 0;
        const needImage = isCreatePage || !currentlyHasImage;

        if (needImage && !hasFile) {
            uploadArea.classList.add('upload-error');
            const imgErr = document.getElementById('imageError');
            if (imgErr) imgErr.classList.remove('d-none');
            valid = false;
        } else {
            uploadArea.classList.remove('upload-error');
            const imgErr = document.getElementById('imageError');
            if (imgErr) imgErr.classList.add('d-none');
        }

        if (!valid) {
            e.preventDefault();
            const firstErr = form.querySelector('.is-invalid, .upload-error');
            if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

});
