<?php
// Safety check - ensure project data is available
if (!isset($project) || empty($project)) {
    header('Location: /projects');
    exit;
}

/**
 * Render service checkboxes với phân cấp cha-con
 */
if (!function_exists('renderServiceCheckboxes')) {
    function renderServiceCheckboxes($services, $selectedIds = [], $depth = 0) {
        $html = '';
        $selectedIds = array_map('intval', $selectedIds);
        foreach ($services as $svc) {
            $svcId  = (int)$svc['id'];
            $checked = in_array($svcId, $selectedIds) ? 'checked' : '';
            $indent  = $depth * 20; // px
            $html .= '<div class="form-check" style="margin-left:' . $indent . 'px;">';
            $html .= '<input class="form-check-input service-checkbox" type="checkbox"';
            $html .= ' name="service_ids[]" value="' . $svcId . '"';
            $html .= ' id="svc_' . $svcId . '" ' . $checked . '>';
            $html .= '<label class="form-check-label" for="svc_' . $svcId . '">';
            $html .= htmlspecialchars($svc['name']);
            $html .= '</label></div>';
            if (!empty($svc['children'])) {
                $html .= renderServiceCheckboxes($svc['children'], $selectedIds, $depth + 1);
            }
        }
        return $html;
    }
}

// Build selected IDs từ projectServices
$selectedServiceIds = [];
foreach ($projectServices ?? [] as $ps) {
    $selectedServiceIds[] = (int)$ps['id'];
}

// Parse existing gallery
$existingGallery = [];
if (!empty($project['gallery'])) {
    $decoded = json_decode($project['gallery'], true);
    if (is_array($decoded)) {
        $existingGallery = $decoded;
    }
}
?>
<div class="page-header">
    <h4><i class="bi bi-building me-2"></i>Chỉnh sửa dự án</h4>
    <a href="/projects" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Quay lại</a>
</div>

<form method="POST" action="/projects/update/<?= $project['id'] ?>" enctype="multipart/form-data" onsubmit="return validateProjectForm()">
    <div class="admin-form-card project-form-tabs">
        <!-- Tab Navigation -->
        <ul class="nav nav-tabs" id="projectTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="specs-tab" data-bs-toggle="tab" data-bs-target="#specs" type="button" role="tab">
                    <i class="bi bi-list-check me-2"></i>10 Thông số dự án
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="media-tab" data-bs-toggle="tab" data-bs-target="#media" type="button" role="tab">
                    <i class="bi bi-images me-2"></i>Hình ảnh & Thư viện Slider
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic" type="button" role="tab">
                    <i class="bi bi-info-circle me-2"></i>Danh mục & Phân loại
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="seo-tab" data-bs-toggle="tab" data-bs-target="#seo" type="button" role="tab">
                    <i class="bi bi-search me-2"></i>SEO & Metadata
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content p-3" id="projectTabContent">
            
            <!-- 10 Thông số dự án Tab -->
            <div class="tab-pane fade show active" id="specs" role="tabpanel">
                <div class="alert alert-info py-2 mb-4">
                    <i class="bi bi-info-circle-fill me-2"></i>10 mục thông số kỹ thuật bên dưới sẽ được hiển thị ở cột thông tin chi tiết của dự án.
                </div>

                <div class="row g-3">
                    <div class="col-md-12">
                        <label for="title" class="form-label fw-bold">1. Tên dự án <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg" id="title" name="title" value="<?= htmlspecialchars($project['title'] ?? '') ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label for="slug" class="form-label fw-bold">Slug URL <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="slug" name="slug" value="<?= htmlspecialchars($project['slug'] ?? '') ?>" required>
                        <div class="form-text">Đường dẫn thân thiện</div>
                    </div>

                    <div class="col-md-6">
                        <label for="capacity" class="form-label fw-bold">2. Công suất</label>
                        <input type="text" class="form-control" id="capacity" name="capacity" value="<?= htmlspecialchars($project['capacity'] ?? '') ?>" placeholder="Ví dụ: 12.000 tấn clinker/ngày">
                    </div>

                    <div class="col-md-6">
                        <label for="location" class="form-label fw-bold">3. Địa điểm xây dựng</label>
                        <input type="text" class="form-control" id="location" name="location" value="<?= htmlspecialchars($project['location'] ?? '') ?>" placeholder="Ví dụ: Xã Thanh Nghị, Huyện Thanh Liêm, Tỉnh Hà Nam">
                    </div>

                    <div class="col-md-6">
                        <label for="client" class="form-label fw-bold">4. Chủ đầu tư dự án</label>
                        <input type="text" class="form-control" id="client" name="client" value="<?= htmlspecialchars($project['client'] ?? '') ?>" placeholder="Ví dụ: Công ty Cổ phần Xi măng Xuân Thành">
                    </div>

                    <div class="col-md-6">
                        <label for="total_investment" class="form-label fw-bold">5. Tổng mức đầu tư</label>
                        <input type="text" class="form-control" id="total_investment" name="total_investment" value="<?= htmlspecialchars($project['total_investment'] ?? '') ?>" placeholder="Ví dụ: 10.500 tỷ VNĐ">
                    </div>

                    <div class="col-md-6">
                        <label for="construction_year" class="form-label fw-bold">6. Năm xây dựng / hoàn thành</label>
                        <input type="text" class="form-control" id="construction_year" name="construction_year" value="<?= htmlspecialchars($project['construction_year'] ?? '') ?>" placeholder="Ví dụ: 2021 - 2023">
                    </div>

                    <div class="col-md-6">
                        <label for="bidding_form" class="form-label fw-bold">7. Hình thức gói thầu (EP/EPC)</label>
                        <input type="text" class="form-control" id="bidding_form" name="bidding_form" value="<?= htmlspecialchars($project['bidding_form'] ?? '') ?>" placeholder="Ví dụ: EPC (Thiết kế - Cung cấp thiết bị - Thi công)">
                    </div>

                    <div class="col-md-6">
                        <label for="equipment_contractor" class="form-label fw-bold">8. Nhà thầu cung cấp thiết bị</label>
                        <input type="text" class="form-control" id="equipment_contractor" name="equipment_contractor" value="<?= htmlspecialchars($project['equipment_contractor'] ?? '') ?>" placeholder="Ví dụ: FLSmidth (Đan Mạch), Loesche (Đức)">
                    </div>

                    <div class="col-md-6">
                        <label for="design_consultant" class="form-label fw-bold">9. Đơn vị tư vấn thiết kế xây dựng</label>
                        <input type="text" class="form-control" id="design_consultant" name="design_consultant" value="<?= htmlspecialchars($project['design_consultant'] ?? '') ?>" placeholder="Ví dụ: Công ty Cổ phần Tư vấn Kỹ thuật MTECH">
                    </div>

                    <div class="col-md-6">
                        <label for="supervision_consultant" class="form-label fw-bold">10. Đơn vị tư vấn giám sát</label>
                        <input type="text" class="form-control" id="supervision_consultant" name="supervision_consultant" value="<?= htmlspecialchars($project['supervision_consultant'] ?? '') ?>" placeholder="Ví dụ: Công ty Cổ phần Tư vấn Kỹ thuật MTECH">
                    </div>
                </div>
            </div>

            <!-- Hình ảnh & Thư viện Media Tab -->
            <div class="tab-pane fade" id="media" role="tabpanel">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header bg-light fw-bold">
                                <i class="bi bi-card-image me-1"></i>Ảnh đại diện chính
                            </div>
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <?php $mainImg = !empty($project['image']) ? $project['image'] : '/assets/images/placeholder-project.jpg'; ?>
                                    <img id="main-image-preview" src="<?= htmlspecialchars($mainImg) ?>" alt="Preview" class="img-thumbnail" style="max-height: 200px; width: 100%; object-fit: cover;">
                                </div>
                                <input type="hidden" name="existing_image" value="<?= htmlspecialchars($project['image'] ?? '') ?>">
                                <input type="file" class="form-control" id="image" name="image" accept="image/*" onchange="previewMainImage(this)">
                                <div class="form-text">Chọn ảnh mới nếu muốn thay đổi</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header bg-light fw-bold">
                                <i class="bi bi-images me-1"></i>Thư viện ảnh Slide (Gallery)
                            </div>
                            <div class="card-body">
                                <!-- Các ảnh hiện có trong gallery -->
                                <?php if (!empty($existingGallery)): ?>
                                    <label class="form-label fw-bold">Ảnh hiện có trong Slider (Bỏ chọn để xóa):</label>
                                    <div class="d-flex flex-wrap gap-2 mb-3 p-2 border rounded bg-light" id="existing-gallery-list">
                                        <?php foreach ($existingGallery as $gIdx => $gUrl): ?>
                                            <div class="position-relative gallery-item-wrap" id="gallery-item-<?= $gIdx ?>">
                                                <img src="<?= htmlspecialchars($gUrl) ?>" class="img-thumbnail" style="width: 80px; height: 60px; object-fit: cover;">
                                                <input type="hidden" name="kept_gallery[]" value="<?= htmlspecialchars($gUrl) ?>">
                                                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 p-0" style="width: 20px; height: 20px; line-height: 1;" onclick="removeExistingGallery(<?= $gIdx ?>)">&times;</button>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <div class="mb-3">
                                    <label for="gallery" class="form-label fw-bold">Tải lên thêm ảnh cho Slider:</label>
                                    <input type="file" class="form-control" id="gallery" name="gallery[]" accept="image/*" multiple onchange="previewGalleryImages(this)">
                                    <div class="form-text">Có thể chọn nhiều ảnh cùng lúc</div>
                                </div>
                                <div id="gallery-preview-container" class="d-flex flex-wrap gap-2 pt-2"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Danh mục & Cài đặt hiển thị Tab -->
            <div class="tab-pane fade" id="basic" role="tabpanel">
                <div class="row">
                    <div class="col-md-7">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Thuộc Lĩnh vực hoạt động <span class="text-danger">*</span></label>
                            <div class="category-checkbox-list border rounded p-3" style="max-height:280px; overflow-y:auto; background: #fafafa;">
                                <?php if (!empty($services)): ?>
                                    <?= renderServiceCheckboxes($services, $selectedServiceIds) ?>
                                <?php else: ?>
                                    <div class="text-muted">Chưa có danh mục nào.</div>
                                <?php endif; ?>
                            </div>
                            <div class="form-text">Chọn một hoặc nhiều lĩnh vực liên quan</div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Mô tả ngắn</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($project['description'] ?? '') ?></textarea>
                        </div>
                    </div>
                    
                    <div class="col-md-5">
                        <div class="mb-3">
                            <label for="status" class="form-label fw-bold">Trạng thái</label>
                            <select class="form-select" id="status" name="status">
                                <option value="1" <?= ($project['status'] ?? 1) == 1 ? 'selected' : '' ?>>Kích hoạt (Hiển thị)</option>
                                <option value="0" <?= ($project['status'] ?? 1) == 0 ? 'selected' : '' ?>>Vô hiệu hóa (Ẩn)</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="sort_order" class="form-label fw-bold">Thứ tự hiển thị</label>
                            <input type="number" class="form-control" id="sort_order" name="sort_order" value="<?= htmlspecialchars($project['sort_order'] ?? 0) ?>" min="0">
                            <div class="form-text">Số nhỏ hơn hiển thị trước</div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="show_on_home" name="show_on_home" value="1" <?= !empty($project['show_on_home']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="show_on_home">Hiển thị trên Trang chủ</label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="project_date" class="form-label fw-bold">Ngày thực hiện</label>
                            <input type="date" class="form-control" id="project_date" name="project_date" value="<?= htmlspecialchars($project['project_date'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- SEO Tab -->
            <div class="tab-pane fade" id="seo" role="tabpanel">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="meta_title" class="form-label fw-bold">Meta Title</label>
                            <input type="text" class="form-control" id="meta_title" name="meta_title" value="<?= htmlspecialchars($project['meta_title'] ?? '') ?>" placeholder="Tiêu đề SEO">
                        </div>
                        
                        <div class="mb-3">
                            <label for="meta_description" class="form-label fw-bold">Meta Description</label>
                            <textarea class="form-control" id="meta_description" name="meta_description" rows="3" placeholder="Mô tả SEO"><?= htmlspecialchars($project['meta_description'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Form Actions -->
        <div class="admin-form-actions p-3 border-top bg-light text-end">
            <a href="/projects" class="btn btn-secondary me-2">Hủy</a>
            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>Cập nhật dự án</button>
        </div>
    </div>
</form>

<script>
function previewMainImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('main-image-preview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function removeExistingGallery(idx) {
    var item = document.getElementById('gallery-item-' + idx);
    if (item) {
        item.remove();
    }
}

function previewGalleryImages(input) {
    var container = document.getElementById('gallery-preview-container');
    container.innerHTML = '';
    if (input.files) {
        Array.from(input.files).forEach(function(file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'img-thumbnail';
                img.style.width = '80px';
                img.style.height = '60px';
                img.style.objectFit = 'cover';
                container.appendChild(img);
            }
            reader.readAsDataURL(file);
        });
    }
}

function validateProjectForm() {
    var title = document.getElementById('title').value.trim();
    if (!title) {
        alert('Vui lòng nhập tên dự án!');
        return false;
    }
    var checkedServices = document.querySelectorAll('.service-checkbox:checked');
    if (checkedServices.length === 0) {
        alert('Vui lòng chọn ít nhất một lĩnh vực hoạt động!');
        return false;
    }
    return true;
}
</script>
