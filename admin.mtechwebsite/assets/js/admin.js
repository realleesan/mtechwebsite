/**
 * Admin Panel - Global JavaScript
 */

document.addEventListener('DOMContentLoaded', function () {

    // ----------------------------------------
    // Dark Mode Toggle
    // ----------------------------------------
    const darkModeToggle = document.getElementById('darkModeToggle');
    const darkModeIcon = document.getElementById('darkModeIcon');
    const darkModeText = darkModeToggle?.querySelector('span');
    
    // Lấy trạng thái dark mode từ localStorage
    const currentTheme = localStorage.getItem('adminTheme') || 'light';
    
    // Áp dụng theme khi tải trang
    if (currentTheme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
        if (darkModeIcon) {
            darkModeIcon.className = 'bi bi-sun-fill';
        }
        if (darkModeText) {
            darkModeText.textContent = 'Light Mode';
        }
    }
    
    // Xử lý sự kiện click toggle
    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', function () {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            // Áp dụng theme mới
            document.documentElement.setAttribute('data-theme', newTheme);
            
            // Lưu vào localStorage
            localStorage.setItem('adminTheme', newTheme);
            
            // Cập nhật icon và text
            if (darkModeIcon) {
                darkModeIcon.className = newTheme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
            }
            if (darkModeText) {
                darkModeText.textContent = newTheme === 'dark' ? 'Light Mode' : 'Dark Mode';
            }
            
            // Cập nhật title
            darkModeToggle.setAttribute('title', newTheme === 'dark' ? 'Chuyển sang chế độ sáng' : 'Chuyển sang chế độ tối');
        });
    }

    // ----------------------------------------
    // Sidebar toggle (mobile)
    // ----------------------------------------
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('adminSidebar');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('show');
        });

        // Đóng sidebar khi click ra ngoài (mobile)
        document.addEventListener('click', function (e) {
            if (window.innerWidth < 992) {
                if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });
    }

    // ----------------------------------------
    // Auto-dismiss flash messages sau 4 giây
    // ----------------------------------------
    const alerts = document.querySelectorAll('.alert.alert-dismissible');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            bsAlert.close();
        }, 4000);
    });

    // ----------------------------------------
    // Confirm trước khi xóa — xử lý tất cả [data-confirm]
    // ----------------------------------------
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            const message = el.getAttribute('data-confirm') || 'Bạn có chắc muốn thực hiện?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });

    
    // ----------------------------------------
    // Preview ảnh khi chọn file
    // ----------------------------------------
    document.querySelectorAll('input[type="file"][data-preview]').forEach(function (input) {
        input.addEventListener('change', function () {
            const previewId = input.getAttribute('data-preview');
            const preview = document.getElementById(previewId);
            if (!preview) return;

            const file = input.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    });

    // ----------------------------------------
    // Tooltip Bootstrap
    // ----------------------------------------
    const tooltipEls = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipEls.forEach(function (el) {
        new bootstrap.Tooltip(el);
    });

});
