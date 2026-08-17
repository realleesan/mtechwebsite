<?php
require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../models/CapacityFieldsModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

/**
 * CapacityFieldsController
 * Quản lý chứng chỉ năng lực hoạt động xây dựng.
 * Routes:
 *   GET  /capacity-fields              → index()
 *   GET  /capacity-fields/create       → createField()
 *   POST /capacity-fields/store        → storeField()
 *   GET  /capacity-fields/edit/{id}    → editField($id)
 *   POST /capacity-fields/update/{id}  → updateField($id)
 *   POST /capacity-fields/delete/{id}  → deleteField($id)
 *
 *   GET  /capacity-fields/{id}/items/create       → createItem($id)
 *   POST /capacity-fields/{id}/items/store        → storeItem($id)
 *   GET  /capacity-fields/items/edit/{itemId}     → editItem($itemId)
 *   POST /capacity-fields/items/update/{itemId}   → updateItem($itemId)
 *   POST /capacity-fields/items/delete/{itemId}   → deleteItem($itemId)
 */
class CapacityFieldsController extends BaseController
{
    private CapacityFieldsModel $model;

    public function __construct()
    {
        AuthMiddleware::requireLogin();
        $this->model = new CapacityFieldsModel();
    }

    // --------------------------------------------------------
    // INDEX — Danh sách lĩnh vực cha + con
    // --------------------------------------------------------

    public function index()
    {
        $fields = $this->model->getAllFields();
        foreach ($fields as &$field) {
            $field['items'] = $this->model->getItemsByField($field['id']);
        }

        $this->view('capacity-fields/index', [
            'title'  => 'Chứng chỉ năng lực - Admin MTech',
            'page'   => 'capacity_fields',
            'fields' => $fields,
            'admin'  => AuthMiddleware::getAdmin(),
        ]);
    }

    // --------------------------------------------------------
    // CREATE / STORE — Lĩnh vực cha
    // --------------------------------------------------------

    public function createField()
    {
        $this->view('capacity-fields/create-field', [
            'title'     => 'Thêm lĩnh vực - Chứng chỉ năng lực',
            'page'      => 'capacity_fields',
            'nextOrder' => $this->model->getNextFieldOrder(),
            'admin'     => AuthMiddleware::getAdmin(),
        ]);
    }

    public function storeField()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/capacity-fields/create');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        if (empty($name)) {
            $_SESSION['error'] = 'Vui lòng nhập tên lĩnh vực';
            $this->redirect('/capacity-fields/create');
            return;
        }

        $id = $this->model->createField([
            'sort_order' => (int)($_POST['sort_order'] ?? 1),
            'name'       => $name,
            'status'     => (int)($_POST['status']     ?? 1),
        ]);

        if ($id) {
            $_SESSION['success'] = 'Thêm lĩnh vực thành công!';
            $this->redirect('/awards?tab=capacity');
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra, vui lòng thử lại';
            $this->redirect('/capacity-fields/create');
        }
    }

    // --------------------------------------------------------
    // EDIT / UPDATE — Lĩnh vực cha
    // --------------------------------------------------------

    public function editField($id)
    {
        $field = $this->model->getFieldById((int)$id);
        if (!$field) {
            $_SESSION['error'] = 'Không tìm thấy lĩnh vực';
            $this->redirect('/capacity-fields');
            return;
        }
        $field['items'] = $this->model->getItemsByField((int)$id);

        $this->view('capacity-fields/edit-field', [
            'title' => 'Chỉnh sửa lĩnh vực - Chứng chỉ năng lực',
            'page'  => 'capacity_fields',
            'field' => $field,
            'admin' => AuthMiddleware::getAdmin(),
        ]);
    }

    public function updateField($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/capacity-fields/edit/' . $id);
            return;
        }

        $field = $this->model->getFieldById((int)$id);
        if (!$field) {
            $_SESSION['error'] = 'Không tìm thấy lĩnh vực';
            $this->redirect('/capacity-fields');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        if (empty($name)) {
            $_SESSION['error'] = 'Vui lòng nhập tên lĩnh vực';
            $this->redirect('/capacity-fields/edit/' . $id);
            return;
        }

        if ($this->model->updateField((int)$id, [
            'sort_order' => (int)($_POST['sort_order'] ?? 1),
            'name'       => $name,
            'status'     => (int)($_POST['status']     ?? 1),
        ])) {
            $_SESSION['success'] = 'Cập nhật lĩnh vực thành công!';
            $this->redirect('/awards?tab=capacity');
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra, vui lòng thử lại';
            $this->redirect('/capacity-fields/edit/' . $id);
        }
    }

    // --------------------------------------------------------
    // DELETE — Lĩnh vực cha (soft delete)
    // --------------------------------------------------------

    public function deleteField($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/capacity-fields');
            return;
        }

        if ($this->model->deleteField((int)$id)) {
            $_SESSION['success'] = 'Đã xóa lĩnh vực thành công';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa';
        }
        $this->redirect('/awards?tab=capacity');
    }

    // --------------------------------------------------------
    // CREATE / STORE — Item (lĩnh vực con)
    // --------------------------------------------------------

    public function createItem($fieldId)
    {
        $field = $this->model->getFieldById((int)$fieldId);
        if (!$field) {
            $_SESSION['error'] = 'Không tìm thấy lĩnh vực';
            $this->redirect('/capacity-fields');
            return;
        }

        $this->view('capacity-fields/create-item', [
            'title'     => 'Thêm mục - ' . htmlspecialchars($field['name']),
            'page'      => 'capacity_fields',
            'field'     => $field,
            'nextOrder' => $this->model->getNextItemOrder((int)$fieldId),
            'admin'     => AuthMiddleware::getAdmin(),
        ]);
    }

    public function storeItem($fieldId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/capacity-fields/' . $fieldId . '/items/create');
            return;
        }

        $field = $this->model->getFieldById((int)$fieldId);
        if (!$field) {
            $_SESSION['error'] = 'Không tìm thấy lĩnh vực';
            $this->redirect('/capacity-fields');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        if (empty($name)) {
            $_SESSION['error'] = 'Vui lòng nhập tên mục';
            $this->redirect('/capacity-fields/' . $fieldId . '/items/create');
            return;
        }

        $id = $this->model->createItem([
            'field_id'   => (int)$fieldId,
            'name'       => $name,
            'rank'       => trim($_POST['rank']       ?? ''),
            'sort_order' => (int)($_POST['sort_order'] ?? 1),
            'status'     => (int)($_POST['status']     ?? 1),
        ]);

        if ($id) {
            $_SESSION['success'] = 'Thêm mục thành công!';
            $this->redirect('/awards?tab=capacity');
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra, vui lòng thử lại';
            $this->redirect('/capacity-fields/' . $fieldId . '/items/create');
        }
    }

    // --------------------------------------------------------
    // EDIT / UPDATE — Item
    // --------------------------------------------------------

    public function editItem($itemId)
    {
        $item = $this->model->getItemById((int)$itemId);
        if (!$item) {
            $_SESSION['error'] = 'Không tìm thấy mục';
            $this->redirect('/capacity-fields');
            return;
        }
        $field = $this->model->getFieldById($item['field_id']);

        $this->view('capacity-fields/edit-item', [
            'title' => 'Chỉnh sửa mục - Chứng chỉ năng lực',
            'page'  => 'capacity_fields',
            'item'  => $item,
            'field' => $field,
            'admin' => AuthMiddleware::getAdmin(),
        ]);
    }

    public function updateItem($itemId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/capacity-fields/items/edit/' . $itemId);
            return;
        }

        $item = $this->model->getItemById((int)$itemId);
        if (!$item) {
            $_SESSION['error'] = 'Không tìm thấy mục';
            $this->redirect('/capacity-fields');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        if (empty($name)) {
            $_SESSION['error'] = 'Vui lòng nhập tên mục';
            $this->redirect('/capacity-fields/items/edit/' . $itemId);
            return;
        }

        if ($this->model->updateItem((int)$itemId, [
            'name'       => $name,
            'rank'       => trim($_POST['rank']       ?? ''),
            'sort_order' => (int)($_POST['sort_order'] ?? 1),
            'status'     => (int)($_POST['status']     ?? 1),
        ])) {
            $_SESSION['success'] = 'Cập nhật mục thành công!';
            $this->redirect('/awards?tab=capacity');
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra, vui lòng thử lại';
            $this->redirect('/capacity-fields/items/edit/' . $itemId);
        }
    }

    // --------------------------------------------------------
    // DELETE — Item (soft delete)
    // --------------------------------------------------------

    public function deleteItem($itemId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/capacity-fields');
            return;
        }

        $item = $this->model->getItemById((int)$itemId);
        $fieldId = $item['field_id'] ?? null;

        if ($this->model->deleteItem((int)$itemId)) {
            $_SESSION['success'] = 'Đã xóa mục thành công';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa';
        }

        $this->redirect('/awards?tab=capacity');
    }
}
