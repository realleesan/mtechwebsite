/**
 * testimonials.js — Trang Testimonials
 */

(function () {
    'use strict';

    // Scroll to top khi click phân trang
    document.querySelectorAll('.testimonial_pagination .page-link').forEach(function (link) {
        link.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });

})();
