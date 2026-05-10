<div class="auth-card">

            <div class="auth-card-header">
                <img src="/assets/images/logo.png" alt="MTECH.JSC Logo" class="login-logo">
                <h5>Quên mật khẩu</h5>
                <small>Nhập email để nhận link đặt lại mật khẩu</small>
            </div>

            <div class="auth-card-body">

                <!-- Flash Messages -->
                <?php if ($success = ($_SESSION['success'] ?? null)): unset($_SESSION['success']); ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($success) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error = ($_SESSION['error'] ?? null)): unset($_SESSION['error']); ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="/forgot-password">
                    <div class="mb-4">
                        <label class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" name="email"
                                   placeholder="Nhập địa chỉ email" required autofocus>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-auth">
                        <i class="bi bi-send me-2"></i>Gửi link đặt lại mật khẩu
                    </button>
                </form>

                <div class="text-center mt-3">
                    <a href="/login" class="auth-link">
                        <i class="bi bi-arrow-left me-1"></i>Quay lại đăng nhập
                    </a>
                </div>

            </div>
        </div>
