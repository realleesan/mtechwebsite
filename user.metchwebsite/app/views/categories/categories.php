<?php
/**
 * categories.php — View: Trang Dịch vụ của chúng tôi
 * Structure theo docs/template/categories/code/categories.html
 */

require_once __DIR__ . '/../../../app/models/CategoriesModel.php';

$categoriesModel = new CategoriesModel();
$services        = $categoriesModel->getAllCategories();

$templateImages = [
    'https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/service_img1.jpg',
    'https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/service_img2.jpg',
    'https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/service_img3.jpg',
    'https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/service_img4.jpg',
    'https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/service_img5.jpg',
    'https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/service_img6.jpg',
];

if (empty($services)) {
    $services = [
        ['id'=>1, 'name'=>'Lập quy hoạch xây dựng và Tư vấn dự án đầu tư',              'slug'=>'lap-quy-hoach-xay-dung-va-tu-van-du-an-dau-tu', 'image'=>$templateImages[0], 'sort_order'=>1],
        ['id'=>2, 'name'=>'Thiết kế xây dựng chuyên dụng',                              'slug'=>'thiet-ke-xay-dung-chuyen-dung',                  'image'=>$templateImages[1], 'sort_order'=>2],
        ['id'=>3, 'name'=>'Quản lý dự án, Giám sát thi công và Kiểm định chất lượng',   'slug'=>'quan-ly-du-an-giam-sat-thi-cong-kiem-dinh',      'image'=>$templateImages[2], 'sort_order'=>3],
        ['id'=>4, 'name'=>'Quản lý chi phí xây dựng và Tư vấn đấu thầu',               'slug'=>'quan-ly-chi-phi-xay-dung-tu-van-dau-thau',       'image'=>$templateImages[3], 'sort_order'=>4],
        ['id'=>5, 'name'=>'Tư vấn kỹ thuật tối ưu hóa năng lượng',                     'slug'=>'tu-van-ky-thuat-toi-uu-hoa-nang-luong',          'image'=>$templateImages[4], 'sort_order'=>5],
        ['id'=>6, 'name'=>'Tổng thầu tư vấn dự án đầu tư',                              'slug'=>'tong-thau-tu-van-du-an-dau-tu',                  'image'=>$templateImages[5], 'sort_order'=>6],
    ];
}
?>

<!-- =====================================================
     BANNER
     ===================================================== -->
<!-- =====================================================
     OUR SERVICES — lưới 3 cột
     ===================================================== -->
<section class="service_area sec_gap">
    <div class="container">
        <div class="section_title mb_55">
            <h2 class="f_600 f_size_32 title_color">Dịch vụ của chúng tôi</h2>
            <span class="title_br"></span>
            <p class="mt_7">Cung cấp các giải pháp tư vấn kỹ thuật chuyên sâu cho các dự án đầu tư xây dựng quy mô lớn trên toàn quốc.</p>
        </div>
        <div class="row">
            <?php $i = 0; foreach ($services as $service): ?>
                <?php
                $img = !empty($service['image'])
                    ? $service['image']
                    : ($templateImages[$i] ?? $templateImages[0]);
                $i++;
                $slug = htmlspecialchars($service['slug'], ENT_QUOTES, 'UTF-8');
                $name = htmlspecialchars($service['name'], ENT_QUOTES, 'UTF-8');
                ?>
                <div class="col-lg-4 col-md-6">
                    <div class="service_item">
                        <div class="service_img">
                            <img src="<?php echo htmlspecialchars($img, ENT_QUOTES, 'UTF-8'); ?>"
                                 alt="<?php echo $name; ?>">
                            <div class="hover_content">
                                <a href="/dich-vu-<?php echo $slug; ?>"
                                   class="read_more">Xem thêm</a>
                            </div>
                        </div>
                        <a href="/dich-vu-<?php echo $slug; ?>">
                            <h3 class="f_size_20 title_color f_600"><?php echo $name; ?></h3>
                        </a>
                        <span class="bottom_br"></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- =====================================================
     QUALITY SERVICE — 2 cột full-width
     Cột trái: background image + overlay tối + nội dung
     Cột phải: text content
     ===================================================== -->
<section class="lt_news_area bg_color">
    <div class="container-fluid p-0">
        <div class="row m-0">
            <div class="lt_news_left">
                <img class="bg_img"
                     src="https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/news_bg-1.jpg"
                     alt="Quality Service background">
                <div class="lt_news_content">
                    <h2 class="f_600 f_size_32 color_w">Dự án chất lượng</h2>
                    <span class="title_br"></span>
                    <p>MTECH là đơn vị tư vấn hạng I trong lĩnh vực thiết kế và giám sát công trình công nghiệp. Chúng tôi cung cấp các giải pháp tư vấn kỹ thuật cho sự phát triển bền vững của doanh nghiệp.</p>
                    <a href="/du-an" class="read_more btn_yellow">Xem thêm</a>
                </div>
            </div>
            <div class="lt_news_right d-flex align-items-center">
                <div class="lt_news_inner service_inner">
                    <h4 class="f_600 title_color">Giải pháp kỹ thuật toàn diện cho sự phát triển bền vững</h4>
                    <p>MTECH tự hào cung cấp đa dạng các dịch vụ chất lượng cao xuyên suốt vòng đời dự án, bao gồm: Lập quy hoạch, thiết kế chuyên dụng, giám sát thi công, kiểm định chất lượng, quản lý chi phí đấu thầu và đóng vai trò tổng thầu tư vấn.</p>
                    <p>Với đội ngũ thạc sĩ, kiến trúc sư, kỹ sư chuyên môn cao cùng nhiều năm kinh nghiệm thực tiễn, chúng tôi tự tin mang đến các giải pháp tối ưu cho các dự án công nghiệp, vật liệu xây dựng, nông nghiệp và năng lượng, đáp ứng yêu cầu khắt khe nhất của các Tập đoàn lớn trong và ngoài nước.</p>
                </div>
            </div>
        </div>
    </div>
</section>
