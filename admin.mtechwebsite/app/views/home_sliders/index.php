<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <h4><i class="bi bi-sliders me-2 text-primary"></i>Quản lý Hero Slider</h4>
    <a href="/home-sliders/create" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Thêm Slide mới
    </a>
</div>

<div class="admin-table">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:60px">#</th>
                    <th>Ảnh 1</th>
                    <th>Ảnh 2</th>
                    <th>Ảnh 3</th>
                    <th style="width:100px">Thứ tự</th>
                    <th style="width:110px">Trạng thái</th>
                    <th style="width:180px">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($slides)): ?>
                    <?php foreach ($slides as $slide): ?>
                    <tr>
                        <td class="text-muted small"><?= $slide['id'] ?></td>
                        <td>
                            <?php if (!empty($slide['image_1'])): ?>
                                <img src="<?= htmlspecialchars($slide['image_1']) ?>" alt="" width="80" height="50" style="object-fit:cover;border-radius:6px;" onerror="this.style.display='none'">
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($slide['image_2'])): ?>
                                <img src="<?= htmlspecialchars($slide['image_2']) ?>" alt="" width="80" height="50" style="object-fit:cover;border-radius:6px;" onerror="this.style.display='none'">
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($slide['image_3'])): ?>
                                <img src="<?= htmlspecialchars($slide['image_3']) ?>" alt="" width="80" height="50" style="object-fit:cover;border-radius:6px;" onerror="this.style.display='none'">
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td><?= (int)($slide['sort_order'] ?? 0) ?></td>
                        <td>
                            <?php if (($slide['status'] ?? 1) == 1): ?>
                                <span class="badge bg-success">Hiển thị</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Ẩn</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="/home-sliders/edit/<?= $slide['id'] ?>" class="btn btn-sm btn-outline-primary" title="Chỉnh sửa">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="/home-sliders/delete/<?= $slide['id'] ?>" onsubmit="return confirm('Bạn có chắc muốn xóa slide này?')">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td colspan="7" class="text-center text-muted py-5">
                    <i class="bi bi-sliders fs-1 d-block mb-3 opacity-50"></i>
                    <p class="mb-3">Chưa có slide nào</p>
                    <a href="/home-sliders/create" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>Thêm slide đầu tiên
                    </a>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="mt-3 text-center">
    <a href="/home-sliders/create" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Thêm Slide mới
    </a>
</div>
</div>
