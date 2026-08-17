<?php
/**
 * AwardsController (admin)
 * Đã thay hoàn toàn để quản lý bảng Chứng chỉ năng lực hoạt động xây dựng.
 * Routes /awards/* → quản lý capacity_fields + capacity_field_items
 */

require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../models/CapacityFieldsModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class AwardsController extends BaseController
{
    private CapacityFieldsModel $model;

    public function __construct()
    {
        AuthMiddleware::requireLogin();
        $this->model = new CapacityFieldsModel();
    }

    // ----------------------------------------
    // INDEX — Danh sách lĩnh vực + mục con
    // ----------------------------------------

    public function index()
    {
        $fields = $this->model->getAllFields();
        foreach ($fields as &$field) {
            $field['items'] = $this->model->getItemsByField($field['id']);
        }
        unset($field);

        $this->view('awards/index', [
            'title'  => 'Chứng chỉ năng lực - Admin MTech',
            'page'   => 'awards',
            'fields' => $fields,
            'admin'  => AuthMiddleware::getAdmin(),
        ]);
    }

    // ----------------------------------------
    // CREATE / STORE — Lĩnh vực cha
    // ----------------------------------------

    public function create()
    {
        $this->view('awards/create', [
            'title'     => 'Thêm lĩnh vực - Chứng chỉ năng lực',
            'page'      => 'award.create',
            'nextOrder' => $this->model->getNextFieldOrder(),
            'admin'     => AuthMiddleware::getAdmin(),
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/awards/create');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        if (empty($name)) {
            $_SESSION['error'] = 'Vui lòng nhập tên lĩnh vực';
            $this->redirect('/awards/create');
            return;
        }

        $id = $this->model->createField([
            'sort_order' => (int)($_POST['sort_order'] ?? 1),
            'name'       => $name,
            'status'     => (int)($_POST['status']     ?? 1),
        ]);

        if ($id) {
            $_SESSION['success'] = 'Thêm lĩnh vực thành công!';
            $this->redirect('/awards');
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra, vui lòng thử lại';
            $this->redirect('/awards/create');
        }
    }

    // ----------------------------------------
    // EDIT / UPDATE — Lĩnh vực cha
    // ----------------------------------------

    public function edit($id)
    {
        $field = $this->model->getFieldById((int)$id);
        if (!$field) {
            $_SESSION['error'] = 'Không tìm thấy lĩnh vực';
            $this->redirect('/awards');
            return;
        }
        $field['items'] = $this->model->getItemsByField((int)$id);

        $this->view('awards/edit', [
            'title' => 'Chỉnh sửa lĩnh vực - Chứng chỉ năng lực',
            'page'  => 'award.edit',
            'field' => $field,
            'admin' => AuthMiddleware::getAdmin(),
        ]);
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/awards/edit/' . $id);
            return;
        }

        $field = $this->model->getFieldById((int)$id);
        if (!$field) {
            $_SESSION['error'] = 'Không tìm thấy lĩnh vực';
            $this->redirect('/awards');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        if (empty($name)) {
            $_SESSION['error'] = 'Vui lòng nhập tên lĩnh vực';
            $this->redirect('/awards/edit/' . $id);
            return;
        }

        if ($this->model->updateField((int)$id, [
            'sort_order' => (int)($_POST['sort_order'] ?? 1),
            'name'       => $name,
            'status'     => (int)($_POST['status']     ?? 1),
        ])) {
            $_SESSION['success'] = 'Cập nhật lĩnh vực thành công!';
            $this->redirect('/awards');
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra, vui lòng thử lại';
            $this->redirect('/awards/edit/' . $id);
        }
    }

    // ----------------------------------------
    // DELETE — Lĩnh vực cha (soft delete kèm items)
    // ----------------------------------------

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/awards');
            return;
        }

        if ($this->model->deleteField((int)$id)) {
            $_SESSION['success'] = 'Đã xóa lĩnh vực thành công';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa';
        }
        $this->redirect('/awards');
    }

    // ----------------------------------------
    // ITEM: CREATE / STORE — Mục con
    // Dùng route: /awards/{fieldId}/items/create  (thêm vào admin router)
    // ----------------------------------------

    public function createItem($fieldId)
    {
        $field = $this->model->getFieldById((int)$fieldId);
        if (!$field) {
            $_SESSION['error'] = 'Không tìm thấy lĩnh vực';
            $this->redirect('/awards');
            return;
        }

        $this->view('awards/create-item', [
            'title'     => 'Thêm mục - ' . htmlspecialchars($field['name']),
            'page'      => 'award.create',
            'field'     => $field,
            'nextOrder' => $this->model->getNextItemOrder((int)$fieldId),
            'admin'     => AuthMiddleware::getAdmin(),
        ]);
    }

    public function storeItem($fieldId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/awards/' . $fieldId . '/items/create');
            return;
        }

        $field = $this->model->getFieldById((int)$fieldId);
        if (!$field) {
            $_SESSION['error'] = 'Không tìm thấy lĩnh vực';
            $this->redirect('/awards');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        if (empty($name)) {
            $_SESSION['error'] = 'Vui lòng nhập tên mục';
            $this->redirect('/awards/' . $fieldId . '/items/create');
            return;
        }

        // Ưu tiên rank_custom nếu có
        $rank = trim($_POST['rank_custom'] ?? '');
        if (empty($rank)) {
            $rank = trim($_POST['rank'] ?? '');
        }

        $id = $this->model->createItem([
            'field_id'   => (int)$fieldId,
            'name'       => $name,
            'rank'       => $rank,
            'sort_order' => (int)($_POST['sort_order'] ?? 1),
            'status'     => (int)($_POST['status']     ?? 1),
        ]);

        if ($id) {
            $_SESSION['success'] = 'Thêm mục thành công!';
            $this->redirect('/awards/edit/' . $fieldId);
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra, vui lòng thử lại';
            $this->redirect('/awards/' . $fieldId . '/items/create');
        }
    }

    // ----------------------------------------
    // ITEM: EDIT / UPDATE
    // ----------------------------------------

    public function editItem($itemId)
    {
        $item = $this->model->getItemById((int)$itemId);
        if (!$item) {
            $_SESSION['error'] = 'Không tìm thấy mục';
            $this->redirect('/awards');
            return;
        }
        $field = $this->model->getFieldById($item['field_id']);

        $this->view('awards/edit-item', [
            'title' => 'Chỉnh sửa mục - Chứng chỉ năng lực',
            'page'  => 'award.edit',
            'item'  => $item,
            'field' => $field,
            'admin' => AuthMiddleware::getAdmin(),
        ]);
    }

    public function updateItem($itemId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/awards/items/edit/' . $itemId);
            return;
        }

        $item = $this->model->getItemById((int)$itemId);
        if (!$item) {
            $_SESSION['error'] = 'Không tìm thấy mục';
            $this->redirect('/awards');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        if (empty($name)) {
            $_SESSION['error'] = 'Vui lòng nhập tên mục';
            $this->redirect('/awards/items/edit/' . $itemId);
            return;
        }

        $rank = trim($_POST['rank_custom'] ?? '');
        if (empty($rank)) {
            $rank = trim($_POST['rank'] ?? '');
        }

        if ($this->model->updateItem((int)$itemId, [
            'name'       => $name,
            'rank'       => $rank,
            'sort_order' => (int)($_POST['sort_order'] ?? 1),
            'status'     => (int)($_POST['status']     ?? 1),
        ])) {
            $_SESSION['success'] = 'Cập nhật mục thành công!';
            $this->redirect('/awards/edit/' . $item['field_id']);
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra, vui lòng thử lại';
            $this->redirect('/awards/items/edit/' . $itemId);
        }
    }

    // ----------------------------------------
    // ITEM: DELETE
    // ----------------------------------------

    public function deleteItem($itemId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/awards');
            return;
        }

        $item    = $this->model->getItemById((int)$itemId);
        $fieldId = $item['field_id'] ?? null;

        if ($this->model->deleteItem((int)$itemId)) {
            $_SESSION['success'] = 'Đã xóa mục thành công';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa';
        }

        $this->redirect($fieldId ? '/awards/edit/' . $fieldId : '/awards');
    }

    // ----------------------------------------
    // Giữ các method cũ để router không lỗi
    // (trash, restore, hardDelete không dùng nữa nhưng route còn đăng ký)
    // ----------------------------------------

    public function trash()       { $this->redirect('/awards'); }
    public function restore($id)  { $this->redirect('/awards'); }
    public function hardDelete($id) { $this->redirect('/awards'); }
}
