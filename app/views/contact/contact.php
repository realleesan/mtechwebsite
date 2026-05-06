<?php
/**
 * Contact Page View
 * Trang liên hệ với form và thông tin
 */

// Thông tin liên hệ MTECH chuẩn từ hồ sơ năng lực
$contactInfo = [
    'address' => 'Tòa nhà 227 phố Nguyễn Ngọc Nại, phường Khương Mai, Quận Thanh Xuân, TP. Hà Nội',
    'headquarters' => 'Số 8, ngõ 151, phố Định Công, Phường Định Công, Quận Hoàng Mai, TP. Hà Nội',
    'phone_primary' => '0243.6231691',
    'phone_secondary' => '0913.034.656',
    'email' => 'mtechjsc2011.info@gmail.com',
    'email_alt' => 'mtechjsc.info@gmail.com',
    'website' => 'www.mtechjsc.com',
    'description' => 'Công ty Cổ phần Tư vấn Kỹ thuật và Thương mại MTECH (MTECH.JSC) là đơn vị chuyên nghiệp được thành lập từ năm 2011, chuyên cung cấp các dịch vụ tư vấn kỹ thuật chuyên sâu cho các dự án đầu tư xây dựng quy mô lớn.',
    'map_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1770.29856830816!2d105.82591253855631!3d20.9965573951612!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ac8841f5e629%3A0x9e2493836c1f1359!2zMjI3IFAuIE5ndXnhu4VuIE5n4buNYyBO4bqhaSwgUGjGsMahbmcgTGnhu4d0LCBIw6AgTuG7mWkgMTAwMDAwLCBWaeG7h3QgTmFt!5e1!3m2!1svi!2s!4v1777798035503!5m2!1svi!2s',
    'map_label' => 'Tòa nhà 227 phố Nguyễn Ngọc Nại, Khương Mai, Thanh Xuân, Hà Nội'
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
                        <h2 class="f_500 title_color">Gửi tin nhắn</h2>
                        <span class="title_br"></span>
                    </div>

                    <form action="/lien-he&action=submit" method="post" class="contact_form row" id="contactForm" novalidate>
                        <div class="form-group col-md-12">
                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                id="name"
                                placeholder="Họ và tên *"
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
                                placeholder="Email *"
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
                                placeholder="Số điện thoại"
                            >
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group col-md-12">
                            <textarea
                                name="message"
                                class="form-control"
                                id="message"
                                rows="6"
                                placeholder="Nội dung tin nhắn *"
                                required
                                aria-required="true"
                            ></textarea>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group col-md-12">
                            <input
                                type="submit"
                                value="Gửi tin nhắn"
                                class="btn read_more btn_yellow wpcf7-submit"
                            >
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Contact Info - Right Side -->
            <div class="col-lg-5">
                <div class="contact_info">
                    <h4 class="f_500 f_p title_color mb_20">Thông tin liên hệ</h4>
                    <p class="mb_40"><?php echo htmlspecialchars($contactInfo['description']); ?></p>

                    <!-- Address -->
                    <div class="contact_info_item">
                        <div class="icon">
                            <img src="assets/icons/land-layer-location.svg" alt="Địa chỉ" class="svg-icon">
                        </div>
                        <div class="content">
                            <h6 class="f_size_18 title_color f_500">Văn phòng giao dịch:</h6>
                            <p><?php echo htmlspecialchars($contactInfo['address']); ?></p>
                        </div>
                    </div>

                    <!-- Headquarters -->
                    <div class="contact_info_item">
                        <div class="icon">
                            <img src="assets/icons/land-layer-location.svg" alt="Trụ sở" class="svg-icon">
                        </div>
                        <div class="content">
                            <h6 class="f_size_18 title_color f_500">Trụ sở chính:</h6>
                            <p><?php echo htmlspecialchars($contactInfo['headquarters']); ?></p>
                        </div>
                    </div>

                    <!-- Phone -->
                    <div class="contact_info_item">
                        <div class="icon">
                            <img src="assets/icons/phone-call.svg" alt="Điện thoại" class="svg-icon">
                        </div>
                        <div class="content">
                            <h6 class="f_size_18 title_color f_500">Điện thoại:</h6>
                            <p>
                                <a href="tel:<?php echo preg_replace('/[^0-9]/', '', $contactInfo['phone_primary']); ?>" class="f_size_16">
                                    <?php echo htmlspecialchars($contactInfo['phone_primary']); ?>
                                </a>
                                <span class="text-muted"> (Hotline)</span><br>
                                <a href="tel:<?php echo preg_replace('/[^0-9]/', '', $contactInfo['phone_secondary']); ?>" class="f_size_16">
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
                            <h6 class="f_size_18 title_color f_500">Email:</h6>
                            <a href="mailto:<?php echo htmlspecialchars($contactInfo['email']); ?>" class="f_size_16">
                                <?php echo htmlspecialchars($contactInfo['email']); ?>
                            </a>
                        </div>
                    </div>

                    <!-- Website -->
                    <div class="contact_info_item">
                        <div class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="svg-icon"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                        </div>
                        <div class="content">
                            <h6 class="f_size_18 title_color f_500">Website:</h6>
                            <a href="https://<?php echo htmlspecialchars($contactInfo['website']); ?>" class="f_size_16" target="_blank">
                                <?php echo htmlspecialchars($contactInfo['website']); ?>
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
