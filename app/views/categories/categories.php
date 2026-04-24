<?php
/**
 * categories.php
 * View: Trang Our Services
 * 
 * Dữ liệu services được lấy động từ bảng `categories` qua CategoriesModel.
 * Không hardcode tên hay hình ảnh service — chỉ cần chỉnh sửa dữ liệu
 * trong database là giao diện tự cập nhật.
 */

require_once __DIR__ . '/../../../app/models/CategoriesModel.php';

$categoriesModel = new CategoriesModel();
$services        = $categoriesModel->getAllCategories();
?>

<!-- ============================================================
     BANNER / PAGE HEADER
     ============================================================ -->
<section class="banner_area">
    <div class="container">
        <div class="banner_content text-center">
            <h2 class="f_600 page_title color_w">Services</h2>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="./">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Services</li>
            </ol>
        </div>
    </div>
</section>

<!-- ============================================================
     OUR SERVICES
     ============================================================ -->
<section class="service_area sec_gap">
    <div class="container">

        <!-- Section Title -->
        <div class="section_title mb_55">
            <h2 class="f_600 f_size_32 title_color">Our Services</h2>
            <span class="title_br"></span>
            <p class="mt_7">
                Podcasting operational change management inside of workflows to establish a framework
                taking seamless key performance indicators.
            </p>
        </div>

        <!-- Services Grid -->
        <div class="row mb-50">

            <?php if (!empty($services)): ?>

                <?php foreach ($services as $service): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="service_item">

                            <!-- Service Image -->
                            <div class="service_img">
                                <img
                                    src="<?php echo htmlspecialchars($service['image'] ?? 'assets/images/categories/placeholder.jpg', ENT_QUOTES, 'UTF-8'); ?>"
                                    alt="<?php echo htmlspecialchars($service['name'], ENT_QUOTES, 'UTF-8'); ?>"
                                >
                                <div class="hover_content">
                                    <a href="?page=categories-details&slug=<?php echo htmlspecialchars($service['slug'], ENT_QUOTES, 'UTF-8'); ?>" class="read_more">
                                        Read More
                                    </a>
                                </div>
                            </div>

                            <!-- Service Name -->
                            <a href="?page=categories-details&slug=<?php echo htmlspecialchars($service['slug'], ENT_QUOTES, 'UTF-8'); ?>">
                                <h3 class="f_size_20 title_color f_600">
                                    <?php echo htmlspecialchars($service['name'], ENT_QUOTES, 'UTF-8'); ?>
                                </h3>
                            </a>

                            <span class="bottom_br"></span>
                        </div>
                    </div>
                <?php endforeach; ?>

            <?php else: ?>

                <!-- Hiển thị khi database chưa có dữ liệu -->
                <div class="col-12">
                    <div class="no-services-message">
                        <i class="fa fa-inbox"></i>
                        No services available.
                    </div>
                </div>

            <?php endif; ?>

        </div><!-- /.row -->
    </div><!-- /.container -->
</section>

<!-- ============================================================
     QUALITY SERVICE SECTION
     ============================================================ -->
<section class="lt_news_area bg_color">
    <div class="container-fluid pl-0 pr-0">
        <div class="row ml-0 mr-0">

            <!-- Left: Background image + text overlay -->
            <div class="lt_news_left">
                <img
                    class="bg_img"
                    src="assets/images/categories/quality_service_bg.jpg"
                    alt="Quality Service"
                >
                <div class="lt_news_content">
                    <h2 class="f_600 f_size_32 color_w">Quality Service</h2>
                    <span class="title_br"></span>
                    <p>
                        Capitalize on low hanging fruit to identify a ballpark value added activity to beta test.
                        Override the digital divide with additional clickthroughs from DevOps. Nanotechnology
                        immersion along the information highway will close the loop on focusing solely on the
                        bottom line.
                    </p>
                    <a href="#" class="read_more btn_yellow">More News</a>
                </div>
            </div>

            <!-- Right: Text content -->
            <div class="lt_news_right d-flex align-items-center">
                <div class="lt_news_inner service_inner">
                    <h4 class="f_600 title_color">
                        Collaboratively administrate empowered markets
                    </h4>
                    <p>
                        Bring to the table win-win survival strategies to ensure proactive domination.
                        At the end of the day, going forward, a new normal that has evolved from generation X
                        is on the runway heading towards a streamlined cloud solution. User generated content
                        in real-time will have multiple touchpoints for offshoring.
                    </p>
                    <p>
                        Capitalize on low hanging fruit to identify a ballpark value added activity to beta test.
                        Override the digital divide with additional clickthroughs from DevOps. Nanotechnology
                        immersion along the information highway will close the loop on focusing solely on the
                        bottom line.
                    </p>
                </div>
            </div>

        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</section>
