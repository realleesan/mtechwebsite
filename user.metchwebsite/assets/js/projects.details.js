/**
 * projects.details.js — Điều khiển Image Slider/Carousel trang Chi tiết dự án
 */

(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        initProjectSlider();
    });

    function initProjectSlider() {
        const sliderContainer = document.getElementById('projectMainSlider');
        if (!sliderContainer) return;

        const slides = sliderContainer.querySelectorAll('.project-slide-item');
        const dots = document.querySelectorAll('#sliderDots .slider-dot');
        const thumbs = document.querySelectorAll('.slider-thumb-item');
        const prevBtn = document.getElementById('sliderPrevBtn');
        const nextBtn = document.getElementById('sliderNextBtn');

        if (slides.length <= 1) return;

        let currentIndex = 0;
        let slideInterval = null;
        const autoPlayDelay = 4000; // 4 giây

        function showSlide(index) {
            if (index < 0) index = slides.length - 1;
            if (index >= slides.length) index = 0;
            currentIndex = index;

            // Cập nhật slides
            slides.forEach((slide, i) => {
                if (i === currentIndex) {
                    slide.classList.add('active');
                } else {
                    slide.classList.remove('active');
                }
            });

            // Cập nhật dots
            dots.forEach((dot, i) => {
                if (i === currentIndex) {
                    dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                }
            });

            // Cập nhật thumbnails
            thumbs.forEach((thumb, i) => {
                if (i === currentIndex) {
                    thumb.classList.add('active');
                    thumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                } else {
                    thumb.classList.remove('active');
                }
            });
        }

        function nextSlide() {
            showSlide(currentIndex + 1);
        }

        function prevSlide() {
            showSlide(currentIndex - 1);
        }

        function startAutoPlay() {
            stopAutoPlay();
            slideInterval = setInterval(nextSlide, autoPlayDelay);
        }

        function stopAutoPlay() {
            if (slideInterval) {
                clearInterval(slideInterval);
                slideInterval = null;
            }
        }

        // Event listeners cho nút Prev / Next
        if (prevBtn) {
            prevBtn.addEventListener('click', function(e) {
                e.preventDefault();
                prevSlide();
                startAutoPlay();
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function(e) {
                e.preventDefault();
                nextSlide();
                startAutoPlay();
            });
        }

        // Event listeners cho Bullet Dots
        dots.forEach((dot, idx) => {
            dot.addEventListener('click', function(e) {
                e.preventDefault();
                showSlide(idx);
                startAutoPlay();
            });
        });

        // Event listeners cho Thumbnails
        thumbs.forEach((thumb, idx) => {
            thumb.addEventListener('click', function(e) {
                e.preventDefault();
                showSlide(idx);
                startAutoPlay();
            });
        });

        // Tạm dừng khi rê chuột vào Slider và tiếp tục khi rê ra
        sliderContainer.addEventListener('mouseenter', stopAutoPlay);
        sliderContainer.addEventListener('mouseleave', startAutoPlay);

        // Hỗ trợ Touch Swipe trên điện thoại
        let touchStartX = 0;
        let touchEndX = 0;

        sliderContainer.addEventListener('touchstart', function(e) {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        sliderContainer.addEventListener('touchend', function(e) {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        }, { passive: true });

        function handleSwipe() {
            if (touchEndX < touchStartX - 40) {
                // Swipe Left
                nextSlide();
                startAutoPlay();
            }
            if (touchEndX > touchStartX + 40) {
                // Swipe Right
                prevSlide();
                startAutoPlay();
            }
        }

        // Bắt đầu chạy tự động
        startAutoPlay();
    }
})();
