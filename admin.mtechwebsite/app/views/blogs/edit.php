<?php
/**
 * Render category checkboxes with hierarchy support for edit form
 */
function renderCategoryCheckboxes($categories, $selectedIds = [], $depth = 0) {
    $html = '';
    // Convert selectedIds to integers for comparison
    $selectedIds = array_map('intval', $selectedIds);
    
    foreach ($categories as $category) {
        $catId = (int)$category['id'];
        $checked = in_array($catId, $selectedIds) ? 'checked' : '';
        $isRecruitment = $catId == 7 ? 'data-is-recruitment="1"' : '';
        $parentId = isset($category['parent_id']) ? htmlspecialchars($category['parent_id']) : '0';
        
        $html .= '<div class="form-check category-checkbox" data-category-id="' . htmlspecialchars($catId) . '" data-depth="' . $depth . '">';
        $html .= '<input class="form-check-input category-checkbox-input" type="checkbox" name="category_ids[]" ';
        $html .= 'value="' . htmlspecialchars($catId) . '" id="cat_' . htmlspecialchars($catId) . '" ';
        $html .= 'data-parent="' . $parentId . '" ';
        $html .= $checked . ' ' . $isRecruitment . ' onchange="checkRecruitmentCategory()">';
        $html .= '<label class="form-check-label" for="cat_' . htmlspecialchars($catId) . '">';
        $html .= htmlspecialchars($category['name']);
        $html .= '</label></div>';
        
        if (!empty($category['children'])) {
            $html .= renderCategoryCheckboxes($category['children'], $selectedIds, $depth + 1);
        }
    }
    return $html;
}

// Ensure all required variables exist
$blog = $blog ?? [];
$categoriesHierarchy = $categoriesHierarchy ?? [];
$selectedCategoryIds = $selectedCategoryIds ?? [];

// Set default values for all blog fields
if (!isset($blog['id'])) $blog['id'] = 0;
if (!isset($blog['title'])) $blog['title'] = '';
if (!isset($blog['slug'])) $blog['slug'] = '';
if (!isset($blog['excerpt'])) $blog['excerpt'] = '';
if (!isset($blog['content'])) $blog['content'] = '';
if (!isset($blog['full_content'])) $blog['full_content'] = '';
if (!isset($blog['image'])) $blog['image'] = '';
if (!isset($blog['author'])) $blog['author'] = 'Admin';
if (!isset($blog['status'])) $blog['status'] = 1;
if (!isset($blog['tags'])) $blog['tags'] = [];
if (!isset($blog['meta_title'])) $blog['meta_title'] = '';
if (!isset($blog['meta_description'])) $blog['meta_description'] = '';
if (!isset($blog['meta_keywords'])) $blog['meta_keywords'] = '';
if (!isset($blog['hiring_status'])) $blog['hiring_status'] = 1;
if (!isset($blog['position'])) $blog['position'] = '';
if (!isset($blog['expires_in_days'])) $blog['expires_in_days'] = '';
if (!isset($blog['contact_email'])) $blog['contact_email'] = '';
if (!isset($blog['contact_phone'])) $blog['contact_phone'] = '';

// Content for editor (prioritize full_content over content)
$contentForEditor = !empty($blog['full_content']) ? $blog['full_content'] : ($blog['content'] ?? '');
$contentForEditor = (string) $contentForEditor;
$contentForEditorHidden = htmlspecialchars($contentForEditor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

// Tags for input
$tagsForInput = [];
foreach ($blog['tags'] ?? [] as $t) {
    if (is_array($t) && array_key_exists('name', $t)) {
        $tagsForInput[] = (string) $t['name'];
    }
}

// Check if recruitment category is selected
$hasRecruitmentCategory = in_array(7, $selectedCategoryIds);
?>

<div class="page-header">
    <h4><i class="bi bi-newspaper me-2"></i>Chỉnh sửa tin tức</h4>
    <a href="/blogs" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Quay lại</a>
</div>

<form method="POST" action="/blogs/update/<?= $blog['id'] ?>" enctype="multipart/form-data" onsubmit="return validateBlogFormWithCategories()">
    <div class="admin-form-card blog-form-tabs">
        <!-- Tab Navigation -->
        <ul class="nav nav-tabs" id="blogTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic" type="button" role="tab">
                    <i class="bi bi-info-circle me-2"></i>Thông tin cơ bản
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab">
                    <i class="bi bi-file-text me-2"></i>Chi tiết tin tức
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="seo-tab" data-bs-toggle="tab" data-bs-target="#seo" type="button" role="tab">
                    <i class="bi bi-search me-2"></i>SEO & Metadata
                </button>
            </li>
            <li class="nav-item" role="presentation" id="recruitment-tab-nav" style="display: <?= $hasRecruitmentCategory ? 'block' : 'none' ?>;">
                <button class="nav-link" id="recruitment-tab" data-bs-toggle="tab" data-bs-target="#recruitment" type="button" role="tab">
                    <i class="bi bi-briefcase me-2"></i>Tuyển dụng
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="blogTabContent">
            
            <!-- Basic Information Tab -->
            <div class="tab-pane fade show active" id="basic" role="tabpanel">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="title" class="form-label">Tiêu đề tin tức <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($blog['title'] ?? '') ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="slug" name="slug" value="<?= htmlspecialchars($blog['slug'] ?? '') ?>">
                            <div class="form-text">URL thân thiện, sẽ tự động tạo từ tiêu đề</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Danh mục tin tức <span class="text-danger">*</span></label>
                            <div class="category-hierarchy-checkboxes border rounded p-3">
                                <div class="form-text mb-2">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Chọn danh mục con → danh mục cha tự động được chọn
                                </div>
                                <?php if (!empty($categoriesHierarchy)): ?>
                                    <?= renderCategoryCheckboxes($categoriesHierarchy, $selectedCategoryIds) ?>
                                <?php else: ?>
                                    <div class="text-muted">Chưa có danh mục nào. <a href="/blog-categories/create">Tạo danh mục đầu tiên</a></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="excerpt" class="form-label">Tóm tắt ngắn</label>
                            <textarea class="form-control" id="excerpt" name="excerpt" rows="3" placeholder="Nhập tóm tắt ngắn về tin tức..."><?= htmlspecialchars($blog['excerpt'] ?? '') ?></textarea>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select class="form-select" id="status" name="status">
                                <option value="1" <?= ($blog['status'] == 1) ? 'selected' : '' ?>>Kích hoạt</option>
                                <option value="0" <?= ($blog['status'] == 0) ? 'selected' : '' ?>>Vô hiệu hóa</option>
                            </select>
                        </div>
                        

                        
                                                
                        <div class="mb-3">
                            <label for="author" class="form-label">Tác giả</label>
                            <input type="text" class="form-control" id="author" name="author" value="<?= htmlspecialchars($blog['author'] ?? 'Admin') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Details Tab -->
            <div class="tab-pane fade" id="details" role="tabpanel">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="tags" class="form-label">Thẻ (Tags)</label>
                            <input type="text" class="form-control" id="tags" name="tags" 
                                value="<?= htmlspecialchars(implode(', ', $tagsForInput), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" 
                                placeholder="công nghệ, xây dựng, tư vấn">
                            <div class="form-text">Phân cách bằng dấu phẩy</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="image" class="form-label">Ảnh bài viết</label>
                            <?php if (!empty($blog['image'])): ?>
                                <div class="mb-2" id="currentImageWrap">
                                    <img src="<?= htmlspecialchars((string) ($blog['image'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" alt="Current image" class="img-thumbnail" style="max-width: 200px;" id="currentImage" data-original-src="<?= htmlspecialchars((string) ($blog['image'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
                                    <div class="form-text">Ảnh hiện tại</div>
                                    <div class="mt-1" id="currentImageBtns">
                                        <button type="button" class="btn btn-sm btn-outline-primary me-2" onclick="editCurrentImage()">
                                            <i class="bi bi-pencil"></i> Chỉnh sửa ảnh
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeCurrentImage()">
                                            <i class="bi bi-trash"></i> Xóa ảnh
                                        </button>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <!-- Hidden inputs cho image editor -->
                            <input type="hidden" id="imageEdited"     name="image_edited"      value="">
                            <input type="hidden" id="imageEditedFlag" name="image_edited_flag"  value="0">
                            <input type="hidden" name="remove_image"  id="removeImageFlag"      value="0">
                            <div class="mt-2">
                                <img id="image-preview" style="max-width: 200px; display: none;" class="img-thumbnail">
                                <!-- Preview controls sẽ được thêm tự động bởi JS -->
                            </div>
                            <div class="form-text">Chọn ảnh mới để thay thế (để trống nếu giữ ảnh cũ). Hỗ trợ: JPG, PNG, GIF, WebP (tối đa 5MB) &nbsp;·&nbsp; Ảnh sẽ mở editor để chỉnh sửa trước khi lưu &nbsp;·&nbsp; Bấm vào ảnh preview để chỉnh sửa lại</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="content" class="form-label">Nội dung chi tiết bài viết</label>
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
                                <div class="rich-editor-content" contenteditable="true" data-placeholder="Nhập nội dung chi tiết bài viết...">
                                    <?= $contentForEditor ?>
                                </div>
                                <input type="hidden" id="content" name="content" value="<?= $contentForEditorHidden ?>">
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
                            <input type="text" class="form-control" id="meta_title" name="meta_title" 
                                value="<?= htmlspecialchars($blog['meta_title'] ?? '') ?>" maxlength="60">
                            <div class="form-text">Tối đa 60 ký tự. <button type="button" class="btn btn-link btn-sm p-0" onclick="generateMetaTitle()">Gợi ý</button></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="meta_description" class="form-label">Meta Description</label>
                            <textarea class="form-control" id="meta_description" name="meta_description" rows="3" maxlength="160"><?= htmlspecialchars($blog['meta_description'] ?? '') ?></textarea>
                            <div class="form-text">Tối đa 160 ký tự. <button type="button" class="btn btn-link btn-sm p-0" onclick="generateMetaDescription()">Gợi ý</button></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="meta_keywords" class="form-label">Meta Keywords</label>
                            <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" 
                                value="<?= htmlspecialchars($blog['meta_keywords'] ?? '') ?>" placeholder="từ khóa 1, từ khóa 2, từ khóa 3">
                            <div class="form-text">Phân cách bằng dấu phẩy. <button type="button" class="btn btn-link btn-sm p-0" onclick="generateMetaKeywords()">Gợi ý</button></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recruitment Tab (chỉ hiển thị khi chọn danh mục tuyển dụng) -->
            <div class="tab-pane fade" id="recruitment" role="tabpanel">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="hiring_status" class="form-label">Trạng thái tuyển dụng</label>
                            <select class="form-select" id="hiring_status" name="hiring_status">
                                <option value="1" <?= ($blog['hiring_status'] == 1) ? 'selected' : '' ?>>Đang tuyển dụng</option>
                                <option value="0" <?= ($blog['hiring_status'] == 0) ? 'selected' : '' ?>>Ngừng tuyển</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="position" class="form-label">Tên vị trí tuyển dụng</label>
                            <input type="text" class="form-control" id="position" name="position" 
                                value="<?= htmlspecialchars($blog['position'] ?? '') ?>" placeholder="Ví dụ: Kỹ sư xây dựng">
                        </div>
                        
                        <div class="mb-3">
                            <label for="expires_in_days" class="form-label">Thời gian ứng tuyển (ngày)</label>
                            <input type="number" class="form-control" id="expires_in_days" name="expires_in_days" 
                                value="<?= htmlspecialchars($blog['expires_in_days'] ?? '') ?>" min="1" max="365" placeholder="30">
                            <div class="form-text">Số ngày kể từ khi đăng tin. Để trống nếu không giới hạn thời gian</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="contact_email" class="form-label">Email liên hệ</label>
                            <input type="email" class="form-control" id="contact_email" name="contact_email" 
                                value="<?= htmlspecialchars($blog['contact_email'] ?? '') ?>" placeholder="hr@mtech.com">
                        </div>
                        
                        <div class="mb-3">
                            <label for="contact_phone" class="form-label">Số điện thoại liên hệ</label>
                            <input type="tel" class="form-control" id="contact_phone" name="contact_phone" 
                                value="<?= htmlspecialchars($blog['contact_phone'] ?? '') ?>" placeholder="0123456789">
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="alert alert-info">
                            <h6><i class="bi bi-info-circle me-2"></i>Thông tin tuyển dụng</h6>
                            <p class="mb-0 small">Các thông tin này sẽ được hiển thị trong form ứng tuyển trên website người dùng.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
            <a href="/blogs" class="btn btn-outline-secondary">
                <i class="bi bi-x-lg me-2"></i>Hủy
            </a>
            <div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-2"></i>Cập nhật tin tức
                </button>
            </div>
        </div>
    </div>
</form>
