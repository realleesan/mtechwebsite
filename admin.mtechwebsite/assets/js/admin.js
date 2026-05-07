/**
 * Admin Panel - Global JavaScript
 */

document.addEventListener('DOMContentLoaded', function () {

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
    // Confirm trước khi xóa
    // ----------------------------------------
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            const message = el.getAttribute('data-confirm') || 'Bạn có chắc muốn xóa?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });

    // ----------------------------------------
    // Submit form xóa khi click nút delete
    // ----------------------------------------
    document.querySelectorAll('.btn-delete').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const message = btn.getAttribute('data-confirm') || 'Bạn có chắc muốn xóa mục này?';
            if (confirm(message)) {
                const form = btn.closest('form') || document.getElementById(btn.getAttribute('data-form'));
                if (form) form.submit();
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
