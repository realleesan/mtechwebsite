/**
 * projects.js — Quản lý bộ lọc lĩnh vực và điều hướng phân cấp (Drill-Down) trang Dự án
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
        initDrillDownClicks();
        initPaginationClicks();
        initHistoryPopstate();
    }

    /**
     * 1. Xử lý Chevron đóng/mở danh mục con trong Sidebar
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
     * 2. Xử lý Checkbox cha - con trong Sidebar (chọn cha tự động check/uncheck con)
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
     * 3. Xử lý Submit Form khi người dùng bấm nút "Lọc dự án" ở Sidebar
     */
    function initFilterFormSubmit() {
        const filterForm = document.getElementById('project-filter-form');
        if (!filterForm) return;

        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const checkedCheckboxes = document.querySelectorAll('.project-filter-checkbox:checked');
            const selectedIds = [];
            checkedCheckboxes.forEach(function(cb) {
                const val = cb.value.trim();
                if (val && !selectedIds.includes(val)) {
                    selectedIds.push(val);
                }
            });

            const params = new URLSearchParams();
            if (selectedIds.length > 0) {
                params.set('categories', selectedIds.join(','));
            }
            const queryUrl = params.toString() ? '/du-an?' + params.toString() : '/du-an';

            fetchAndRender(queryUrl, 1, true, true);
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

                // Thu thập lại các IDs còn lại
                const checked = document.querySelectorAll('.project-filter-checkbox:checked');
                const remainingIds = [];
                checked.forEach(function(c) {
                    if (c.value) remainingIds.push(c.value);
                });

                const queryUrl = remainingIds.length > 0 ? '/du-an?categories=' + remainingIds.join(',') : '/du-an';
                fetchAndRender(queryUrl, 1, false, true);
                return;
            }

            // Xử lý nút "Xóa tất cả" / "Xóa bộ lọc" / "Về tất cả lĩnh vực"
            const clearAllBtn = e.target.closest('.btn-clear-all-tags') || e.target.closest('#btn-reset-project-filter') || e.target.closest('.btn_filter_reset_inline');
            if (clearAllBtn) {
                e.preventDefault();
                const checkboxes = document.querySelectorAll('.project-filter-checkbox');
                checkboxes.forEach(function(cb) {
                    cb.checked = false;
                    const item = cb.closest('.project-cat-item');
                    if (item) item.classList.remove('is-checked');
                });

                fetchAndRender('/du-an', 1, false, true);
                return;
            }
        });
    }

    /**
     * 5. Xử lý click vào Card Lĩnh vực (Drill-Down) hoặc Breadcrumbs
     */
    function initDrillDownClicks() {
        document.addEventListener('click', function(e) {
            // Click vào thẻ lĩnh vực hoặc link breadcrumb
            const drillDownLink = e.target.closest('.btn-drilldown-cat') || e.target.closest('.btn-drilldown-cat-title') || e.target.closest('.drilldown-bc-link');
            if (!drillDownLink) return;

            e.preventDefault();
            const href = drillDownLink.getAttribute('href');
            if (href) {
                fetchAndRender(href, 1, true, true);
            }
        });
    }

    /**
     * 6. Phân trang AJAX
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
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('p', targetPage);
                fetchAndRender(currentUrl.pathname + currentUrl.search, targetPage, true, true);
            }
        });
    }

    /**
     * 7. Nút Back/Forward của trình duyệt (History Popstate)
     */
    function initHistoryPopstate() {
        window.addEventListener('popstate', function() {
            const currentPathWithSearch = window.location.pathname + window.location.search;
            fetchAndRender(currentPathWithSearch, 1, false, false);
        });
    }

    /**
     * HÀM LÕI: Gửi AJAX request và cập nhật toàn bộ giao diện
     */
    function fetchAndRender(targetUrlString, pageNum = 1, shouldScroll = false, shouldPushState = true) {
        if (isRequestRunning) return;
        isRequestRunning = true;

        // Parse target URL
        const parsedUrl = new URL(targetUrlString, window.location.origin);
        parsedUrl.searchParams.set('ajax', '1');

        const listContainer = document.getElementById('projects-list-container');
        if (listContainer) {
            listContainer.style.opacity = '0.5';
            listContainer.style.transition = 'opacity 0.2s ease';
        }

        fetch(parsedUrl.toString(), {
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
                // 1. Cập nhật Grid chính
                if (listContainer) {
                    listContainer.innerHTML = data.html;
                }

                // 2. Cập nhật Breadcrumbs
                const breadcrumbsWrapper = document.getElementById('project-breadcrumbs-wrapper');
                if (breadcrumbsWrapper) {
                    breadcrumbsWrapper.innerHTML = data.breadcrumbs_html || '';
                }

                // 3. Cập nhật Phân trang
                const paginationContainer = document.getElementById('projects-pagination-container');
                if (paginationContainer) {
                    paginationContainer.innerHTML = data.pagination_html || '';
                }

                // 4. Cập nhật Badges
                const badgesWrapper = document.getElementById('active-filter-badges-wrapper');
                if (badgesWrapper) {
                    badgesWrapper.innerHTML = data.badges_html || '';
                }

                // 5. Cập nhật Count Text
                const countTextSpan = document.querySelector('.projects_count_text');
                if (countTextSpan && data.total !== undefined) {
                    if (data.mode === 'categories') {
                        countTextSpan.innerHTML = `Hiển thị <strong>${data.total}</strong> dự án theo lĩnh vực`;
                    } else {
                        countTextSpan.innerHTML = `Hiển thị <strong>${data.total}</strong> dự án`;
                    }
                }

                // 6. Đồng bộ lại trạng thái Checkboxes trong Sidebar
                const selectedIds = data.selected_ids || [];
                const checkboxes = document.querySelectorAll('.project-filter-checkbox');
                checkboxes.forEach(function(cb) {
                    const isSelected = selectedIds.includes(parseInt(cb.value, 10)) || selectedIds.includes(cb.value);
                    cb.checked = isSelected;
                    const item = cb.closest('.project-cat-item');
                    if (item) {
                        if (isSelected) {
                            item.classList.add('is-checked');
                            // Mở rộng parent của nó
                            let parentItem = item.parentElement.closest('.project-cat-item');
                            while (parentItem) {
                                parentItem.classList.add('is-expanded');
                                const tBtn = parentItem.querySelector(':scope > .project-cat-row .btn-tree-toggle');
                                if (tBtn) {
                                    tBtn.setAttribute('aria-expanded', 'true');
                                    const icon = tBtn.querySelector('i');
                                    if (icon) {
                                        icon.classList.remove('fa-chevron-right');
                                        icon.classList.add('fa-chevron-down');
                                    }
                                }
                                const cList = parentItem.querySelector(':scope > .project-cat-children');
                                if (cList) cList.style.display = 'block';
                                parentItem = parentItem.parentElement.closest('.project-cat-item');
                            }
                        } else {
                            item.classList.remove('is-checked');
                        }
                    }
                });

                // 7. Cập nhật URL trình duyệt
                if (shouldPushState) {
                    const cleanUrl = new URL(targetUrlString, window.location.origin);
                    cleanUrl.searchParams.delete('ajax');
                    const newPath = cleanUrl.pathname + (cleanUrl.search ? cleanUrl.search : '');
                    window.history.pushState({ path: newPath }, '', newPath);
                }

                // 8. Cuộn trang mượt nếu cần
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
            console.error('[Projects] Error fetching data:', error);
            // Fallback: nếu AJAX gặp trục trặc, chuyển trang trình duyệt bình thường
            if (shouldPushState) {
                window.location.href = targetUrlString;
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
