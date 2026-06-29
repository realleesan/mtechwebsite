<div class="page-header">
    <h4><i class="bi bi-trash me-2 text-warning"></i>Thùng rác Hero Slider</h4>
    <a href="/home-sliders" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Quay lại
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
                    <th>Ngày xóa</th>
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
                        <td class="text-muted small">
                            <?= isset($slide['deleted_at']) ? date('d/m/Y H:i', strtotime($slide['deleted_at'])) : '—' ?>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <form method="POST" action="/home-sliders/restore/<?= $slide['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-success"
                                            onclick="return confirm('Khôi phục slide này?')">
                                        <i class="bi bi-arrow-counterclockwise"></i> Khôi phục
                                    </button>
                                </form>
                                <form method="POST" action="/home-sliders/hard-delete/<?= $slide['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('XÓA VĨNH VIỄN slide này? Không thể khôi phục!')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                        Thùng rác trống
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
