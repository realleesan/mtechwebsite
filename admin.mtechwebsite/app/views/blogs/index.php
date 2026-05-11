<?php
// $blogs, $categories, $total, $currentPage, $totalPages, $search, $catId
?>

<!-- Page Header -->
<div class="page-header">
    <h4><i class="bi bi-newspaper me-2"></i>Quản lý Tin tức</h4>
    <div class="d-flex gap-2">
        <a href="/blogs/categories" class="btn btn-outline-primary">
            <i class="bi bi-tags me-1"></i>Quản lý danh mục
        </a>
        <a href="/blogs/create" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Thêm mới
        </a>
    </div>
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
                    <th style="width:80px">Ảnh</th>
                    <th>Tiêu đề</th>
                    <th>Danh mục</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th class="blog-col-views text-center">Lượt xem</th>
                    <th style="width:120px">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($blogs)): ?>
                    <?php foreach ($blogs as $i => $blog): ?>
                        <tr>
                            <td class="text-muted small"><?= $blog['id'] ?></td>
                            <td>
                                <?php if (!empty($blog['image'])): ?>
                                    <img src="<?= htmlspecialchars($blog['image']) ?>"
                                         alt="" width="50" height="50"
                                         style="object-fit:cover; border-radius:6px;"
                                         onerror="this.style.display='none'">
                                <?php else: ?>
                                    <div class="bg-light d-flex align-items-center justify-content-center" 
                                         style="width:50px; height:50px; border-radius:6px;">
                                        <i class="bi bi-image text-muted"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="fw-medium"><?= htmlspecialchars($blog['title']) ?></div>
                                <small class="text-muted"><?= htmlspecialchars($blog['slug'] ?? '') ?></small>
                            </td>
                            <td>
                                <span class="badge bg-secondary">
                                    <?= htmlspecialchars($blog['category_name'] ?? 'Chưa có') ?>
                                </span>
                            </td>
                            <td>
                                <?php 
                                $status = (int)($blog['status'] ?? 1);
                                $isFeatured = (int)($blog['is_featured'] ?? 0);
                                ?>
                                <div class="d-flex flex-column gap-1">
                                    <?php if ($status === 1): ?>
                                        <span class="badge bg-success">Kích hoạt</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Vô hiệu hóa</span>
                                    <?php endif; ?>
                                    
                                    <?php if ($isFeatured === 1): ?>
                                        <span class="badge bg-warning text-dark">Nổi bật</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-muted small">
                                <?= isset($blog['created_at']) ? date('d/m/Y H:i', strtotime($blog['created_at'])) : '' ?>
                            </td>
                            <td class="text-center text-muted small"><?= number_format($blog['views'] ?? 0) ?></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="/blogs/view/<?= $blog['id'] ?>"
                                       class="btn btn-sm btn-outline-info" title="Xem chi tiết">
                                        <i class="bi bi-eye"></i>
                                    </a>
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
                        <td colspan="9" class="text-center text-muted py-4">
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