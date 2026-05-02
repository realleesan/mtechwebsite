<?php
/**
 * Footer Layout - Dynamic Data
 * Sử dụng FooterModel và CategoriesModel
 */

// Load models
require_once __DIR__ . '/../../models/FooterModel.php';
require_once __DIR__ . '/../../models/CategoriesModel.php';
require_once __DIR__ . '/../../models/HeaderModel.php';

$footerModel = new FooterModel();
$categoriesModel = new CategoriesModel();
$headerModel = new HeaderModel();

// Lấy dữ liệu footer
$footerSettings = $footerModel->getSettings();
$usefulLinks = $footerModel->getActiveLinks();
$services = $categoriesModel->getMenuServices(5);
$socialLinks = $footerModel->getVisibleSocialLinks();
$headerSettings = $headerModel->getSettings();

// Thông tin MTECH chuẩn từ hồ sơ năng lực
$companyInfo = [
    'name' => 'Công ty Cổ phần Tư vấn Kỹ thuật và Thương mại MTECH',
    'short_name' => 'MTECH.,JSC',
    'description' => 'Đơn vị tư vấn hạng I trong lĩnh vực thiết kế và giám sát công trình công nghiệp.',
    'address' => 'Tòa nhà 227 phố Nguyễn Ngọc Nại, phường Khương Mai, Quận Thanh Xuân, TP. Hà Nội',
    'phone' => $headerSettings['phone'] ?? '0243.6231691',
    'email' => 'mtechjsc2011.info@gmail.com',
    'website' => 'www.mtechjsc.com'
];
?>
<footer class="footer_area">
    <div class="footer_top">
        <div class="container">
            <div class="row">
                <!-- Cột 1: Thông tin công ty -->
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <aside class="f_widget about_widget">
                        <a href="./" class="f_logo">
                            <img src="assets/images/logo.png" alt="<?php echo htmlspecialchars($companyInfo['short_name']); ?>">
                            <span class="footer_logo_text"><?php echo htmlspecialchars($companyInfo['short_name']); ?></span>
                        </a>
                        <div id="custom_html-4">
                            <div class="textwidget custom-html-widget">
                                <p><?php echo htmlspecialchars($companyInfo['description']); ?></p>
                                <p style="margin-top: 15px; font-size: 13px;">
                                    <i class="fa fa-map-marker"></i> <?php echo htmlspecialchars($companyInfo['address']); ?><br>
                                    <i class="fa fa-phone"></i> <?php echo htmlspecialchars($companyInfo['phone']); ?><br>
                                    <i class="fa fa-envelope"></i> <?php echo htmlspecialchars($companyInfo['email']); ?>
                                </p>
                            </div>
                        </div>
                    </aside>
                </div>

                <!-- Cột 2: Liên kết -->
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div id="custom_html-5">
                        <div class="textwidget custom-html-widget">
                            <aside class="f_widget link_widget">
                                <h4 class="f_title f_size_20 f_500 color_w">
                                    <?php echo htmlspecialchars($footerSettings['useful_links_title'] ?? 'Liên kết'); ?>
                                </h4>
                                <ul class="list-unstyled mb-0">
                                    <?php foreach ($usefulLinks as $link): ?>
                                    <li>
                                        <a href="<?php echo htmlspecialchars($link['url']); ?>">
                                            <?php echo htmlspecialchars($link['title']); ?>
                                        </a>
                                    </li>
                                    <?php endforeach; ?>
                                    <?php if (empty($usefulLinks)): ?>
                                    <li><a href="./">Trang chủ</a></li>
                                    <li><a href="?page=about">Giới thiệu</a></li>
                                    <li><a href="?page=services">Dịch vụ</a></li>
                                    <li><a href="?page=projects">Dự án</a></li>
                                    <li><a href="?page=contact">Liên hệ</a></li>
                                    <?php endif; ?>
                                </ul>
                            </aside>
                        </div>
                    </div>
                </div>

                <!-- Cột 3: Dịch vụ (Dynamic) -->
                <div class="col-lg-3 col-sm-6">
                    <div id="custom_html-6">
                        <div class="textwidget custom-html-widget">
                            <aside class="f_widget link_widget">
                                <h4 class="f_title f_size_20 f_500 color_w">
                                    Dịch vụ
                                </h4>
                                <ul class="list-unstyled mb-0">
                                    <?php foreach ($services as $service): ?>
                                    <li>
                                        <a href="?page=categories-details&slug=<?php echo urlencode($service['slug']); ?>">
                                            <?php echo htmlspecialchars($service['name']); ?>
                                        </a>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </aside>
                        </div>
                    </div>
                </div>

                <!-- Cột 4: Đăng ký nhận tin & Mạng xã hội -->
                <div class="col-lg-4 col-sm-6">
                    <div id="text-2">
                        <div class="textwidget">
                            <aside class="f_widget news_widget">
                                <h4 class="f_title f_size_20 f_500 color_w">
                                    Đăng ký nhận tin
                                </h4>
                                <h5 class="mb_0">Nhận thông tin cập nhật và ưu đãi mới nhất.</h5>
                                <div role="form" class="wpcf7" id="wpcf7-f228-o2" lang="vi" dir="ltr">
                                    <div class="screen-reader-response"></div>
                                    <form action="" method="post" class="wpcf7-form" novalidate="novalidate">
                                        <div style="display: none;">
                                            <input type="hidden" name="_wpcf7" value="228">
                                            <input type="hidden" name="_wpcf7_version" value="5.1.4">
                                            <input type="hidden" name="_wpcf7_locale" value="vi">
                                            <input type="hidden" name="_wpcf7_unit_tag" value="wpcf7-f228-o2">
                                            <input type="hidden" name="_wpcf7_container_post" value="0">
                                        </div>
                                        <div class="mailchimp" method="post">
                                            <div class="input-group subscrib_form">
                                                <input type="email" name="email" value="" class="form-control memail" aria-invalid="false" placeholder="Nhập email của bạn">
                                                <button type="submit" class="submit_btn_b">
                                                    <img src="assets/icons/paper-plane.svg" alt="Đăng ký">
                                                </button>
                                            </div>
                                        </div>
                                        <div class="wpcf7-response-output wpcf7-display-none"></div>
                                    </form>
                                </div>
                                <ul class="nav social_icon">
                                    <?php if (!empty($socialLinks['facebook'])): ?>
                                    <li><a href="<?php echo htmlspecialchars($socialLinks['facebook']); ?>" target="_blank"><i class="fa fa-facebook"></i></a></li>
                                    <?php endif; ?>
                                    <?php if (!empty($socialLinks['linkedin'])): ?>
                                    <li><a href="<?php echo htmlspecialchars($socialLinks['linkedin']); ?>" target="_blank"><i class="fa fa-linkedin"></i></a></li>
                                    <?php endif; ?>
                                    <?php if (!empty($socialLinks['twitter'])): ?>
                                    <li><a href="<?php echo htmlspecialchars($socialLinks['twitter']); ?>" target="_blank"><i class="fa fa-twitter"></i></a></li>
                                    <?php endif; ?>
                                    <?php if (!empty($socialLinks['youtube'])): ?>
                                    <li><a href="<?php echo htmlspecialchars($socialLinks['youtube']); ?>" target="_blank"><i class="fa fa-youtube"></i></a></li>
                                    <?php endif; ?>
                                    <?php if (empty($socialLinks)): ?>
                                    <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                                    <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                                    <?php endif; ?>
                                </ul>
                            </aside>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer_copyright">
        <div class="container">
            <div class="bottom_info d-flex justify-content-between">
                <div class="pull-left">
                    Bản quyền © <?php echo date('Y'); ?> <a href="./"><?php echo htmlspecialchars($companyInfo['short_name']); ?></a>. Đã đăng ký bảo hộ.
                </div>
                <div class="pull-right">
                    MST: 0105330414 | Thiết kế bởi: <a href="https://www.mistydev.id.vn/" target="_blank">MistyTeam</a>
                </div>
            </div>
        </div>
    </div>
</footer>
