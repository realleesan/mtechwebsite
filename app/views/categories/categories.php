<?php
/**
 * categories.php — View: Trang Our Services
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
        ['id'=>1, 'name'=>'Agricultural Processing', 'slug'=>'agricultural-processing', 'image'=>$templateImages[0], 'sort_order'=>1],
        ['id'=>2, 'name'=>'Chemical Industry',       'slug'=>'chemical-industry',       'image'=>$templateImages[1], 'sort_order'=>2],
        ['id'=>3, 'name'=>'Civil Engineering',       'slug'=>'civil-engineering',       'image'=>$templateImages[2], 'sort_order'=>3],
        ['id'=>4, 'name'=>'Energy & Power',          'slug'=>'energy-power',            'image'=>$templateImages[3], 'sort_order'=>4],
        ['id'=>5, 'name'=>'Mechanical Engineering',  'slug'=>'mechanical-engineering',  'image'=>$templateImages[4], 'sort_order'=>5],
        ['id'=>6, 'name'=>'Oil & Gas Engineering',   'slug'=>'oil-gas-engineering',     'image'=>$templateImages[5], 'sort_order'=>6],
    ];
}
?>

<!-- =====================================================
     BANNER
     ===================================================== -->
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

<!-- =====================================================
     OUR SERVICES — lưới 3 cột
     ===================================================== -->
<section class="service_area sec_gap">
    <div class="container">
        <div class="section_title mb_55">
            <h2 class="f_600 f_size_32 title_color">Our Services</h2>
            <span class="title_br"></span>
            <p class="mt_7">Podcasting operational change management inside of workflows to establish a framework taking seamless key performance indicators.</p>
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
                                <a href="?page=categories-details&slug=<?php echo $slug; ?>"
                                   class="read_more">Read More</a>
                            </div>
                        </div>
                        <a href="?page=categories-details&slug=<?php echo $slug; ?>">
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
                    <h2 class="f_600 f_size_32 color_w">Quality Service</h2>
                    <span class="title_br"></span>
                    <p>Capitalize on low hanging fruit to identify a ballpark value added activity to beta test. Override the digital divide with additional clickthroughs from DevOps. Nanotechnology immersion along the information highway will close the loop on focusing solely on the bottom line.</p>
                    <a href="#" class="read_more btn_yellow">More News</a>
                </div>
            </div>
            <div class="lt_news_right d-flex align-items-center">
                <div class="lt_news_inner service_inner">
                    <h4 class="f_600 title_color">Collaboratively administrate empowered markets</h4>
                    <p>Bring to the table win-win survival strategies to ensure proactive domination. At the end of the day, going forward, a new normal that has evolved from generation X is on the runway heading towards a streamlined cloud solution. User generated content in real-time will have multiple touchpoints for offshoring.</p>
                    <p>Capitalize on low hanging fruit to identify a ballpark value added activity to beta test. Override the digital divide with additional clickthroughs from DevOps. Nanotechnology immersion along the information highway will close the loop on focusing solely on the bottom line.</p>
                </div>
            </div>
        </div>
    </div>
</section>
