<?php
$predefinedRanks = ['Hạng I', 'Hạng II', 'Hạng III', 'Hạng IV'];
$currentRank     = $item['rank'] ?? '';
$isPredefined    = in_array($currentRank, $predefinedRanks);
?>

<div class="page-header">
    <h4><i class="bi bi-patch-check me-2"></i>Chỉnh sửa mục</h4>
    <a href="/awards/edit/<?= $field['id'] ?? '' ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Quay lại
    </a>
</div>

<form method="POST" action="/awards/items/update/<?= $item['id'] ?>">
    <div class="admin-form-card">

        <?php if ($field): ?>
        <div class="mb-3 pb-3 border-bottom">
            <span class="text-muted small">Thuộc lĩnh vực:</span>
            <strong class="ms-2"><?= htmlspecialchars($field['name']) ?></strong>
        </div>
        <?php endif; ?>

        <div class="mb-3">
            <label for="name" class="form-label">Tên mục <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="name" name="name"
                   value="<?= htmlspecialchars($item['name']) ?>" required>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="rank" class="form-label">Hạng chứng chỉ</label>
                <select class="form-select" id="rank" name="rank">
                    <option value="">— Chưa có hạng —</option>
                    <?php foreach ($predefinedRanks as $r): ?>
                    <option value="<?= $r ?>" <?= ($isPredefined && $currentRank === $r) ? 'selected' : '' ?>><?= $r ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label for="rank_custom" class="form-label">Hạng tùy chỉnh</label>
                <input type="text" class="form-control" id="rank_custom" name="rank_custom"
                       value="<?= !$isPredefined && $currentRank ? htmlspecialchars($currentRank) : '' ?>"
                       placeholder="Điền vào đây sẽ ghi đè lựa chọn bên trái">
            </div>
            <div class="col-md-2 mb-3">
                <label for="sort_order" class="form-label">Thứ tự</label>
                <input type="number" class="form-control" id="sort_order" name="sort_order"
                       value="<?= (int)$item['sort_order'] ?>" min="1">
            </div>
            <div class="col-md-2 mb-3">
                <label for="status" class="form-label">Trạng thái</label>
                <select class="form-select" id="status" name="status">
                    <option value="1" <?= $item['status'] == 1 ? 'selected' : '' ?>>Hiển thị</option>
                    <option value="0" <?= $item['status'] == 0 ? 'selected' : '' ?>>Ẩn</option>
                </select>
            </div>
        </div>

        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
            <a href="/awards/edit/<?= $field['id'] ?? '' ?>" class="btn btn-outline-secondary">
                <i class="bi bi-x-lg me-2"></i>Hủy
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-2"></i>Lưu thay đổi
            </button>
        </div>

    </div>
</form>
