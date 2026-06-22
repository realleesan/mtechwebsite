<div class="page-header">
    <h4><i class="bi bi-grid me-2"></i>Thêm dịch vụ mới</h4>
    <a href="/categories" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Quay lại
    </a>
</div>

<form method="POST" action="/categories/store"
      enctype="multipart/form-data" id="categoryForm" onsubmit="return prepareCategoryForm()">

    <div class="admin-form-card">

        <!-- Tab Navigation -->
        <ul class="nav nav-tabs mb-4" id="categoryTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="basic-tab" data-bs-toggle="tab"
                        data-bs-target="#basic" type="button" role="tab">
                    <i class="bi bi-info-circle me-2"></i>Thông tin cơ bản
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="detail-tab" data-bs-toggle="tab"
                        data-bs-target="#detail" type="button" role="tab">
                    <i class="bi bi-file-text me-2"></i>Chi tiết dịch vụ
                </button>
            </li>
        </ul>

        <div class="tab-content" id="categoryTabContent">

            <!-- ===== TAB 1: THÔNG TIN CƠ BẢN ===== -->
            <div class="tab-pane fade show active" id="basic" role="tabpanel">
                <div class="row">

                    <!-- Left col -->
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên dịch vụ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name"
                                   placeholder="Nhập tên dịch vụ" required>
                        </div>
                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="slug" name="slug"
                                   placeholder="url-than-thien" required>
                            <div class="form-text">URL thân thiện, tự động tạo từ tên dịch vụ</div>
                        </div>
                        <div class="mb-3">
                            <label for="parent_id" class="form-label">Dịch vụ cha (Không bắt buộc)</label>
                            <select class="form-select" id="parent_id" name="parent_id">
                                <option value="">-- Là dịch vụ gốc --</option>
                                <?php if (!empty($categories) && is_array($categories)): ?>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="form-text">Chọn dịch vụ cấp trên của dịch vụ này nếu có</div>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả ngắn</label>
                            <textarea class="form-control" id="description" name="description" rows="3"
                                      placeholder="Mô tả ngắn hiển thị trong danh sách dịch vụ..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="detail_description" class="form-label">Mô tả chi tiết dịch vụ</label>
                            <textarea class="form-control" id="detail_description" name="detail_description" rows="5"
                                      placeholder="Mô tả đầy đủ hiển thị trong trang chi tiết dịch vụ..."></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="sort_order" class="form-label">Thứ tự hiển thị</label>
                                <input type="number" class="form-control" id="sort_order" name="sort_order"
                                       value="0" min="0">
                                <div class="form-text">Số nhỏ hơn hiển thị trước</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Trạng thái</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="1">Hiển thị</option>
                                    <option value="0">Ẩn</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label d-block">Vị trí hiển thị</label>
                            <div class="d-flex flex-wrap gap-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                           id="show_in_footer" name="show_in_footer" value="1">
                                    <label class="form-check-label" for="show_in_footer">
                                        <i class="bi bi-layout-text-window-reverse me-1 text-secondary"></i>
                                        Hiển thị ở Footer
                                    </label>
                                </div>
                            </div>
                            <div class="form-text">Dịch vụ sẽ xuất hiện trong cột dịch vụ ở footer trang web</div>
                        </div>
                    </div>

                    <!-- Right col: Ảnh đại diện upload -->
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Ảnh đại diện dịch vụ <span class="text-danger">*</span></label>
                            <div class="cat-upload-area" id="mainUploadArea" data-target="image">
                                <div class="cat-upload-placeholder" id="mainPlaceholder">
                                    <i class="bi bi-image fs-1 mb-2 text-muted"></i>
                                    <p class="mb-1 text-muted">Click hoặc kéo thả ảnh vào đây</p>
                                    <small class="text-muted">JPG, PNG, WEBP (tối đa 5MB)</small>
                                </div>
                                <img id="mainPreview" src="" alt="Preview" class="cat-preview d-none">
                                <input type="file" id="image" name="image" accept="image/*" class="cat-file-input">
                            </div>
                            <div id="imageError" class="text-danger small mt-1 d-none">
                                <i class="bi bi-exclamation-circle me-1"></i>Vui lòng tải lên ảnh đại diện
                            </div>
                            <div class="form-text mt-1">Ảnh hiển thị trong danh sách dịch vụ</div>
                        </div>
                    </div>

                </div>
            </div><!-- /tab basic -->

            <!-- ===== TAB 2: CHI TIẾT DỊCH VỤ ===== -->
            <div class="tab-pane fade" id="detail" role="tabpanel">

                <!-- Gallery ảnh -->
                <h6 class="fw-semibold mb-3 border-bottom pb-2">
                    <i class="bi bi-images me-2 text-primary"></i>Ảnh gallery trang chi tiết
                    <small class="text-danger ms-1">* Ảnh 1 bắt buộc</small>
                </h6>
                <div class="row mb-4">
                    <?php
                    $gallerySlots = [
                        ['id' => 'image_1', 'label' => 'Ảnh 1', 'required' => true],
                        ['id' => 'image_2', 'label' => 'Ảnh 2', 'required' => false],
                        ['id' => 'image_3', 'label' => 'Ảnh 3', 'required' => false],
                    ];
                    foreach ($gallerySlots as $slot):
                    ?>
                    <div class="col-md-4 mb-3 cat-gallery-slot">
                        <label class="form-label">
                            <?= $slot['label'] ?>
                            <?php if ($slot['required']): ?><span class="text-danger">*</span><?php endif; ?>
                        </label>
                        <div class="cat-upload-area cat-upload-sm" id="<?= $slot['id'] ?>UploadArea" data-target="<?= $slot['id'] ?>">
                            <div class="cat-upload-placeholder" id="<?= $slot['id'] ?>Placeholder">
                                <i class="bi bi-image fs-2 mb-1 text-muted"></i>
                                <small class="text-muted d-block">Click hoặc kéo thả</small>
                            </div>
                            <img id="<?= $slot['id'] ?>Preview" src="" alt="Preview" class="cat-preview d-none">
                            <input type="file" id="<?= $slot['id'] ?>" name="<?= $slot['id'] ?>" accept="image/*" class="cat-file-input">
                        </div>
                        <?php if ($slot['required']): ?>
                        <div id="image1Error" class="text-danger small mt-1 d-none">
                            <i class="bi bi-exclamation-circle me-1"></i>Vui lòng tải lên ảnh 1
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Benefit section -->
                <h6 class="fw-semibold mb-3 border-bottom pb-2">
                    <i class="bi bi-star me-2 text-warning"></i>Phần Lợi ích dịch vụ (Benefit)
                </h6>
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label for="benefit_title" class="form-label">Tiêu đề phần Benefit</label>
                        <input type="text" class="form-control" id="benefit_title" name="benefit_title"
                               placeholder="VD: Lợi ích dịch vụ">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ảnh minh họa Benefit <span class="text-danger">*</span></label>
                        <div class="cat-upload-area cat-upload-sm" id="benefitImgUploadArea" data-target="benefit_image">
                            <div class="cat-upload-placeholder" id="benefitImgPlaceholder">
                                <i class="bi bi-image fs-2 mb-1 text-muted"></i>
                                <small class="text-muted d-block">Click hoặc kéo thả</small>
                            </div>
                            <img id="benefitImgPreview" src="" alt="Preview" class="cat-preview d-none">
                            <input type="file" id="benefit_image" name="benefit_image" accept="image/*" class="cat-file-input">
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="benefit_description" class="form-label">Mô tả phần Benefit</label>
                        <textarea class="form-control" id="benefit_description" name="benefit_description" rows="3"
                                  placeholder="Mô tả ngắn về lợi ích..."></textarea>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="benefit_items_text" class="form-label">Danh sách lợi ích (mỗi dòng 1 mục)</label>
                        <textarea class="form-control" id="benefit_items_text" name="benefit_items_text" rows="5"
                                  placeholder="Lợi ích 1&#10;Lợi ích 2&#10;Lợi ích 3"></textarea>
                        <input type="hidden" id="benefit_items" name="benefit_items" value="">
                        <div class="form-text">Mỗi dòng là một bullet point</div>
                    </div>
                </div>

                <!-- Feature section -->
                <h6 class="fw-semibold mb-3 border-bottom pb-2">
                    <i class="bi bi-building me-2 text-info"></i>Dự án tiêu biểu
                </h6>
                <div class="row mb-4">
                    <div class="col-md-12 mb-3 cat-feature-img">
                        <label class="form-label">Ảnh minh họa Dự án <span class="text-danger">*</span></label>
                        <div class="cat-upload-area cat-upload-sm" id="featureImgUploadArea" data-target="feature_image">
                            <div class="cat-upload-placeholder" id="featureImgPlaceholder">
                                <i class="bi bi-image fs-2 mb-1 text-muted"></i>
                                <small class="text-muted d-block">Click hoặc kéo thả</small>
                            </div>
                            <img id="featureImgPreview" src="" alt="Preview" class="cat-preview d-none">
                            <input type="file" id="feature_image" name="feature_image" accept="image/*" class="cat-file-input">
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card border-0 bg-light p-3">
                            <h6 class="small fw-semibold mb-2 text-muted">DỰ ÁN 1</h6>
                            <div class="mb-2">
                                <label class="form-label small">Icon class</label>
                                <input type="text" class="form-control form-control-sm" name="feature_1_icon" placeholder="flaticon-clock">
                            </div>
                            <div class="mb-2">
                                <label class="form-label small">Tiêu đề</label>
                                <input type="text" class="form-control form-control-sm" name="feature_1_title" placeholder="Tên dự án 1">
                            </div>
                            <div>
                                <label class="form-label small">Mô tả</label>
                                <textarea class="form-control form-control-sm" name="feature_1_text" rows="3" placeholder="Mô tả dự án 1..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card border-0 bg-light p-3">
                            <h6 class="small fw-semibold mb-2 text-muted">DỰ ÁN 2</h6>
                            <div class="mb-2">
                                <label class="form-label small">Icon class</label>
                                <input type="text" class="form-control form-control-sm" name="feature_2_icon" placeholder="flaticon-gear">
                            </div>
                            <div class="mb-2">
                                <label class="form-label small">Tiêu đề</label>
                                <input type="text" class="form-control form-control-sm" name="feature_2_title" placeholder="Tên dự án 2">
                            </div>
                            <div>
                                <label class="form-label small">Mô tả</label>
                                <textarea class="form-control form-control-sm" name="feature_2_text" rows="3" placeholder="Mô tả dự án 2..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQ section -->
                <h6 class="fw-semibold mb-3 border-bottom pb-2">
                    <i class="bi bi-question-circle me-2 text-success"></i>Câu hỏi thường gặp (FAQ)
                </h6>
                <div id="faqContainer" class="mb-3"></div>
                <button type="button" class="btn btn-sm btn-outline-success mb-3" id="addFaqBtn">
                    <i class="bi bi-plus-lg me-1"></i>Thêm câu hỏi
                </button>
                <input type="hidden" id="faq_items" name="faq_items" value="[]">

            </div><!-- /tab detail -->

        </div><!-- /tab-content -->

        <!-- Form Actions -->
        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
            <a href="/categories" class="btn btn-outline-secondary">
                <i class="bi bi-x-lg me-2"></i>Hủy
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-2"></i>Lưu dịch vụ
            </button>
        </div>

    </div><!-- /admin-form-card -->
</form>
