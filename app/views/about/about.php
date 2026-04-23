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
                <h2 class="f_600 f_size_32 title_color mb_20">About Our Industry</h2>
                <h3 class="f_300 title_color">Leverage agile frameworks to provide a robust synopsis for high level overviews.</h3>
                <p class="mb_30">Iterative approaches to corporate strategy foster collaborative thinking to further the overall value proposition. Organically grow the holistic world view of disruptive innovation via workplace diversity and empowerment.</p>
                <p class="mb_40">Bring to the table win-win survival strategies to ensure proactive domination. At the end of the day, going forward, a new normal that has evolved from generation X is on the runway heading towards a streamlined cloud solution. User generated content in real-time will have multiple touchpoints for offshoring.</p>
                <a href="?page=company.history" class="read_more btn_yellow">Know more</a>
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
            <h5 class="f_size_32 f_600 mb_20">Our Mission</h5>
            <span class="title_br"></span>
            <p class="mb_30">Consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
            <p class="mb_55">Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
            <!-- Icons row -->
            <div class="mission_icons_row">
                <div class="mission_icon_item">
                    <div class="mission_icon_circle">
                        <i class="fa fa-line-chart"></i>
                    </div>
                    <h4 class="f_size_18 f_600">20% High<br>Growth</h4>
                </div>
                <div class="mission_icon_divider"></div>
                <div class="mission_icon_item">
                    <div class="mission_icon_circle">
                        <i class="fa fa-users"></i>
                    </div>
                    <h4 class="f_size_18 f_600">Best<br>Engineers</h4>
                </div>
                <div class="mission_icon_divider"></div>
                <div class="mission_icon_item">
                    <div class="mission_icon_circle">
                        <i class="fa fa-headphones"></i>
                    </div>
                    <h4 class="f_size_18 f_600">24/7<br>Supports</h4>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== SECTION 3: Our Workers (nhúng từ teams.php) ===== -->
<section class="team_area sec_gap">
    <div class="container">
        <div class="section_title mb_55 scroll-reveal reveal-up">
            <h2 class="f_600 f_size_32 title_color">Our Workers</h2>
            <span class="title_br"></span>
            <p class="mt_7">Osed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt neque porro quisquam est.</p>
        </div>
        <!-- Nhúng grid team từ teams.php -->
        <div class="row team_inner mb-30">
            <div class="col-lg-3 col-sm-6 scroll-reveal reveal-up" style="transition-delay:0s">
                <div class="team_member text-center">
                    <div class="team_img">
                        <img src="https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/team1.jpg" alt="Merry Joe">
                        <div class="overlay"></div>
                        <ul class="nav social_icon">
                            <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                            <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                            <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                            <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
                        </ul>
                    </div>
                    <h5 class="f_600 f_size_20 title_color mb-0">Merry Joe</h5>
                    <p>Junior Engineer</p>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 scroll-reveal reveal-up" style="transition-delay:0.1s">
                <div class="team_member text-center">
                    <div class="team_img">
                        <img src="https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/team2.jpg" alt="Robert Joe">
                        <div class="overlay"></div>
                        <ul class="nav social_icon">
                            <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                            <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                            <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                            <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
                        </ul>
                    </div>
                    <h5 class="f_600 f_size_20 title_color mb-0">Robert Joe</h5>
                    <p>Machine Expert</p>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 scroll-reveal reveal-up" style="transition-delay:0.2s">
                <div class="team_member text-center">
                    <div class="team_img">
                        <img src="https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/team3.jpg" alt="Michale">
                        <div class="overlay"></div>
                        <ul class="nav social_icon">
                            <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                            <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                            <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                            <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
                        </ul>
                    </div>
                    <h5 class="f_600 f_size_20 title_color mb-0">Michale</h5>
                    <p>Hardcore Engineer</p>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 scroll-reveal reveal-up" style="transition-delay:0.3s">
                <div class="team_member text-center">
                    <div class="team_img">
                        <img src="https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/team4.jpg" alt="Satlen Joe">
                        <div class="overlay"></div>
                        <ul class="nav social_icon">
                            <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                            <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                            <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                            <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
                        </ul>
                    </div>
                    <h5 class="f_600 f_size_20 title_color mb-0">Satlen Joe</h5>
                    <p>Senior Machine Operator</p>
                </div>
            </div>
        </div>
        <div class="text-center mt-4">
            <a href="?page=teams" class="read_more btn_yellow">Know more</a>
        </div>
    </div>
</section>

<!-- ===== SECTION 4: Our History ===== -->
<section class="history_area">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 scroll-reveal reveal-left">
                <div class="section_title">
                    <h2 class="f_600 f_size_32 title_color d-block mb-0">Our History</h2>
                    <span class="title_br ml-0"></span>
                </div>
            </div>
            <div class="col-lg-8 scroll-reveal reveal-right">
                <div class="history_content">
                    <h4 class="f_play f_size_20 title_color d-inline-block">Working since 1992</h4>
                    <h5 class="f_size_20 title_color f_500 mb_20">Leverage agile frameworks to provide a robust synopsis for high level overviews. Iterative approaches to corporate strategy foster collaborative thinking to further the overall value proposition.</h5>
                    <p class="mb-0">Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== SECTION 5: Clients Logo ===== -->
<section class="clients_logo_area">
    <div class="container">
        <div class="d-flex justify-content-between flex-wrap">
            <div class="clients_logo_item">
                <a href="#"><img src="https://via.placeholder.com/120x60?text=Client+1" alt="Client 1"></a>
            </div>
            <div class="clients_logo_item">
                <a href="#"><img src="https://via.placeholder.com/120x60?text=Client+2" alt="Client 2"></a>
            </div>
            <div class="clients_logo_item">
                <a href="#"><img src="https://via.placeholder.com/120x60?text=Client+3" alt="Client 3"></a>
            </div>
            <div class="clients_logo_item">
                <a href="#"><img src="https://via.placeholder.com/120x60?text=Client+4" alt="Client 4"></a>
            </div>
            <div class="clients_logo_item">
                <a href="#"><img src="https://via.placeholder.com/120x60?text=Client+5" alt="Client 5"></a>
            </div>
        </div>
    </div>
</section>
