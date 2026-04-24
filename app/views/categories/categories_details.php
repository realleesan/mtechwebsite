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

<!--================ Start Service Details Area =================-->
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
                        <?php foreach ($allCategories as $cat): ?>
                            <li class="nav-item<?php echo ($cat['slug'] === $currentSlug) ? ' active' : ''; ?>">
                                <a class="nav-link<?php echo ($cat['slug'] === $currentSlug) ? ' active' : ''; ?>"
                                   href="?page=categories-details&slug=<?php echo $h($cat['slug']); ?>"
                                   title="<?php echo $h($cat['name']); ?>">
                                    <?php echo $h($cat['name']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <!-- Get in touch -->
                    <div class="sidebar_contact_info mb_40">
                        <h3 class="f_600 title_color">Get in touch</h3>
                        <span class="title_br"></span>
                        <a href="tel:18006854321"><i class="fa fa-phone"></i>1800 685 4321</a>
                        <a href="mailto:info@infratek.com"><i class="fa fa-paper-plane"></i>info@infratek.com</a>
                    </div>

                    <!-- Download buttons -->
                    <div class="download_info">
                        <a href="#" class="download-btn2">
                            <i class="fa fa-file-pdf-o"></i> PDF. Download
                        </a>
                        <a href="#" class="download-btn2">
                            <i class="fa fa-file-word-o"></i> DOC. Download
                        </a>
                    </div>

                </div>
            </div><!-- /.col-lg-3 -->

            <!-- ================================================
                 NỘI DUNG CHÍNH PHẢI
                 ================================================ -->
            <div class="col-lg-9">
                <div class="service_right_sidebar">

                    <!-- ── Gallery 2 ảnh ── -->
                    <?php if (!empty($categoryDetail['image_1']) || !empty($categoryDetail['image_2'])): ?>
                    <div class="service_img mr">
                        <?php if (!empty($categoryDetail['image_1'])): ?>
                        <div class="image w_55">
                            <img src="<?php echo $h($categoryDetail['image_1']); ?>"
                                 alt="<?php echo $h($categoryDetail['name']); ?>">
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($categoryDetail['image_2'])): ?>
                        <div class="image w_45">
                            <img src="<?php echo $h($categoryDetail['image_2']); ?>"
                                 alt="<?php echo $h($categoryDetail['name']); ?>">
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php elseif (!empty($categoryDetail['image'])): ?>
                    <div class="service_img mr">
                        <div class="image w_100">
                            <img src="<?php echo $h($categoryDetail['image']); ?>"
                                 alt="<?php echo $h($categoryDetail['name']); ?>">
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
                                 alt="<?php echo $h($categoryDetail['benefit_title'] ?? 'Benefit'); ?>">
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

                        <!-- ── Feature Items ── -->
                        <?php
                        $hasFeature = !empty($categoryDetail['feature_1_title']) || !empty($categoryDetail['feature_2_title']);
                        if ($hasFeature):
                        ?>
                        <div class="feature_service">
                            <?php if (!empty($categoryDetail['feature_image'])): ?>
                            <img src="<?php echo $h($categoryDetail['feature_image']); ?>"
                                 alt="Features">
                            <?php endif; ?>
                            <div class="media-body">
                                <?php if (!empty($categoryDetail['feature_1_title'])): ?>
                                <div class="servie_item">
                                    <div class="servie_icon">
                                        <!-- Icon: clock / time -->
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" aria-hidden="true">
                                            <path d="M32 4C16.536 4 4 16.536 4 32s12.536 28 28 28 28-12.536 28-28S47.464 4 32 4zm0 52C18.745 56 8 45.255 8 32S18.745 8 32 8s24 10.745 24 24-10.745 24-24 24zm2-38h-4v18l10.586 10.586 2.828-2.828L34 33.172V18z"/>
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
                                        <!-- Icon: gear / settings -->
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" aria-hidden="true">
                                            <path d="M54.122 34.344a22.543 22.543 0 0 0 .2-2.344c0-.8-.078-1.578-.2-2.344l5.056-3.944a1.208 1.208 0 0 0 .288-1.534l-4.8-8.312a1.2 1.2 0 0 0-1.456-.528l-5.966 2.4a17.44 17.44 0 0 0-4.034-2.344l-.9-6.344A1.164 1.164 0 0 0 41.122 8h-9.6a1.164 1.164 0 0 0-1.188 1.022l-.9 6.344a18.3 18.3 0 0 0-4.034 2.344l-5.966-2.4a1.164 1.164 0 0 0-1.456.528l-4.8 8.312a1.176 1.176 0 0 0 .288 1.534l5.056 3.944A18.5 18.5 0 0 0 18.322 32c0 .778.078 1.556.2 2.344l-5.056 3.944a1.208 1.208 0 0 0-.288 1.534l4.8 8.312a1.2 1.2 0 0 0 1.456.528l5.966-2.4a17.44 17.44 0 0 0 4.034 2.344l.9 6.344A1.164 1.164 0 0 0 31.522 56h9.6a1.164 1.164 0 0 0 1.188-1.022l.9-6.344a18.3 18.3 0 0 0 4.034-2.344l5.966 2.4a1.164 1.164 0 0 0 1.456-.528l4.8-8.312a1.176 1.176 0 0 0-.288-1.534zM36.322 40a8 8 0 1 1 8-8 8.009 8.009 0 0 1-8 8z"/>
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
                        </div>
                        <?php endif; ?>

                        <!-- ── FAQ Accordion ── -->
                        <?php if (!empty($categoryDetail['faq_items']) && is_array($categoryDetail['faq_items'])): ?>
                        <div id="accordion" class="panel-group faq-accordion service_accordion" role="tablist" aria-multiselectable="true">
                            <h3 class="s_title title_color f_600">More information</h3>

                            <?php foreach ($categoryDetail['faq_items'] as $index => $faq):
                                $collapseId  = 'collapse_' . $h($currentSlug) . '_' . $index;
                                $headingId   = 'heading_'  . $h($currentSlug) . '_' . $index;
                            ?>
                            <div class="card">
                                <div id="<?php echo $headingId; ?>" class="card-header">
                                    <h4 class="panel-title">
                                        <button class="btn btn-link btn-accordion collapsed"
                                                data-toggle="collapse"
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
<!--================ End Service Details Area =================-->
