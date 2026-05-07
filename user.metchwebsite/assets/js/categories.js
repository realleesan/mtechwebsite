/**
 * categories.js
 * JavaScript cho trang Our Services (categories)
 */

(function () {
    'use strict';

    // ----------------------------------------------------------------
    // Hover effect fallback cho thiết bị không hỗ trợ CSS :hover tốt
    // ----------------------------------------------------------------
    function initServiceHover() {
        var items = document.querySelectorAll('.service_item');
        if (!items.length) return;

        items.forEach(function (item) {
            var hoverContent = item.querySelector('.hover_content');
            if (!hoverContent) return;

            item.addEventListener('mouseenter', function () {
                hoverContent.style.opacity = '1';
            });

            item.addEventListener('mouseleave', function () {
                hoverContent.style.opacity = '0';
            });
        });
    }

    // ----------------------------------------------------------------
    // Khởi chạy khi DOM sẵn sàng
    // ----------------------------------------------------------------
    document.addEventListener('DOMContentLoaded', function () {
        initServiceHover();
    });
})();
