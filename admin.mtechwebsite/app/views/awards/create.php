<div class="page-header">
    <h4><i class="bi bi-patch-check me-2"></i>Thêm lĩnh vực mới</h4>
    <a href="/awards" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Quay lại
    </a>
</div>

<form method="POST" action="/awards/store">
    <div class="admin-form-card">

        <div class="row">
            <div class="col-md-2 mb-3">
                <label for="sort_order" class="form-label">Số thứ tự <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="sort_order" name="sort_order"
                       value="<?= (int)($nextOrder ?? 1) ?>" min="1" max="99" required>
                <div class="form-text">Hiển thị dạng La Mã (I, II, III...)</div>
            </div>
            <div class="col-md-7 mb-3">
                <label for="name" class="form-label">Tên lĩnh vực <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name"
                       placeholder="VD: Thiết kế, thẩm tra thiết kế xây dựng" required>
            </div>
            <div class="col-md-3 mb-3">
                <label for="status" class="form-label">Trạng thái</label>
                <select class="form-select" id="status" name="status">
                    <option value="1">Hiển thị</option>
                    <option value="0">Ẩn</option>
                </select>
            </div>
        </div>

        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
            <a href="/awards" class="btn btn-outline-secondary">
                <i class="bi bi-x-lg me-2"></i>Hủy
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-2"></i>Lưu lĩnh vực
            </button>
        </div>

    </div>
</form>
