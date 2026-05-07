<?php // $contacts, $total, $currentPage, $totalPages ?>
<div class="page-header">
    <h4><i class="bi bi-envelope me-2"></i>Quản lý Liên hệ</h4>
</div>
<div class="admin-table">
    <div class="p-3 border-bottom"><span class="text-muted small">Tổng: <strong><?= $total ?? 0 ?></strong> liên hệ</span></div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>#</th><th>Họ tên</th><th>Email</th><th>Điện thoại</th><th>Thời gian</th><th style="width:100px">Thao tác</th></tr></thead>
            <tbody>
                <?php if (!empty($contacts)): foreach ($contacts as $c): ?>
                <tr>
                    <td class="text-muted small"><?= $c['id'] ?></td>
                    <td class="fw-medium"><?= htmlspecialchars($c['name'] ?? '') ?></td>
                    <td class="text-muted small"><?= htmlspecialchars($c['email'] ?? '') ?></td>
                    <td class="text-muted small"><?= htmlspecialchars($c['phone'] ?? '') ?></td>
                    <td class="text-muted small"><?= isset($c['created_at']) ? date('d/m/Y H:i', strtotime($c['created_at'])) : '' ?></td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="/contacts/view/<?= $c['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                            <form method="POST" action="/contacts/delete/<?= $c['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger btn-delete" data-confirm="Xóa liên hệ này?"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="6" class="text-center text-muted py-4"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Chưa có liên hệ nào</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
