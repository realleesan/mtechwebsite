<?php
/**
 * _layout/client_logos.php — Partial: Client Logo Area
 * Dùng chung cho home.php và about.php
 * Carousel tự động chạy từ phải qua trái với drag/swipe support
 * 
 * Biến nhận: $clientLogos (array) - được truyền từ controller/index.php
 */

// Fallback: nếu không có dữ liệu từ controller, sử dụng dữ liệu mặc định
if (!isset($clientLogos) || empty($clientLogos)) {
    $clientLogos = [
        ['logo' => 'assets/images/client_logo_pics/coninomi.jpg',      'name' => 'Coninomi',        'url' => 'https://conincomi.vn/'],
        ['logo' => 'assets/images/client_logo_pics/longsoncement.png', 'name' => 'Long Son Cement', 'url' => 'https://longsoncement.com.vn/'],
        ['logo' => 'assets/images/client_logo_pics/thanglong.png',     'name' => 'Thang Long',      'url' => 'https://www.thanglongcement.com.vn/'],
        ['logo' => 'assets/images/client_logo_pics/vicem.jpg',         'name' => 'Vicem',           'url' => 'https://vicem.vn/'],
        ['logo' => 'assets/images/client_logo_pics/xuanthanh.png',     'name' => 'Xuan Thanh',      'url' => 'https://ximangxuanthanh.vn/'],
    ];
}
?>

<section class="clients_logo_area">
    <div class="container">
        <div class="client_logo_carousel_wrapper">
            <div class="client_logo_carousel_track">
                <?php 
                // Nhân đôi logos để tạo hiệu ứng infinite loop
                $duplicatedLogos = array_merge($clientLogos, $clientLogos, $clientLogos);
                foreach ($duplicatedLogos as $client): 
                ?>
                    <div class="client_logo_slide">
                        <a href="<?php echo htmlspecialchars($client['url'] ?? '#'); ?>"
                           <?php echo ($client['url'] !== '#') ? 'target="_blank" rel="noopener"' : ''; ?>>
                            <img src="<?php echo htmlspecialchars($client['logo']); ?>"
                                 alt="<?php echo htmlspecialchars($client['name']); ?>"
                                 class="client_logo_img">
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
