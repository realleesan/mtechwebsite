<div class="page-header">
    <h4><i class="bi bi-layout-text-window-reverse me-2"></i>Quản lý Header</h4>
</div>
<div class="admin-form-card">
    <div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>Chức năng đang phát triển.</div>
    <?php if (!empty($header)): ?>
    <pre class="bg-light p-3 rounded small"><?= htmlspecialchars(print_r($header, true)) ?></pre>
    <?php endif; ?>
</div>
