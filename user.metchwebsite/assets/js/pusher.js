/**
 * pusher.js — Scroll to Top Button
 *
 * - Hiện nút khi cuộn xuống > 300px
 * - Ẩn nút khi ở gần đầu trang
 * - Click → cuộn mượt về đầu trang
 */

(function () {
    'use strict';

    var SCROLL_THRESHOLD = 300; // px — ngưỡng hiện nút
    var btn = document.getElementById('scrollToTopBtn');

    if (!btn) return;

    /* ---- Hiện / ẩn nút theo vị trí cuộn ---- */
    function onScroll() {
        var scrollY = window.pageYOffset || document.documentElement.scrollTop;

        if (scrollY > SCROLL_THRESHOLD) {
            btn.classList.add('pusher-visible');
        } else {
            btn.classList.remove('pusher-visible');
        }
    }

    /* ---- Cuộn mượt về đầu trang ---- */
    function scrollToTop() {
        // Dùng scrollTo với behavior smooth (hỗ trợ rộng)
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    /* ---- Gắn sự kiện ---- */
    window.addEventListener('scroll', onScroll, { passive: true });
    btn.addEventListener('click', scrollToTop);

    /* ---- Kiểm tra trạng thái ban đầu (trang load ở giữa) ---- */
    onScroll();
})();
