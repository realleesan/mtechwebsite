<?php
/**
 * About Page View
 * app/views/about/about.php
 */
?>

<!-- ===== SECTION 1: About Our Industry ===== -->
<section class="about_area sec_gap">
    <div class="container">
        <div class="about_info">
            <div class="about_img scroll-reveal reveal-left">
                <img src="assets/images/about_img.png" alt="About Our Industry">
            </div>
            <div class="about_text scroll-reveal reveal-right">
                <h2 class="f_600 f_size_32 title_color mb_20">Về MTECH.JSC</h2>
                <p class="mb_30">Công ty A Cổ phần Tư vấn Kỹ thuật và Thương mại MTECH (thành lập từ năm 2011) là một trong những đơn vị uy tín hàng đầu tại Việt Nam, chuyên cung cấp các lĩnh vực khép kín từ lập quy hoạch, khảo sát, thiết kế bản vẽ thi công, đến giám sát và quản lý dự án cho các công trình công nghiệp, dân dụng và hạ tầng kỹ thuật.</p>
                <a href="/lich-su-hinh-thanh-phat-trien" class="read_more btn_yellow">Tìm hiểu thêm</a>
            </div>
        </div>
    </div>
</section>

<!-- ===== SECTION 2: Our Mission ===== -->
<section class="mission_area">
    <!-- Left: grayscale photo -->
    <div class="mission_left_img scroll-reveal reveal-left">
        <img src="assets/images/about_our_mission_bg.png" alt="Our Mission">
    </div>
    <!-- Right: yellow background with bg image overlay -->
    <div class="mision_right scroll-reveal reveal-right" style="background-image: url('assets/images/about_our_mission_bg.png');">
        <div class="mission_content scroll-reveal reveal-up">
            <h5 class="f_size_32 f_600 mb_20">Sứ mệnh của chúng tôi</h5>
            <span class="title_br"></span>
            <p class="mb_30">Mang đến các giải pháp kỹ thuật tối ưu, toàn diện và bền vững nhất cho các nhà đầu tư trong các dự án quy mô lớn, đặc biệt là trong lĩnh vực công nghiệp nặng, vật liệu xây dựng (xi măng, thép) và nông nghiệp công nghệ cao.</p>
            <p class="mb_55">Với đội ngũ 25 chuyên gia và kỹ sư giàu kinh nghiệm, chúng tôi cam kết đồng hành cùng khách hàng từ khâu tư vấn quy hoạch đến khi dự án đi vào vận hành, bảo chứng cho chất lượng, an toàn, tiến độ và hiệu quả đầu tư ở mức cao nhất.</p>
            <!-- Icons row -->
            <div class="mission_icons_row">
                <div class="mission_icon_item">
                    <div class="mission_icon_circle">
                        <i class="fa fa-line-chart"></i>
                    </div>
                    <h4 class="f_size_18 f_600">25 Chuyên gia<br>Giàu kinh nghiệm</h4>
                </div>
                <div class="mission_icon_divider"></div>
                <div class="mission_icon_item">
                    <div class="mission_icon_circle">
                        <i class="fa fa-users"></i>
                    </div>
                    <h4 class="f_size_18 f_600">Đội ngũ<br>Chuyên nghiệp</h4>
                </div>
                <div class="mission_icon_divider"></div>
                <div class="mission_icon_item">
                    <div class="mission_icon_circle">
                        <i class="fa fa-headphones"></i>
                    </div>
                    <h4 class="f_size_18 f_600">Hỗ trợ<br>Toàn diện</h4>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== SECTION 4: Our History ===== -->
<section class="history_area">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 scroll-reveal reveal-left">
                <div class="section_title">
                    <h2 class="f_600 f_size_32 title_color d-block mb-0">Lịch sử phát triển</h2>
                    <span class="title_br ml-0"></span>
                </div>
            </div>
            <div class="col-lg-8 scroll-reveal reveal-right">
                <div class="history_content">
                    <h4 class="f_play f_size_20 title_color d-inline-block">Thành lập từ năm 2011</h4>
                    <h5 class="f_size_20 title_color f_500 mb_20">Được thành lập ngày 26/05/2011, MTECH.JSC đã vươn lên mạnh mẽ và trở thành đối tác chiến lược của nhiều tập đoàn lớn như: Tập đoàn Xuân Thiện, Tập đoàn Long Sơn, Tập đoàn Vissai, Tập đoàn SCG.</h5>
                    <p class="mb-0">Trải qua nhiều năm hoạt động chuyên sâu, MTECH tự hào sở hữu các chứng chỉ năng lực xây dựng cấp cao (Hạng I, Hạng II) do Bộ Xây dựng cấp phép trong lĩnh vực Thiết kế, Thẩm tra, Giám sát và Quản lý dự án công trình công nghiệp. Bằng việc áp dụng các công nghệ, quy chuẩn hiện đại và tối ưu hóa năng lượng, chúng tôi luôn mang tới những giá trị thiết thực nhất, đồng hành kiến tạo nên những công trình bền vững trên khắp cả nước.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== SECTION 5: Clients Logo ===== -->
<?php include_once __DIR__ . '/../_layout/client_logos.php'; ?>
