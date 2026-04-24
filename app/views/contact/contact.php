<?php
/**
 * Contact Page View
 * Trang liên hệ với form và thông tin
 */

// Xử lý form submission
if (isset($_GET['action']) && $_GET['action'] === 'submit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Start output buffering để bắt mọi output
    if (!ob_get_level()) {
        ob_start();
    }
    
    // Enable error logging nhưng không hiển thị
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    
    // Global exception handler để trả về JSON
    set_exception_handler(function($e) {
        if (ob_get_level()) {
            ob_clean();
        }
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Exception: ' . $e->getMessage()
        ]);
        exit;
    });
    
    // Global error handler
    set_error_handler(function($errno, $errstr, $errfile, $errline) {
        if (ob_get_level()) {
            ob_clean();
        }
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => "Error [$errno]: $errstr in $errfile:$errline"
        ]);
        exit;
    });
    
    try {
        // Validate input
    $errors = [];
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    
    if (empty($name)) {
        $errors['name'] = 'Vui lòng nhập họ tên';
    }
    
    if (empty($email)) {
        $errors['email'] = 'Vui lòng nhập email';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email không hợp lệ';
    }
    
    if (empty($message)) {
        $errors['message'] = 'Vui lòng nhập nội dung';
    } elseif (strlen($message) < 10) {
        $errors['message'] = 'Nội dung phải có ít nhất 10 ký tự';
    }
    
    // Nếu có lỗi validation
    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'message' => 'Vui lòng kiểm tra lại thông tin',
            'errors' => $errors
        ]);
        exit;
    }
    
    // Lưu vào database
    require_once __DIR__ . '/../../models/ContactsModel.php';
    $contactsModel = new ContactsModel();
    
    $contactData = [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'message' => $message
    ];
    
    $contactId = $contactsModel->create($contactData);
    
    if ($contactId) {
        // Gửi email thông báo
        try {
            require_once __DIR__ . '/../../services/EmailNotificationService.php';
            $emailService = new EmailNotificationService();
            
            if ($emailService->isConfigured()) {
                // Gửi email xác nhận cho người dùng
                $emailService->sendContactConfirmation($contactData);
                // Gửi email thông báo cho admin
                $emailService->sendNewContactNotification($contactData);
            }
        } catch (Exception $e) {
            // Log lỗi nhưng không ảnh hưởng đến kết quả
            error_log('Email sending failed: ' . $e->getMessage());
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Cảm ơn bạn đã liên hệ! Hệ thống đã nhận được thông tin và sẽ phản hồi sớm nhất.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Có lỗi xảy ra khi lưu thông tin. Vui lòng thử lại.'
        ]);
    }
    
    } catch (Throwable $e) {
        // Bắt mọi lỗi PHP và trả về JSON
        error_log('Contact form error: ' . $e->getMessage());
        error_log('Stack trace: ' . $e->getTraceAsString());
        
        // Clean buffer và set header
        if (ob_get_level()) {
            ob_clean();
        }
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Có lỗi hệ thống: ' . $e->getMessage()
        ]);
    }
    exit;
}

// Thông tin liên hệ (có thể load từ config hoặc database)
$contactInfo = [
    'address' => '54B, Tailstoi Town 5238 MT, La city, IA 5224',
    'phone_primary' => '1800 456 7890',
    'phone_secondary' => '1254 897 3654',
    'email' => 'contact@infratek.com',
    'description' => 'Completely synergize resource taxing relationships via premier niche markets. Professionally cultivate one-to-one customer service.',
    'map_url' => 'https://maps.google.com/maps?q=London%20Eye%2C%20London%2C%20United%20Kingdom&t=m&z=10&output=embed&iwloc=near',
    'map_label' => 'London Eye, London, United Kingdom'
];
?>

<!-- Contact Section -->
<section class="contact_area sec_gap">
    <div class="container">
        <div class="row contact_inner_two">
            <!-- Contact Form - Left Side -->
            <div class="col-lg-7">
                <div class="contact_us_form">
                    <div class="section_title mb_30">
                        <h2 class="f_500 title_color">Drop a Message</h2>
                        <span class="title_br"></span>
                    </div>
                    
                    <form action="?page=contact&action=submit" method="post" class="contact_form row" id="contactForm" novalidate>
                        <div class="form-group col-md-12">
                            <input 
                                type="text" 
                                name="name" 
                                class="form-control" 
                                id="name" 
                                placeholder="Your Name *" 
                                required
                                aria-required="true"
                            >
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="form-group col-md-12">
                            <input 
                                type="email" 
                                name="email" 
                                class="form-control" 
                                id="email" 
                                placeholder="Your Email *" 
                                required
                                aria-required="true"
                            >
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="form-group col-md-12">
                            <input 
                                type="tel" 
                                name="phone" 
                                class="form-control" 
                                id="phone" 
                                placeholder="Phone No."
                            >
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="form-group col-md-12">
                            <textarea 
                                name="message" 
                                class="form-control" 
                                id="message" 
                                rows="6" 
                                placeholder="Message *" 
                                required
                                aria-required="true"
                            ></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="form-group col-md-12">
                            <input 
                                type="submit" 
                                value="Submit now" 
                                class="btn read_more btn_yellow wpcf7-submit"
                            >
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Contact Info - Right Side -->
            <div class="col-lg-5">
                <div class="contact_info">
                    <h4 class="f_500 f_p title_color mb_20">Our Address</h4>
                    <p class="mb_40"><?php echo htmlspecialchars($contactInfo['description']); ?></p>
                    
                    <!-- Address -->
                    <div class="contact_info_item">
                        <div class="icon">
                            <img src="assets/icons/land-layer-location.svg" alt="Address" class="svg-icon">
                        </div>
                        <div class="content">
                            <h6 class="f_size_18 title_color f_500">Address :</h6>
                            <p><?php echo htmlspecialchars($contactInfo['address']); ?></p>
                        </div>
                    </div>
                    
                    <!-- Phone -->
                    <div class="contact_info_item">
                        <div class="icon">
                            <img src="assets/icons/phone-call.svg" alt="Phone" class="svg-icon">
                        </div>
                        <div class="content">
                            <h6 class="f_size_18 title_color f_500">Phone :</h6>
                            <p>
                                <a href="tel:<?php echo preg_replace('/\s+/', '', $contactInfo['phone_primary']); ?>" class="f_size_16">
                                    <?php echo htmlspecialchars($contactInfo['phone_primary']); ?>
                                </a> / 
                                <a href="tel:<?php echo preg_replace('/\s+/', '', $contactInfo['phone_secondary']); ?>" class="f_size_16">
                                    <?php echo htmlspecialchars($contactInfo['phone_secondary']); ?>
                                </a>
                            </p>
                        </div>
                    </div>
                    
                    <!-- Email -->
                    <div class="contact_info_item">
                        <div class="icon">
                            <img src="assets/icons/envelope.svg" alt="Email" class="svg-icon">
                        </div>
                        <div class="content">
                            <h6 class="f_size_18 title_color f_500">Email :</h6>
                            <a href="mailto:<?php echo htmlspecialchars($contactInfo['email']); ?>" class="f_size_16">
                                <?php echo htmlspecialchars($contactInfo['email']); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Google Maps Section -->
<section class="map_section">
    <div class="elementor-custom-embed">
        <iframe 
            frameborder="0" 
            scrolling="no" 
            marginheight="0" 
            marginwidth="0" 
            src="<?php echo htmlspecialchars($contactInfo['map_url']); ?>" 
            aria-label="<?php echo htmlspecialchars($contactInfo['map_label']); ?>"
        ></iframe>
    </div>
</section>
