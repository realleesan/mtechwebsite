<?php
// $categories
?>

<div class="page-header">
    <h4><i class="bi bi-newspaper me-2"></i>Thêm tin tức mới</h4>
    <a href="/blogs" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Quay lại</a>
</div>

<form method="POST" action="/blogs/store" enctype="multipart/form-data" onsubmit="return validateBlogForm()">
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
            <li class="nav-item" role="presentation" id="recruitment-tab-nav" style="display: none;">
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
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="slug" name="slug" required>
                            <div class="form-text">URL thân thiện, sẽ tự động tạo từ tiêu đề</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Danh mục <span class="text-danger">*</span></label>
                            <select class="form-select" id="category_id" name="category_id" required onchange="toggleRecruitmentTab()">
                                <option value="">-- Chọn danh mục --</option>
                                <?php foreach ($categories ?? [] as $category): ?>
                                    <option value="<?= htmlspecialchars($category['id']) ?>">
                                        <?= htmlspecialchars($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="excerpt" class="form-label">Tóm tắt ngắn</label>
                            <textarea class="form-control" id="excerpt" name="excerpt" rows="3" placeholder="Nhập tóm tắt ngắn về tin tức..."></textarea>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select class="form-select" id="status" name="status">
                                <option value="1">Kích hoạt</option>
                                <option value="0">Vô hiệu hóa</option>
                            </select>
                        </div>
                        
                        
                                                
                        <div class="mb-3">
                            <label for="author" class="form-label">Tác giả</label>
                            <input type="text" class="form-control" id="author" name="author" value="<?= htmlspecialchars($admin['username'] ?? 'Admin') ?>">
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
                            <input type="text" class="form-control" id="tags" name="tags" placeholder="công nghệ, xây dựng, tư vấn">
                            <div class="form-text">Phân cách bằng dấu phẩy</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="image" class="form-label">Ảnh bài viết</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <!-- Hidden inputs cho image editor -->
                            <input type="hidden" id="imageEdited"     name="image_edited"   value="">
                            <input type="hidden" id="imageEditedFlag" name="image_edited_flag" value="0">
                            <div class="mt-2">
                                <img id="image-preview" style="max-width: 200px; display: none;" class="img-thumbnail">
                            </div>
                            <div class="form-text">Hỗ trợ: JPG, PNG, GIF, WebP (tối đa 5MB) &nbsp;·&nbsp; Ảnh sẽ mở editor để chỉnh sửa trước khi lưu</div>
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
                                </div>
                                <input type="hidden" id="content" name="content" value="">
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
                            <input type="text" class="form-control" id="meta_title" name="meta_title" maxlength="60">
                            <div class="form-text">Tối đa 60 ký tự. <button type="button" class="btn btn-link btn-sm p-0" onclick="generateMetaTitle()">Gợi ý</button></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="meta_description" class="form-label">Meta Description</label>
                            <textarea class="form-control" id="meta_description" name="meta_description" rows="3" maxlength="160"></textarea>
                            <div class="form-text">Tối đa 160 ký tự. <button type="button" class="btn btn-link btn-sm p-0" onclick="generateMetaDescription()">Gợi ý</button></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="meta_keywords" class="form-label">Meta Keywords</label>
                            <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" placeholder="từ khóa 1, từ khóa 2, từ khóa 3">
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
                                <option value="1">Đang tuyển dụng</option>
                                <option value="0">Ngừng tuyển</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="position" class="form-label">Tên vị trí tuyển dụng</label>
                            <input type="text" class="form-control" id="position" name="position" placeholder="Ví dụ: Kỹ sư xây dựng">
                        </div>
                        
                        <div class="mb-3">
                            <label for="expires_in_days" class="form-label">Thời gian ứng tuyển (ngày)</label>
                            <input type="number" class="form-control" id="expires_in_days" name="expires_in_days" min="1" max="365" placeholder="30">
                            <div class="form-text">Số ngày kể từ khi đăng tin. Để trống nếu không giới hạn thời gian</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="contact_email" class="form-label">Email liên hệ</label>
                            <input type="email" class="form-control" id="contact_email" name="contact_email" placeholder="hr@mtech.com">
                        </div>
                        
                        <div class="mb-3">
                            <label for="contact_phone" class="form-label">Số điện thoại liên hệ</label>
                            <input type="tel" class="form-control" id="contact_phone" name="contact_phone" placeholder="0123456789">
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
                    <i class="bi bi-check-lg me-2"></i>Lưu tin tức
                </button>
            </div>
        </div>
    </div>
</form>

<script>
// Toggle recruitment tab based on category selection
function toggleRecruitmentTab() {
    const categorySelect = document.getElementById('category_id');
    const recruitmentTabNav = document.getElementById('recruitment-tab-nav');
    
    // Giả sử category tuyển dụng có ID = 7 (dựa vào ảnh bạn gửi)
    if (categorySelect.value == '7') {
        recruitmentTabNav.style.display = 'block';
    } else {
        recruitmentTabNav.style.display = 'none';
        // Reset recruitment tab if it was active
        const recruitmentTab = document.getElementById('recruitment-tab');
        if (recruitmentTab.classList.contains('active')) {
            document.getElementById('basic-tab').click();
        }
    }
}

// Auto-generate slug from title
document.getElementById('title').addEventListener('input', function() {
    const title = this.value;
    const slug = title.toLowerCase()
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
        .trim('-');
    document.getElementById('slug').value = slug;
});

// Generate meta suggestions
function generateMetaTitle() {
    const title = document.getElementById('title').value;
    if (title) {
        document.getElementById('meta_title').value = title + ' - MTech';
    }
}

function generateMetaDescription() {
    const excerpt = document.getElementById('excerpt').value;
    if (excerpt) {
        document.getElementById('meta_description').value = excerpt.substring(0, 160);
    }
}

function generateMetaKeywords() {
    const tags = document.getElementById('tags').value;
    if (tags) {
        document.getElementById('meta_keywords').value = tags;
    }
}

// Form validation
function validateBlogForm() {
    const title = document.getElementById('title').value.trim();
    const slug = document.getElementById('slug').value.trim();
    const categoryId = document.getElementById('category_id').value;
    
    if (!title) {
        alert('Vui lòng nhập tiêu đề tin tức');
        return false;
    }
    
    if (!slug) {
        alert('Vui lòng nhập slug');
        return false;
    }
    
    if (!categoryId) {
        alert('Vui lòng chọn danh mục');
        return false;
    }
    
    // Update rich editor content to hidden input
    const richEditorContent = document.querySelector('.rich-editor-content');
    const contentInput = document.querySelector('input[name="content"]');
    if (richEditorContent && contentInput) {
        contentInput.value = richEditorContent.innerHTML;
    }
    
    return true;
}
</script>
