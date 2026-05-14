<?php
/**
 * Teams Page View
 * Biến nhận: $teams (array từ TeamsModel::getAllActive())
 * Carousel tự động chạy từ phải qua trái (giống awards)
 */

$teams = $teams ?? [];

// Không render nếu không có dữ liệu
if (empty($teams)) return;

// Nhân 3 lần để tạo infinite loop (giống awards)
$duplicated = array_merge($teams, $teams, $teams);
?>

<!-- Modal Lightbox cho Teams -->
<div class="teams_lightbox_overlay" id="teamsLightbox">
    <div class="teams_lightbox_box">
        <button class="teams_lightbox_close" id="teamsLightboxClose" aria-label="Close">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
        <div class="teams_lightbox_content">
            <img id="teamsLightboxImg" src="" alt="Member" class="teams_lightbox_img">
            <div class="teams_lightbox_info">
                <h3 id="teamsLightboxName" class="teams_lightbox_name"></h3>
                <p id="teamsLightboxPosition" class="teams_lightbox_position"></p>
                <p id="teamsLightboxBio" class="teams_lightbox_bio"></p>
            </div>
        </div>
    </div>
</div>

<section class="teams_area sec_gap">
    <div class="container">

        <!-- Section Title -->
        <div class="section_title mb_55">
            <h2 class="f_600 f_size_32 title_color">Đội Ngũ Chuyên Gia</h2>
            <span class="title_br"></span>
            <p class="mt_7">
                Sức mạnh cốt lõi của MTECH nằm ở đội ngũ gồm 25 Thạc sĩ, Kỹ sư và Chuyên gia am hiểu sâu sắc
                trong các lĩnh vực vật liệu, xây dựng, kiến trúc, cơ điện và kinh tế.
                Chúng tôi luôn tận tâm mang đến các giải pháp thiết kế và quản lý dự án tối ưu nhất.
            </p>
        </div>

    </div>

    <!-- Carousel full-width (không bị giới hạn bởi container) -->
    <div class="teams_carousel_wrapper">
        <div class="teams_carousel_track">
            <?php foreach ($duplicated as $member): ?>
                <div class="teams_slide">
                    <!-- Khung ảnh vuông -->
                    <div class="teams_img_wrap teams_clickable"
                         data-image="<?= htmlspecialchars($member['image'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                         data-name="<?= htmlspecialchars($member['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                         data-position="<?= htmlspecialchars($member['position'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                         data-bio="<?= htmlspecialchars($member['bio'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <?php if (!empty($member['image'])): ?>
                            <img src="<?= htmlspecialchars($member['image'], ENT_QUOTES, 'UTF-8') ?>"
                                 alt="<?= htmlspecialchars($member['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                 class="teams_img">
                        <?php else: ?>
                            <div class="teams_img_placeholder">
                                <i class="fa fa-user"></i>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Text bên dưới -->
                    <div class="teams_info">
                        <h4 class="teams_name"><?= htmlspecialchars($member['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></h4>
                        <?php if (!empty($member['position'])): ?>
                            <p class="teams_position"><?= htmlspecialchars($member['position'], ENT_QUOTES, 'UTF-8') ?></p>
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
                    <!-- Thông báo kết quả gửi form -->
                    <div id="questionFormMsg" class="question_form_msg" style="display:none" role="alert" aria-live="polite"></div>

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
                            <input type="submit" value="Gửi câu hỏi" class="btn submit_btn" id="questionSubmitBtn" />
                        </div>
                    </form>
                </div>

<script>
(function () {
    var form    = document.getElementById('questionForm');
    var msgBox  = document.getElementById('questionFormMsg');
    var submitBtn = document.getElementById('questionSubmitBtn');

    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        // Ẩn thông báo cũ
        msgBox.style.display = 'none';
        msgBox.className = 'question_form_msg';

        // Disable nút tránh double-submit
        submitBtn.disabled = true;
        submitBtn.value = 'Đang gửi...';

        var data = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: data
        })
        .then(function (res) { return res.json(); })
        .then(function (json) {
            // Xóa lỗi cũ
            form.querySelectorAll('.invalid-feedback').forEach(function (el) {
                el.textContent = '';
            });
            form.querySelectorAll('.form-control').forEach(function (el) {
                el.classList.remove('is-invalid');
            });

            if (json.success) {
                // Hiển thị thông báo thành công
                msgBox.className = 'question_form_msg question_form_msg--success';
                msgBox.innerHTML =
                    '<span class="qmsg_icon">&#10003;</span>' +
                    '<span>' + (json.message || 'Câu hỏi của bạn đã được gửi thành công!') + '</span>' +
                    '<button type="button" class="qmsg_close" aria-label="Đóng">&times;</button>';
                msgBox.style.display = 'flex';
                form.reset();

                // Tự ẩn sau 6 giây
                setTimeout(function () { msgBox.style.display = 'none'; }, 6000);
            } else {
                // Hiển thị lỗi validation từng field
                if (json.errors) {
                    var fieldMap = { email: 'q_email', subject: 'q_subject', message: 'q_message' };
                    Object.keys(json.errors).forEach(function (key) {
                        var inputId = fieldMap[key];
                        if (inputId) {
                            var input = document.getElementById(inputId);
                            if (input) {
                                input.classList.add('is-invalid');
                                var fb = input.nextElementSibling;
                                if (fb && fb.classList.contains('invalid-feedback')) {
                                    fb.textContent = json.errors[key];
                                }
                            }
                        }
                    });
                }

                // Thông báo lỗi chung
                msgBox.className = 'question_form_msg question_form_msg--error';
                msgBox.innerHTML =
                    '<span class="qmsg_icon">&#9888;</span>' +
                    '<span>' + (json.message || 'Vui lòng kiểm tra lại thông tin.') + '</span>' +
                    '<button type="button" class="qmsg_close" aria-label="Đóng">&times;</button>';
                msgBox.style.display = 'flex';
            }
        })
        .catch(function () {
            msgBox.className = 'question_form_msg question_form_msg--error';
            msgBox.innerHTML =
                '<span class="qmsg_icon">&#9888;</span>' +
                '<span>Có lỗi kết nối, vui lòng thử lại.</span>' +
                '<button type="button" class="qmsg_close" aria-label="Đóng">&times;</button>';
            msgBox.style.display = 'flex';
        })
        .finally(function () {
            submitBtn.disabled = false;
            submitBtn.value = 'Gửi câu hỏi';
        });
    });

    // Nút đóng thông báo
    document.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('qmsg_close')) {
            msgBox.style.display = 'none';
        }
    });
}());
</script>

<style>
.question_form_msg {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 14px 16px;
    border-radius: 6px;
    font-size: 0.95rem;
    line-height: 1.5;
    margin-bottom: 20px;
}
.question_form_msg--success {
    background: #eaf7ee;
    border: 1px solid #b2dfca;
    color: #1a6b3a;
}
.question_form_msg--error {
    background: #fff3f3;
    border: 1px solid #f5c6cb;
    color: #842029;
}
.qmsg_icon {
    font-size: 1.1rem;
    flex-shrink: 0;
    margin-top: 1px;
}
.qmsg_close {
    margin-left: auto;
    background: none;
    border: none;
    font-size: 1.2rem;
    line-height: 1;
    cursor: pointer;
    color: inherit;
    opacity: 0.6;
    padding: 0 0 0 8px;
    flex-shrink: 0;
}
.qmsg_close:hover { opacity: 1; }
</style>
            </div>
        </div>
    </div>
</section>
