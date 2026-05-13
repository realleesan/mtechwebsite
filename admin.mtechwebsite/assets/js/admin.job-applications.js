/**
 * admin.job-applications.js
 * JS cho trang quản lý Đơn ứng tuyển
 */

document.addEventListener('DOMContentLoaded', function () {

    // ----------------------------------------------------------------
    // 1. Status option selector (trang edit)
    // ----------------------------------------------------------------
    const statusOptions = document.querySelectorAll('.job-status-option');

    if (statusOptions.length > 0) {
        statusOptions.forEach(function (option) {
            const radio = option.querySelector('input[type="radio"]');

            option.addEventListener('click', function () {
                // Xóa active khỏi tất cả
                statusOptions.forEach(o => o.classList.remove('active'));
                // Thêm active vào option được chọn
                option.classList.add('active');
                if (radio) radio.checked = true;
            });
        });
    }

    // ----------------------------------------------------------------
    // 2. Confirm trước khi submit form xóa / khôi phục
    //    (dùng chung data-confirm attribute với admin.js)
    // ----------------------------------------------------------------
    // Đã được xử lý bởi admin.js toàn cục — không cần duplicate

    // ----------------------------------------------------------------
    // 3. Highlight hàng khi hover trên trang index
    // ----------------------------------------------------------------
    const pendingRows = document.querySelectorAll('.job-row-pending');
    pendingRows.forEach(function (row) {
        row.addEventListener('mouseenter', function () {
            this.style.backgroundColor = '#fff8d6';
        });
        row.addEventListener('mouseleave', function () {
            this.style.backgroundColor = '#fffdf0';
        });
    });

    // ----------------------------------------------------------------
    // 4. Auto-resize textarea ghi chú
    // ----------------------------------------------------------------
    const adminNote = document.getElementById('admin_note');
    if (adminNote) {
        adminNote.addEventListener('input', function () {
            this.style.height = 'auto';
            this.style.height = Math.max(130, this.scrollHeight) + 'px';
        });
    }

    // ----------------------------------------------------------------
    // 5. Xác nhận trước khi xóa vĩnh viễn (trang trash)
    // ----------------------------------------------------------------
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            const msg = this.getAttribute('data-confirm');
            if (msg && !confirm(msg)) {
                e.preventDefault();
            }
        });
    });

});
