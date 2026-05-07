<div class="page-header">
    <h4><i class="bi bi-gear me-2"></i>Cài đặt</h4>
</div>
<div class="admin-form-card">
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>
        Trang cài đặt đang được phát triển.
    </div>
    <h6 class="fw-bold mb-3">Thông tin hệ thống</h6>
    <table class="table table-sm">
        <tr><td class="text-muted">PHP Version</td><td><?= phpversion() ?></td></tr>
        <tr><td class="text-muted">Server</td><td><?= $_SERVER['SERVER_SOFTWARE'] ?? 'N/A' ?></td></tr>
        <tr><td class="text-muted">Admin Email</td><td><?= htmlspecialchars($admin['email'] ?? '') ?></td></tr>
        <tr><td class="text-muted">Thời gian server</td><td><?= date('d/m/Y H:i:s') ?></td></tr>
    </table>
</div>
