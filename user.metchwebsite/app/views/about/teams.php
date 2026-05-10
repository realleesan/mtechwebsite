<?php
/**
 * Teams Page View
 * Biến nhận: $teams (array từ TeamsModel::getAllActive())
 */

$teams = $teams ?? [];

// Fallback nếu DB chưa có dữ liệu
if (empty($teams)) {
    $teams = [
        ['id' => 1, 'name' => 'Nguyễn Tùng Giang',  'position' => 'Giám đốc',                      'image' => 'https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/team2.jpg', 'bio' => ''],
        ['id' => 2, 'name' => 'Đỗ Bá Dương',         'position' => 'Chủ nhiệm Khảo sát & Thiết kế', 'image' => 'https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/team1.jpg', 'bio' => ''],
        ['id' => 3, 'name' => 'Trần Văn Bình',        'position' => 'Chủ trì thiết kế Điện',          'image' => 'https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/team3.jpg', 'bio' => ''],
        ['id' => 4, 'name' => 'Nguyễn Ngọc Trường',  'position' => 'Chủ trì TK Cấp thoát nước',     'image' => 'https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/team4.jpg', 'bio' => ''],
    ];
}
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
            <?php foreach ($teams as $member): ?>
            <div class="col-lg-3 col-sm-6">
                <div class="team_member text-center">
                    <div class="team_img">
                        <?php if (!empty($member['image'])): ?>
                            <img src="<?= htmlspecialchars($member['image'], ENT_QUOTES, 'UTF-8') ?>"
                                 alt="<?= htmlspecialchars($member['name'], ENT_QUOTES, 'UTF-8') ?>">
                        <?php else: ?>
                            <div class="team_img_placeholder">
                                <i class="fa fa-user"></i>
                            </div>
                        <?php endif; ?>
                        <div class="overlay"></div>
                        <ul class="nav social_icon">
                            <li><a href="#" aria-label="Facebook"><i class="fa fa-facebook"></i></a></li>
                            <li><a href="#" aria-label="LinkedIn"><i class="fa fa-linkedin"></i></a></li>
                            <li><a href="#" aria-label="Twitter"><i class="fa fa-twitter"></i></a></li>
                            <li><a href="#" aria-label="Google Plus"><i class="fa fa-google-plus"></i></a></li>
                        </ul>
                    </div>
                    <h5 class="f_600 f_size_20 title_color mb-0">
                        <?= htmlspecialchars($member['name'], ENT_QUOTES, 'UTF-8') ?>
                    </h5>
                    <p><?= htmlspecialchars($member['position'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php if (!empty($member['bio'])): ?>
                        <p class="team_bio"><?= htmlspecialchars($member['bio'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
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
                    <form action="/doi-ngu/submit-question" method="post" id="questionForm" novalidate>
                        <div class="form-group">
                            <input type="email" name="email" value="" class="form-control" id="q_email"
                                   aria-required="true" placeholder="Địa chỉ Email*" required />
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <input type="text" name="subject" value="" class="form-control" id="q_subject"
                                   aria-required="true" placeholder="Tiêu đề*" required />
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <textarea name="message" cols="40" rows="6" class="form-control" id="q_message"
                                      aria-required="true" placeholder="Nội dung câu hỏi của bạn" required></textarea>
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
