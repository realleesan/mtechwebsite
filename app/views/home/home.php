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
     SECTION 1: PLACEHOLDER FOR OTHER MEMBERS
     ========================================== -->
<!-- 
    NOTE: Segment 1 - Để trống cho thành viên khác code
    Thành viên khác sẽ code phần này (Banner, Hero, About summary, v.v.)
-->
<section class="segment-1-placeholder">
    <!-- Để trống cho thành viên khác -->
</section>


<!-- ==========================================
     SECTION 2: SERVICES & FEATURED PROJECTS
     ========================================== -->

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
