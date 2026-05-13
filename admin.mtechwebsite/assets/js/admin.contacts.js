/**
 * admin.contacts.js
 * JS cho trang quản lý Liên hệ
 */

document.addEventListener('DOMContentLoaded', function () {

    // ----------------------------------------------------------------
    // 1. Status option selector (trang edit)
    // ----------------------------------------------------------------
    const statusOptions = document.querySelectorAll('.contact-status-option');
    const adminReplyTextarea = document.getElementById('admin_reply');

    if (statusOptions.length > 0) {
        statusOptions.forEach(function (option) {
            const radio = option.querySelector('input[type="radio"]');

            // Sync active class khi click
            option.addEventListener('click', function () {
                statusOptions.forEach(o => o.classList.remove('active'));
                option.classList.add('active');
                if (radio) radio.checked = true;
            });
        });
    }

    // ----------------------------------------------------------------
    // 2. Tự động chuyển status → "Đã phản hồi" (value=2)
    //    khi textarea admin_reply có nội dung và submit form
    // ----------------------------------------------------------------
    const contactEditForm = document.getElementById('contactEditForm');

    if (contactEditForm && adminReplyTextarea) {
        contactEditForm.addEventListener('submit', function () {
            const replyContent = adminReplyTextarea.value.trim();

            if (replyContent.length > 0) {
                // Tìm radio value=2 và check nó
                const repliedRadio = contactEditForm.querySelector('input[name="status"][value="2"]');
                if (repliedRadio) {
                    repliedRadio.checked = true;

                    // Cập nhật active class
                    statusOptions.forEach(function (opt) {
                        const r = opt.querySelector('input[type="radio"]');
                        opt.classList.toggle('active', r && r.checked);
                    });
                }
            }
        });

        // Gợi ý trực quan khi người dùng bắt đầu nhập phản hồi
        adminReplyTextarea.addEventListener('input', function () {
            const hasContent = this.value.trim().length > 0;
            const opt2 = document.getElementById('opt-2');
            if (opt2) {
                opt2.style.outline = hasContent ? '2px dashed #28a745' : '';
            }
        });
    }

});
