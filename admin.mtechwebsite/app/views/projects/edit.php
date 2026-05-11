<?php
// Safety check - ensure project data is available
if (!isset($project) || empty($project)) {
    // Redirect or show error if no project data
    header('Location: /projects');
    exit;
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
                <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic" type="button" role="tab">
                    <i class="bi bi-info-circle me-2"></i>Thông tin cơ bản
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab">
                    <i class="bi bi-file-text me-2"></i>Chi tiết dự án
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="media-tab" data-bs-toggle="tab" data-bs-target="#media" type="button" role="tab">
                    <i class="bi bi-images me-2"></i>Hình ảnh & Media
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="seo-tab" data-bs-toggle="tab" data-bs-target="#seo" type="button" role="tab">
                    <i class="bi bi-search me-2"></i>SEO & Metadata
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="projectTabContent">
            
            <!-- Basic Information Tab -->
            <div class="tab-pane fade show active" id="basic" role="tabpanel">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="title" class="form-label">Tiêu đề dự án <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($project['title'] ?? '') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="slug" name="slug" value="<?= htmlspecialchars($project['slug'] ?? '') ?>" required>
                            <div class="form-text">URL thân thiện, sẽ tự động tạo từ tiêu đề</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="service_id" class="form-label">Danh mục <span class="text-danger">*</span></label>
                            <select class="form-select" id="service_id" name="service_id" required>
                                <option value="">-- Chọn danh mục --</option>
                                <?php foreach ($services ?? [] as $service): ?>
                                    <option value="<?= htmlspecialchars($service['id']) ?>" 
                                        <?php 
                                        // Check if this service is assigned to current project (take first one)
                                        $isSelected = false;
                                        if (!empty($projectServices) && isset($projectServices[0])) {
                                            if ($projectServices[0]['id'] == $service['id']) {
                                                $isSelected = true;
                                            }
                                        }
                                        echo $isSelected ? 'selected' : '';
                                        ?>>
                                        <?= htmlspecialchars($service['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả ngắn</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($project['description'] ?? '') ?></textarea>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select class="form-select" id="status" name="status">
                                <option value="1" <?= ($project['status'] ?? 1) == 1 ? 'selected' : '' ?>>Kích hoạt</option>
                                <option value="0" <?= ($project['status'] ?? 1) == 0 ? 'selected' : '' ?>>Vô hiệu hóa</option>
                                <option value="2" <?= ($project['status'] ?? 1) == 2 ? 'selected' : '' ?>>Nổi bật</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="show_on_home" name="show_on_home" value="1" <?= ($project['show_on_home'] ?? 0) == 1 ? 'checked' : '' ?>>
                                <label class="form-check-label" for="show_on_home">Hiển thị trang chủ</label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="show_in_menu" name="show_in_menu" value="1" <?= ($project['show_in_menu'] ?? 0) == 1 ? 'checked' : '' ?>>
                                <label class="form-check-label" for="show_in_menu">Hiển thị menu</label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="project_date" class="form-label">Ngày thực hiện</label>
                            <input type="date" class="form-control" id="project_date" name="project_date" value="<?= $project['project_date'] ?? '' ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="client" class="form-label">Khách hàng</label>
                            <input type="text" class="form-control" id="client" name="client" value="<?= htmlspecialchars($project['client'] ?? '') ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="location" class="form-label">Địa điểm</label>
                            <input type="text" class="form-control" id="location" name="location" value="<?= htmlspecialchars($project['location'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Project Details Tab -->
            <div class="tab-pane fade" id="details" role="tabpanel">
                <div class="wireframe-container" data-placeholder="Chi tiết dự án">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status_label" class="form-label">Nhãn trạng thái</label>
                                <select class="form-select" id="status_label" name="status_label">
                                    <option value="Completed" <?= ($project['status_label'] ?? 'Completed') == 'Completed' ? 'selected' : '' ?>>Đã hoàn thành</option>
                                    <option value="In Progress" <?= ($project['status_label'] ?? '') == 'In Progress' ? 'selected' : '' ?>>Đang thực hiện</option>
                                    <option value="On Hold" <?= ($project['status_label'] ?? '') == 'On Hold' ? 'selected' : '' ?>>Tạm dừng</option>
                                    <option value="Planning" <?= ($project['status_label'] ?? '') == 'Planning' ? 'selected' : '' ?>>Lên kế hoạch</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="live_demo" class="form-label">Đường dẫn URL</label>
                                <input type="url" class="form-control" id="live_demo" name="live_demo" value="<?= htmlspecialchars($project['live_demo'] ?? '') ?>" placeholder="https://example.com">
                            </div>
                            
                            <div class="mb-3">
                                <label for="tags" class="form-label">Thẻ</label>
                                <input type="text" class="form-control" id="tags" name="tags" value="<?= htmlspecialchars($project['tags'] ?? '') ?>" placeholder="industrial, welding, chemical">
                                <div class="form-text">Phân cách bằng dấu phẩy</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="wireframe-image-placeholder" data-placeholder="Ảnh chi tiết chính" data-current-image="<?= htmlspecialchars($project['detail_image'] ?? '') ?>">
                                <?php if (!empty($project['detail_image'])): ?>
                                    <img src="<?= htmlspecialchars($project['detail_image']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                    <input type="hidden" name="existing_detail_image" value="<?= htmlspecialchars($project['detail_image']) ?>">
                                <?php else: ?>
                                    <i class="bi bi-image fs-1"></i>
                                    <p class="mb-0">Click để thêm ảnh chi tiết</p>
                                <?php endif; ?>
                                <input type="file" name="detail_image" accept="image/*" style="display: none;">
                            </div>
                        </div>
                    </div>
                    
                    <!-- What We Did Section -->
                    <div class="wireframe-container mt-4" data-placeholder="Những gì chúng tôi đã làm">
                        <div class="mb-3">
                            <label for="what_we_did_title" class="form-label">Tiêu đề thành phần</label>
                            <input type="text" class="form-control" id="what_we_did_title" name="what_we_did_title" 
                                   value="<?= htmlspecialchars($project['what_we_did_title'] ?? '') ?>" 
                                   placeholder="Nhập tiêu đề cho phần những gì chúng tôi đã làm">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="rich-editor-container">
                                    <div class="rich-editor-toolbar">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-command="bold" title="Bold">
                                            <i class="bi bi-type-bold"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-command="italic" title="Italic">
                                            <i class="bi bi-type-italic"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-command="underline" title="Underline">
                                            <i class="bi bi-type-underline"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-command="insertUnorderedList" title="Bullet List">
                                            <i class="bi bi-list-ul"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-command="insertOrderedList" title="Numbered List">
                                            <i class="bi bi-list-ol"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-command="justifyLeft" title="Align Left">
                                            <i class="bi bi-text-left"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-command="justifyCenter" title="Align Center">
                                            <i class="bi bi-text-center"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-command="justifyRight" title="Align Right">
                                            <i class="bi bi-text-right"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-command="removeFormat" title="Clear Formatting">
                                            <i class="bi bi-eraser"></i>
                                        </button>
                                    </div>
                                    <div class="rich-editor-content" contenteditable="true" data-placeholder="Nhập nội dung cho những gì chúng tôi đã làm...">
                                        <?= $project['what_we_did'] ?? '' ?>
                                    </div>
                                    <input type="hidden" name="what_we_did" value="<?= htmlspecialchars($project['what_we_did'] ?? '') ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="wireframe-image-placeholder" data-placeholder="Ảnh những gì chúng tôi đã làm" data-current-image="<?= htmlspecialchars($project['what_we_did_image'] ?? '') ?>">
                                    <?php if (!empty($project['what_we_did_image'])): ?>
                                        <img src="<?= htmlspecialchars($project['what_we_did_image']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                        <input type="hidden" name="existing_what_we_did_image" value="<?= htmlspecialchars($project['what_we_did_image']) ?>">
                                    <?php else: ?>
                                        <i class="bi bi-image fs-1"></i>
                                        <p class="mb-0">Click để thêm ảnh</p>
                                    <?php endif; ?>
                                    <input type="file" name="what_we_did_image" accept="image/*" style="display: none;">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Results Section -->
                    <div class="wireframe-container mt-4" data-placeholder="Kết quả đạt được">
                        <div class="mb-3">
                            <label for="results_title" class="form-label">Tiêu đề thành phần</label>
                            <input type="text" class="form-control" id="results_title" name="results_title" 
                                   value="<?= htmlspecialchars($project['results_title'] ?? '') ?>" 
                                   placeholder="Nhập tiêu đề cho phần kết quả đạt được">
                        </div>
                        
                        <div class="mb-3">
                            <label for="results" class="form-label">Nội dung kết quả đạt được</label>
                            <div class="rich-editor-container">
                                <div class="rich-editor-toolbar">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-command="bold" title="Bold">
                                        <i class="bi bi-type-bold"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-command="italic" title="Italic">
                                        <i class="bi bi-type-italic"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-command="underline" title="Underline">
                                        <i class="bi bi-type-underline"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-command="insertUnorderedList" title="Bullet List">
                                        <i class="bi bi-list-ul"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-command="insertOrderedList" title="Numbered List">
                                        <i class="bi bi-list-ol"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-command="removeFormat" title="Clear Formatting">
                                        <i class="bi bi-eraser"></i>
                                    </button>
                                </div>
                                <div class="rich-editor-content" contenteditable="true" data-placeholder="Nhập nội dung cho kết quả đạt được...">
                                    <?= $project['results'] ?? '' ?>
                                </div>
                                <input type="hidden" name="results" value="<?= htmlspecialchars($project['results'] ?? '') ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Kết quả nổi bật <small class="text-muted">(Thêm các kết quả dự án)</small></label>
                            <div id="result-items-container" class="result-items-container">
                                <div class="result-item mb-2">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="result_items[]" placeholder="Nhập kết quả 1">
                                        <button type="button" class="btn btn-outline-danger" onclick="removeResultItem(this)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addResultItem()">
                                <i class="bi bi-plus-circle me-1"></i>Thêm kết quả
                            </button>
                            <input type="hidden" id="result_items_json" name="result_items" value="<?= htmlspecialchars($project['result_items'] ?? '') ?>">
                            <div class="form-text">Nhập các kết quả nổi bật của dự án</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Media Tab -->
            <div class="tab-pane fade" id="media" role="tabpanel">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="image" class="form-label">Ảnh đại diện</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*" data-preview="image-preview">
                            <?php if (!empty($project['image'])): ?>
                                <div class="mt-2">
                                    <img src="<?= htmlspecialchars($project['image']) ?>" style="max-width: 200px;" class="img-thumbnail">
                                    <div class="form-text">Ảnh hiện tại</div>
                                    <input type="hidden" name="existing_image" value="<?= htmlspecialchars($project['image']) ?>">
                                </div>
                            <?php else: ?>
                                <div class="mt-2">
                                    <img id="image-preview" style="max-width: 200px; display: none;" class="img-thumbnail">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Gallery Images</label>
                            <div class="gallery-upload-area">
                                <i class="bi bi-cloud-upload fs-1 mb-2"></i>
                                <p class="mb-2">Kéo thả ảnh vào đây hoặc click để chọn</p>
                                <small class="text-muted">Hỗ trợ: JPG, PNG, GIF (Tối đa 5MB)</small>
                                <input type="file" id="gallery-input" name="gallery[]" multiple accept="image/*" style="display: none;">
                            </div>
                            <div class="gallery-preview">
                                <?php if (!empty($project['gallery'])): ?>
                                    <?php $galleryImages = json_decode($project['gallery'], true); ?>
                                    <?php if (is_array($galleryImages)): ?>
                                        <?php foreach ($galleryImages as $img): ?>
                                            <div class="gallery-preview-item existing">
                                                <img src="<?= htmlspecialchars($img) ?>" alt="Gallery">
                                                <small class="text-muted d-block mt-1"><?= htmlspecialchars(basename($img)) ?></small>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SEO Tab -->
            <div class="tab-pane fade" id="seo" role="tabpanel">
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="meta_title" class="form-label">Meta Title</label>
                            <input type="text" class="form-control" id="meta_title" name="meta_title" value="<?= htmlspecialchars($project['meta_title'] ?? '') ?>" maxlength="60">
                            <div class="form-text">Tối đa 60 ký tự</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="meta_description" class="form-label">Meta Description</label>
                            <textarea class="form-control" id="meta_description" name="meta_description" rows="3" maxlength="160"><?= htmlspecialchars($project['meta_description'] ?? '') ?></textarea>
                            <div class="form-text">Tối đa 160 ký tự</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
            <a href="/projects" class="btn btn-outline-secondary">
                <i class="bi bi-x-lg me-2"></i>Hủy
            </a>
            <div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-2"></i>Cập nhật dự án
                </button>
            </div>
        </div>
    </div>
</form>

