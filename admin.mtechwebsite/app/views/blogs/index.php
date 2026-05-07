<?php
// $blogs, $categories, $total, $currentPage, $totalPages, $search, $catId
?>

<!-- Page Header -->
<div class="page-header">
    <h4><i class="bi bi-newspaper me-2"></i>Quản lý Tin tức</h4>
    <a href="/blogs/create" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Thêm mới
    </a>
</div>

<!-- Filter Bar -->
<div class="card mb-3 border-0 shadow-sm">
    <div class="card-body py-2">
        <form method="GET" action="/blogs" class="row g-2 align-items-center">
            <div class="col-12 col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" name="search"
                           placeholder="Tìm kiếm tiêu đề..."
                           value="<?= htmlspecialchars($search ?? '') ?>">
                </div>
            </div>
            <div class="col-12 col-md-4">
                <select class="form-select form-select-sm" name="cat">
                    <option value="0">-- Tất cả danh mục --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"
                            <?= ($catId ?? 0) == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary">Lọc</button>
                <a href="/blogs" class="btn btn-sm btn-outline-secondary ms-1">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="admin-table">
    <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
        <span class="text-muted small">Tổng: <strong><?= $total ?? 0 ?></strong> tin tức</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th style="width:60px">#</th>
                    <th>Tiêu đề</th>
                    <th>Danh mục</th>
                    <th>Ngày tạo</th>
                    <th>Lượt xem</th>
                    <th style="width:120px">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($blogs)): ?>
                    <?php foreach ($blogs as $i => $blog): ?>
                        <tr>
                            <td class="text-muted small"><?= $blog['id'] ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <?php if (!empty($blog['image'])): ?>
                                        <img src="<?= htmlspecialchars($blog['image']) ?>"
                                             alt="" width="40" height="40"
                                             style="object-fit:cover; border-radius:6px;"
                                             onerror="this.style.display='none'">
                                    <?php endif; ?>
                                    <div>
                                        <div class="fw-medium"><?= htmlspecialchars($blog['title']) ?></div>
                                        <small class="text-muted"><?= htmlspecialchars($blog['slug'] ?? '') ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary">
                                    <?= htmlspecialchars($blog['category_name'] ?? '') ?>
                                </span>
                            </td>
                            <td class="text-muted small">
                                <?= isset($blog['created_at']) ? date('d/m/Y', strtotime($blog['created_at'])) : '' ?>
                            </td>
                            <td class="text-muted small"><?= $blog['views'] ?? 0 ?></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="/blogs/edit/<?= $blog['id'] ?>"
                                       class="btn btn-sm btn-outline-primary" title="Chỉnh sửa">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="/blogs/delete/<?= $blog['id'] ?>" class="d-inline">
                                        <button type="submit" class="btn btn-sm btn-outline-danger btn-delete"
                                                data-confirm="Xóa tin tức này?" title="Xóa">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                            Chưa có tin tức nào
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if (($totalPages ?? 1) > 1): ?>
        <div class="d-flex justify-content-center py-3">
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                        <li class="page-item <?= $p == $currentPage ? 'active' : '' ?>">
                            <a class="page-link"
                               href="/blogs?page=<?= $p ?>&search=<?= urlencode($search ?? '') ?>&cat=<?= $catId ?? 0 ?>">
                                <?= $p ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>
