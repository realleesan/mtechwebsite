/**
 * projects.js — Quản lý bộ lọc lĩnh vực và tương tác trang Dự án
 * Chỉ thực hiện lọc khi người dùng nhấn nút "Lọc dự án"
 */

(function() {
    'use strict';

    let isRequestRunning = false;

    // Khởi tạo khi DOM sẵn sàng
    document.addEventListener('DOMContentLoaded', function() {
        initProjectsPage();
    });

    function initProjectsPage() {
        initChevronToggle();
        initCheckboxInteractions();
        initFilterFormSubmit();
        initActiveTagRemoval();
        initPaginationClicks();
        initHistoryPopstate();
    }

    /**
     * 1. Xử lý Chevron đóng/mở danh mục con
     */
    function initChevronToggle() {
        document.addEventListener('click', function(e) {
            const toggleBtn = e.target.closest('.btn-tree-toggle');
            if (!toggleBtn) return;

            e.preventDefault();
            e.stopPropagation();

            const item = toggleBtn.closest('.project-cat-item');
            if (!item) return;

            const childrenList = item.querySelector(':scope > .project-cat-children');
            if (!childrenList) return;

            const isExpanded = item.classList.contains('is-expanded');
            const icon = toggleBtn.querySelector('i');

            if (isExpanded) {
                // Đóng lại
                item.classList.remove('is-expanded');
                toggleBtn.setAttribute('aria-expanded', 'false');
                if (icon) {
                    icon.classList.remove('fa-chevron-down');
                    icon.classList.add('fa-chevron-right');
                }
                slideUp(childrenList, 200);
            } else {
                // Mở ra
                item.classList.add('is-expanded');
                toggleBtn.setAttribute('aria-expanded', 'true');
                if (icon) {
                    icon.classList.remove('fa-chevron-right');
                    icon.classList.add('fa-chevron-down');
                }
                slideDown(childrenList, 200);
            }
        });
    }

    /**
     * 2. Xử lý Checkbox cha - con (chọn cha tự động check/uncheck con)
     * (KHÔNG tự động lọc, chỉ cập nhật trạng thái ô checkbox)
     */
    function initCheckboxInteractions() {
        document.addEventListener('change', function(e) {
            const checkbox = e.target.closest('.project-filter-checkbox');
            if (!checkbox) return;

            const isChecked = checkbox.checked;
            const item = checkbox.closest('.project-cat-item');

            if (item) {
                if (isChecked) {
                    item.classList.add('is-checked');
                } else {
                    item.classList.remove('is-checked');
                }

                // Tự động check/uncheck các danh mục con trực thuộc
                const childCheckboxes = item.querySelectorAll('.project-cat-children .project-filter-checkbox');
                childCheckboxes.forEach(function(childCb) {
                    childCb.checked = isChecked;
                    const childItem = childCb.closest('.project-cat-item');
                    if (childItem) {
                        if (isChecked) childItem.classList.add('is-checked');
                        else childItem.classList.remove('is-checked');
                    }
                });

                // Mở rộng chevron nếu chọn cha
                if (isChecked && childCheckboxes.length > 0 && !item.classList.contains('is-expanded')) {
                    const toggleBtn = item.querySelector(':scope > .project-cat-row .btn-tree-toggle');
                    const childrenList = item.querySelector(':scope > .project-cat-children');
                    if (toggleBtn && childrenList) {
                        item.classList.add('is-expanded');
                        toggleBtn.setAttribute('aria-expanded', 'true');
                        const icon = toggleBtn.querySelector('i');
                        if (icon) {
                            icon.classList.remove('fa-chevron-right');
                            icon.classList.add('fa-chevron-down');
                        }
                        slideDown(childrenList, 200);
                    }
                }
            }
        });
    }

    /**
     * 3. Xử lý Submit Form khi người dùng bấm nút "Lọc dự án"
     */
    function initFilterFormSubmit() {
        const filterForm = document.getElementById('project-filter-form');
        if (!filterForm) return;

        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            applyFilter(1, true, true);
        });
    }

    /**
     * 4. Bỏ chọn một badge tag đang lọc hoặc Xóa tất cả bộ lọc
     */
    function initActiveTagRemoval() {
        document.addEventListener('click', function(e) {
            // Xử lý nút xóa 1 tag cụ thể (x)
            const removeBtn = e.target.closest('.btn-remove-tag');
            if (removeBtn) {
                e.preventDefault();
                const catId = removeBtn.getAttribute('data-id');
                if (!catId) return;

                // Tìm checkbox tương ứng và uncheck
                const targetCb = document.querySelector(`.project-filter-checkbox[value="${catId}"]`);
                if (targetCb) {
                    targetCb.checked = false;
                    const item = targetCb.closest('.project-cat-item');
                    if (item) {
                        item.classList.remove('is-checked');
                        const childCbs = item.querySelectorAll('.project-cat-children .project-filter-checkbox');
                        childCbs.forEach(function(c) {
                            c.checked = false;
                            const cItem = c.closest('.project-cat-item');
                            if (cItem) cItem.classList.remove('is-checked');
                        });
                    }
                }

                applyFilter(1, false, true);
                return;
            }

            // Xử lý nút "Xóa tất cả" / "Xóa bộ lọc"
            const clearAllBtn = e.target.closest('.btn-clear-all-tags') || e.target.closest('#btn-reset-project-filter') || e.target.closest('.btn_filter_reset_inline');
            if (clearAllBtn) {
                e.preventDefault();
                const checkboxes = document.querySelectorAll('.project-filter-checkbox');
                checkboxes.forEach(function(cb) {
                    cb.checked = false;
                    const item = cb.closest('.project-cat-item');
                    if (item) item.classList.remove('is-checked');
                });

                applyFilter(1, false, true);
                return;
            }
        });
    }

    /**
     * 5. Phân trang AJAX
     */
    function initPaginationClicks() {
        document.addEventListener('click', function(e) {
            const pageLink = e.target.closest('.projects-pagination-nav .page-link');
            if (!pageLink) return;

            e.preventDefault();
            const parentItem = pageLink.closest('.page-item');
            if (parentItem && (parentItem.classList.contains('disabled') || parentItem.classList.contains('active'))) {
                return;
            }

            const targetPage = parseInt(pageLink.getAttribute('data-page'), 10);
            if (!isNaN(targetPage) && targetPage > 0) {
                applyFilter(targetPage, true, true);
            }
        });
    }

    /**
     * 6. Nút Back/Forward của trình duyệt
     */
    function initHistoryPopstate() {
        window.addEventListener('popstate', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const catParam = urlParams.get('categories') || '';
            const pageParam = parseInt(urlParams.get('p') || '1', 10);
            const selectedIds = catParam ? catParam.split(',').map(function(id) { return id.trim(); }) : [];

            // Cập nhật lại checkbox
            const checkboxes = document.querySelectorAll('.project-filter-checkbox');
            checkboxes.forEach(function(cb) {
                const isSelected = selectedIds.includes(cb.value);
                cb.checked = isSelected;
                const item = cb.closest('.project-cat-item');
                if (item) {
                    if (isSelected) item.classList.add('is-checked');
                    else item.classList.remove('is-checked');
                }
            });

            applyFilter(pageParam, false, false);
        });
    }

    /**
     * Hàm lõi: Thực hiện lọc dự án qua AJAX
     */
    function applyFilter(pageNum = 1, shouldScroll = false, shouldPushState = true) {
        if (isRequestRunning) return;
        isRequestRunning = true;

        // 1. Lấy danh sách ID các checkbox được chọn
        const checkedCheckboxes = document.querySelectorAll('.project-filter-checkbox:checked');
        const selectedIds = [];
        checkedCheckboxes.forEach(function(cb) {
            const val = cb.value.trim();
            if (val && !selectedIds.includes(val)) {
                selectedIds.push(val);
            }
        });

        // 2. Hiệu ứng làm mờ nhẹ danh sách (không overlay)
        const listContainer = document.getElementById('projects-list-container');
        if (listContainer) {
            listContainer.style.opacity = '0.6';
            listContainer.style.transition = 'opacity 0.2s ease';
        }

        // 3. Xây dựng URL
        const currentPath = window.location.pathname;
        const params = new URLSearchParams();
        params.set('ajax', '1');
        params.set('p', pageNum);
        if (selectedIds.length > 0) {
            params.set('categories', selectedIds.join(','));
        }

        const requestUrl = currentPath + '?' + params.toString();

        // 4. Gửi AJAX request
        fetch(requestUrl, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(function(response) {
            if (!response.ok) {
                throw new Error('HTTP status ' + response.status);
            }
            return response.json();
        })
        .then(function(data) {
            if (data.status === 'success') {
                // Cập nhật danh sách dự án
                if (listContainer) {
                    listContainer.innerHTML = data.html;
                }

                // Cập nhật phân trang
                const paginationContainer = document.getElementById('projects-pagination-container');
                if (paginationContainer) {
                    paginationContainer.innerHTML = data.pagination_html;
                }

                // Cập nhật số lượng dự án
                const countTextStrong = document.querySelector('.projects_count_text strong');
                if (countTextStrong && data.total !== undefined) {
                    countTextStrong.textContent = data.total;
                }

                // Cập nhật badges
                const badgesWrapper = document.getElementById('active-filter-badges-wrapper');
                if (badgesWrapper) {
                    badgesWrapper.innerHTML = data.badges_html;
                }

                // Cập nhật URL trình duyệt
                if (shouldPushState) {
                    const cleanParams = new URLSearchParams();
                    if (pageNum > 1) {
                        cleanParams.set('p', pageNum);
                    }
                    if (selectedIds.length > 0) {
                        cleanParams.set('categories', selectedIds.join(','));
                    }
                    const newUrl = currentPath + (cleanParams.toString() ? '?' + cleanParams.toString() : '');
                    window.history.pushState({ path: newUrl }, '', newUrl);
                }

                // Cuộn trang mượt nếu cần
                if (shouldScroll) {
                    const scrollTarget = document.querySelector('.projects_filter_status_bar') || document.querySelector('.projects_area');
                    if (scrollTarget) {
                        const topOffset = scrollTarget.getBoundingClientRect().top + window.pageYOffset - 80;
                        window.scrollTo({ top: topOffset, behavior: 'smooth' });
                    }
                }
            }
        })
        .catch(function(error) {
            console.error('[Projects Filter] Lỗi tải dữ liệu:', error);
            // Fallback: nếu AJAX gặp lỗi trên hosting, submit form theo chuẩn HTML
            const form = document.getElementById('project-filter-form');
            if (form && shouldPushState) {
                form.submit();
            }
        })
        .finally(function() {
            if (listContainer) {
                listContainer.style.opacity = '1';
            }
            isRequestRunning = false;
        });
    }

    /**
     * Vanilla Slide Up Animation
     */
    function slideUp(target, duration = 200) {
        if (!target) return;
        target.style.transitionProperty = 'height, margin, padding';
        target.style.transitionDuration = duration + 'ms';
        target.style.boxSizing = 'border-box';
        target.style.height = target.offsetHeight + 'px';
        target.offsetHeight;
        target.style.overflow = 'hidden';
        target.style.height = 0;
        target.style.paddingTop = 0;
        target.style.paddingBottom = 0;
        target.style.marginTop = 0;
        target.style.marginBottom = 0;
        window.setTimeout(() => {
            target.style.display = 'none';
            target.style.removeProperty('height');
            target.style.removeProperty('padding-top');
            target.style.removeProperty('padding-bottom');
            target.style.removeProperty('margin-top');
            target.style.removeProperty('margin-bottom');
            target.style.removeProperty('overflow');
            target.style.removeProperty('transition-duration');
            target.style.removeProperty('transition-property');
        }, duration);
    }

    /**
     * Vanilla Slide Down Animation
     */
    function slideDown(target, duration = 200) {
        if (!target) return;
        target.style.removeProperty('display');
        let display = window.getComputedStyle(target).display;
        if (display === 'none') display = 'block';
        target.style.display = display;
        let height = target.offsetHeight;
        target.style.overflow = 'hidden';
        target.style.height = 0;
        target.style.paddingTop = 0;
        target.style.paddingBottom = 0;
        target.style.marginTop = 0;
        target.style.marginBottom = 0;
        target.offsetHeight;
        target.style.boxSizing = 'border-box';
        target.style.transitionProperty = 'height, margin, padding';
        target.style.transitionDuration = duration + 'ms';
        target.style.height = height + 'px';
        target.style.removeProperty('padding-top');
        target.style.removeProperty('padding-bottom');
        target.style.removeProperty('margin-top');
        target.style.removeProperty('margin-bottom');
        window.setTimeout(() => {
            target.style.removeProperty('height');
            target.style.removeProperty('overflow');
            target.style.removeProperty('transition-duration');
            target.style.removeProperty('transition-property');
        }, duration);
    }

})();
