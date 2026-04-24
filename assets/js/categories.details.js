/**
 * categories.details.js
 * JavaScript cho trang Service Details (categories_details.php)
 */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {

        // ── Accordion: toggle đóng/mở, chỉ 1 item mở tại 1 thời điểm ──
        var accordion = document.getElementById('accordion');
        if (accordion) {
            var buttons = accordion.querySelectorAll('.btn-accordion');

            buttons.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var targetId = btn.getAttribute('data-target');
                    var panel    = document.querySelector(targetId);
                    if (!panel) return;

                    var isOpen = panel.classList.contains('show');

                    // Đóng tất cả panels
                    accordion.querySelectorAll('.panel-collapse').forEach(function (p) {
                        p.classList.remove('show');
                    });
                    accordion.querySelectorAll('.btn-accordion').forEach(function (b) {
                        b.classList.add('collapsed');
                        b.setAttribute('aria-expanded', 'false');
                    });

                    // Nếu panel đang đóng thì mở lên, nếu đang mở thì giữ đóng
                    if (!isOpen) {
                        panel.classList.add('show');
                        btn.classList.remove('collapsed');
                        btn.setAttribute('aria-expanded', 'true');
                    }
                });
            });
        }

        // ── Highlight active sidebar item ──
        var params      = new URLSearchParams(window.location.search);
        var currentSlug = params.get('slug') || '';
        var sidebarLinks = document.querySelectorAll('.service_menu_tab .nav-link');

        sidebarLinks.forEach(function (link) {
            var href       = link.getAttribute('href') || '';
            var linkParams = new URLSearchParams(href.replace(/^.*\?/, ''));
            if (linkParams.get('slug') === currentSlug) {
                link.classList.add('active');
                var li = link.closest('.nav-item');
                if (li) li.classList.add('active');
            }
        });

        // ── Smooth scroll to top khi chuyển service ──
        sidebarLinks.forEach(function (link) {
            link.addEventListener('click', function () {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });

    });

})();
