/**
 * Header JavaScript - MTech Website
 */

/**
 * Chuyển chuỗi tiếng Việt có dấu thành slug không dấu
 * Ví dụ: "Tuyển Dụng" → "tuyen-dung"
 */
function slugifyVi(str) {
    var from = 'àáảãạăắằẳẵặâấầẩẫậèéẻẽẹêếềểễệìíỉĩịòóỏõọôốồổỗộơớờởỡợùúủũụưứừửữựỳýỷỹỵđ'
             + 'ÀÁẢÃẠĂẮẰẲẴẶÂẤẦẨẪẬÈÉẺẼẸÊẾỀỂỄỆÌÍỈĨỊÒÓỎÕỌÔỐỒỔỖỘƠỚỜỞỠỢÙÚỦŨỤƯỨỪỬỮỰỲÝỶỸỴĐ';
    var to   = 'aaaaaaaaaaaaaaaaaeeeeeeeeeeeiiiiiooooooooooooooooouuuuuuuuuuuyyyyyd'
             + 'AAAAAAAAAAAAAAAAAEEEEEEEEEEEIIIIIOOOOOOOOOOOOOOOOOUUUUUUUUUUUYYYYYD';
    // Dùng normalize + replace từng ký tự
    var result = '';
    for (var i = 0; i < str.length; i++) {
        var idx = from.indexOf(str[i]);
        result += idx !== -1 ? to[idx] : str[i];
    }
    return result
        .toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .replace(/^-|-$/g, '');
}

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



        // ── Desktop Dropdown với delay ─────────────────────────────
        // Mỗi submenu item có timer riêng để tránh đóng ngay
        const HIDE_DELAY = 180; // ms — thời gian trễ trước khi đóng (180ms cho chuột di chuyển)

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
                    item.classList.add('show');
                }
            }

            function hideDropdown() {
                if (window.innerWidth >= 992) {
                    hideTimer = setTimeout(function () {
                        item.classList.remove('show');
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

        // ── Nested Dropdown (multi-level) ───────────────────────────
        const nestedSubmenus = document.querySelectorAll('.dropdown-submenu');

        nestedSubmenus.forEach(function (item) {
            const nestedDropdown = item.querySelector('.dropdown-menu-nested');
            if (!nestedDropdown) return;

            // Nested dropdowns use CSS hover, but we need to handle mobile
            const link = item.querySelector('a');
            if (link) {
                link.addEventListener('click', function (e) {
                    if (window.innerWidth < 992) {
                        e.preventDefault();
                        e.stopPropagation();

                        const isOpen = nestedDropdown.style.display === 'block';

                        // Đóng tất cả nested dropdowns cùng cấp
                        const parentUl = item.closest('ul');
                        if (parentUl) {
                            parentUl.querySelectorAll('.dropdown-menu-nested').forEach(function (dd) {
                                dd.style.display = 'none';
                            });
                        }

                        // Toggle current
                        nestedDropdown.style.display = isOpen ? 'none' : 'block';
                    }
                });
            }
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

        // ── Nested Dropdown Items Click (Mobile support for n-levels) ──────
        // Support cấp 2, 3, 4, n... - recursive click handlers
        const allNestedSubmenus = document.querySelectorAll('ul.menu > li.nav-item.submenu:not(.services-dropdown) li.nav-item.submenu > a.nav-link');

        allNestedSubmenus.forEach(function (link) {
            link.addEventListener('click', function (e) {
                if (window.innerWidth < 992) {
                    e.preventDefault();

                    const parent = this.closest('li.nav-item.submenu');
                    const childMenu = parent.querySelector(':scope > ul.dropdown-menu');
                    const isOpen = parent.classList.contains('show');

                    // Đóng tất cả sibling nested menus
                    const parentUl = parent.closest('ul.dropdown-menu');
                    if (parentUl) {
                        parentUl.querySelectorAll(':scope > li.nav-item.submenu').forEach(function (el) {
                            el.classList.remove('show');
                        });
                    }

                    // Toggle current nested menu
                    if (childMenu && !isOpen) {
                        parent.classList.add('show');
                    } else if (isOpen) {
                        parent.classList.remove('show');
                    }
                }
            });
        });

        // ── Services Accordion (Mobile click only) ────────────────────────
        // Sử dụng cấu trúc .nav-item.submenu (đồng bộ với Blog)
        const servicesAccordionLinks = document.querySelectorAll('.services-dropdown .nav-item.submenu > a.nav-link');
         
        servicesAccordionLinks.forEach(function (link) {
            link.addEventListener('click', function (e) {
                const parentItem = this.closest('.nav-item.submenu');
                const submenu = parentItem.querySelector(':scope > ul.dropdown-menu');
                
                if (window.innerWidth < 992 && submenu) {
                    e.preventDefault();
                    parentItem.classList.toggle('show');
                }
            });
        });

        // ── Đóng mobile menu khi resize lên desktop ───────────────
        let resizeTimeout;
        window.addEventListener('resize', function () {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(function () {
                if (window.innerWidth >= 992) {
                    if (collapse) closeMenu();

                    // Reset tất cả dropdown về trạng thái ban đầu
                    submenus.forEach(function (item) {
                        const dd = item.querySelector('ul.dropdown-menu');
                        if (dd) dd.style.display = 'none';
                        item.classList.remove('show');
                    });

                    // Re-bind hover listeners for desktop after resize
                    rebindDesktopDropdowns();
                }
            }, 250);
        });

        /**
         * Rebind desktop dropdown hover listeners
         * Called after resize to desktop breakpoint
         */
        function rebindDesktopDropdowns() {
            const submenus = document.querySelectorAll('ul.menu > li.nav-item.submenu');
            const HIDE_DELAY = 180;

            submenus.forEach(function (item) {
                // Clone and replace to remove all old listeners
                const newItem = item.cloneNode(true);
                item.parentNode.replaceChild(newItem, item);

                let hideTimer = null;
                const dropdown = newItem.querySelector('ul.dropdown-menu');
                const topLink = newItem.querySelector(':scope > a.nav-link');
                if (!dropdown) return;

                if (topLink) {
                    topLink.addEventListener('click', function (e) {
                        e.preventDefault();
                    });
                }

                function showDropdown() {
                    clearTimeout(hideTimer);
                    if (window.innerWidth >= 992) {
                        newItem.classList.add('show');
                    }
                }

                function hideDropdown() {
                    if (window.innerWidth >= 992) {
                        hideTimer = setTimeout(function () {
                            newItem.classList.remove('show');
                        }, HIDE_DELAY);
                    }
                }

                newItem.addEventListener('mouseenter', showDropdown);
                newItem.addEventListener('mouseleave', hideDropdown);
                dropdown.addEventListener('mouseenter', showDropdown);
                dropdown.addEventListener('mouseleave', hideDropdown);

                const dropdownLinks = dropdown.querySelectorAll('a');
                dropdownLinks.forEach(function (link) {
                    link.addEventListener('click', function () {
                        clearTimeout(hideTimer);
                    });
                });
            });
        }

        // ── ESC để đóng mobile menu ────────────────────────────────
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                if (collapse && collapse.classList.contains('show')) {
                    closeMenu();
                }
                // Đóng search overlay nếu đang mở
                if (searchOverlay && searchOverlay.classList.contains('active')) {
                    closeSearch();
                }
            }
        });

        // ── Search Overlay ─────────────────────────────────────────
        const searchOverlay = document.getElementById('searchOverlay');
        const searchToggle  = document.querySelector('.search_toggle');
        const searchClose   = document.getElementById('searchClose');
        const searchInput   = document.getElementById('searchInput');

        function openSearch() {
            if (!searchOverlay) return;
            if (searchInput) searchInput.value = '';
            searchOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
            setTimeout(function () { if (searchInput) searchInput.focus(); }, 100);
        }

        function closeSearch() {
            if (!searchOverlay) return;
            searchOverlay.classList.remove('active');
            document.body.style.overflow = '';
            if (searchInput) searchInput.value = '';
        }

        // Validate: không cho submit khi input rỗng
        // Redirect đến /ket-qua-tim-kiem-{keyword} (không dấu)
        const searchOverlayForm = searchOverlay ? searchOverlay.querySelector('form') : null;
        if (searchOverlayForm) {
            searchOverlayForm.addEventListener('submit', function (e) {
                e.preventDefault();
                var keyword = searchInput ? searchInput.value.trim() : '';
                if (!keyword) {
                    if (searchInput) searchInput.focus();
                    return;
                }
                // Bỏ dấu tiếng Việt và chuyển thành slug
                var slug = slugifyVi(keyword);
                window.location.href = '/ket-qua-tim-kiem-' + slug;
            });
        }

        if (searchToggle) {
            searchToggle.addEventListener('click', function (e) {
                e.stopPropagation();
                openSearch();
            });
        }

        if (searchClose) {
            searchClose.addEventListener('click', closeSearch);
        }

        // Click vào backdrop để đóng
        if (searchOverlay) {
            searchOverlay.addEventListener('click', function (e) {
                if (e.target === searchOverlay) closeSearch();
            });
        }

    });

})();
