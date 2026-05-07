<?php
/**
 * categories_details.php
 * View: Trang chi tiết danh mục dịch vụ (Service Details)
 *
 * Biến được truyền từ index.php:
 *   $categoryDetail  - array: dữ liệu chi tiết category hiện tại
 *   $allCategories   - array: tất cả categories (dùng cho sidebar menu)
 */

// Guard: đảm bảo dữ liệu tồn tại
if (empty($categoryDetail) || empty($allCategories)) {
    require_once 'app/models/CategoriesModel.php';
    $categoriesModel = new CategoriesModel();

    if (empty($categoryDetail)) {
        $slug           = isset($_GET['slug']) ? trim($_GET['slug']) : '';
        $categoryDetail = $categoriesModel->getCategoryDetailBySlug($slug);
    }

    if (empty($allCategories)) {
        $allCategories = $categoriesModel->getAllCategories();
    }
}

if (empty($categoryDetail)) {
    include 'errors/404.php';
    return;
}

// Helper: lấy giá trị an toàn
$h = fn($v) => htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
$currentSlug = $categoryDetail['slug'] ?? '';
?>

<!--================ Bắt đầu Khu vực Chi tiết Dịch vụ =================-->
<section class="service_details_area sec_gap">
    <div class="container">
        <div class="row">

            <!-- ================================================
                 SIDEBAR TRÁI
                 ================================================ -->
            <div class="col-lg-3">
                <div class="service_left_sidebar">

                    <!-- Danh sách tất cả services -->
                    <ul class="nav service_menu_tab mb_40">
                        <?php foreach ($allCategories as $cat): 
                            $isActive = (trim($currentSlug) === trim($cat['slug']));
                        ?>
                            <li class="nav-item <?php echo $isActive ? 'active' : ''; ?>">
                                <a class="nav-link <?php echo $isActive ? 'active' : ''; ?>"
                                   href="/dich-vu-<?php echo $h($cat['slug']); ?>"
                                   title="<?php echo $h($cat['name']); ?>">
                                    <?php echo $h($cat['name']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <!-- Tải tài liệu -->
                    <div class="download_info">
                        <a href="#" class="download-btn2">
                            <i class="fa fa-file-pdf-o"></i> Tải tài liệu PDF
                        </a>
                        <a href="#" class="download-btn2">
                            <i class="fa fa-file-word-o"></i> Tải tài liệu DOC
                        </a>
                    </div>

                </div>
            </div><!-- /.col-lg-3 -->

            <!-- ================================================
                 NỘI DUNG CHÍNH PHẢI
                 ================================================ -->
            <div class="col-lg-9">
                <div class="service_right_sidebar">

                    <!-- ── Image Slider ── -->
                    <?php
                    $slides = array_filter([
                        $categoryDetail['image_1'] ?? '',
                        $categoryDetail['image_2'] ?? '',
                        $categoryDetail['image_3'] ?? '',
                    ]);
                    if (empty($slides) && !empty($categoryDetail['image'])) {
                        $slides = [$categoryDetail['image']];
                    }
                    $slides     = array_values($slides);
                    $slidesJson = htmlspecialchars(json_encode($slides), ENT_QUOTES, 'UTF-8');
                    $altText    = $h($categoryDetail['name']);
                    ?>
                    <?php if (!empty($slides)): ?>
                    <div class="service_slider" id="serviceSlider" data-slides="<?php echo $slidesJson; ?>">
                        <div class="slider_main">
                            <button class="slider_arrow slider_prev" aria-label="Trước">
                                <svg viewBox="0 0 24 24"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg>
                            </button>
                            <img class="slider_main_img"
                                 src="<?php echo $h($slides[0]); ?>"
                                 alt="<?php echo $altText; ?>">
                            <button class="slider_arrow slider_next" aria-label="Tiếp">
                                <svg viewBox="0 0 24 24"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg>
                            </button>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- ── Tiêu đề + mô tả chi tiết ── -->
                    <div class="service_content">
                        <h3 class="f_size_28 f_600 title_color mb_20">
                            <?php echo $h($categoryDetail['name']); ?>
                        </h3>

                        <?php if (!empty($categoryDetail['detail_description'])): ?>
                            <?php
                            // Render xuống dòng thành <p>
                            $paragraphs = array_filter(
                                array_map('trim', explode("\n", $categoryDetail['detail_description']))
                            );
                            foreach ($paragraphs as $para):
                            ?>
                            <p><?php echo $h($para); ?></p>
                            <?php endforeach; ?>
                        <?php elseif (!empty($categoryDetail['description'])): ?>
                            <p><?php echo $h($categoryDetail['description']); ?></p>
                        <?php endif; ?>

                        <!-- ── Benefit of Service ── -->
                        <?php if (!empty($categoryDetail['benefit_image']) || !empty($categoryDetail['benefit_title'])): ?>
                        <div class="benefit_service two">
                            <?php if (!empty($categoryDetail['benefit_image'])): ?>
                            <img src="<?php echo $h($categoryDetail['benefit_image']); ?>"
                                 alt="<?php echo $h($categoryDetail['benefit_title'] ?? 'Lợi ích dịch vụ'); ?>">
                            <?php endif; ?>
                            <div class="media-body">
                                <?php if (!empty($categoryDetail['benefit_title'])): ?>
                                <h3 class="s_title title_color f_600">
                                    <?php echo $h($categoryDetail['benefit_title']); ?>
                                </h3>
                                <?php endif; ?>
                                <?php if (!empty($categoryDetail['benefit_description'])): ?>
                                <p><?php echo $h($categoryDetail['benefit_description']); ?></p>
                                <?php endif; ?>
                                <?php if (!empty($categoryDetail['benefit_items']) && is_array($categoryDetail['benefit_items'])): ?>
                                <ul class="list-unstyled benefit_list">
                                    <?php foreach ($categoryDetail['benefit_items'] as $item): ?>
                                    <li>
                                        <i class="fa fa-angle-right"></i>
                                        <?php echo $h($item); ?>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- ── Đặc điểm nổi bật (Feature Items) ── -->
                        <?php
                        $hasFeature = !empty($categoryDetail['feature_1_title']) || !empty($categoryDetail['feature_2_title']);
                        if ($hasFeature):
                        ?>
                        <div class="feature_service">
                            <!-- Chữ bên trái -->
                            <div class="media-body">
                                <?php if (!empty($categoryDetail['feature_1_title'])): ?>
                                <div class="servie_item">
                                    <div class="servie_icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <circle cx="12" cy="12" r="10"/>
                                            <polyline points="12 6 12 12 16.5 14.5"/>
                                            <circle cx="12" cy="12" r="0.5" fill="currentColor"/>
                                        </svg>
                                    </div>
                                    <div class="servie_text">
                                        <h4 class="f_size_18 f_600 title_color">
                                            <?php echo $h($categoryDetail['feature_1_title']); ?>
                                        </h4>
                                        <?php if (!empty($categoryDetail['feature_1_text'])): ?>
                                        <p><?php echo $h($categoryDetail['feature_1_text']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if (!empty($categoryDetail['feature_2_title'])): ?>
                                <div class="servie_item">
                                    <div class="servie_icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <circle cx="12" cy="12" r="3"/>
                                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                                        </svg>
                                    </div>
                                    <div class="servie_text">
                                        <h4 class="f_size_18 f_600 title_color">
                                            <?php echo $h($categoryDetail['feature_2_title']); ?>
                                        </h4>
                                        <?php if (!empty($categoryDetail['feature_2_text'])): ?>
                                        <p><?php echo $h($categoryDetail['feature_2_text']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                            <!-- Ảnh bên phải -->
                            <?php if (!empty($categoryDetail['feature_image'])): ?>
                            <img src="<?php echo $h($categoryDetail['feature_image']); ?>"
                                 alt="Đặc điểm nổi bật">
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <!-- ── FAQ Accordion ── -->
                        <?php if (!empty($categoryDetail['faq_items']) && is_array($categoryDetail['faq_items'])): ?>
                        <div id="accordion" class="panel-group faq-accordion service_accordion" role="tablist" aria-multiselectable="true">
                            <h3 class="s_title title_color f_600">Câu hỏi thường gặp</h3>

                            <?php foreach ($categoryDetail['faq_items'] as $index => $faq):
                                $collapseId = 'collapse_' . $h($currentSlug) . '_' . $index;
                                $headingId  = 'heading_'  . $h($currentSlug) . '_' . $index;
                            ?>
                            <div class="card">
                                <div id="<?php echo $headingId; ?>" class="card-header">
                                    <h4 class="panel-title">
                                        <button class="btn btn-link btn-accordion collapsed"
                                                data-target="#<?php echo $collapseId; ?>"
                                                aria-expanded="false"
                                                aria-controls="<?php echo $collapseId; ?>">
                                            <span class="pluse">+</span>
                                            <span class="minus">–</span>
                                            <?php echo $h($faq['question'] ?? ''); ?>
                                        </button>
                                    </h4>
                                </div>
                                <div id="<?php echo $collapseId; ?>"
                                     class="panel-collapse collapse"
                                     aria-labelledby="<?php echo $headingId; ?>"
                                     data-parent="#accordion">
                                    <div class="card-body panel-body">
                                        <?php echo $h($faq['answer'] ?? ''); ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>

                        </div><!-- /#accordion -->
                        <?php endif; ?>

                    </div><!-- /.service_content -->
                </div><!-- /.service_right_sidebar -->
            </div><!-- /.col-lg-9 -->

        </div><!-- /.row -->
    </div><!-- /.container -->
</section>
<!--================ Kết thúc Khu vực Chi tiết Dịch vụ =================-->
