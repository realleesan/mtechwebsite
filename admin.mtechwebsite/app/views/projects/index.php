<?php // $projects, $total, $currentPage, $totalPages, $search ?>
<div class="page-header">
    <h4><i class="bi bi-building me-2"></i>Quản lý Dự án</h4>
    <div class="d-flex gap-2">
        <a href="/projects/trash" class="btn btn-warning"><i class="bi bi-trash me-1"></i>Thùng rác</a>
        <a href="/projects/create" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Thêm mới</a>
    </div>
</div>
<div class="card mb-3 border-0 shadow-sm">
    <div class="card-body py-2">
        <form method="GET" action="/projects" class="row g-2 align-items-center">
            <div class="col-12 col-md-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" name="search" placeholder="Tìm kiếm..." value="<?= htmlspecialchars($search ?? '') ?>">
                </div>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary">Lọc</button>
                <a href="/projects" class="btn btn-sm btn-outline-secondary ms-1">Reset</a>
            </div>
        </form>
    </div>
</div>
<div class="admin-table">
    <div class="p-3 border-bottom"><span class="text-muted small">Tổng: <strong><?= $total ?? 0 ?></strong> dự án</span></div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>#</th><th>Tên dự án</th><th>Danh mục</th><th>Trạng thái</th><th>Ngày tạo</th><th style="width:120px">Thao tác</th></tr></thead>
            <tbody>
                <?php if (!empty($projects)): foreach ($projects as $p): ?>
                <tr>
                    <td class="text-muted small"><?= $p['id'] ?></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <?php if (!empty($p['image'])): ?>
                                <img src="<?= htmlspecialchars($p['image']) ?>" width="40" height="40" style="object-fit:cover;border-radius:6px" onerror="this.style.display='none'">
                            <?php endif; ?>
                            <div>
                                <div class="fw-medium"><?= htmlspecialchars($p['title'] ?? '') ?></div>
                                <small class="text-muted"><?= htmlspecialchars($p['slug'] ?? '') ?></small>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge bg-secondary"><?= htmlspecialchars($p['category_name'] ?? '') ?></span></td>
                    <td>
                        <?php 
                        $status = $p['status'] ?? 1;
                        $statusLabel = match((int)$status) {
                            0 => '<span class="badge bg-danger">Vô hiệu hóa</span>',
                            1 => '<span class="badge bg-success">Kích hoạt</span>',
                            2 => '<span class="badge bg-primary">Nổi bật</span>',
                            default => '<span class="badge bg-secondary">Không xác định</span>',
                        };
                        echo $statusLabel;
                        ?>
                    </td>
                    <td class="text-muted small"><?= isset($p['created_at']) ? date('d/m/Y', strtotime($p['created_at'])) : '' ?></td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="/projects/edit/<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="/projects/delete/<?= $p['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger btn-delete" data-confirm="Xóa dự án này?"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="6" class="text-center text-muted py-4"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Chưa có dự án nào</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
