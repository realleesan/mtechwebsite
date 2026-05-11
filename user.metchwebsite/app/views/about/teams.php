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
