/**
 * CLIENT LOGO CAROUSEL
 * assets/js/client.logos.js
 *
 * Dùng requestAnimationFrame thay vì CSS keyframes
 * → carousel tiếp tục từ đúng vị trí sau khi thả drag, không giật
 */

(function () {
    'use strict';

    const SPEED         = 1.4;  // px mỗi frame (~84px/s ở 60fps)
    const DRAG_THRESHOLD = 5;   // px — dưới ngưỡng này vẫn là click

    function init() {
        const wrapper = document.querySelector('.client_logo_carousel_wrapper');
        const track   = document.querySelector('.client_logo_carousel_track');
        if (!wrapper || !track) return;

        // Tắt hoàn toàn CSS animation (đã xử lý bằng rAF)
        track.style.animation = 'none';

        // Chiều rộng 1 set logo (track nhân 3 lần → chia 3)
        // Đợi layout xong mới đọc scrollWidth
        let unitWidth = 0;
        function getUnitWidth() {
            unitWidth = track.scrollWidth / 3;
        }
        getUnitWidth();

        let currentX  = 0;   // vị trí translateX hiện tại (luôn âm hoặc 0)
        let rafId     = null;
        let running   = true;

        // ── RAF loop ────────────────────────────────────────────────────────
        function tick() {
            if (running) {
                currentX -= SPEED;
                // Reset về 0 khi đã đi hết 1 unit (infinite loop)
                if (Math.abs(currentX) >= unitWidth) {
                    currentX += unitWidth;
                }
                track.style.transform = `translateX(${currentX}px)`;
            }
            rafId = requestAnimationFrame(tick);
        }
        rafId = requestAnimationFrame(tick);

        // ── Drag / Swipe ─────────────────────────────────────────────────
        let isDragging = false;
        let hasMoved   = false;
        let startX     = 0;
        let startTranslateX = 0;

        function dragStart(e) {
            if (e.type === 'mousedown' && e.button !== 0) return;
            isDragging      = true;
            hasMoved        = false;
            running         = false;   // dừng auto-scroll
            startTranslateX = currentX;
            startX = e.type.includes('mouse') ? e.pageX : e.touches[0].clientX;
            wrapper.style.cursor = 'grabbing';
        }

        function dragMove(e) {
            if (!isDragging) return;
            const clientX = e.type.includes('mouse') ? e.pageX : e.touches[0].clientX;
            const diff    = clientX - startX;

            if (Math.abs(diff) > DRAG_THRESHOLD) hasMoved = true;

            // Cập nhật vị trí theo ngón tay / chuột
            let next = startTranslateX + diff;

            // Giữ trong vòng lặp infinite
            if (next > 0)              next -= unitWidth;
            if (next <= -unitWidth)    next += unitWidth;

            currentX = next;
            track.style.transform = `translateX(${currentX}px)`;

            if (e.cancelable && e.type.includes('touch')) e.preventDefault();
        }

        function dragEnd() {
            if (!isDragging) return;
            isDragging = false;
            wrapper.style.cursor = 'grab';
            // Tiếp tục chạy từ đúng vị trí currentX — không reset gì cả
            running = true;
        }
        wrapper.addEventListener('click', (e) => {
            if (hasMoved) {
                e.preventDefault();
                e.stopPropagation();
                hasMoved = false;
            }
        }, true);

        wrapper.addEventListener('mousedown',  dragStart);
        wrapper.addEventListener('mousemove',  dragMove);
        wrapper.addEventListener('mouseup',    dragEnd);
        wrapper.addEventListener('mouseleave', dragEnd);

        wrapper.addEventListener('touchstart', dragStart, { passive: true });
        wrapper.addEventListener('touchmove',  dragMove,  { passive: false });
        wrapper.addEventListener('touchend',   dragEnd);

        wrapper.addEventListener('contextmenu', (e) => { if (hasMoved) e.preventDefault(); });

        // Recalc unitWidth khi resize
        window.addEventListener('resize', () => {
            setTimeout(() => { getUnitWidth(); }, 250);
        });
    }

    // ── Mobile: grayscale → color khi vào viewport ──────────────────────
    function initMobileColorReveal() {
        if (window.innerWidth >= 992) return;
        const logoArea = document.querySelector('.clients_logo_area');
        if (!logoArea) return;

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('in-viewport');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.3 });

        observer.observe(logoArea);
    }

    // ── Boot ─────────────────────────────────────────────────────────────
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => { init(); initMobileColorReveal(); });
    } else {
        init();
        initMobileColorReveal();
    }

})();
