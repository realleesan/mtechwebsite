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
 * - $homeSliders: Mảng hero slider từ bảng home_sliders (status=1)
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
        <?php if (!empty($homeSliders)): ?>
        <div class="slider_track">
            <?php foreach ($homeSliders as $index => $slide): ?>
            <div class="slider_page <?= $index === 0 ? 'active' : '' ?>">
                <div class="slider_grid">
                    <?php if (!empty($slide['image_1'])): ?>
                    <div class="slider_grid_item" style="background-image: url('<?php echo htmlspecialchars($slide['image_1']); ?>');"></div>
                    <?php endif; ?>
                    <?php if (!empty($slide['image_2'])): ?>
                    <div class="slider_grid_item" style="background-image: url('<?php echo htmlspecialchars($slide['image_2']); ?>');"></div>
                    <?php endif; ?>
                    <?php if (!empty($slide['image_3'])): ?>
                    <div class="slider_grid_item" style="background-image: url('<?php echo htmlspecialchars($slide['image_3']); ?>');"></div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="slider_pagination">
            <?php foreach ($homeSliders as $index => $slide): ?>
            <button class="slider_bullet <?= $index === 0 ? 'active' : '' ?>" data-page="<?= $index ?>"></button>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="slider_track">
            <div class="slider_page active">
                <div class="slider_grid">
                    <div class="slider_grid_item" style="background-image: url('assets/images/home_slider/home_slider1.jpg');"></div>
                    <div class="slider_grid_item" style="background-image: url('assets/images/home_slider/home_slider2.jpg');"></div>
                    <div class="slider_grid_item" style="background-image: url('assets/images/home_slider/home_slider3.jpg');"></div>
                </div>
            </div>
            <div class="slider_page">
                <div class="slider_grid">
                    <div class="slider_grid_item" style="background-image: url('assets/images/home_slider/home_slider1.jpg');"></div>
                    <div class="slider_grid_item" style="background-image: url('assets/images/home_slider/home_slider2.jpg');"></div>
                    <div class="slider_grid_item" style="background-image: url('assets/images/home_slider/home_slider3.jpg');"></div>
                </div>
            </div>
            <div class="slider_page">
                <div class="slider_grid">
                    <div class="slider_grid_item" style="background-image: url('assets/images/home_slider/home_slider1.jpg');"></div>
                    <div class="slider_grid_item" style="background-image: url('assets/images/home_slider/home_slider2.jpg');"></div>
                    <div class="slider_grid_item" style="background-image: url('assets/images/home_slider/home_slider3.jpg');"></div>
                </div>
            </div>
        </div>
        <div class="slider_pagination">
            <button class="slider_bullet active" data-page="0"></button>
            <button class="slider_bullet" data-page="1"></button>
            <button class="slider_bullet" data-page="2"></button>
        </div>
        <?php endif; ?>
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
                    <h5 class="welcome_sub">Chào mừng đến với MTECH</h5>
                    <h1 class="welcome_title">Hơn 15 năm kiến tạo những công trình công nghiệp bền vững.</h1>
                    <p class="welcome_desc">Được thành lập từ ngày 26/05/2011, Công ty Cổ phần Tư vấn Kỹ thuật và Thương mại MTECH tự hào là đơn vị uy tín cung cấp chuỗi lĩnh vực khép kín từ lập quy hoạch, khảo sát, thiết kế bản vẽ thi công, đến giám sát và quản lý dự án. Với đội ngũ chuyên gia tận tâm, chúng tôi luôn mang tới những giải pháp tối ưu nhất, đồng hành cùng nhà đầu tư kiến tạo nên các dự án luyện kim - năng lượng, vật liệu xây dựng và nông nghiệp công nghệ cao mang tầm vóc quốc tế.</p>
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
            <h2 class="f_600 f_size_32 title_color">Lĩnh vực của chúng tôi</h2>
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
                                    <a href="/linh-vuc-<?php echo htmlspecialchars($service['slug']); ?>" class="read_more">Xem thêm</a>
                                </div>
                            </div>
                            <a href="/linh-vuc-<?php echo htmlspecialchars($service['slug']); ?>">
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



