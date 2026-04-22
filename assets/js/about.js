/**
 * About Page JavaScript
 * - Scroll reveal animations
 * - Timeline navigation
 */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {

        // ── Scroll Reveal ──────────────────────────────────────────
        const revealEls = document.querySelectorAll('.reveal');

        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target); // chỉ animate 1 lần
                }
            });
        }, {
            threshold: 0.15,
            rootMargin: '0px 0px -50px 0px'
        });

        revealEls.forEach(function (el) {
            observer.observe(el);
        });

        // ── Timeline ──────────────────────────────────────────────
        const yearItems  = document.querySelectorAll('.tl_item');
        const slides     = document.querySelectorAll('.timeline_slide');
        const btnUp      = document.getElementById('tl_up');
        const btnDown    = document.getElementById('tl_down');

        if (!yearItems.length) return;

        let currentIndex = Array.from(yearItems).findIndex(el => el.classList.contains('active'));
        if (currentIndex < 0) currentIndex = 0;

        function goToIndex(idx) {
            if (idx < 0 || idx >= yearItems.length) return;

            // Update year list
            yearItems.forEach(el => el.classList.remove('active'));
            yearItems[idx].classList.add('active');

            // Update slides
            const targetYear = yearItems[idx].dataset.year;
            slides.forEach(function (slide) {
                slide.classList.remove('active');
                if (slide.dataset.slide === targetYear) {
                    slide.classList.add('active');
                }
            });

            currentIndex = idx;
        }

        // Click vào năm
        yearItems.forEach(function (item, idx) {
            item.addEventListener('click', function () {
                goToIndex(idx);
            });
        });

        // Nút lên
        if (btnUp) {
            btnUp.addEventListener('click', function () {
                goToIndex(currentIndex - 1);
            });
        }

        // Nút xuống
        if (btnDown) {
            btnDown.addEventListener('click', function () {
                goToIndex(currentIndex + 1);
            });
        }

    });

})();
