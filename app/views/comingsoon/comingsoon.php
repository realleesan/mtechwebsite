<?php
/**
 * Coming Soon Page View
 * 
 * Trang bảo trì / Coming Soon
 * - Hiển thị đếm ngược đến ngày launch
 * - Form đăng ký nhận thông báo
 * - Giao diện full screen, không dùng master layout
 */

// Lấy cấu hình từ model
require_once __DIR__ . '/../../models/ComingsoonModel.php';
$comingsoonModel = new ComingsoonModel();
$settings = $comingsoonModel->getSettings();

// Xử lý AJAX subscribe
if (isset($_GET['action']) && $_GET['action'] === 'subscribe' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $email = $_POST['email'] ?? '';
    $result = $comingsoonModel->saveSubscriber($email);
    echo json_encode($result);
    exit;
}

// Mặc định nếu không lấy được settings
$title = $settings['title'] ?? "Website đang bảo trì...";
$description = $settings['description'] ?? 'Website đang được xây dựng. Chúng tôi sẽ sớm ra mắt với phiên bản mới,';
$subscribeText = $settings['subscribe_text'] ?? 'Đăng ký để nhận thông báo.';
$emailPlaceholder = $settings['email_placeholder'] ?? 'Nhập địa chỉ email của bạn';
$buttonText = $settings['button_text'] ?? 'Đăng ký ngay';
$targetTimestamp = $comingsoonModel->getTargetTimestamp() ?? (time() + 30 * 24 * 60 * 60) * 1000;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Playfair+Display:ital@1&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css">
    
    <!-- Coming Soon CSS -->
    <link rel="stylesheet" href="assets/css/comingsoon.css">
</head>
<body class="comingsoon-page">

<section class="commingsoon_section d-flex align-items-center">
    <div class="container">
        <h2><?php echo htmlspecialchars($title); ?></h2>
        
        <!-- Countdown Timer -->
        <div id="timer" class="row timer no-gutters justify-content-center">
            <div class="col-3">
                <div class="timer__section days">
                    <div class="timer_text">
                        <div class="timer__number">00</div>
                        <div class="timer__label">ngày</div>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="timer__section hours">
                    <div class="timer_text">
                        <div class="timer__number">00</div>
                        <div class="timer__label">giờ</div>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="timer__section minutes">
                    <div class="timer_text">
                        <div class="timer__number">00</div>
                        <div class="timer__label">phút</div>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="timer__section seconds">
                    <div class="timer_text">
                        <div class="timer__number">00</div>
                        <div class="timer__label">giây</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Description -->
        <p>
            Website đang được xây dựng. Chúng tôi sẽ sớm ra mắt với phiên bản mới,<br>
            <span class="f_play">Đăng ký để nhận thông báo.</span>
        </p>
        
        <!-- Subscribe Form -->
        <form class="mailchimp" method="post" action="?page=comingsoon&action=subscribe">
            <div class="subscrib_form">
                <input type="email" name="email" class="form-control memail"
                       placeholder="Nhập địa chỉ email của bạn"
                       required>
                <button class="btn" type="submit">Đăng ký ngay</button>
            </div>
        </form>
    </div>
</section>

<!-- Pass target date to JavaScript -->
<script>
    window.comingSoonTargetDate = <?php echo $targetTimestamp; ?>;
</script>

<!-- jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
<!-- Coming Soon JS -->
<script src="assets/js/comingsoon.js"></script>

</body>
</html>
