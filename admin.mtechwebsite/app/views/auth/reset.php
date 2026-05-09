<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Đặt lại mật khẩu - Admin MTECH.JSC') ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/assets/css/auth.css">
</head>
<body class="auth-page">

    <!-- Ảnh nền + lớp phủ đen 80% -->
    <div class="auth-bg"></div>

    <!-- Overlay chứa card -->
    <div class="auth-overlay">
        <div class="auth-card">

            <div class="auth-card-header">
                <i class="bi bi-shield-lock"></i>
                <h5>Đặt lại mật khẩu</h5>
            </div>

            <div class="auth-card-body">

                <!-- Flash Messages -->
                <?php if ($error = ($_SESSION['error'] ?? null)): unset($_SESSION['error']); ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="/reset-password">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">

                    <div class="mb-3">
                        <label class="form-label">Mật khẩu mới</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" name="password"
                                   placeholder="Tối thiểu 6 ký tự" required autofocus minlength="6">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Xác nhận mật khẩu</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" class="form-control" name="password_confirm"
                                   placeholder="Nhập lại mật khẩu" required minlength="6">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-auth">
                        <i class="bi bi-check-lg me-2"></i>Đặt lại mật khẩu
                    </button>
                </form>

                <div class="text-center mt-3">
                    <a href="/login" class="auth-link">
                        <i class="bi bi-arrow-left me-1"></i>Quay lại đăng nhập
                    </a>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
