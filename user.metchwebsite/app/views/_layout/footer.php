<?php

/**
 * Footer Layout - Dynamic Data
 * Sử dụng FooterModel và CategoriesModel
 */

// Load models
require_once __DIR__ . '/../../models/FooterModel.php';
require_once __DIR__ . '/../../models/CategoriesModel.php';
require_once __DIR__ . '/../../models/HeaderModel.php';

$footerSettings = [];
$usefulLinks    = [];
$services       = [];
$socialLinks    = [];
$headerSettings = [];

try {
    $footerModel     = new FooterModel();
    $categoriesModel = new CategoriesModel();
    $headerModel     = new HeaderModel();

    $footerSettings = $footerModel->getSettings();
    $usefulLinks    = $footerModel->getActiveLinks();
    $services       = $categoriesModel->getMenuServices(5);
    $socialLinks    = $footerModel->getVisibleSocialLinks();
    $headerSettings = $headerModel->getSettings();
} catch (Throwable $e) {
    error_log('Footer layout load error: ' . $e->getMessage());
}

// Thông tin MTECH chuẩn từ hồ sơ năng lực
$companyInfo = [
    'name' => 'CÔNG TY CỔ PHẦN TƯ VẤN KỸ THUẬT VÀ THƯƠNG MẠI',
    'short_name' => 'MTECH',
    'description' => 'CÔNG TY CỔ PHẦN TƯ VẤN KỸ THUẬT VÀ THƯƠNG MẠI',
    'office_address' => 'Văn phòng làm việc: Toà nhà 227 Nguyễn Ngọc Nại, phường Phương Liệt, TP. Hà Nội',
    'business_address' => 'Địa chỉ đăng ký kinh doanh: Số 8, ngõ 151, phố Định Công, Phường Định Công, TP. Hà Nội',
    'phone' => $headerSettings['phone'] ?? '0913.034.656',
    'email' => 'mtechjsc2011.info@gmail.com',
    'website' => 'www.mtechjsc.com'
];
?>
<footer class="footer_area">
    <!-- Phần tiêu đề: Logo + Tên công ty -->
    <div class="footer_header" style="text-align: center;">
        <a href="./" class="f_logo" style="justify-content: center; display: flex; align-items: center; gap: 12px;">
            <img src="<?php echo image_url('assets/images/logo_mtech.png'); ?>" alt="<?php echo htmlspecialchars($companyInfo['short_name']); ?>" style="max-height: 64px; width: auto;" loading="lazy" decoding="async">
            <span class="footer_logo_text" style="font-size: calc(1em + 10px); text-transform: uppercase;"><?php echo htmlspecialchars($companyInfo['short_name']); ?> | <?php echo htmlspecialchars($companyInfo['name']); ?></span>
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
                                <div class="f_contact_item" style="display: flex; align-items: flex-start; gap: 10px; margin-bottom: 12px;">
                                    <i class="fa fa-map-marker f_contact_icon" style="flex: 0 0 16px; width: 16px; margin-top: 4px; text-align: center; color: rgba(255, 255, 255, 0.85); font-size: 14px;"></i>
                                    <div class="f_contact_text" style="flex: 1; font-size: 14px; line-height: 1.6; color: #ffffff; word-break: break-word;">
                                        <strong>Văn phòng làm việc:</strong> Toà nhà 227 Nguyễn Ngọc Nại, phường Phương Liệt, TP. Hà Nội
                                    </div>
                                </div>
                                <div class="f_contact_item" style="display: flex; align-items: flex-start; gap: 10px; margin-bottom: 12px;">
                                    <i class="fa fa-map-marker f_contact_icon" style="flex: 0 0 16px; width: 16px; margin-top: 4px; text-align: center; color: rgba(255, 255, 255, 0.85); font-size: 14px;"></i>
                                    <div class="f_contact_text" style="flex: 1; font-size: 14px; line-height: 1.6; color: #ffffff; word-break: break-word;">
                                        <strong>Địa chỉ đăng ký kinh doanh:</strong> Số 8, ngõ 151, phố Định Công, Phường Định Công, TP. Hà Nội
                                    </div>
                                </div>
                                <div class="f_contact_item" style="display: flex; align-items: flex-start; gap: 10px; margin-bottom: 12px;">
                                    <i class="fa fa-phone f_contact_icon" style="flex: 0 0 16px; width: 16px; margin-top: 4px; text-align: center; color: rgba(255, 255, 255, 0.85); font-size: 14px;"></i>
                                    <div class="f_contact_text" style="flex: 1; font-size: 14px; line-height: 1.6; color: #ffffff; word-break: break-word; display: flex; align-items: flex-start; gap: 4px;">
                                        <strong style="white-space: nowrap;">Điện thoại:</strong>
                                        <div>
                                            <div>0913.034.656 (Hotline)</div>
                                            <div>0243.623.1691</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="f_contact_item" style="display: flex; align-items: flex-start; gap: 10px; margin-bottom: 0;">
                                    <i class="fa fa-envelope f_contact_icon" style="flex: 0 0 16px; width: 16px; margin-top: 4px; text-align: center; color: rgba(255, 255, 255, 0.85); font-size: 14px;"></i>
                                    <div class="f_contact_text" style="flex: 1; font-size: 14px; line-height: 1.6; color: #ffffff; word-break: break-word;">
                                        <strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($companyInfo['email']); ?>" style="color: #ffffff; text-decoration: none;"><?php echo htmlspecialchars($companyInfo['email']); ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>

                <!-- Cột 2: Liên kết -->
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <aside class="f_widget link_widget">
                        <div class="link_inner_wrap">
                            <h4 class="f_title f_size_20 f_500 color_w">
                                <span class="f_title_inner">
                                    <?php echo htmlspecialchars($footerSettings['useful_links_title'] ?? 'Liên kết'); ?>
                                </span>
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
                            <!-- DMCA Protected Badge -->
                            <div class="dmca-widget-wrap" style="margin-top: 15px;">
                                <a href="https://www.dmca.com/Protection/Status.aspx?refurl=https://mtechjsc.com" title="DMCA.com Protection Status" class="dmca-badge" target="_blank" rel="noopener noreferrer">
                                    <img src="https://images.dmca.com/Badges/dmca_protected_sml_120m.png" alt="DMCA.com Protection Status" style="height: 28px; width: auto;" loading="lazy">
                                </a>
                                <script src="https://images.dmca.com/Badges/DMCABadgeHelper.min.js"> </script>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </div>
    <div class="footer_copyright">
        <div class="container">
            <div class="bottom_info d-flex justify-content-between align-items-center flex-wrap" style="gap: 12px;">
                <div class="pull-left d-flex align-items-center flex-wrap" style="gap: 12px;">
                    <span>Bản quyền © <?php echo date('Y'); ?> <a href="./"><?php echo htmlspecialchars($companyInfo['short_name']); ?></a>. Đã đăng ký bảo hộ.</span>
                    <a href="https://www.dmca.com/Protection/Status.aspx?refurl=https://mtechjsc.com" title="DMCA.com Protection Status" class="dmca-badge" target="_blank" rel="noopener noreferrer" style="display: inline-flex; align-items: center;">
                        <img src="https://images.dmca.com/Badges/dmca_protected_sml_120m.png" alt="DMCA.com Protection Status" style="height: 24px; width: auto; vertical-align: middle;">
                    </a>
                </div>
                <div class="pull-right">
                    Thiết kế bởi: <a href="https://www.mistydev.id.vn/" target="_blank">MistySoft</a>
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