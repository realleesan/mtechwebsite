/**
 * Admin Categories - JavaScript
 * Dùng chung cho cả trang create và edit dịch vụ
 */

document.addEventListener('DOMContentLoaded', function () {

    const MAX_SIZE = 5 * 1024 * 1024; // 5MB

    // Reset tất cả file inputs khi trang load (tránh browser restore file cũ sau redirect)
    document.querySelectorAll('.cat-file-input').forEach(function (input) {
        try { input.value = ''; } catch (e) {}
    });

    // ================================================================
    // UPLOAD AREA — khởi tạo cho từng khung kéo thả
    // ================================================================
    function initUploadArea(areaId) {
        const area = document.getElementById(areaId + 'UploadArea')
                  || document.getElementById(areaId);
        if (!area) return;

        const fileInput   = area.querySelector('.cat-file-input');
        const preview     = area.querySelector('.cat-preview');
        const placeholder = area.querySelector('.cat-upload-placeholder');
        if (!fileInput || !preview || !placeholder) return;

        const targetName = area.dataset.target;

        // Tạo nút "Đổi ảnh" overlay — chỉ hiện khi có preview
        const changeBtn = document.createElement('button');
        changeBtn.type = 'button';
        changeBtn.className = 'cat-change-btn';
        changeBtn.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i>Đổi ảnh';
        area.appendChild(changeBtn);

        // Click vào nút "Đổi ảnh" → mở dialog
        changeBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            // Reset input value để có thể chọn cùng file lần nữa
            fileInput.value = '';
            fileInput.click();
        });

        // Click vào area → chỉ mở dialog khi CHƯA có preview
        area.addEventListener('click', function (e) {
            // Nếu click vào input file hoặc nút đổi ảnh, không xử lý
            if (e.target === fileInput || e.target === changeBtn || changeBtn.contains(e.target)) return;
            // Nếu click vào preview image, không xử lý
            if (e.target === preview) return;
            
            const hasPreview = !preview.classList.contains('d-none') && preview.src && preview.src !== window.location.href;
            if (!hasPreview) {
                // Reset input value để có thể chọn cùng file lần nữa
                fileInput.value = '';
                fileInput.click();
            }
        });

        // Cập nhật hiển thị nút đổi ảnh theo trạng thái preview
        function updateChangeBtn() {
            const hasPreview = !preview.classList.contains('d-none') && preview.src && preview.src !== window.location.href;
            changeBtn.style.display = hasPreview ? 'flex' : 'none';
            
            // Ẩn input file khi có preview để tránh bao phủ
            if (hasPreview) {
                fileInput.classList.add('hidden-on-preview');
            } else {
                fileInput.classList.remove('hidden-on-preview');
            }
        }
        // Kiểm tra ngay khi init (trang edit đã có ảnh sẵn)
        updateChangeBtn();

        // Lắng nghe sự kiện lỗi ảnh (onerror từ PHP render) để cập nhật nút đổi ảnh
        preview.addEventListener('error', function () {
            this.classList.add('d-none');
            this.src = '';
            placeholder.classList.remove('d-none');
            updateChangeBtn();
        });

        // Chọn file qua dialog
        fileInput.addEventListener('change', function () {
            if (this.files[0]) {
                showPreview(this.files[0]);
                // Không reset value để giữ file trong form submit
            }
        });

        // Drag & Drop
        area.addEventListener('dragover', function (e) {
            e.preventDefault();
            e.stopPropagation();
            area.classList.add('dragover');
        });
        area.addEventListener('dragleave', function (e) {
            e.stopPropagation();
            area.classList.remove('dragover');
        });
        area.addEventListener('drop', function (e) {
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

        function showPreview(file) {
            if (file.size > MAX_SIZE) {
                alert('Ảnh quá lớn. Tối đa 5MB.');
                fileInput.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
                placeholder.classList.add('d-none');
                area.classList.remove('cat-upload-error');

                // Ẩn thông báo lỗi tương ứng
                if (targetName === 'image') {
                    const err = document.getElementById('imageError');
                    if (err) err.classList.add('d-none');
                }
                if (targetName === 'image_1') {
                    const err = document.getElementById('image1Error');
                    if (err) err.classList.add('d-none');
                }
                updateChangeBtn();
            };
            reader.readAsDataURL(file);
        }
    }

    // Khởi tạo tất cả upload areas
    ['main', 'image_1', 'image_2', 'image_3', 'benefitImg', 'featureImg']
        .forEach(id => initUploadArea(id));

    // ================================================================
    // AUTO-GENERATE SLUG
    // ================================================================
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    if (nameInput && slugInput) {
        nameInput.addEventListener('input', function () {
            slugInput.value = this.value.toLowerCase()
                .replace(/[àáạảãâầấậẩẫăằắặẳẵ]/g, 'a')
                .replace(/[èéẹẻẽêềếệểễ]/g, 'e')
                .replace(/[ìíịỉĩ]/g, 'i')
                .replace(/[òóọỏõôồốộổỗơờớợởỡ]/g, 'o')
                .replace(/[ùúụủũưừứựửữ]/g, 'u')
                .replace(/[ỳýỵỷỹ]/g, 'y')
                .replace(/đ/g, 'd')
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .replace(/^-|-$/g, '');
        });
    }

    // ================================================================
    // FAQ — thêm / xóa câu hỏi
    // ================================================================
    const addFaqBtn    = document.getElementById('addFaqBtn');
    const faqContainer = document.getElementById('faqContainer');

    if (addFaqBtn && faqContainer) {
        addFaqBtn.addEventListener('click', function () {
            const count = faqContainer.querySelectorAll('.faq-item').length + 1;
            const div   = document.createElement('div');
            div.className = 'faq-item card border-0 bg-light p-3 mb-2';
            div.innerHTML = `
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="small fw-semibold text-muted">Câu hỏi ${count}</span>
                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-faq">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="mb-2">
                    <input type="text" class="form-control form-control-sm faq-question" placeholder="Câu hỏi...">
                </div>
                <div>
                    <textarea class="form-control form-control-sm faq-answer" rows="3" placeholder="Câu trả lời..."></textarea>
                </div>`;
            faqContainer.appendChild(div);
        });

        // Xóa FAQ item — event delegation
        faqContainer.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-remove-faq');
            if (btn) btn.closest('.faq-item').remove();
        });
    }

    // ================================================================
    // FORM SUBMIT — validate + chuẩn bị JSON
    // ================================================================
    const form = document.getElementById('categoryForm');
    if (!form) return;

    // Xác định trang create hay edit
    const isCreate = form.action.includes('/categories/store');

    form.addEventListener('submit', function (e) {
        if (!prepareCategoryForm()) {
            e.preventDefault();
        }
    });

    // Gắn hàm vào window để onsubmit="return prepareCategoryForm()" vẫn hoạt động
    window.prepareCategoryForm = function () {
        let valid = true;

        // --- Validate ảnh đại diện (CHỈ bắt buộc khi CREATE) ---
        const mainArea    = document.getElementById('mainUploadArea');
        const mainInput   = document.getElementById('image');
        const mainPreview = mainArea ? mainArea.querySelector('.cat-preview') : null;
        const hasOldMain  = mainPreview
                         && !mainPreview.classList.contains('d-none')
                         && mainPreview.src
                         && mainPreview.src !== window.location.href;
        const hasNewMain  = mainInput && mainInput.files && mainInput.files.length > 0;
        const imageError  = document.getElementById('imageError');

        // Chỉ validate khi CREATE
        if (isCreate && !hasNewMain) {
            if (imageError) imageError.classList.remove('d-none');
            if (mainArea)   mainArea.classList.add('cat-upload-error');
            document.getElementById('basic-tab')?.click();
            valid = false;
        } else {
            if (imageError) imageError.classList.add('d-none');
            if (mainArea)   mainArea.classList.remove('cat-upload-error');
        }

        // --- Validate ảnh 1 gallery (CHỈ validate khi CREATE hoặc khi EDIT mà chưa có ảnh cũ) ---
        const img1Area    = document.getElementById('image_1UploadArea');
        const img1Input   = document.getElementById('image_1');
        const img1Preview = img1Area ? img1Area.querySelector('.cat-preview') : null;
        const hasOldImg1  = img1Preview
                         && !img1Preview.classList.contains('d-none')
                         && img1Preview.src
                         && img1Preview.src !== window.location.href;
        const hasNewImg1  = img1Input && img1Input.files && img1Input.files.length > 0;
        const image1Error = document.getElementById('image1Error');

        // Chỉ validate nếu: CREATE và chưa upload HOẶC EDIT và không có ảnh cũ cũng không có ảnh mới
        if (isCreate && !hasNewImg1) {
            if (image1Error) image1Error.classList.remove('d-none');
            if (img1Area)    img1Area.classList.add('cat-upload-error');
            if (valid) document.getElementById('detail-tab')?.click();
            valid = false;
        } else {
            if (image1Error) image1Error.classList.add('d-none');
            if (img1Area)    img1Area.classList.remove('cat-upload-error');
        }

        // --- Validate ảnh 2 gallery ---
        const img2Area    = document.getElementById('image_2UploadArea');
        const img2Input   = document.getElementById('image_2');
        const img2Preview = img2Area ? img2Area.querySelector('.cat-preview') : null;
        const hasOldImg2  = img2Preview
                         && !img2Preview.classList.contains('d-none')
                         && img2Preview.src
                         && img2Preview.src !== window.location.href;
        const hasNewImg2  = img2Input && img2Input.files && img2Input.files.length > 0;

        // Tạo/lấy error element cho image_2
        let image2Error = document.getElementById('image2Error');
        if (!image2Error && img2Area) {
            image2Error = document.createElement('div');
            image2Error.id = 'image2Error';
            image2Error.className = 'text-danger small mt-1 d-none';
            image2Error.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i>Vui lòng tải lên ảnh 2';
            img2Area.parentNode.insertBefore(image2Error, img2Area.nextSibling);
        }

        // Chỉ validate khi CREATE
        if (isCreate && !hasNewImg2) {
            if (image2Error) image2Error.classList.remove('d-none');
            if (img2Area)    img2Area.classList.add('cat-upload-error');
            if (valid) document.getElementById('detail-tab')?.click();
            valid = false;
        } else {
            if (image2Error) image2Error.classList.add('d-none');
            if (img2Area)    img2Area.classList.remove('cat-upload-error');
        }

        // --- Validate ảnh 3 gallery ---
        const img3Area    = document.getElementById('image_3UploadArea');
        const img3Input   = document.getElementById('image_3');
        const img3Preview = img3Area ? img3Area.querySelector('.cat-preview') : null;
        const hasOldImg3  = img3Preview
                         && !img3Preview.classList.contains('d-none')
                         && img3Preview.src
                         && img3Preview.src !== window.location.href;
        const hasNewImg3  = img3Input && img3Input.files && img3Input.files.length > 0;

        // Tạo/lấy error element cho image_3
        let image3Error = document.getElementById('image3Error');
        if (!image3Error && img3Area) {
            image3Error = document.createElement('div');
            image3Error.id = 'image3Error';
            image3Error.className = 'text-danger small mt-1 d-none';
            image3Error.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i>Vui lòng tải lên ảnh 3';
            img3Area.parentNode.insertBefore(image3Error, img3Area.nextSibling);
        }

        // Chỉ validate khi CREATE
        if (isCreate && !hasNewImg3) {
            if (image3Error) image3Error.classList.remove('d-none');
            if (img3Area)    img3Area.classList.add('cat-upload-error');
            if (valid) document.getElementById('detail-tab')?.click();
            valid = false;
        } else {
            if (image3Error) image3Error.classList.add('d-none');
            if (img3Area)    img3Area.classList.remove('cat-upload-error');
        }

        // --- Validate ảnh Benefit (CHỈ bắt buộc khi CREATE) ---
        const benefitArea    = document.getElementById('benefitImgUploadArea');
        const benefitInput   = document.getElementById('benefit_image');
        const benefitPreview = benefitArea ? benefitArea.querySelector('.cat-preview') : null;
        const hasOldBenefit  = benefitPreview
                            && !benefitPreview.classList.contains('d-none')
                            && benefitPreview.src
                            && benefitPreview.src !== window.location.href;
        const hasNewBenefit  = benefitInput && benefitInput.files && benefitInput.files.length > 0;

        // Tạo/lấy error element cho benefit
        let benefitError = document.getElementById('benefitImgError');
        if (!benefitError && benefitArea) {
            benefitError = document.createElement('div');
            benefitError.id = 'benefitImgError';
            benefitError.className = 'text-danger small mt-1 d-none';
            benefitError.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i>Vui lòng tải lên ảnh minh họa Benefit';
            benefitArea.parentNode.insertBefore(benefitError, benefitArea.nextSibling);
        }

        // Chỉ validate khi CREATE
        if (isCreate && !hasNewBenefit) {
            if (benefitError) benefitError.classList.remove('d-none');
            if (benefitArea)  benefitArea.classList.add('cat-upload-error');
            if (valid) document.getElementById('detail-tab')?.click();
            valid = false;
        } else {
            if (benefitError) benefitError.classList.add('d-none');
            if (benefitArea)  benefitArea.classList.remove('cat-upload-error');
        }

        // --- Validate ảnh Dự án / Feature (CHỈ bắt buộc khi CREATE) ---
        const featureArea    = document.getElementById('featureImgUploadArea');
        const featureInput   = document.getElementById('feature_image');
        const featurePreview = featureArea ? featureArea.querySelector('.cat-preview') : null;
        const hasOldFeature  = featurePreview
                            && !featurePreview.classList.contains('d-none')
                            && featurePreview.src
                            && featurePreview.src !== window.location.href;
        const hasNewFeature  = featureInput && featureInput.files && featureInput.files.length > 0;

        // Tạo/lấy error element cho feature
        let featureError = document.getElementById('featureImgError');
        if (!featureError && featureArea) {
            featureError = document.createElement('div');
            featureError.id = 'featureImgError';
            featureError.className = 'text-danger small mt-1 d-none';
            featureError.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i>Vui lòng tải lên ảnh minh họa Dự án';
            featureArea.parentNode.insertBefore(featureError, featureArea.nextSibling);
        }

        // Chỉ validate khi CREATE
        if (isCreate && !hasNewFeature) {
            if (featureError) featureError.classList.remove('d-none');
            if (featureArea)  featureArea.classList.add('cat-upload-error');
            if (valid) document.getElementById('detail-tab')?.click();
            valid = false;
        } else {
            if (featureError) featureError.classList.add('d-none');
            if (featureArea)  featureArea.classList.remove('cat-upload-error');
        }

        if (!valid) return false;

        // --- Chuyển benefit_items textarea → JSON ---
        const benefitText = document.getElementById('benefit_items_text');
        const benefitHidden = document.getElementById('benefit_items');
        if (benefitText && benefitHidden) {
            const arr = benefitText.value
                .split('\n')
                .map(s => s.trim())
                .filter(s => s.length > 0);
            benefitHidden.value = JSON.stringify(arr);
        }

        // --- Chuyển FAQ DOM → JSON ---
        const faqHidden = document.getElementById('faq_items');
        if (faqContainer && faqHidden) {
            const items = [];
            faqContainer.querySelectorAll('.faq-item').forEach(item => {
                const q = item.querySelector('.faq-question')?.value?.trim() || '';
                const a = item.querySelector('.faq-answer')?.value?.trim()   || '';
                if (q) items.push({ question: q, answer: a });
            });
            faqHidden.value = JSON.stringify(items);
        }

        return true;
    };

});
