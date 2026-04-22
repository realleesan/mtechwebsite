/**
 * Header JavaScript - MTech Website
 */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {

        // ── Language Switcher ──────────────────────────────────────
        const langSelector = document.querySelector('.lang_selector');
        const langDropdown = document.querySelector('.lang_dropdown');
        const langCurrent  = document.querySelector('.lang_current');

        if (langSelector && langDropdown) {
            langSelector.addEventListener('click', function (e) {
                e.stopPropagation();
                langDropdown.classList.toggle('show');
            });

            // Chọn ngôn ngữ
            langDropdown.querySelectorAll('a').forEach(function (link) {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    const flag = this.querySelector('.flag_icon').cloneNode(true);
                    const text = this.textContent.trim();

                    // Cập nhật current
                    langCurrent.innerHTML = '';
                    langCurrent.appendChild(flag);
                    langCurrent.insertAdjacentHTML('beforeend',
                        ' ' + text + ' <span class="lang_caret">&#9660;</span>');

                    // Active state
                    langDropdown.querySelectorAll('li').forEach(li => li.classList.remove('active'));
                    this.closest('li').classList.add('active');

                    langDropdown.classList.remove('show');
                });
            });

            // Đóng khi click ngoài
            document.addEventListener('click', function () {
                langDropdown.classList.remove('show');
            });
        }

        // ── Sticky Header ──────────────────────────────────────────
        const header = document.querySelector('.menu_absolute');

        if (header) {
            window.addEventListener('scroll', function () {
                if (window.pageYOffset > 80) {
                    header.classList.add('sticky');
                } else {
                    header.classList.remove('sticky');
                }
            });
        }

        // ── Desktop Dropdown với delay ─────────────────────────────
        // Mỗi submenu item có timer riêng để tránh đóng ngay
        const HIDE_DELAY = 45; // ms — thời gian trễ trước khi đóng

        const submenus = document.querySelectorAll('ul.menu > li.nav-item.submenu');

        submenus.forEach(function (item) {
            let hideTimer = null;
            const dropdown = item.querySelector('ul.dropdown-menu');
            const topLink  = item.querySelector(':scope > a.nav-link');
            if (!dropdown) return;

            // Chặn navigate khi click vào nav link có dropdown (mọi thiết bị)
            if (topLink) {
                topLink.addEventListener('click', function (e) {
                    e.preventDefault();
                });
            }

            function showDropdown() {
                clearTimeout(hideTimer);
                if (window.innerWidth >= 992) {
                    dropdown.style.display = 'block';
                }
            }

            function hideDropdown() {
                if (window.innerWidth >= 992) {
                    hideTimer = setTimeout(function () {
                        dropdown.style.display = 'none';
                    }, HIDE_DELAY);
                }
            }

            item.addEventListener('mouseenter', showDropdown);
            item.addEventListener('mouseleave', hideDropdown);
            dropdown.addEventListener('mouseenter', showDropdown);
            dropdown.addEventListener('mouseleave', hideDropdown);

            const dropdownLinks = dropdown.querySelectorAll('a');
            dropdownLinks.forEach(function (link) {
                link.addEventListener('click', function () {
                    clearTimeout(hideTimer);
                });
            });
        });

        // ── Hamburger Menu (Mobile) ────────────────────────────────
        const toggler  = document.querySelector('.navbar-toggler');
        const collapse = document.querySelector('.navbar-collapse');
        const closeBtn = document.querySelector('.nav-close-btn');

        function openMenu() {
            toggler.classList.remove('collapsed');
            collapse.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeMenu() {
            toggler.classList.add('collapsed');
            collapse.classList.remove('show');
            document.body.style.overflow = '';
        }

        if (toggler && collapse) {
            toggler.addEventListener('click', function (e) {
                e.stopPropagation();
                collapse.classList.contains('show') ? closeMenu() : openMenu();
            });

            // Nút Back/Close bên trong sidebar
            if (closeBtn) {
                closeBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    closeMenu();
                });
            }

            // Click overlay (::after pseudo) để đóng
            document.addEventListener('click', function (e) {
                if (
                    collapse.classList.contains('show') &&
                    !collapse.contains(e.target) &&
                    !toggler.contains(e.target)
                ) {
                    closeMenu();
                }
            });
        }

        // ── Mobile Dropdown Toggle ─────────────────────────────────
        const mobileSubmenus = document.querySelectorAll('ul.menu > li.nav-item.submenu > a.nav-link');

        mobileSubmenus.forEach(function (link) {
            link.addEventListener('click', function (e) {
                if (window.innerWidth < 992) {
                    e.preventDefault();
                    e.stopPropagation();

                    const parent = this.closest('li.nav-item.submenu');
                    const isOpen = parent.classList.contains('show');

                    // Đóng tất cả trước
                    document.querySelectorAll('ul.menu > li.nav-item.submenu').forEach(function (el) {
                        el.classList.remove('show');
                    });

                    // Nếu chưa mở thì mở, nếu đang mở thì đóng (toggle)
                    if (!isOpen) {
                        parent.classList.add('show');
                    }
                }
            });
        });

        // ── Đóng mobile menu khi resize lên desktop ───────────────
        window.addEventListener('resize', function () {
            if (window.innerWidth >= 992) {
                if (collapse) closeMenu();

                // Reset tất cả dropdown về trạng thái ban đầu
                submenus.forEach(function (item) {
                    const dd = item.querySelector('ul.dropdown-menu');
                    if (dd) dd.style.display = 'none';
                    item.classList.remove('show');
                });
            }
        });

        // ── ESC để đóng mobile menu ────────────────────────────────
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                if (collapse && collapse.classList.contains('show')) {
                    closeMenu();
                }
            }
        });

    });

})();
