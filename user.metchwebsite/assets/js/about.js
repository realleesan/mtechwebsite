/**
 * About Page JavaScript
 * assets/js/about.js
 */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================
    // SCROLL REVEAL — vào khi scroll tới, ra khi scroll qua
    // ============================================
    const revealEls = document.querySelectorAll('.scroll-reveal');

    if (!revealEls.length) return;

    function checkReveal() {
        const windowH = window.innerHeight;

        revealEls.forEach(function (el) {
            const rect = el.getBoundingClientRect();
            const inView = rect.top < windowH * 0.88 && rect.bottom > windowH * 0.12;

            if (inView) {
                el.classList.add('is-visible');
                el.classList.remove('is-hidden');
            } else {
                el.classList.remove('is-visible');
                el.classList.add('is-hidden');
            }
        });
    }

    // Chạy lần đầu
    checkReveal();

    // Lắng nghe scroll
    window.addEventListener('scroll', checkReveal, { passive: true });

    // ============================================
    // TEAM MEMBER — touch device fallback
    // ============================================
    const teamMembers = document.querySelectorAll('.team_area .team_member');
    teamMembers.forEach(function (member) {
        member.addEventListener('touchstart', function () {
            teamMembers.forEach(function (m) { m.classList.remove('touch-hover'); });
            member.classList.toggle('touch-hover');
        }, { passive: true });
    });

});
