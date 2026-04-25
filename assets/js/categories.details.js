/**
 * categories.details.js
 * JavaScript cho trang Service Details (categories_details.php)
 */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {

        // ── Image Slider ─────────────────────────────────────────
        var sliderEl = document.getElementById('serviceSlider');
        if (sliderEl) {
            var slides     = JSON.parse(sliderEl.getAttribute('data-slides') || '[]');
            var mainImg    = sliderEl.querySelector('.slider_main_img');
            var thumbs     = sliderEl.querySelectorAll('.slider_thumb');
            var btnPrev    = sliderEl.querySelector('.slider_prev');
            var btnNext    = sliderEl.querySelector('.slider_next');
            var current    = 0;

            function goTo(index) {
                if (slides.length === 0) return;
                current = (index + slides.length) % slides.length;

                // Fade transition
                mainImg.classList.add('fading');
                setTimeout(function () {
                    mainImg.src = slides[current];
                    mainImg.classList.remove('fading');
                }, 200);

                // Update thumbnails
                thumbs.forEach(function (t, i) {
                    t.classList.toggle('active', i === current);
                });
            }

            if (btnPrev) btnPrev.addEventListener('click', function () { goTo(current - 1); });
            if (btnNext) btnNext.addEventListener('click', function () { goTo(current + 1); });

            thumbs.forEach(function (thumb) {
                thumb.addEventListener('click', function () {
                    goTo(parseInt(thumb.getAttribute('data-index'), 10));
                });
            });
        }

        // ── Accordion ────────────────────────────────────────────
        var accordion = document.getElementById('accordion');
        if (accordion) {
            accordion.querySelectorAll('.btn-accordion').forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    var targetId = btn.getAttribute('data-target');
                    var panel    = document.querySelector(targetId);
                    var card     = btn.closest('.card');
                    if (!panel || !card) return;

                    var isOpen = panel.classList.contains('show');

                    // Đóng tất cả — set max-height về 0
                    accordion.querySelectorAll('.panel-collapse').forEach(function (p) {
                        p.style.maxHeight = '0px';
                        p.classList.remove('show');
                    });
                    accordion.querySelectorAll('.btn-accordion').forEach(function (b) {
                        b.classList.add('collapsed');
                        b.setAttribute('aria-expanded', 'false');
                    });
                    accordion.querySelectorAll('.card').forEach(function (c) {
                        c.classList.remove('open');
                    });

                    // Nếu trước đó đang đóng → mở với đúng scrollHeight
                    if (!isOpen) {
                        panel.classList.add('show');
                        panel.style.maxHeight = panel.scrollHeight + 'px';
                        btn.classList.remove('collapsed');
                        btn.setAttribute('aria-expanded', 'true');
                        card.classList.add('open');
                    }
                });
            });
        }

        // ── Smooth scroll to top khi chuyển service ──
        document.querySelectorAll('.service_menu_tab .nav-link').forEach(function (link) {
            link.addEventListener('click', function () {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });

    });

})();
