<?php // $logos ?>
<div class="page-header">
    <h4><i class="bi bi-images me-2"></i>Quản lý Logo đối tác</h4>
    <a href="/client-logos/create" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Thêm mới</a>
</div>

<?php if (!empty($logos)): ?>
<div class="admin-table">
    <div class="p-3 border-bottom">
        <span class="text-muted small">Tổng: <strong><?= count($logos) ?></strong> logo đối tác</span>
    </div>
    <div class="table-responsive">
<<<<<<< HEAD
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th style="width:60px">#</th>
                    <th style="width:100px">Logo</th>
                    <th>Tên đối tác</th>
                    <th>Link website</th>
                    <th style="width:80px">Thứ tự</th>
                    <th style="width:100px">Trạng thái</th>
                    <th style="width:120px">Thao tác</th>
=======
        <table class="table table-hover mb-0 align-middle client-logos-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Logo</th>
                    <th>Tên đối tác</th>
                    <th>Link website</th>
                    <th class="client-logo-col-sort text-center">Thứ tự</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
>>>>>>> 70909aa2291eb80ef37d22b71f05807579217647
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logos as $logo): ?>
                <tr>
                    <td class="text-muted small"><?= $logo['id'] ?></td>
                    <td>
                        <div class="client-logo-thumb">
                            <?php if (!empty($logo['logo'])): ?>
                                <img src="<?= htmlspecialchars($logo['logo']) ?>"
                                     alt="<?= htmlspecialchars($logo['name'] ?? '') ?>"
                                     onerror="this.src='https://via.placeholder.com/80x40?text=Logo'">
                            <?php else: ?>
                                <div class="client-logo-thumb-empty">
                                    <i class="bi bi-image text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <span class="fw-medium"><?= htmlspecialchars($logo['name'] ?? '') ?></span>
                    </td>
                    <td>
                        <?php if (!empty($logo['url'])): ?>
                            <a href="<?= htmlspecialchars($logo['url']) ?>" target="_blank" rel="noopener"
                               class="text-decoration-none text-muted small">
                                <i class="bi bi-box-arrow-up-right me-1"></i>
                                <?= htmlspecialchars(parse_url($logo['url'], PHP_URL_HOST) ?: $logo['url']) ?>
                            </a>
                        <?php else: ?>
                            <span class="text-muted small">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center text-muted small"><?= (int)($logo['sort_order'] ?? 0) ?></td>
                    <td>
                        <?php if (($logo['status'] ?? 1) == 1): ?>
                            <span class="badge bg-success">Hiển thị</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Ẩn</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="/client-logos/edit/<?= $logo['id'] ?>"
                               class="btn btn-sm btn-outline-primary" title="Chỉnh sửa">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="/client-logos/delete/<?= $logo['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger btn-delete"
                                        data-confirm="Xóa logo đối tác này?" title="Xóa">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php else: ?>
<div class="text-center text-muted py-5">
    <i class="bi bi-images fs-1 d-block mb-3 opacity-50"></i>
    <p class="mb-3">Chưa có logo đối tác nào</p>
    <a href="/client-logos/create" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Thêm logo đầu tiên
    </a>
</div>
<?php endif; ?>
