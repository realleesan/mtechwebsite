<?php
function toRomanEdit(int $n): string {
    $map = [1=>'I',2=>'II',3=>'III',4=>'IV',5=>'V',6=>'VI',7=>'VII',8=>'VIII',9=>'IX',10=>'X'];
    return $map[$n] ?? (string)$n;
}
?>

<div class="page-header">
    <h4><i class="bi bi-patch-check me-2"></i>Chỉnh sửa lĩnh vực</h4>
    <a href="/awards" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Quay lại
    </a>
</div>

<!-- Form sửa lĩnh vực cha -->
<form method="POST" action="/awards/update/<?= $field['id'] ?>">
    <div class="admin-form-card mb-4">
        <h6 class="fw-bold mb-3 pb-2 border-bottom">Thông tin lĩnh vực</h6>
        <div class="row">
            <div class="col-md-2 mb-3">
                <label for="sort_order" class="form-label">Số thứ tự</label>
                <input type="number" class="form-control" id="sort_order" name="sort_order"
                       value="<?= (int)$field['sort_order'] ?>" min="1" max="99" required>
                <div class="form-text">Hiện: <?= toRomanEdit((int)$field['sort_order']) ?></div>
            </div>
            <div class="col-md-7 mb-3">
                <label for="name" class="form-label">Tên lĩnh vực <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name"
                       value="<?= htmlspecialchars($field['name']) ?>" required>
            </div>
            <div class="col-md-3 mb-3">
                <label for="status" class="form-label">Trạng thái</label>
                <select class="form-select" id="status" name="status">
                    <option value="1" <?= $field['status'] == 1 ? 'selected' : '' ?>>Hiển thị</option>
                    <option value="0" <?= $field['status'] == 0 ? 'selected' : '' ?>>Ẩn</option>
                </select>
            </div>
        </div>
        <div class="d-flex justify-content-between pt-3 border-top">
            <a href="/awards" class="btn btn-outline-secondary">
                <i class="bi bi-x-lg me-2"></i>Hủy
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-2"></i>Lưu thay đổi
            </button>
        </div>
    </div>
</form>

<!-- Danh sách mục con -->
<div class="admin-form-card">
    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <h6 class="fw-bold mb-0">Các mục trong lĩnh vực này</h6>
        <a href="/awards/<?= $field['id'] ?>/items/create" class="btn btn-sm btn-success">
            <i class="bi bi-plus-lg me-1"></i>Thêm mục
        </a>
    </div>

    <?php if (!empty($field['items'])): ?>
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:60px" class="text-center">STT</th>
                    <th>Tên mục (lĩnh vực con)</th>
                    <th style="width:120px" class="text-center">Hạng</th>
                    <th style="width:90px" class="text-center">Trạng thái</th>
                    <th style="width:100px" class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($field['items'] as $item): ?>
                <tr>
                    <td class="text-center text-muted"><?= (int)$item['sort_order'] ?></td>
                    <td>– <?= htmlspecialchars($item['name']) ?></td>
                    <td class="text-center">
                        <?php if (!empty($item['rank'])): ?>
                            <span class="badge bg-light text-dark border"><?= htmlspecialchars($item['rank']) ?></span>
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <span class="badge <?= $item['status'] == 1 ? 'bg-success' : 'bg-secondary' ?>">
                            <?= $item['status'] == 1 ? 'Hiển thị' : 'Ẩn' ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <a href="/awards/items/edit/<?= $item['id'] ?>"
                               class="btn btn-sm btn-outline-primary" title="Sửa">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="/awards/items/delete/<?= $item['id'] ?>" class="d-inline">
                                <button type="submit" class="btn btn-sm btn-outline-danger btn-delete"
                                        data-confirm="Xóa mục này?" title="Xóa">
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
    <?php else: ?>
    <div class="text-center text-muted py-4">
        <i class="bi bi-list-ul fs-3 d-block mb-2 opacity-50"></i>
        <p class="mb-2">Chưa có mục nào</p>
        <a href="/awards/<?= $field['id'] ?>/items/create" class="btn btn-sm btn-success">
            <i class="bi bi-plus-lg me-1"></i>Thêm mục đầu tiên
        </a>
    </div>
    <?php endif; ?>
</div>
