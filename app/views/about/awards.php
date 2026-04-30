<?php
/**
 * awards.php — Trang Giải thưởng & Chứng chỉ
 * Carousel tự động chạy từ phải qua trái (giống client_logos)
 * Biến nhận: $awards (array)
 */

$awards = $awards ?? [];

// Fallback nếu chưa có dữ liệu
if (empty($awards)) {
    $awards = [
        ['id' => 1, 'name' => 'Giải thưởng Chất lượng Quốc gia',  'certificate' => 'Bộ Khoa học & Công nghệ', 'image' => null],
        ['id' => 2, 'name' => 'Chứng chỉ ISO 9001:2015',           'certificate' => 'Bureau Veritas',          'image' => null],
        ['id' => 3, 'name' => 'Top 10 Doanh nghiệp Tiêu biểu',     'certificate' => 'VCCI',                    'image' => null],
        ['id' => 4, 'name' => 'Chứng chỉ ISO 14001:2015',          'certificate' => 'TÜV Rheinland',           'image' => null],
        ['id' => 5, 'name' => 'Giải thưởng Sao Vàng Đất Việt',     'certificate' => 'Hội Doanh nghiệp trẻ VN','image' => null],
        ['id' => 6, 'name' => 'Chứng nhận Nhà thầu Uy tín',        'certificate' => 'Bộ Xây dựng',            'image' => null],
    ];
}

// Nhân 3 lần để tạo infinite loop (giống client_logos)
$duplicated = array_merge($awards, $awards, $awards);
?>

<section class="awards_area sec_gap">
    <div class="container">

        <!-- Section Title -->
        <div class="section_title mb_55">
            <h2 class="f_600 f_size_32 title_color">Awards &amp; Certificates</h2>
            <span class="title_br"></span>
            <p class="mt_7">
                Những giải thưởng và chứng chỉ ghi nhận chất lượng, uy tín và năng lực của MTECHJSC
                trong lĩnh vực cơ khí công nghiệp và xây dựng.
            </p>
        </div>

    </div>

    <!-- Carousel full-width (không bị giới hạn bởi container) -->
    <div class="awards_carousel_wrapper">
        <div class="awards_carousel_track">
            <?php foreach ($duplicated as $award): ?>
                <div class="awards_slide">
                    <!-- Khung ảnh hình chữ nhật dọc -->
                    <div class="awards_img_wrap">
                        <?php if (!empty($award['image'])): ?>
                            <img src="<?php echo htmlspecialchars($award['image']); ?>"
                                 alt="<?php echo htmlspecialchars($award['name']); ?>"
                                 class="awards_img">
                        <?php else: ?>
                            <!-- Placeholder khi chưa có ảnh -->
                            <div class="awards_img_placeholder">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48"
                                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="8" r="3"/>
                                    <path d="M2 20c0-4 4-7 10-7s10 3 10 7"/>
                                    <path d="M17 3l1.5 1.5L21 2"/>
                                    <path d="M17 7l1.5-1.5L21 8"/>
                                </svg>
                                <span>Award Photo</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Text bên dưới -->
                    <div class="awards_info">
                        <h4 class="awards_name"><?php echo htmlspecialchars($award['name']); ?></h4>
                        <?php if (!empty($award['certificate'])): ?>
                            <p class="awards_cert"><?php echo htmlspecialchars($award['certificate']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</section>
