<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Đăng nhập - Admin MTech') ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1A3FBF 0%, #2d5dd8 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }
        .login-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            overflow: hidden;
            max-width: 420px;
            width: 100%;
        }
        .login-header {
            background: #1A3FBF;
            color: #fff;
            padding: 32px 24px;
            text-align: center;
        }
        .login-header h4 {
            margin: 0;
            font-weight: 700;
        }
        .login-body {
            padding: 32px 24px;
        }
        .form-control:focus {
            border-color: #1A3FBF;
            box-shadow: 0 0 0 0.2rem rgba(26, 63, 191, 0.15);
        }
        .btn-login {
            background: #1A3FBF;
            border: none;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-login:hover {
            background: #1535a8;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(26, 63, 191, 0.3);
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <i class="bi bi-shield-lock fs-1 mb-2"></i>
        <h4>Admin Panel</h4>
        <p class="mb-0 small opacity-75">MTech Logistics</p>
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
                           value="baominhkpkp@gmail.com">
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Mật khẩu</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password"
                           placeholder="Nhập mật khẩu" required>
                </div>
                <small class="text-muted">Mật khẩu mặc định: admin123</small>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
            </div>

            <button type="submit" class="btn btn-primary btn-login w-100">
                <i class="bi bi-box-arrow-in-right me-2"></i>Đăng nhập
            </button>

        </form>

        <div class="text-center mt-3">
            <a href="/forgot-password" class="text-decoration-none small">Quên mật khẩu?</a>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
