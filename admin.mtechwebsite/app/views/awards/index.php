<?php
function toRoman(int $n): string {
    $map = [1=>'I',2=>'II',3=>'III',4=>'IV',5=>'V',6=>'VI',7=>'VII',8=>'VIII',9=>'IX',10=>'X'];
    return $map[$n] ?? (string)$n;
}
?>

<div class="page-header">
    <h4><i class="bi bi-patch-check me-2"></i>Chứng chỉ năng lực hoạt động xây dựng</h4>
    <div class="d-flex gap-2">
        <a href="/chung-chi-nang-luc" target="_blank" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-eye me-1"></i>Xem website
        </a>
        <a href="/awards/create" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Thêm lĩnh vực
        </a>
    </div>
</div>

<?php if (!empty($fields)): ?>
<div class="admin-table">
    <div class="p-3 border-bottom">
        <span class="text-muted small">Tổng: <strong><?= count($fields) ?></strong> lĩnh vực</span>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:60px" class="text-center">TT</th>
                    <th>Lĩnh vực hoạt động</th>
                    <th style="width:120px" class="text-center">Hạng</th>
                    <th style="width:100px" class="text-center">Trạng thái</th>
                    <th style="width:150px" class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fields as $field): ?>

                <!-- Hàng lĩnh vực cha -->
                <tr class="table-primary">
                    <td class="text-center fw-bold"><?= toRoman((int)$field['sort_order']) ?></td>
                    <td class="fw-bold"><?= htmlspecialchars($field['name']) ?></td>
                    <td class="text-center text-muted">—</td>
                    <td class="text-center">
                        <span class="badge <?= $field['status'] == 1 ? 'bg-success' : 'bg-secondary' ?>">
                            <?= $field['status'] == 1 ? 'Hiển thị' : 'Ẩn' ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <a href="/awards/<?= $field['id'] ?>/items/create"
                               class="btn btn-sm btn-outline-success" title="Thêm mục con">
                                <i class="bi bi-plus"></i>
                            </a>
                            <a href="/awards/edit/<?= $field['id'] ?>"
                               class="btn btn-sm btn-outline-primary" title="Sửa lĩnh vực">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="/awards/delete/<?= $field['id'] ?>" class="d-inline">
                                <button type="submit" class="btn btn-sm btn-outline-danger btn-delete"
                                        data-confirm="Xóa lĩnh vực và tất cả mục con?" title="Xóa">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>

                <!-- Hàng mục con -->
                <?php if (!empty($field['items'])): ?>
                    <?php foreach ($field['items'] as $item): ?>
                    <tr>
                        <td class="text-center text-muted">–</td>
                        <td class="ps-4">– <?= htmlspecialchars($item['name']) ?></td>
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
                                   class="btn btn-sm btn-outline-primary" title="Sửa mục">
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
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted small fst-italic py-2 ps-5">
                            Chưa có mục —
                            <a href="/awards/<?= $field['id'] ?>/items/create">Thêm mục con</a>
                        </td>
                    </tr>
                <?php endif; ?>

                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php else: ?>
<div class="text-center text-muted py-5">
    <i class="bi bi-patch-check fs-1 d-block mb-3 opacity-50"></i>
    <p class="mb-3">Chưa có lĩnh vực nào</p>
    <a href="/awards/create" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Thêm lĩnh vực đầu tiên
    </a>
</div>
<?php endif; ?>
