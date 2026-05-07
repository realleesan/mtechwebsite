<?php // $awards ?>
<div class="page-header">
    <h4><i class="bi bi-trophy me-2"></i>Quản lý Giải thưởng</h4>
    <a href="/awards/create" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Thêm mới</a>
</div>
<div class="admin-table">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>#</th><th>Tên giải thưởng</th><th>Năm</th><th style="width:120px">Thao tác</th></tr></thead>
            <tbody>
                <?php if (!empty($awards)): foreach ($awards as $a): ?>
                <tr>
                    <td class="text-muted small"><?= $a['id'] ?></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <?php if (!empty($a['image'])): ?>
                                <img src="<?= htmlspecialchars($a['image']) ?>" width="40" height="40" style="object-fit:cover;border-radius:6px" onerror="this.style.display='none'">
                            <?php endif; ?>
                            <span class="fw-medium"><?= htmlspecialchars($a['title'] ?? $a['name'] ?? '') ?></span>
                        </div>
                    </td>
                    <td class="text-muted small"><?= htmlspecialchars($a['year'] ?? '') ?></td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="/awards/edit/<?= $a['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="/awards/delete/<?= $a['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger btn-delete" data-confirm="Xóa giải thưởng này?"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="4" class="text-center text-muted py-4"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Chưa có giải thưởng nào</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
