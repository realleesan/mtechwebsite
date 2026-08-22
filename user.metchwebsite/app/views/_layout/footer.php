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

// Debug: Kiểm tra dữ liệu social links
error_log('Social Links Debug: ' . json_encode($socialLinks));

// Thông tin MTECH chuẩn từ hồ sơ năng lực
$companyInfo = [
    'name' => 'Công ty Cổ phần Tư vấn Kỹ thuật và Thương mại',
    'short_name' => 'MTECH',
    'description' => 'CÔNG TY CỔ PHẦN TƯ VẤN KỸ THUẬT VÀ THƯƠNG MẠI',
    'office_address' => 'Văn phòng làm việc: Toà nhà 227 Nguyễn Ngọc Nại, phường Phương Liệt, quận Thanh Xuân, TP. Hà Nội',
    'business_address' => 'Địa chỉ đăng ký kinh doanh: Số 8, ngõ 151, phố Định Công, Phường Định Công, Quận Hoàng Mai, TP. Hà Nội',
    'phone' => $headerSettings['phone'] ?? '0913.034.656',
    'email' => 'mtechjsc2011.info@gmail.com',
    'website' => 'www.mtechjsc.com'
];
?>
<footer class="footer_area">
    <!-- Phần tiêu đề: Logo + Tên công ty -->
    <div class="footer_header" style="text-align: center;">
        <a href="./" class="f_logo" style="justify-content: center; display: flex; align-items: center; gap: 12px;">
            <img src="/assets/images/logo_mtech.png" alt="<?php echo htmlspecialchars($companyInfo['short_name']); ?>" style="max-height: 48px; width: auto;">
            <span class="footer_logo_text" style="font-size: calc(1em + 10px);"><?php echo htmlspecialchars($companyInfo['short_name']); ?> | <?php echo htmlspecialchars($companyInfo['name']); ?></span>
        </a>
    </div>

    <div class="footer_top">
        <div class="container">
            <!-- 3 Cột bố cục -->
            <div class="row">
                <!-- Cột 1: Thông tin -->
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <aside class="f_widget info_widget">
                        <h4 class="f_title f_size_20 f_500 color_w">Thông tin liên hệ</h4>
                        <div class="f_widget_body">
                            <div class="textwidget custom-html-widget">
                                <p style="font-size: 14px; line-height: 1.6; margin: 0; margin-bottom: 12px;">
                                    <i class="fa fa-map-marker"></i> <strong>Văn phòng làm việc:</strong> Tòa nhà 227 phố Nguyễn Ngọc Nại, phường Khương Mai, Quận Thanh Xuân, TP. Hà Nội
                                </p>
                                <p style="font-size: 14px; line-height: 1.6; margin: 0; margin-bottom: 12px;">
                                    <i class="fa fa-map-marker"></i> <strong>Địa chỉ đăng ký kinh doanh:</strong> Số 8, ngõ 151, phố Định Công, Phường Định Công, Quận Hoàng Mai, TP. Hà Nội
                                </p>
                                <p style="font-size: 14px; line-height: 1.6; margin: 0;">
                                    <i class="fa fa-phone"></i> <?php echo htmlspecialchars($companyInfo['phone']); ?><br>
                                    <i class="fa fa-envelope"></i> <?php echo htmlspecialchars($companyInfo['email']); ?>
                                </p>
                            </div>
                        </div>
                    </aside>
                </div>

                <!-- Cột 2: Liên kết -->
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <aside class="f_widget link_widget">
                        <h4 class="f_title f_size_20 f_500 color_w">
                            <?php echo htmlspecialchars($footerSettings['useful_links_title'] ?? 'Liên kết'); ?>
                        </h4>
                        <div class="f_widget_body">
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
                                <li><a href="/ve-chung-toi">Thư ngỏ</a></li>
                                <li><a href="/linh-vuc">Lĩnh vực</a></li>
                                <li><a href="/du-an">Dự án</a></li>
                                <li><a href="/tin-tuc">Tin tức</a></li>
                                <li><a href="/lien-he">Liên hệ</a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </aside>
                </div>

                <!-- Cột 3: Đăng ký nhận tin -->
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <aside class="f_widget news_widget">
                        <h4 class="f_title f_size_20 f_500 color_w">Đăng ký nhận tin</h4>
                        <div class="f_widget_body">
                            <h5 class="mb_0" style="font-size: 14px; line-height: 1.6; margin-bottom: 15px;">Nhận thông tin cập nhật và ưu đãi mới nhất.</h5>
                            <div role="form" class="wpcf7" id="wpcf7-f228-o2" lang="vi" dir="ltr">
                                <div class="screen-reader-response"></div>
                                <form action="/newsletter/subscribe" method="post" class="wpcf7-form" id="newsletterForm" novalidate="novalidate">
                                    <div class="mailchimp" method="post">
                                        <div class="input-group subscrib_form">
                                            <input type="email" name="email" value="" class="form-control memail" aria-invalid="false" placeholder="Nhập email của bạn" required>
                                            <button type="submit" class="submit_btn_b">
                                                <img src="assets/icons/paper-plane.svg" alt="Đăng ký">
                                            </button>
                                        </div>
                                    </div>
                                    <div class="wpcf7-response-output wpcf7-display-none"></div>
                                </form>
                            </div>
                            <ul class="nav social_icon">
                                <?php if (isset($socialLinks['facebook'])): ?>
                                <li><a href="<?php echo htmlspecialchars($socialLinks['facebook']); ?>" target="_blank" <?php echo $socialLinks['facebook'] === '#' ? 'onclick="return false;"' : ''; ?>><i class="fa fa-facebook"></i></a></li>
                                <?php endif; ?>
                                <?php if (isset($socialLinks['linkedin'])): ?>
                                <li><a href="<?php echo htmlspecialchars($socialLinks['linkedin']); ?>" target="_blank" <?php echo $socialLinks['linkedin'] === '#' ? 'onclick="return false;"' : ''; ?>><i class="fa fa-linkedin"></i></a></li>
                                <?php endif; ?>
                                <?php if (isset($socialLinks['twitter'])): ?>
                                <li><a href="<?php echo htmlspecialchars($socialLinks['twitter']); ?>" target="_blank" <?php echo $socialLinks['twitter'] === '#' ? 'onclick="return false;"' : ''; ?>><i class="fa fa-twitter"></i></a></li>
                                <?php endif; ?>
                                <?php if (isset($socialLinks['google'])): ?>
                                <li><a href="<?php echo htmlspecialchars($socialLinks['google']); ?>" target="_blank" <?php echo $socialLinks['google'] === '#' ? 'onclick="return false;"' : ''; ?>><i class="fa fa-google"></i></a></li>
                                <?php endif; ?>
                                <?php if (isset($socialLinks['youtube'])): ?>
                                <li><a href="<?php echo htmlspecialchars($socialLinks['youtube']); ?>" target="_blank" <?php echo $socialLinks['youtube'] === '#' ? 'onclick="return false;"' : ''; ?>><i class="fa fa-youtube"></i></a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </aside>
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
                    Thiết kế bởi: <a href="https://www.mistydev.id.vn/" target="_blank">MistyTeam</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<script>
(function() {
    function initFooterAccordion() {
        if (window.innerWidth > 767) return;

        var toggles = document.querySelectorAll('.footer_accordion_toggle');
        toggles.forEach(function(toggle) {
            var widget = toggle.closest('.f_widget');
            if (!widget || widget.classList.contains('about_widget')) return;

            toggle.addEventListener('click', function() {
                widget.classList.toggle('is-open');
            });
        });
    }

    document.addEventListener('DOMContentLoaded', initFooterAccordion);
    window.addEventListener('resize', function() {
        // Reset khi resize về desktop
        if (window.innerWidth > 767) {
            document.querySelectorAll('.f_widget.is-open').forEach(function(w) {
                w.classList.remove('is-open');
            });
        }
    });
})();
</script>

