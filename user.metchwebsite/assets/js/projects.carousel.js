/**
 * FEATURED PROJECTS CAROUSEL
 * assets/js/projects.carousel.js
 *
 * Clone logic từ awards.js
 * — Carousel chạy liên tục, drag/swipe để di chuyển
 */

(function () {
    'use strict';

    const SPEED          = 1.0;  // px/frame (~60px/s ở 60fps)
    const DRAG_THRESHOLD = 5;    // px — dưới ngưỡng này vẫn là click

    function init() {
        const wrapper = document.querySelector('.projects_carousel_wrapper');
        const track   = document.querySelector('.projects_carousel_track');
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
            running = true; // tiếp tục từ vị trí hiện tại, không giật
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

    // ── Boot ─────────────────────────────────────────────────────────
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
