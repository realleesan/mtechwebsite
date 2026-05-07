/**
 * blogs.js — Trang danh sách Blog
 */

(function () {
    'use strict';

    // ----------------------------------------------------------------
    // Smooth scroll to top khi click phân trang
    // ----------------------------------------------------------------
    document.querySelectorAll('.blog_pagination .page-link').forEach(function (link) {
        link.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });

    // ----------------------------------------------------------------
    // Active tag highlight trong sidebar
    // ----------------------------------------------------------------
    var params = new URLSearchParams(window.location.search);
    var activeTag = params.get('tag');
    var activeCat = params.get('cat');

    if (activeTag) {
        document.querySelectorAll('.tagcloud .tag-cloud-link').forEach(function (el) {
            var href = el.getAttribute('href') || '';
            if (href.indexOf('tag=' + encodeURIComponent(activeTag)) !== -1) {
                el.style.background = '#e65c00';
                el.style.color = '#fff';
                el.style.borderColor = '#e65c00';
            }
        });
    }

    if (activeCat) {
        document.querySelectorAll('.widget_categories ul li a').forEach(function (el) {
            var href = el.getAttribute('href') || '';
            if (href.indexOf('cat=' + activeCat) !== -1) {
                el.parentElement.classList.add('active');
            }
        });
    }

    // ----------------------------------------------------------------
    // Blog card hover — đã xử lý bằng CSS, JS chỉ fallback IE
    // ----------------------------------------------------------------

})();
