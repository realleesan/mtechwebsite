/**
 * TEAMS CAROUSEL
 * assets/js/teams.js
 *
 * Clone 100% logic từ awards.js
 * — Carousel chạy liên tục từ phải qua trái
 * — Drag/swipe để di chuyển
 * — Lightbox khi click vào ảnh thành viên
 */

(function () {
    'use strict';

    const SPEED          = 1.4;  // px/frame (~84px/s ở 60fps)
    const DRAG_THRESHOLD = 5;    // px — dưới ngưỡng này vẫn là click

    function initCarousel() {
        const wrapper = document.querySelector('.teams_carousel_wrapper');
        const track   = document.querySelector('.teams_carousel_track');
        if (!wrapper || !track) return;

        let unitWidth = 0;
        function getUnitWidth() {
            unitWidth = track.scrollWidth / 3; // nhân 3 lần → chia 3
        }
        getUnitWidth();

        let currentX = 0;
        let running  = true;

        // ── RAF loop ────────────────────────────────────────────────────
        function tick() {
            if (running) {
                currentX -= SPEED;
                if (Math.abs(currentX) >= unitWidth) {
                    currentX += unitWidth;
                }
                track.style.transform = `translateX(${currentX}px)`;
            }
            requestAnimationFrame(tick);
        }
        requestAnimationFrame(tick);

        // ── Drag / Swipe ─────────────────────────────────────────────
        let isDragging      = false;
        let hasMoved        = false;
        let startX          = 0;
        let startTranslateX = 0;

        function dragStart(e) {
            if (e.type === 'mousedown' && e.button !== 0) return;
            isDragging      = true;
            hasMoved        = false;
            running         = false;
            startTranslateX = currentX;
            startX = e.type.includes('mouse') ? e.pageX : e.touches[0].clientX;
            wrapper.style.cursor = 'grabbing';
        }

        function dragMove(e) {
            if (!isDragging) return;
            const clientX = e.type.includes('mouse') ? e.pageX : e.touches[0].clientX;
            const diff    = clientX - startX;

            if (Math.abs(diff) > DRAG_THRESHOLD) hasMoved = true;

            let next = startTranslateX + diff;
            if (next > 0)           next -= unitWidth;
            if (next <= -unitWidth) next += unitWidth;

            currentX = next;
            track.style.transform = `translateX(${currentX}px)`;

            if (e.cancelable && e.type.includes('touch')) e.preventDefault();
        }

        function dragEnd() {
            if (!isDragging) return;
            isDragging = false;
            wrapper.style.cursor = 'grab';
            running = true;
        }

        // Mouse events
        wrapper.addEventListener('mousedown',  dragStart);
        wrapper.addEventListener('mousemove',  dragMove);
        wrapper.addEventListener('mouseup',    dragEnd);
        wrapper.addEventListener('mouseleave', dragEnd);

        // Touch events
        wrapper.addEventListener('touchstart', dragStart, { passive: true });
        wrapper.addEventListener('touchmove',  dragMove,  { passive: false });
        wrapper.addEventListener('touchend',   dragEnd);

        // Recalc khi resize
        window.addEventListener('resize', () => {
            setTimeout(() => { getUnitWidth(); }, 250);
        });
    }

    // ── LIGHTBOX LOGIC ───────────────────────────────────────────────
    function initLightbox() {
        const overlay    = document.getElementById('teamsLightbox');
        const closeBtn   = document.getElementById('teamsLightboxClose');
        const clickables = document.querySelectorAll('.teams_clickable');

        if (!overlay || !closeBtn || clickables.length === 0) return;

        const imgEl      = document.getElementById('teamsLightboxImg');
        const nameEl     = document.getElementById('teamsLightboxName');
        const positionEl = document.getElementById('teamsLightboxPosition');
        const bioEl      = document.getElementById('teamsLightboxBio');

        clickables.forEach(el => {
            el.addEventListener('click', function () {
                const image    = this.dataset.image    || '';
                const name     = this.dataset.name     || '';
                const position = this.dataset.position || '';
                const bio      = this.dataset.bio      || '';

                if (!image) return;

                imgEl.src          = image;
                imgEl.alt          = name;
                nameEl.textContent = name;
                positionEl.textContent = position;
                bioEl.textContent  = bio;
                bioEl.style.display = bio ? 'block' : 'none';

                overlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            });
        });

        function closeLightbox() {
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        closeBtn.addEventListener('click', closeLightbox);

        overlay.addEventListener('click', function (e) {
            if (e.target === this) closeLightbox();
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && overlay.classList.contains('active')) {
                closeLightbox();
            }
        });
    }

    // ── Boot ─────────────────────────────────────────────────────────
    function init() {
        initCarousel();
        initLightbox();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
