/**
 * Admin Client Logos - JavaScript
 */

document.addEventListener('DOMContentLoaded', function () {

    const uploadArea    = document.getElementById('logoUploadArea');
    const fileInput     = document.getElementById('logo');
    const preview       = document.getElementById('logoPreview');
    const placeholder   = document.getElementById('logoPlaceholder');
    const previewActions = document.getElementById('logoPreviewActions');
    const removeBtn     = document.getElementById('removeLogoBtn');

    if (!uploadArea || !fileInput) return;

    // ---- Show preview when file selected ----
    fileInput.addEventListener('change', function () {
        const file = this.files[0];
        if (file) showPreview(file);
    });

    // ---- Drag & Drop ----
    uploadArea.addEventListener('dragover', function (e) {
        e.preventDefault();
        this.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', function () {
        this.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', function (e) {
        e.preventDefault();
        this.classList.remove('dragover');
        const file = e.dataTransfer.files[0];
        if (file && file.type.startsWith('image/')) {
            // Assign to input
            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;
            showPreview(file);
        }
    });

    // ---- Remove logo ----
    if (removeBtn) {
        removeBtn.addEventListener('click', function () {
            fileInput.value = '';
            preview.src = '';
            preview.classList.add('d-none');
            placeholder.classList.remove('d-none');
            previewActions.classList.add('d-none');
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
            placeholder.classList.add('d-none');
            if (previewActions) previewActions.classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    }

    // ---- Form validation ----
    const form = document.getElementById('clientLogoForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            const nameInput = document.getElementById('name');
            if (!nameInput || nameInput.value.trim() === '') {
                e.preventDefault();
                nameInput.classList.add('is-invalid');
                nameInput.focus();
                return;
            }
            nameInput.classList.remove('is-invalid');
        });
    }

});
