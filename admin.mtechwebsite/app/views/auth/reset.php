<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Đặt lại mật khẩu - Admin MTech') ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: linear-gradient(135deg, #1A3FBF 0%, #2d5dd8 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Segoe UI', system-ui, sans-serif; }
        .card { border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); max-width: 420px; width: 100%; border: none; }
        .card-header { background: #1A3FBF; color: #fff; border-radius: 16px 16px 0 0 !important; padding: 28px 24px; text-align: center; }
        .form-control:focus { border-color: #1A3FBF; box-shadow: 0 0 0 0.2rem rgba(26,63,191,0.15); }
        .btn-primary { background: #1A3FBF; border-color: #1A3FBF; }
        .btn-primary:hover { background: #1535a8; border-color: #1535a8; }
    </style>
</head>
<body>
<div class="card">
    <div class="card-header">
        <i class="bi bi-shield-lock fs-1 mb-2 d-block"></i>
        <h5 class="mb-0">Đặt lại mật khẩu</h5>
    </div>
    <div class="card-body p-4">
        <?php if ($error = ($_SESSION['error'] ?? null)): unset($_SESSION['error']); ?>
            <div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="/reset-password">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">

            <div class="mb-3">
                <label class="form-label">Mật khẩu mới</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control" name="password" placeholder="Tối thiểu 6 ký tự" required autofocus minlength="6">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Xác nhận mật khẩu</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" class="form-control" name="password_confirm" placeholder="Nhập lại mật khẩu" required minlength="6">
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-check-lg me-2"></i>Đặt lại mật khẩu
            </button>
        </form>

        <div class="text-center mt-3">
            <a href="/login" class="text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Quay lại đăng nhập</a>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
