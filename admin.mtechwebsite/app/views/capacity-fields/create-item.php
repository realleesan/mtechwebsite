<div class="page-header">
    <h4><i class="bi bi-patch-check me-2"></i>Thêm mục vào: <?= htmlspecialchars($field['name']) ?></h4>
    <a href="/awards?tab=capacity" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Quay lại
    </a>
</div>

<form method="POST" action="/capacity-fields/<?= $field['id'] ?>/items/store">
    <div class="admin-form-card">

        <div class="mb-3">
            <label class="form-label text-muted small">Thuộc lĩnh vực</label>
            <p class="fw-bold mb-0"><?= htmlspecialchars($field['name']) ?></p>
        </div>

        <hr>

        <div class="mb-3">
            <label for="name" class="form-label">Tên mục (lĩnh vực con) <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="name" name="name"
                   placeholder="VD: Công trình Nhà công nghiệp" required>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="rank" class="form-label">Hạng chứng chỉ</label>
                <select class="form-select" id="rank" name="rank">
                    <option value="">— Chưa có hạng —</option>
                    <option value="Hạng I">Hạng I</option>
                    <option value="Hạng II">Hạng II</option>
                    <option value="Hạng III">Hạng III</option>
                    <option value="Hạng IV">Hạng IV</option>
                </select>
                <div class="form-text">Hoặc nhập thủ công bên dưới nếu không có trong danh sách</div>
            </div>
            <div class="col-md-4 mb-3">
                <label for="rank_custom" class="form-label">Hạng tùy chỉnh</label>
                <input type="text" class="form-control" id="rank_custom" name="rank_custom"
                       placeholder="Nhập hạng nếu không có trong danh sách">
            </div>
            <div class="col-md-2 mb-3">
                <label for="sort_order" class="form-label">Thứ tự</label>
                <input type="number" class="form-control" id="sort_order" name="sort_order"
                       value="<?= (int)($nextOrder ?? 1) ?>" min="1">
            </div>
            <div class="col-md-2 mb-3">
                <label for="status" class="form-label">Trạng thái</label>
                <select class="form-select" id="status" name="status">
                    <option value="1">Hiển thị</option>
                    <option value="0">Ẩn</option>
                </select>
            </div>
        </div>

        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
            <a href="/capacity-fields/edit/<?= $field['id'] ?>" class="btn btn-outline-secondary">
                <i class="bi bi-x-lg me-2"></i>Hủy
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-2"></i>Lưu mục
            </button>
        </div>

    </div>
</form>

<script>
// Ưu tiên rank_custom nếu có giá trị, ngược lại dùng select rank
document.querySelector('form').addEventListener('submit', function () {
    const custom = document.getElementById('rank_custom').value.trim();
    if (custom) {
        document.getElementById('rank').name = '_rank_select_disabled';
        document.getElementById('rank_custom').name = 'rank';
    }
});
</script>
