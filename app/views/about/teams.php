<?php
/**
 * Teams Page View
 * Template: docs/template/about/code/teams.html
 */
?>
<!-- Team Area -->
<section class="team_area sec_gap">
    <div class="container">
        <div class="section_title mb_55">
            <h2 class="f_600 f_size_32 title_color">Đội Ngũ Chuyên Gia</h2>
            <span class="title_br"></span>
            <p class="mt_7">Sức mạnh cốt lõi của MTECH nằm ở đội ngũ gồm 25 Thạc sĩ, Kỹ sư và Chuyên gia am hiểu sâu sắc trong các lĩnh vực vật liệu, xây dựng, kiến trúc, cơ điện và kinh tế. Chúng tôi luôn tận tâm mang đến các giải pháp thiết kế và quản lý dự án tối ưu nhất.</p>
        </div>
        <div class="row team_inner mb-30">
            <div class="col-lg-3 col-sm-6">
                <div class="team_member text-center">
                    <div class="team_img">
                        <img src="https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/team2.jpg" alt="team">
                        <div class="overlay"></div>
                        <ul class="nav social_icon">
                            <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                            <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                            <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                            <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
                        </ul>
                    </div>
                    <h5 class="f_600 f_size_20 title_color mb-0">Nguyễn Tùng Giang</h5>
                    <p>Giám đốc</p>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="team_member text-center">
                    <div class="team_img">
                        <img src="https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/team1.jpg" alt="team">
                        <div class="overlay"></div>
                        <ul class="nav social_icon">
                            <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                            <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                            <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                            <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
                        </ul>
                    </div>
                    <h5 class="f_600 f_size_20 title_color mb-0">Đỗ Bá Dương</h5>
                    <p>Chủ nhiệm Khảo sát & Thiết kế</p>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="team_member text-center">
                    <div class="team_img">
                        <img src="https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/team3.jpg" alt="team">
                        <div class="overlay"></div>
                        <ul class="nav social_icon">
                            <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                            <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                            <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                            <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
                        </ul>
                    </div>
                    <h5 class="f_600 f_size_20 title_color mb-0">Trần Văn Bình</h5>
                    <p>Chủ trì thiết kế Điện</p>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="team_member text-center">
                    <div class="team_img">
                        <img src="https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/team4.jpg" alt="team">
                        <div class="overlay"></div>
                        <ul class="nav social_icon">
                            <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                            <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                            <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                            <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
                        </ul>
                    </div>
                    <h5 class="f_600 f_size_20 title_color mb-0">Nguyễn Ngọc Trường</h5>
                    <p>Chủ trì TK Cấp thoát nước</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Question Area -->
<section class="question_area sec_gap">
    <div class="container">
        <div class="row">
            <div class="col-md-9">
                <div class="section_title mb_30">
                    <h2 class="f_600 f_size_32 title_color">Bạn chưa tìm thấy câu trả lời? Hãy gửi câu hỏi cho MTECH</h2>
                    <span class="title_br dark"></span>
                </div>
                <div class="question_form">
                    <form action="/doi-ngu?action=submit_question" method="post" id="questionForm" novalidate>
                        <div class="form-group">
                            <input type="email" name="email" value="" class="form-control" id="q_email" aria-required="true" placeholder="Địa chỉ Email*" required />
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <input type="text" name="subject" value="" class="form-control" id="q_subject" aria-required="true" placeholder="Tiêu đề*" required />
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <textarea name="message" cols="40" rows="10" class="form-control" id="q_message" aria-required="true" placeholder="Nội dung câu hỏi của bạn" required></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <input type="submit" value="Gửi câu hỏi" class="btn submit_btn" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>