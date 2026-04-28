/**
 * Errors Page JavaScript - 404 Not Found
 * Xử lý các tương tác trên trang lỗi 404
 */

document.addEventListener('DOMContentLoaded', function () {

    // ==========================================
    // Hiệu ứng đếm ngược tự động về trang chủ (tuỳ chọn)
    // Hiện tại để comment, bật lên nếu cần
    // ==========================================
    // var countdown = 10;
    // var countdownEl = document.getElementById('countdown');
    // if (countdownEl) {
    //     var timer = setInterval(function () {
    //         countdown--;
    //         countdownEl.textContent = countdown;
    //         if (countdown <= 0) {
    //             clearInterval(timer);
    //             window.location.href = 'index.php';
    //         }
    //     }, 1000);
    // }

    // ==========================================
    // Hiệu ứng fade-in cho error content
    // ==========================================
    var errorContent = document.querySelector('.error_content');
    if (errorContent) {
        errorContent.style.opacity = '0';
        errorContent.style.transform = 'translateY(30px)';
        errorContent.style.transition = 'opacity 0.7s ease, transform 0.7s ease';

        // Trigger animation sau khi DOM load
        setTimeout(function () {
            errorContent.style.opacity = '1';
            errorContent.style.transform = 'translateY(0)';
        }, 100);
    }

    // ==========================================
    // Nút "Go to home page" - điều hướng về trang chủ
    // ==========================================
    var homeBtn = document.querySelector('.btn_error');
    if (homeBtn) {
        homeBtn.addEventListener('click', function (e) {
            // Đảm bảo href đúng
            var href = this.getAttribute('href');
            if (href) {
                window.location.href = href;
            }
        });
    }

});
