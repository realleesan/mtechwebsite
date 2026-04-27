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
 */
?>

<!-- ==========================================
     SECTION 1: HOME BANNER + WELCOME INFO
     ========================================== -->

<!-- ---- 1A: HERO SLIDER ---- -->
<section class="home_banner_area">
    <div class="home_slider" id="homeBannerSlider">

        <!-- Slide 1 -->
        <div class="slider_item active" style="background-image: url('https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/slider1.jpg');">
            <div class="slider_overlay"></div>
            <div class="slider_caption">
                <p class="slider_line1">Have 12 Years Experience</p>
                <p class="slider_line2">in <span class="slider_highlight">Industry Fabrication</span></p>
                <p class="slider_desc">Leverage agile frameworks to provide a robust synopsis for high level overviews. Iterative approaches to corporate strategy.</p>
                <a href="?page=categories" class="slider_btn">View all services</a>
            </div>
        </div>

        <!-- Slide 2 -->
        <div class="slider_item" style="background-image: url('https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/slider2.jpg');">
            <div class="slider_overlay"></div>
            <div class="slider_caption">
                <p class="slider_line1">Big Leader In Power &amp;</p>
                <p class="slider_line2">Automation in <span class="slider_highlight">Technologies</span></p>
                <p class="slider_desc">Leverage agile frameworks to provide a robust synopsis for high level overviews. Iterative approaches to corporate strategy.</p>
                <a href="?page=categories" class="slider_btn">View all services</a>
            </div>
        </div>

        <!-- Slide 3 -->
        <div class="slider_item" style="background-image: url('https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/slider3.jpg');">
            <div class="slider_overlay"></div>
            <div class="slider_caption">
                <p class="slider_line1">We Are Providing Best</p>
                <p class="slider_line2">Service In <span class="slider_highlight">Industrial Area</span></p>
                <p class="slider_desc">Leverage agile frameworks to provide a robust synopsis for high level overviews. Iterative approaches to corporate strategy.</p>
                <a href="?page=categories" class="slider_btn">View all services</a>
            </div>
        </div>

        <!-- Prev / Next arrows -->
        <button class="slider_arrow slider_prev" aria-label="Previous slide">&#10094;</button>
        <button class="slider_arrow slider_next" aria-label="Next slide">&#10095;</button>
    </div>

    <!-- ---- 1B: PROMO BOX ITEMS (overlay lên slider) ---- -->
    <div class="promo_boxes_wrap">
        <div class="container">
            <div class="about_promo_box">

                <!-- Box vàng -->
                <div class="promo_box_item box_one">
                    <div class="promo_box_icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                    </div>
                    <div class="promo_box_body">
                        <h3>High Standard in Service</h3>
                        <p>Capitalize on low hanging fruit to iden- tify a ballpark value added activity to beta test. Override the digital divide.</p>
                    </div>
                </div>

                <!-- Box đen -->
                <div class="promo_box_item box_two">
                    <div class="promo_box_icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    </div>
                    <div class="promo_box_body">
                        <h3>25 Years Experience in Industry</h3>
                        <p>Capitalize on low hanging fruit to iden- tify a ballpark value added activity to beta test. Override the digital divide.</p>
                    </div>
                </div>

                <!-- Box xám -->
                <div class="promo_box_item box_three">
                    <div class="promo_box_icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9.5 2A2.5 2.5 0 0 1 12 4.5v15a2.5 2.5 0 0 1-4.96-.46 2.5 2.5 0 0 1-2.96-3.08 3 3 0 0 1-.34-5.58 2.5 2.5 0 0 1 1.32-4.24 2.5 2.5 0 0 1 4.44-1.14Z"/><path d="M14.5 2A2.5 2.5 0 0 0 12 4.5v15a2.5 2.5 0 0 0 4.96-.46 2.5 2.5 0 0 0 2.96-3.08 3 3 0 0 0 .34-5.58 2.5 2.5 0 0 0-1.32-4.24 2.5 2.5 0 0 0-4.44-1.14Z"/></svg>
                    </div>
                    <div class="promo_box_body">
                        <h3>Best Position in Market</h3>
                        <p>Capitalize on low hanging fruit to iden- tify a ballpark value added activity to beta test. Override the digital divide.</p>
                    </div>
                </div>

            </div>
        </div>
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
                    <h5 class="welcome_sub">Welcome to our Industry</h5>
                    <h1 class="welcome_title">25+ years of experiences for give you better results.</h1>
                    <p class="welcome_desc">Bring to the table win-win survival strategies to ensure proactive domination. At the end of the day, going forward, a new normal that has evolved from generation X is on the runway heading towards a streamlined cloud solution. Bring to the table win-win survival strategies to ensure proactive domination. At the end of the day, going forward ew normal that has evolved.</p>
                    <h6 class="welcome_ceo">Company CEO : <a href="#">Michale John</a></h6>
                    <a href="#" class="sign_btn">
                        <img src="https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/sign.png" alt="signature">
                    </a>
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
            <h2 class="f_600 f_size_32 title_color">Our Services</h2>
            <span class="title_br"></span>
            <p class="mt_7">Podcasting operational change management inside of workflows to establish a framework taking seamless key performanceindicators.</p>
        </div>
        <div class="row mb-50">
            <?php if (isset($homeServices) && !empty($homeServices)): ?>
                <?php foreach ($homeServices as $service): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="service_item">
                            <div class="service_img">
                                <img src="<?php echo htmlspecialchars($service['image'] ?? 'assets/images/placeholder.jpg'); ?>" alt="<?php echo htmlspecialchars($service['name']); ?>">
                                <div class="hover_content">
                                    <a href="?page=categories-details&slug=<?php echo htmlspecialchars($service['slug']); ?>" class="read_more">Read More</a>
                                </div>
                            </div>
                            <a href="?page=categories-details&slug=<?php echo htmlspecialchars($service['slug']); ?>">
                                <h3 class="f_size_20 title_color f_600"><?php echo htmlspecialchars($service['name']); ?></h3>
                            </a>
                            <span class="bottom_br"></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback: Hiển thị dữ liệu mẫu nếu không có dữ liệu từ DB -->
                <div class="col-lg-4 col-md-6">
                    <div class="service_item">
                        <div class="service_img">
                            <img src="https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/service_img1.jpg" alt="Agricultural Processing">
                            <div class="hover_content">
                                <a href="?page=categories-details&slug=agricultural-processing" class="read_more">Read More</a>
                            </div>
                        </div>
                        <a href="?page=categories-details&slug=agricultural-processing"><h3 class="f_size_20 title_color f_600">Agricultural Processing</h3></a>
                        <span class="bottom_br"></span>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="service_item">
                        <div class="service_img">
                            <img src="https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/service_img2.jpg" alt="Chemical Industry">
                            <div class="hover_content">
                                <a href="?page=categories-details&slug=chemical-industry" class="read_more">Read More</a>
                            </div>
                        </div>
                        <a href="?page=categories-details&slug=chemical-industry"><h3 class="f_size_20 title_color f_600">Chemical Industry</h3></a>
                        <span class="bottom_br"></span>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="service_item">
                        <div class="service_img">
                            <img src="https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/service_img3.jpg" alt="Civil Engineering">
                            <div class="hover_content">
                                <a href="?page=categories-details&slug=civil-engineering" class="read_more">Read More</a>
                            </div>
                        </div>
                        <a href="?page=categories-details&slug=civil-engineering"><h3 class="f_size_20 title_color f_600">Civil Engineering</h3></a>
                        <span class="bottom_br"></span>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="service_item">
                        <div class="service_img">
                            <img src="https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/service_img4.jpg" alt="Energy & Power">
                            <div class="hover_content">
                                <a href="?page=categories-details&slug=energy-power" class="read_more">Read More</a>
                            </div>
                        </div>
                        <a href="?page=categories-details&slug=energy-power"><h3 class="f_size_20 title_color f_600">Energy &amp; Power</h3></a>
                        <span class="bottom_br"></span>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="service_item">
                        <div class="service_img">
                            <img src="https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/service_img5.jpg" alt="Mechanical Engineering">
                            <div class="hover_content">
                                <a href="?page=categories-details&slug=mechanical-engineering" class="read_more">Read More</a>
                            </div>
                        </div>
                        <a href="?page=categories-details&slug=mechanical-engineering"><h3 class="f_size_20 title_color f_600">Mechanical Engineering</h3></a>
                        <span class="bottom_br"></span>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="service_item">
                        <div class="service_img">
                            <img src="https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/service_img6.jpg" alt="Oil & Gas Engineering">
                            <div class="hover_content">
                                <a href="?page=categories-details&slug=oil-gas-engineering" class="read_more">Read More</a>
                            </div>
                        </div>
                        <a href="?page=categories-details&slug=oil-gas-engineering"><h3 class="f_size_20 title_color f_600">Oil &amp; Gas Engineering</h3></a>
                        <span class="bottom_br"></span>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Featured Projects Section -->
<section class="featured_area sec_gap">
    <div class="container">
        <div class="section_title mb_55">
            <h2 class="f_600 f_size_32 color_w">Our Featured <span class="f_play">Projects</span></h2>
            <span class="title_br"></span>
            <p class="mt_7">Podcasting operational change management inside of workflows to establish a framework taking seamless key performanceindicators.</p>
        </div>
    </div>
    <div class="featured_project mb-30">
        <div class="row">
            <?php if (isset($homeProjects) && !empty($homeProjects)): ?>
                <?php foreach ($homeProjects as $project): ?>
                    <div class="f_width">
                        <div class="featured_pr_item">
                            <a href="?page=project-details&slug=<?php echo htmlspecialchars($project['slug']); ?>">
                                <img src="<?php echo htmlspecialchars($project['image'] ?? 'assets/images/placeholder.jpg'); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>">
                                <div class="overlay"></div>
                                <p class="f_p f_500"><?php echo htmlspecialchars($project['title']); ?></p>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback: Hiển thị dữ liệu mẫu nếu không có dữ liệu từ DB -->
                <div class="f_width">
                    <div class="featured_pr_item">
                        <a href="?page=project-details&slug=chemical-chamber">
                            <img src="https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/f_pr_1.jpg" alt="Chemical Chamber">
                            <div class="overlay"></div>
                            <p class="f_p f_500">Chemical Chamber</p>
                        </a>
                    </div>
                </div>
                <div class="f_width">
                    <div class="featured_pr_item">
                        <a href="?page=project-details&slug=welding-processing">
                            <img src="https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/f_pr_2.jpg" alt="Welding Processing">
                            <div class="overlay"></div>
                            <p class="f_p f_500">Welding Processing</p>
                        </a>
                    </div>
                </div>
                <div class="f_width">
                    <div class="featured_pr_item">
                        <a href="?page=project-details&slug=railway-project">
                            <img src="https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/f_pr_3.jpg" alt="Railway Project">
                            <div class="overlay"></div>
                            <p class="f_p f_500">Railway Project</p>
                        </a>
                    </div>
                </div>
                <div class="f_width">
                    <div class="featured_pr_item">
                        <a href="?page=project-details&slug=material-engineering">
                            <img src="https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/f_pr_4.jpg" alt="Material Engineering">
                            <div class="overlay"></div>
                            <p class="f_p f_500">Material Engineering</p>
                        </a>
                    </div>
                </div>
                <div class="f_width">
                    <div class="featured_pr_item">
                        <a href="?page=project-details&slug=wind-power-project">
                            <img src="https://shtheme.info/demosd/wokrate/wp-content/uploads/2019/12/f_pr_5.jpg" alt="Wind Power Project">
                            <div class="overlay"></div>
                            <p class="f_p f_500">Wind Power Project</p>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Quote/CTA Section -->
<section class="quote_area">
    <div class="container d-flex">
        <h2 class="f_600 title_color mb-0">We provide innovative product solutions for<br> <span class="f_play f_700">sustainable progress.</span></h2>
        <div class="quote_button">
            <a href="?page=contact" class="read_more quote_btn">Enter your button</a>
        </div>
    </div>
</section>


<!-- ==========================================
     SECTION 3: PLACEHOLDER FOR OTHER MEMBERS
     ========================================== -->
<!-- 
    NOTE: Segment 3 - Để trống cho thành viên khác code
    Thành viên khác sẽ code phần này (Testimonials, Blog, Partners, v.v.)
-->
<section class="segment-3-placeholder">
    <!-- Để trống cho thành viên khác -->
</section>
