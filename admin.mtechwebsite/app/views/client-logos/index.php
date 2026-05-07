<?php // $logos ?>
<div class="page-header">
    <h4><i class="bi bi-images me-2"></i>Quản lý Logo đối tác</h4>
    <a href="/client-logos/create" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Thêm mới</a>
</div>
<div class="row g-3">
    <?php if (!empty($logos)): foreach ($logos as $logo): ?>
    <div class="col-6 col-md-3 col-lg-2">
        <div class="card border-0 shadow-sm text-center p-3">
            <img src="<?= htmlspecialchars($logo['logo'] ?? $logo['image'] ?? '') ?>" alt="<?= htmlspecialchars($logo['name'] ?? '') ?>"
                 class="img-fluid mb-2" style="max-height:60px;object-fit:contain"
                 onerror="this.src='https://via.placeholder.com/120x60?text=Logo'">
            <small class="text-muted d-block mb-2"><?= htmlspecialchars($logo['name'] ?? '') ?></small>
            <div class="d-flex gap-1 justify-content-center">
                <a href="/client-logos/edit/<?= $logo['id'] ?>" class="btn btn-xs btn-outline-primary btn-sm"><i class="bi bi-pencil"></i></a>
                <form method="POST" action="/client-logos/delete/<?= $logo['id'] ?>">
                    <button type="submit" class="btn btn-xs btn-outline-danger btn-sm btn-delete" data-confirm="Xóa logo này?"><i class="bi bi-trash"></i></button>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; else: ?>
    <div class="col-12 text-center text-muted py-5">
        <i class="bi bi-images fs-1 d-block mb-2"></i>Chưa có logo nào
    </div>
    <?php endif; ?>
</div>
