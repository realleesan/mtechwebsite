<?php
// $blog — hiển thị cùng nguồn ưu tiên như trang user (full_content)
$previewBody = !empty($blog['full_content']) ? $blog['full_content'] : ($blog['content'] ?? '');

$h = static function ($v): string {
    return htmlspecialchars((string) ($v ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};

// Ensure all fields have default values to prevent undefined index errors
if (!isset($blog['id'])) $blog['id'] = 0;
if (!isset($blog['title'])) $blog['title'] = '';
if (!isset($blog['slug'])) $blog['slug'] = '';
if (!isset($blog['category_name'])) $blog['category_name'] = 'Chưa có';
if (!isset($blog['author'])) $blog['author'] = 'Admin';
if (!isset($blog['excerpt'])) $blog['excerpt'] = '';
if (!isset($blog['image'])) $blog['image'] = '';
if (!isset($blog['status'])) $blog['status'] = 0;
if (!isset($blog['is_featured'])) $blog['is_featured'] = 0;
if (!isset($blog['views'])) $blog['views'] = 0;
if (!isset($blog['created_at'])) $blog['created_at'] = '';
if (!isset($blog['updated_at'])) $blog['updated_at'] = '';
if (!isset($blog['tags'])) $blog['tags'] = [];
if (!isset($blog['meta_title'])) $blog['meta_title'] = '';
if (!isset($blog['meta_description'])) $blog['meta_description'] = '';
if (!isset($blog['meta_keywords'])) $blog['meta_keywords'] = '';
if (!isset($blog['category_id'])) $blog['category_id'] = 0;
if (!isset($blog['hiring_status'])) $blog['hiring_status'] = 0;
if (!isset($blog['position'])) $blog['position'] = '';
if (!isset($blog['expires_in_days'])) $blog['expires_in_days'] = '';
if (!isset($blog['contact_email'])) $blog['contact_email'] = '';
if (!isset($blog['contact_phone'])) $blog['contact_phone'] = '';
?>
<div class="page-header">
    <h4><i class="bi bi-newspaper me-2"></i>Chi tiết tin tức</h4>
    <div class="d-flex gap-2">
        <a href="/blogs" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Quay lại
        </a>
        <a href="/blogs/edit/<?= $blog['id'] ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-pencil me-1"></i>Chỉnh sửa
        </a>
    </div>
</div>

<div class="admin-form-card">
    <div class="row">
        <div class="col-md-8">
            <!-- Basic Info -->
            <div class="mb-4">
                <h5 class="border-bottom pb-2 mb-3">Thông tin cơ bản</h5>
                
                <div class="row mb-3">
                    <div class="col-sm-3"><strong>ID:</strong></div>
                    <div class="col-sm-9"><?= $blog['id'] ?></div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-sm-3"><strong>Tiêu đề:</strong></div>
                    <div class="col-sm-9"><?= $h($blog['title'] ?? '') ?></div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-sm-3"><strong>Slug:</strong></div>
                    <div class="col-sm-9"><code><?= $h($blog['slug'] ?? '') ?></code></div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-sm-3"><strong>Danh mục:</strong></div>
                    <div class="col-sm-9">
                        <span class="badge bg-secondary"><?= $h($blog['category_name'] ?? 'Chưa có') ?></span>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-sm-3"><strong>Tác giả:</strong></div>
                    <div class="col-sm-9"><?= $h($blog['author'] ?? 'Admin') ?></div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-sm-3"><strong>Tóm tắt:</strong></div>
                    <div class="col-sm-9"><?= $h($blog['excerpt'] ?? '') ?></div>
                </div>
            </div>

            <!-- Content -->
            <?php if ($previewBody !== ''): ?>
            <div class="mb-4">
                <h5 class="border-bottom pb-2 mb-3">Nội dung</h5>
                <div class="content-preview border rounded p-3">
                    <?= $previewBody ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Tags -->
            <?php if (!empty($blog['tags'])): ?>
            <div class="mb-4">
                <h5 class="border-bottom pb-2 mb-3">Tags</h5>
                <div>
                    <?php foreach (($blog['tags'] ?? []) as $tag): ?>
                        <span class="badge bg-info me-1"><?= $h(is_array($tag) ? ($tag['name'] ?? '') : '') ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- SEO Metadata -->
            <?php if (!empty($blog['meta_title']) || !empty($blog['meta_description']) || !empty($blog['meta_keywords'])): ?>
            <div class="mb-4">
                <h5 class="border-bottom pb-2 mb-3">SEO & Metadata</h5>
                
                <?php if (!empty($blog['meta_title'])): ?>
                <div class="row mb-3">
                    <div class="col-sm-3"><strong>Meta Title:</strong></div>
                    <div class="col-sm-9"><?= $h($blog['meta_title'] ?? '') ?></div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($blog['meta_description'])): ?>
                <div class="row mb-3">
                    <div class="col-sm-3"><strong>Meta Description:</strong></div>
                    <div class="col-sm-9"><?= htmlspecialchars($blog['meta_description']) ?></div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($blog['meta_keywords'])): ?>
                <div class="row mb-3">
                    <div class="col-sm-3"><strong>Meta Keywords:</strong></div>
                    <div class="col-sm-9"><?= $h($blog['meta_keywords'] ?? '') ?></div>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Recruitment Info (if applicable) -->
            <?php if ((int) ($blog['category_id'] ?? 0) === 7): ?>
            <div class="mb-4">
                <h5 class="border-bottom pb-2 mb-3">Thông tin tuyển dụng</h5>
                
                <div class="row mb-3">
                    <div class="col-sm-3"><strong>Trạng thái tuyển dụng:</strong></div>
                    <div class="col-sm-9">
                        <?php if ((int) ($blog['hiring_status'] ?? 0) === 1): ?>
                            <span class="badge bg-success">Đang tuyển dụng</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Ngừng tuyển</span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if (!empty($blog['position'])): ?>
                <div class="row mb-3">
                    <div class="col-sm-3"><strong>Vị trí tuyển dụng:</strong></div>
                    <div class="col-sm-9"><?= $h($blog['position'] ?? '') ?></div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($blog['expires_in_days'])): ?>
                <div class="row mb-3">
                    <div class="col-sm-3"><strong>Thời hạn ứng tuyển:</strong></div>
                    <div class="col-sm-9"><?= $blog['expires_in_days'] ?> ngày</div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($blog['contact_email'])): ?>
                <div class="row mb-3">
                    <div class="col-sm-3"><strong>Email liên hệ:</strong></div>
                    <div class="col-sm-9">
                        <a href="mailto:<?= $h($blog['contact_email'] ?? '') ?>">
                            <?= $h($blog['contact_email'] ?? '') ?>
                        </a>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($blog['contact_phone'])): ?>
                <div class="row mb-3">
                    <div class="col-sm-3"><strong>Số điện thoại:</strong></div>
                    <div class="col-sm-9">
                        <a href="tel:<?= $h($blog['contact_phone'] ?? '') ?>">
                            <?= $h($blog['contact_phone'] ?? '') ?>
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="col-md-4">
            <!-- Image -->
            <?php if (!empty($blog['image'])): ?>
            <div class="mb-4">
                <h6 class="border-bottom pb-2 mb-3">Ảnh bài viết</h6>
                <img src="<?= $h($blog['image'] ?? '') ?>" alt="<?= $h($blog['title'] ?? '') ?>" 
                     class="img-fluid rounded border" style="max-width: 100%;">
            </div>
            <?php endif; ?>

            <!-- Status & Stats -->
            <div class="mb-4">
                <h6 class="border-bottom pb-2 mb-3">Trạng thái & Thống kê</h6>
                
                <div class="mb-3">
                    <strong>Trạng thái:</strong><br>
                    <?php if ((int) ($blog['status'] ?? 0) === 1): ?>
                        <span class="badge bg-success">Kích hoạt</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Vô hiệu hóa</span>
                    <?php endif; ?>
                    
                    <?php if ((int) ($blog['is_featured'] ?? 0) === 1): ?>
                        <span class="badge bg-warning text-dark ms-1">Nổi bật</span>
                    <?php endif; ?>
                </div>
                
                
                <div class="mb-3">
                    <strong>Lượt xem:</strong><br>
                    <span class="text-muted"><?= number_format($blog['views'] ?? 0) ?></span>
                </div>
                
                <div class="mb-3">
                    <strong>Ngày tạo:</strong><br>
                    <span class="text-muted"><?= isset($blog['created_at']) ? date('d/m/Y H:i', strtotime($blog['created_at'])) : '' ?></span>
                </div>
                
                <?php if (!empty($blog['updated_at']) && $blog['updated_at'] != $blog['created_at']): ?>
                <div class="mb-3">
                    <strong>Cập nhật lần cuối:</strong><br>
                    <span class="text-muted"><?= date('d/m/Y H:i', strtotime($blog['updated_at'])) ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.content-preview {
    max-height: 400px;
    overflow-y: auto;
    background: #f8f9fa;
}

.content-preview img {
    max-width: 100%;
    height: auto;
}
</style>