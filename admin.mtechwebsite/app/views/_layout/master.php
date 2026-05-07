<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Admin MTech') ?></title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Admin CSS -->
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>

<div class="admin-wrapper d-flex">

    <!-- ========== SIDEBAR ========== -->
    <?php include __DIR__ . '/sidebar.php'; ?>

    <!-- ========== MAIN CONTENT ========== -->
    <div class="admin-main flex-grow-1 d-flex flex-column">

        <!-- Topbar -->
        <?php include __DIR__ . '/topbar.php'; ?>

        <!-- Page Content -->
        <main class="admin-content flex-grow-1 p-4">

            <!-- Flash Messages -->
            <?php if ($success = ($GLOBALS['_SESSION']['success'] ?? null)): unset($_SESSION['success']); ?>
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

            <!-- View Content -->
            <?php include $content; ?>

        </main>

        <!-- Footer -->
        <footer class="admin-footer text-center text-muted py-3 border-top">
            <small>© <?= date('Y') ?> MTech Admin Panel</small>
        </footer>

    </div><!-- /.admin-main -->

</div><!-- /.admin-wrapper -->

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Admin JS -->
<script src="/assets/js/admin.js"></script>

</body>
</html>
