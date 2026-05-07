<?php
/**
 * Home Page View - Segment 2 Implementation
 * 
 * NOTE: Đây là file view cho trang chủ, thực hiện Segment 2:
 * - Section 1: Để trống cho thành viên khác code
 * - Section 2: Services & Featured Projects (đã thực hiện)
 * - Section 3: Để trống cho thành viên khác code
 * 
 * Dữ liệu động:
 * - $homeServices: Mảng 6 services từ bảng categories (show_on_home=1)
 * - $homeProjects: Mảng 5 projects từ bảng projects (show_on_home=1)
 */

// ==========================================
// AJAX: Xử lý form "Drop a Message" (giống pattern contact.php)
// URL: ?page=home&action=contact-submit  METHOD: POST
?>

<!-- ==========================================
     SECTION 1: HOME BANNER + WELCOME INFO
     ========================================== -->

<!-- ---- 1A: HERO SLIDER ---- -->
<section class="home_banner_area">
    <div class="home_slider" id="homeBannerSlider">

        <!-- Slide 1 -->
        <div class="slider_item active" style="background-image: url('https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/slider1.jpg');">
            <div class="slider_overlay"></div>
            <div class="slider_caption">
                <p class="slider_line1">Hơn 15 Năm Kinh Nghiệm</p>
                <p class="slider_line2">trong <span class="slider_highlight">Tư vấn Kỹ thuật & Quản lý</span></p>
                <p class="slider_desc">Cung cấp các giải pháp thiết kế, giám sát và quản lý dự án chuyên nghiệp cho các công trình công nghiệp và dân dụng quy mô lớn trên toàn quốc.</p>
                <a href="/dich-vu" class="slider_btn">Xem tất cả dịch vụ</a>
            </div>
        </div>

        <!-- Slide 2 -->
        <div class="slider_item" style="background-image: url('https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/slider2.jpg');">
            <div class="slider_overlay"></div>
            <div class="slider_caption">
                <p class="slider_line1">Đối tác Chiến lược của</p>
                <p class="slider_line2">Các Tập đoàn <span class="slider_highlight">Công nghiệp Lớn</span></p>
                <p class="slider_desc">Tiên phong trong tư vấn lập quy hoạch, thiết kế cơ sở và giải pháp tối ưu hóa năng lượng cho ngành luyện kim và vật liệu xây dựng (xi măng, thép).</p>
                <a href="/dich-vu" class="slider_btn">Xem tất cả dịch vụ</a>
            </div>
        </div>

        <!-- Slide 3 -->
        <div class="slider_item" style="background-image: url('https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/slider3.jpg');">
            <div class="slider_overlay"></div>
            <div class="slider_caption">
                <p class="slider_line1">Giải pháp Toàn diện cho</p>
                <p class="slider_line2">Nông nghiệp <span class="slider_highlight">Công nghệ cao</span></p>
                <p class="slider_desc">Đồng hành cùng nhà đầu tư từ khâu khảo sát, lập dự án đến giám sát thi công các dự án chăn nuôi và chế biến nông sản hàng ngàn hecta.</p>
                <a href="/dich-vu" class="slider_btn">Xem tất cả dịch vụ</a>
            </div>
        </div>

        <!-- Prev / Next arrows -->
        <button class="slider_arrow slider_prev" aria-label="Previous slide">&#10094;</button>
        <button class="slider_arrow slider_next" aria-label="Next slide">&#10095;</button>
    </div>

    <!-- ---- 1B: PROMO BOX ITEMS (overlay lên slider) ---- -->
    <div class="promo_boxes_wrap">
        <div class="container">
            <div class="about_promo_box">

                <!-- Box vàng -->
                <div class="promo_box_item box_one">
                    <div class="promo_box_icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                    </div>
                    <div class="promo_box_body">
                        <h3>Chất lượng dịch vụ Hạng I</h3>
                        <p>Sở hữu các chứng chỉ năng lực hoạt động xây dựng Hạng I cấp cao nhất do Bộ Xây dựng cấp phép, đảm bảo tính chuẩn xác và an toàn tuyệt đối.</p>
                    </div>
                </div>

                <!-- Box đen -->
                <div class="promo_box_item box_two">
                    <div class="promo_box_icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    </div>
                    <div class="promo_box_body">
                        <h3>Hơn 15 Năm Kinh Nghiệm</h3>
                        <p>Bề dày kinh nghiệm hoạt động từ năm 2011, thực hiện thành công hàng loạt các siêu dự án trọng điểm trên khắp cả nước.</p>
                    </div>
                </div>

                <!-- Box xám -->
                <div class="promo_box_item box_three">
                    <div class="promo_box_icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9.5 2A2.5 2.5 0 0 1 12 4.5v15a2.5 2.5 0 0 1-4.96-.46 2.5 2.5 0 0 1-2.96-3.08 3 3 0 0 1-.34-5.58 2.5 2.5 0 0 1 1.32-4.24 2.5 2.5 0 0 1 4.44-1.14Z"/><path d="M14.5 2A2.5 2.5 0 0 0 12 4.5v15a2.5 2.5 0 0 0 4.96-.46 2.5 2.5 0 0 0 2.96-3.08 3 3 0 0 0 .34-5.58 2.5 2.5 0 0 0-1.32-4.24 2.5 2.5 0 0 0-4.44-1.14Z"/></svg>
                    </div>
                    <div class="promo_box_body">
                        <h3>Vị thế Hàng đầu Thị trường</h3>
                        <p>Là đối tác chiến lược tin cậy của Tập đoàn Xuân Thiện, Long Sơn, Vissai, SCG cùng nhiều Chủ đầu tư lớn mạnh khác.</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- ---- 1C: WELCOME INFO ---- -->
<section class="welcome_area">
    <div class="container">
        <div class="row welcome_info">
            <!-- Ảnh overlay trong suốt -->
            <img class="wel_bg" src="https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/welcome_img.png" alt="">

            <div class="col-lg-7">
                <div class="welcome_text">
                    <h5 class="welcome_sub">Chào mừng đến với MTECH.JSC</h5>
                    <h1 class="welcome_title">Hơn 15 năm kiến tạo những công trình công nghiệp bền vững.</h1>
                    <p class="welcome_desc">Được thành lập từ ngày 26/05/2011, Công ty Cổ phần Tư vấn Kỹ thuật và Thương mại MTECH tự hào là đơn vị uy tín cung cấp chuỗi dịch vụ khép kín từ lập quy hoạch, khảo sát, thiết kế bản vẽ thi công, đến giám sát và quản lý dự án. Với đội ngũ chuyên gia tận tâm, chúng tôi luôn mang tới những giải pháp tối ưu nhất, đồng hành cùng nhà đầu tư kiến tạo nên các dự án luyện kim - năng lượng, vật liệu xây dựng và nông nghiệp công nghệ cao mang tầm vóc quốc tế.</p>
                    <h6 class="welcome_ceo">Giám đốc : Nguyễn Tùng Giang</a></h6>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ==========================================
     SECTION 2: SERVICES & FEATURED PROJECTS
     ========================================== -->

<!-- Client Logos — Đối tác chiến lược -->
<?php include_once __DIR__ . '/../_layout/client_logos.php'; ?>

<!-- Services Section -->
<section class="service_area sec_gap">
    <div class="container">
        <div class="section_title mb_55">
            <h2 class="f_600 f_size_32 title_color">Dịch vụ của chúng tôi</h2>
            <span class="title_br"></span>
            <p class="mt_7">Cung cấp các giải pháp tư vấn kỹ thuật chuyên sâu cho các dự án đầu tư xây dựng quy mô lớn trên toàn quốc.</p>
        </div>
        <div class="row mb-50">
            <?php if (isset($homeServices) && !empty($homeServices)): ?>
                <?php foreach ($homeServices as $service): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="service_item">
                            <div class="service_img">
                                <img src="<?php echo htmlspecialchars($service['image'] ?? 'assets/images/placeholder.jpg'); ?>" alt="<?php echo htmlspecialchars($service['name']); ?>">
                                <div class="hover_content">
                                    <a href="/dich-vu-<?php echo htmlspecialchars($service['slug']); ?>" class="read_more">Xem thêm</a>
                                </div>
                            </div>
                            <a href="/dich-vu-<?php echo htmlspecialchars($service['slug']); ?>">
                                <h3 class="f_size_20 title_color f_600"><?php echo htmlspecialchars($service['name']); ?></h3>
                            </a>
                            <span class="bottom_br"></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php
// Prepare projects data for carousel (duplicate 3x for infinite loop)
$carouselProjects = [];
if (isset($homeProjects) && !empty($homeProjects)) {
    $carouselProjects = array_merge($homeProjects, $homeProjects, $homeProjects);
}
?>

<!-- Featured Projects Section -->
<section class="featured_area sec_gap">
    <div class="container">
        <div class="section_title mb_55">
            <h2 class="f_600 f_size_32 color_w">Dự án <span class="f_play">tiêu biểu</span></h2>
            <span class="title_br"></span>
            <p class="mt_7">Các dự án điện, năng lượng tái tạo và công nghiệp tiêu biểu MTECH đã thực hiện.</p>
        </div>
    </div>
    <!-- Carousel full-width -->
    <div class="projects_carousel_wrapper">
        <div class="projects_carousel_track">
            <?php foreach ($carouselProjects as $project): ?>
                <div class="projects_slide">
                    <div class="featured_pr_item">
                        <a href="/chi-tiet-du-an-<?php echo htmlspecialchars($project['slug']); ?>">
                            <img src="<?php echo htmlspecialchars($project['image'] ?? 'assets/images/placeholder.jpg'); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>">
                            <div class="overlay"></div>
                            <p class="f_p f_500"><?php echo htmlspecialchars($project['title']); ?></p>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Quote/CTA Section -->
<section class="quote_area">
    <div class="container d-flex">
        <h2 class="f_600 title_color mb-0">Chúng tôi cung cấp các giải pháp tư vấn kỹ thuật<br> <span class="f_play f_700">cho sự phát triển bền vững.</span></h2>
        <div class="quote_button">
            <a href="/lien-he" class="read_more quote_btn">Liên hệ ngay</a>
        </div>
    </div>
</section>


<!-- ==========================================
     SECTION 3: AWARDS, LATEST NEWS, PROMO, CTA
     ========================================== -->

<!-- ---- 3A: AWARDS CAROUSEL ---- -->
<?php
require_once __DIR__ . '/../../models/AwardsModel.php';
$awardsModel = new AwardsModel();
$awards = $awardsModel->getAllActive();
include_once __DIR__ . '/../about/awards.php';
?>

<!-- ---- 3B: LATEST NEWS ---- -->
<section class="lt_news_area bg_color">
    <div class="container-fluid pl-0 pr-0">
        <div class="row ml-0 mr-0 lt_news_row">

            <!-- Left: Latest News intro + overlay image -->
            <div class="lt_news_left">
                <img class="bg_img" src="https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/news_bg.jpg" alt="News Background">
                <div class="lt_news_content">
                    <h2 class="f_600 color_w">Tin tức mới nhất</h2>
                    <span class="title_br"></span>
                    <p>Cập nhật những thông tin mới nhất về các dự án, hoạt động và thành tựu của MTECH trong lĩnh vực tư vấn kỹ thuật và xây dựng công nghiệp.</p>
                    <a href="/tin-tuc" class="read_more btn_blue">Xem thêm tin tức</a>
                </div>
            </div>

            <!-- Right: 3 blog items động -->
            <div class="lt_news_right d-flex align-items-center">
                <div class="lt_news_inner">
                    <?php
                    $homeBlogs = $homeBlogs ?? [];
                    // Fallback static nếu không có dữ liệu
                    $staticBlogs = [
                        ['slug'=>'#','image'=>'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/news-1.jpg','title'=>'Capitalize on low hanging fruit to identify','excerpt'=>'Podcasting operational change management inside of workflows to establish a framework. Taking seamless key...','created_at'=>'2019-12-12','author'=>'admin'],
                        ['slug'=>'#','image'=>'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/news-2.jpg','title'=>'Capitalize on low hanging fruit to identify','excerpt'=>'Podcasting operational change management inside of workflows to establish a framework. Taking seamless key...','created_at'=>'2019-12-12','author'=>'admin'],
                        ['slug'=>'#','image'=>'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/news-3.jpg','title'=>'Capitalize on low hanging fruit to identify','excerpt'=>'Podcasting operational change management inside of workflows to establish a framework. Taking seamless key...','created_at'=>'2019-12-12','author'=>'admin'],
                    ];
                    $displayBlogs = !empty($homeBlogs) ? $homeBlogs : $staticBlogs;
                    foreach ($displayBlogs as $blog):
                        // URL đồng bộ với sidebar blogs và blog details
                        if ($blog['slug'] && $blog['slug'] !== '#') {
                            $isHiring = (($blog['category_id'] ?? 0) == 7);
                            $blogUrl  = $isHiring
                                ? '/chi-tiet-' . $blog['slug']
                                : '/chi-tiet-tin-tuc-' . $blog['slug'];
                        } else {
                            $blogUrl = '/tin-tuc';
                        }
                        $imgSrc   = !empty($blog['image']) ? $blog['image'] : 'https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/news-1.jpg';
                        $dateStr  = !empty($blog['created_at']) ? format_date_vietnamese(date('d F, Y', strtotime($blog['created_at']))) : '';
                        $author   = htmlspecialchars($blog['author'] ?? 'admin');
                        $title    = htmlspecialchars($blog['title'] ?? '');
                        $excerpt  = htmlspecialchars($blog['excerpt'] ?? '');
                        if (mb_strlen($excerpt) > 100) $excerpt = mb_substr($excerpt, 0, 100) . '...';
                    ?>
                    <div class="lt_news_item media">
                        <a href="<?php echo $blogUrl; ?>" class="lt_news_img_wrap">
                            <img src="<?php echo htmlspecialchars($imgSrc); ?>" alt="<?php echo $title; ?>">
                        </a>
                        <div class="media-body">
                            <h4 class="f_size_18 title_color f_600">
                                <a href="<?php echo $blogUrl; ?>"><?php echo $title; ?></a>
                            </h4>
                            <p><?php echo $excerpt; ?></p>
                            <div class="blog-meta">
                                <a href="#"><?php echo $dateStr; ?></a>
                                <a href="#"><?php echo $author; ?></a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ---- 3C: PROMO BANNER (2 columns, blue bg) ---- -->
<section class="promo_area" style="background-image: url('https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/contact_bg.jpg');">
    <div class="promo_overlay"></div>
    <div class="container">
        <div class="promo_info">
            <div class="promo_col_left">
                <h2>Chúng tôi tự hào đồng hành cùng các tập đoàn lớn, tạo ra những công trình công nghiệp <span class="promo_italic">bền vững</span> và hiệu quả trên toàn quốc.</h2>
            </div>
            <div class="promo_divider_v"></div>
            <div class="promo_col_right">
                <p class="p_text">Với hơn 15 năm kinh nghiệm, MTECH cung cấp giải pháp tư vấn kỹ thuật toàn diện từ quy hoạch, thiết kế đến giám sát thi công, đảm bảo chất lượng và tiến độ cho mọi dự án.</p>
            </div>
        </div>
    </div>
</section>

<!-- ---- 3D: CTA CONTACT SECTION (overlapping promo + white) ---- -->
<section class="contact_sec_area">
    <div class="container">
        <div class="contact_inner m_top">
            <div class="row">

                <!-- Left: Contact Info -->
                <div class="col-lg-4">
                    <div class="contact_info_box">
                        <!-- Logo Mtech -->
                        <a href="./" class="c_logo">
                            <img src="assets/images/logo.png" alt="MTECH.JSC" class="c_logo_img">
                            <span class="c_logo_text">MTECH.JSC</span>
                        </a>

                        <!-- Phone -->
                        <div class="contact_info_item">
                            <div class="icon">
                                <img src="assets/icons/phone-call.svg" alt="phone" class="svg-icon">
                            </div>
                            <div class="contact_info_body">
                                <a href="tel:18004567890" class="f_600 f_size_18 title_color">1800 456 7890</a>
                                <p>Thứ 2 - Thứ 6, 8:00 - 17:30</p>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="contact_info_item">
                            <div class="icon">
                                <img src="assets/icons/envelope.svg" alt="email" class="svg-icon">
                            </div>
                            <div class="contact_info_body">
                                <a href="mailto:contact@mtech.com" class="f_600 f_size_18 title_color">contact@mtech.com</a>
                                <p>Phản hồi trong vòng 48 giờ</p>
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="contact_info_item">
                            <div class="icon">
                                <img src="assets/icons/land-layer-location.svg" alt="address" class="svg-icon">
                            </div>
                            <div class="contact_info_body">
                                <p>547, Mainroad Suit, Mount Lane<br>Montonrian, New York</p>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Right: Drop a Message Form -->
                <div class="col-lg-8">
                    <div class="contact_us_form">
                        <div class="section_title mb_30">
                            <h2 class="f_600 f_size_32 title_color">Gửi tin nhắn cho chúng tôi</h2>
                            <span class="title_br"></span>
                        </div>
                        <form class="contact_form row" id="homeContactForm" method="post" action="?page=home&action=contact-submit" novalidate>
                            <div class="form-group col-md-6">
                                <input type="text" name="name" class="form-control" id="hc_name" placeholder="Họ và tên" required>
                            </div>
                            <div class="form-group col-md-6">
                                <input type="email" name="email" class="form-control" id="hc_email" placeholder="Email" required>
                            </div>
                            <div class="form-group col-md-6">
                                <input type="text" name="tphone" class="form-control" id="hc_phone" placeholder="Số điện thoại" required>
                            </div>
                            <div class="form-group col-md-6">
                                <input type="text" name="subject" class="form-control" id="hc_subject" placeholder="Tiêu đề" required>
                            </div>
                            <div class="form-group col-md-12">
                                <textarea name="message" class="form-control" id="hc_message" rows="6" placeholder="Nội dung tin nhắn" required></textarea>
                            </div>
                            <div class="form-group col-md-12">
                                <button type="submit" class="btn_blue submit_btn">Gửi ngay</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
