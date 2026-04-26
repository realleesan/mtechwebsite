<?php
/**
 * testimonials.php — Trang đánh giá khách hàng
 * Carousel 3×3: mỗi slide hiển thị 9 thẻ (3 hàng × 3 cột)
 * Biến nhận: $testimonials (array), $totalTestimonials (int)
 */

$testimonials      = $testimonials      ?? [];
$totalTestimonials = $totalTestimonials ?? 0;

// Chia thành các nhóm 9 thẻ
$slides = array_chunk($testimonials, 9);
$totalSlides = count($slides);
?>

<section class="testimonial_area sec_gap">
    <div class="container">

        <!-- Section Title -->
        <div class="section_title mb_55">
            <h2 class="f_600 f_size_32 title_color">
                What Our Customers Says
                <span class="title_br"></span>
            </h2>
            <p class="mt_7">
                Leverage agile frameworks to provide a robust synopsis for high level overviews.
                Iterative approaches to corporate strategy foster collaborative thinking to further
                the overall value proposition. Organically grow the holistic world view of
                disruptive innovation via workplace diversity.
            </p>
        </div>

        <?php if (empty($slides)): ?>
            <p class="text-muted text-center">Chưa có đánh giá nào.</p>
        <?php else: ?>

        <!-- Carousel wrapper -->
        <div class="testi_carousel" id="testiCarousel">

            <!-- Arrow trái -->
            <button class="testi_arrow testi_arrow_prev" id="testiPrev" aria-label="Previous">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                     stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
            </button>

            <!-- Track chứa các slide -->
            <div class="testi_track_wrap">
                <div class="testi_track" id="testiTrack">
                    <?php foreach ($slides as $slideIndex => $slideItems): ?>
                        <div class="testi_slide" data-slide="<?php echo $slideIndex; ?>">
                            <div class="testimonial_inner testimonial_inner_two">
                                <?php foreach ($slideItems as $item): ?>
                                    <?php
                                    $logo    = !empty($item['company_logo'])
                                               ? $item['company_logo']
                                               : 'https://shtheme.com/demosd/wokrate/wp-content/uploads/2019/12/t_img2.png';
                                    $city    = htmlspecialchars($item['location_city'] ?? '');
                                    $country = htmlspecialchars($item['location_country'] ?? '');
                                    $name    = htmlspecialchars($item['company_name']);
                                    $review  = htmlspecialchars($item['review_content']);
                                    ?>
                                    <div class="testimonial_item_width">
                                        <div class="testimonial_item text-center">
                                            <img class="testimonial_img rounded-circle"
                                                 src="<?php echo $logo; ?>"
                                                 alt="<?php echo $name; ?>">
                                            <h6 class="f_600 title_color"><?php echo $name; ?></h6>
                                            <div class="country_name">
                                                <?php if ($city): ?><span><?php echo $city; ?></span><?php endif; ?>
                                                <?php if ($country): ?><span><?php echo $country; ?></span><?php endif; ?>
                                            </div>
                                            <p><?php echo $review; ?></p>
                                            <i class="q_icon"></i>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div><!-- /.testi_slide -->
                    <?php endforeach; ?>
                </div><!-- /.testi_track -->
            </div><!-- /.testi_track_wrap -->

            <!-- Arrow phải -->
            <button class="testi_arrow testi_arrow_next" id="testiNext" aria-label="Next">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                     stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"/>
                </svg>
            </button>

        </div><!-- /.testi_carousel -->

        <!-- Dots indicator -->
        <?php if ($totalSlides > 1): ?>
            <div class="testi_dots" id="testiDots">
                <?php for ($i = 0; $i < $totalSlides; $i++): ?>
                    <button class="testi_dot <?php echo $i === 0 ? 'active' : ''; ?>"
                            data-index="<?php echo $i; ?>"
                            aria-label="Slide <?php echo $i + 1; ?>"></button>
                <?php endfor; ?>
            </div>
        <?php endif; ?>

        <?php endif; ?>

    </div>
</section>

<script>
(function () {
    var track      = document.getElementById('testiTrack');
    var prevBtn    = document.getElementById('testiPrev');
    var nextBtn    = document.getElementById('testiNext');
    var dotsWrap   = document.getElementById('testiDots');
    var dots       = dotsWrap ? dotsWrap.querySelectorAll('.testi_dot') : [];
    var total      = <?php echo $totalSlides; ?>;
    var current    = 0;
    var isAnimating = false;

    if (!track || total <= 1) {
        // Ẩn arrow nếu chỉ có 1 slide
        if (prevBtn) prevBtn.style.display = 'none';
        if (nextBtn) nextBtn.style.display = 'none';
        return;
    }

    function goTo(index) {
        if (isAnimating) return;
        isAnimating = true;

        current = (index + total) % total;
        track.style.transform = 'translateX(-' + (current * 100) + '%)';

        // Update dots
        dots.forEach(function (d, i) {
            d.classList.toggle('active', i === current);
        });

        // Update arrow state
        prevBtn.classList.toggle('disabled', false);
        nextBtn.classList.toggle('disabled', false);

        setTimeout(function () { isAnimating = false; }, 650);
    }

    prevBtn.addEventListener('click', function () { goTo(current - 1); });
    nextBtn.addEventListener('click', function () { goTo(current + 1); });

    dots.forEach(function (dot) {
        dot.addEventListener('click', function () {
            goTo(parseInt(this.dataset.index));
        });
    });

    // Swipe support (mobile)
    var startX = 0;
    track.addEventListener('touchstart', function (e) {
        startX = e.touches[0].clientX;
    }, { passive: true });
    track.addEventListener('touchend', function (e) {
        var diff = startX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 50) goTo(diff > 0 ? current + 1 : current - 1);
    }, { passive: true });

    // Auto-play mỗi 6 giây
    var autoPlay = setInterval(function () { goTo(current + 1); }, 6000);

    // Dừng autoplay khi hover
    var wrapper = document.getElementById('testiCarousel');
    if (wrapper) {
        wrapper.addEventListener('mouseenter', function () { clearInterval(autoPlay); });
        wrapper.addEventListener('mouseleave', function () {
            autoPlay = setInterval(function () { goTo(current + 1); }, 6000);
        });
    }
}());
</script>
