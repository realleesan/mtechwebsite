<div class="login-card">

            <div class="login-header">
                <img src="/assets/images/logo.png" alt="MTECH.JSC Logo" class="login-logo">
                <h4 class="login-title">ADMIN PANEL</h4>
                <div class="brand-sub">MTECH.JSC</div>
            </div>

            <div class="login-body">

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

                <!-- Login Form -->
                <form method="POST" action="/login">

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email"
                                   placeholder="Nhập email" required autofocus
                                   value="@gmail.com">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Mật khẩu</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password"
                                   placeholder="Nhập mật khẩu" required>
                        </div>
                        <small class="text-muted"></small>
                    </div>

                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
                    </div>

                    <button type="submit" class="btn btn-auth">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Đăng nhập
                    </button>

                </form>

                <div class="text-center mt-3">
                    <a href="/forgot-password" class="auth-link">Quên mật khẩu?</a>
                </div>

            </div>
        </div>
