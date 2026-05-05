# KẾ HOẠCH REFACTOR CHUẨN MVC + SERVICE

> Ngày tạo: May 5, 2026
> Mục tiêu: Tách Controller và Service đang lẫn trong View và index.php, chuyển về đúng vị trí MVC

---

## TỔNG QUAN

**Tình trạng hiện tại:**
- ✅ Model: Đã tách tốt, chỉ chứa query database
- ❌ View: Đang chứa business logic (validation, gọi Model, gọi Service, AJAX handling)
- ❌ Controller: Gần như không tồn tại (4 file empty), logic nằm trong `index.php` (916 dòng)
- ⚠️ Service: `EmailNotificationService` tốt nhưng bị gọi từ View; 3 Service khác empty

**Kết quả sau refactor:**
- Model: Chỉ query DB
- View: Chỉ HTML
- Controller: Xử lý request, gọi Model + Service, trả response
- Service: Xử lý business logic (validation, email, auth)

---

## DANH SÁCH FILE CẦN TẠO MỚI

### 1. CORE LAYER (2 files)

#### `core/BaseController.php`
**Vị trí:** `d:\xampp\htdocs\mtechwebsite\core\BaseController.php`

**Mục đích:** Class cha cho tất cả controllers, cung cấp helper methods chung

**Methods cần có:**
- `view($viewPath, $data)` - Render view với data
- `json($data)` - Trả JSON response
- `redirect($url)` - Chuyển hướng
- `validate($rules)` - Validate input

**Tách từ:** Các đoạn code lặp lại trong View (output buffering, header JSON, error handling)

---

#### `core/Router.php`
**Vị trí:** `d:\xampp\htdocs\mtechwebsite\core\Router.php`

**Mục đích:** Thay thế switch-case 660 dòng trong `index.php`

**Chức năng:**
- Map URL → Controller::method
- Hỗ trợ GET, POST
- Trả 404 nếu không match

**Tách từ:** `index.php:256-916` (toàn bộ switch-case routing)

---

### 2. CONTROLLER LAYER (13 files)

#### `app/controllers/HomeController.php`
**Vị trí:** `d:\xampp\htdocs\mtechwebsite\app\controllers\HomeController.php`

**Methods:**
- `index()` - Trang chủ
  - **Tách từ:** `index.php:261-284`
  - Load: `CategoriesModel`, `ProjectsModel`, `ClientLogosModel`, `BlogsModel`
  - Trả biến: `$homeServices`, `$homeProjects`, `$clientLogos`, `$homeBlogs`
  
- `contactSubmit()` - AJAX form "Drop a Message"
  - **Tách từ:** `app/views/home/home.php:19-108`
  - Validate input, gọi `ContactsModel::create()`, gọi `EmailNotificationService`
  - Trả JSON

---

#### `app/controllers/ContactController.php`
**Vị trí:** `d:\xampp\htdocs\mtechwebsite\app\controllers\ContactController.php`

**Methods:**
- `index()` - Hiển thị form liên hệ
  - **Tách từ:** `index.php:610-617`
  - Chỉ set title, render view
  
- `submit()` - AJAX submit form
  - **Tách từ:** `app/views/contact/contact.php:7-145`
  - Validate (name, email, message)
  - Gọi `ContactsModel::create()`
  - Gọi `EmailNotificationService::sendContactConfirmation()` + `sendNewContactNotification()`
  - Trả JSON response

---

#### `app/controllers/TeamsController.php`
**Vị trí:** `d:\xampp\htdocs\mtechwebsite\app\controllers\TeamsController.php`

**Methods:**
- `index()` - Trang đội ngũ
  - **Tách từ:** `index.php:305-312`
  
- `submitQuestion()` - AJAX form question
  - **Tách từ:** `app/views/about/teams.php:7-105`
  - Validate (email, subject, message)
  - Gọi `ContactsModel::create()`
  - Gọi `EmailNotificationService::sendQuestionConfirmation()` + `sendNewQuestionNotification()`
  - Trả JSON

---

#### `app/controllers/BlogsController.php`
**Vị trí:** `d:\xampp\htdocs\mtechwebsite\app\controllers\BlogsController.php`

**Methods:**
- `index()` - Danh sách blog
  - **Tách từ:** `index.php:355-408`
  - Xử lý filter: `cat`, `cat_slug`, `tag`, `search`
  - Pagination: `p`, `perPage`
  - Load: blogs list, categories, recent blogs, all tags
  - Trả biến: `$blogs`, `$totalBlogs`, `$currentPage`, `$blogCategories`, etc.

---

#### `app/controllers/BlogController.php`
**Vị trí:** `d:\xampp\htdocs\mtechwebsite\app\controllers\BlogController.php`

**Methods:**
- `details($slug)` - Chi tiết blog
  - **Tách từ:** `index.php:413-459`
  - Lấy slug từ URL
  - Gọi `BlogsModel::getBlogDetailsBySlug()`
  - Nếu không found → 404
  - Gọi `BlogsModel::incrementViews()`
  - Load sidebar data: categories, recent blogs, tags
  - Trả biến: `$blogDetail`, `$blogCategories`, `$recentBlogs`, `$allTags`, `$hiringPositions`

---

#### `app/controllers/ProjectsController.php`
**Vị trí:** `d:\xampp\htdocs\mtechwebsite\app\controllers\ProjectsController.php`

**Methods:**
- `index()` - Trang dự án
  - **Tách từ:** `index.php:499-506`
  
- `details()` - Chi tiết dự án
  - **Tách từ:** `app/views/projects/projects.details.php:12-58`
  - Lấy slug hoặc id từ URL
  - Gọi `ProjectsModel::getBySlug()` hoặc `getById()`
  - Nếu không found → set `$projectNotFound = true`
  - Parse tags, result_items, format ngày
  - Trả biến: `$project`, `$tags`, `$resultItems`, `$detailImage`, etc.

---

#### `app/controllers/CategoriesController.php`
**Vị trí:** `d:\xampp\htdocs\mtechwebsite\app\controllers\CategoriesController.php`

**Methods:**
- `index()` - Trang danh mục dịch vụ
  - **Tách từ:** `index.php:523-529`
  
- `details($slug)` - Chi tiết category
  - **Tách từ:** `index.php:534-565`
  - Lấy slug từ URL
  - Gọi `CategoriesModel::getCategoryDetailBySlug()`
  - Nếu không found → 404
  - Trả biến: `$categoryDetail`, `$allCategories`, `$breadcrumbs`

---

#### `app/controllers/SearchController.php`
**Vị trí:** `d:\xampp\htdocs\mtechwebsite\app\controllers\SearchController.php`

**Methods:**
- `index()` - Tìm kiếm
  - **Tách từ:** `index.php:464-494`
  - Lấy query từ `$_GET['q']`
  - Filter type: `blog`, `service`, `project`
  - Pagination
  - Gọi `SearchModel::search()`
  - Trả biến: `$searchResults`, `$totalResults`, `$searchQuery`, `$searchType`

---

#### `app/controllers/AwardsController.php`
**Vị trí:** `d:\xampp\htdocs\mtechwebsite\app\controllers\AwardsController.php`

**Methods:**
- `index()` - Trang giải thưởng
  - **Tách từ:** `index.php:594-605`
  - Gọi `AwardsModel::getAllActive()`
  - Trả biến: `$awards`

---

#### `app/controllers/AboutController.php`
**Vị trí:** `d:\xampp\htdocs\mtechwebsite\app\controllers\AboutController.php`

**Methods:**
- `index()` - Trang giới thiệu
  - **Tách từ:** `index.php:289-300`
  - Gọi `ClientLogosModel::getAllActive()`
  - Trả biến: `$clientLogos`
  
- `companyHistory()` - Lịch sử công ty
  - **Tách từ:** `index.php:317-323`

---

#### `app/controllers/ComingSoonController.php`
**Vị trí:** `d:\xampp\htdocs\mtechwebsite\app\controllers\ComingSoonController.php`

**Methods:**
- `index()` - Trang coming soon
  - **Tách từ:** `app/views/comingsoon/comingsoon.php:12-14`
  - Gọi `ComingsoonModel::getSettings()`, `getTargetTimestamp()`
  - Trả biến: `$settings`, `$targetTimestamp`, etc.
  
- `subscribe()` - AJAX đăng ký nhận thông báo
  - **Tách từ:** `app/views/comingsoon/comingsoon.php:17-23`
  - Lấy email từ POST
  - Gọi `ComingsoonModel::saveSubscriber()`
  - Trả JSON

---

#### `app/controllers/AuthController.php`
**Vị trí:** `d:\xampp\htdocs\mtechwebsite\app\controllers\AuthController.php`

**Methods:**
- `login()` - Đăng nhập form
  - **Tách từ:** `index.php:622-637`
  
- `processLogin()` - Xử lý đăng nhập
  - Gọi `AuthService::login()`
  
- `register()` - Đăng ký form
  - **Tách từ:** `index.php:639-643`
  
- `processRegister()` - Xử lý đăng ký
  
- `forgot()` - Quên mật khẩu form
  - **Tách từ:** `index.php:645-649`
  
- `logout()` - Đăng xuất
  - **Tách từ:** `index.php:651-656`
  - Gọi `AuthService::logout()`

---

#### `app/controllers/UserController.php`
**Vị trí:** `d:\xampp\htdocs\mtechwebsite\app\controllers\UserController.php`

**Methods:**
- `dashboard()` - Dashboard
  - **Tách từ:** `index.php:671-675`
  
- `account()` - Thông tin tài khoản
  - **Tách từ:** `index.php:677-679`
  
- `orders()` - Đơn hàng
  - **Tách từ:** `index.php:681-683`
  
- `cart()` - Giỏ hàng
  - **Tách từ:** `index.php:685-687`
  
- `wishlist()` - Yêu thích
  - **Tách từ:** `index.php:689-691`

---

### 3. SERVICE LAYER (1 file mới, 3 file cần implement)

#### `app/services/ValidationService.php` ⭐ NEW
**Vị trí:** `d:\xampp\htdocs\mtechwebsite\app\services\ValidationService.php`

**Mục đích:** Tách validation logic khỏi View và Controller

**Tách từ:**
- `app/views/contact/contact.php:46-67` (validation rules)
- `app/views/home/home.php:39-51` (validation)
- `app/views/about/teams.php:31-50` (validation)

**Methods:**
- `make($data, $rules)` - Tạo validation mới
- `required($field)` - Kiểm tra bắt buộc
- `email($field)` - Kiểm tra email hợp lệ
- `min($field, $length)` - Kiểm tra độ dài tối thiểu
- `passes()` - Kiểm tra có pass hết không
- `errors()` - Lấy mảng lỗi
- `first()` - Lấy lỗi đầu tiên

**Usage ví dụ:**
```php
$validator = new ValidationService();
$validator->make($_POST, [
    'name' => 'required',
    'email' => 'required|email',
    'message' => 'required|min:10'
]);

if (!$validator->passes()) {
    return $this->json(['success' => false, 'errors' => $validator->errors()]);
}
```

---

#### `app/services/AuthService.php` - IMPLEMENT
**Vị trí:** `d:\xampp\htdocs\mtechwebsite\app\services\AuthService.php` (đang empty)

**Methods cần implement:**
- `login($email, $password)` - Xác thực, tạo session
- `logout()` - Hủy session
- `check()` - Kiểm tra đã đăng nhập chưa
- `user()` - Lấy thông tin user hiện tại
- `hashPassword($password)` - Hash password
- `verifyPassword($password, $hash)` - Verify password

---

#### `app/services/UserService.php` - IMPLEMENT
**Vị trí:** `d:\xampp\htdocs\mtechwebsite\app\services\UserService.php` (đang empty)

**Methods cần implement:**
- `getDashboard($userId)` - Data cho dashboard
- `getProfile($userId)` - Thông tin profile
- `updateProfile($userId, $data)` - Cập nhật profile
- `getOrders($userId)` - Danh sách đơn hàng

---

#### `app/services/AdminService.php` - IMPLEMENT
**Vị trí:** `d:\xampp\htdocs\mtechwebsite\app\services\AdminService.php` (đang empty)

**Methods cần implement:**
- Tùy theo chức năng admin

---

## FILE CẦN SỬA (KHÔNG TẠO MỚI)

### Views - XÓA business logic

| File | Xóa dòng | Giữ lại |
|------|----------|---------|
| `app/views/contact/contact.php` | 7-145 | HTML form từ dòng 145+ |
| `app/views/home/home.php` | 19-108 | HTML từ dòng 110+ |
| `app/views/about/teams.php` | 7-105 | HTML từ dòng 107+ |
| `app/views/comingsoon/comingsoon.php` | 12-14, 17-23 | HTML từ dòng 31+ |
| `app/views/projects/projects.details.php` | 12-58 | HTML từ dòng 58+ |
| `app/views/blogs/blogs.php` | 23-50 | Chuyển helper functions sang `core/helpers.php` |

### Models - Điều chỉnh nhỏ

| File | Sửa | Chi tiết |
|------|-----|----------|
| `app/models/ContactsModel.php` | Dòng 55-56 | Bỏ `$_SERVER` trực tiếp, nhận từ Controller qua `$data` |

### Core

| File | Sửa | Chi tiết |
|------|-----|----------|
| `core/helpers.php` | Thêm | Chuyển `format_date_vietnamese()` và `blogs_page_url()` từ `blogs.php` |

### Entry Point

| File | Sửa | Chi tiết |
|------|-----|----------|
| `index.php` | 256-916 | Bỏ toàn bộ switch-case, thay bằng `Router::dispatch()` |

---

## TỔNG KẾT

### Số lượng file

| Loại | Số lượng | Chi tiết |
|------|----------|----------|
| **Tạo mới** | 18 | 2 core + 13 controllers + 3 services |
| **Sửa lại** | 8 | 6 views + 1 model + 1 entry point |
| **Tổng cộng** | 26 | 18 tạo mới + 8 sửa lại |

### Thứ tự thực hiện đề xuất

1. **Phase 1:** Tạo `core/BaseController.php` và `core/Router.php`
2. **Phase 2:** Tạo `ValidationService.php` (cần cho các controller)
3. **Phase 3:** Tạo các đơn giản: `AwardsController`, `AboutController`, `ComingSoonController`
4. **Phase 4:** Tạo phức tạp: `ContactController`, `HomeController`, `TeamsController` (có AJAX)
5. **Phase 5:** Tạo còn lại: `BlogsController`, `BlogController`, `ProjectsController`, etc.
6. **Phase 6:** Sửa `index.php` để dùng Router thay vì switch-case
7. **Phase 7:** Dọn dẹp Views (xóa business logic)
8. **Phase 8:** Implement các Service empty
9. **Phase 9:** Test toàn bộ

---

## LƯU Ý QUAN TRỌNG

1. **Service gọi đúng chỗ:** `EmailNotificationService` đang bị gọi từ View → chuyển sang Controller
2. **Validation tách ra:** Không để validation trong View nữa
3. **Model không dùng `$_SERVER`:** Truyền qua parameter từ Controller
4. **Router xử lý AJAX:** Các route POST cho form submit cần được định nghĩa

---

## VÍ DỤ FLOW SAU REFACTOR

**Trước (sai):**
```
User → index.php → (switch-case) → contact.php 
                                      ↓
                              (validation, new Model, new Service)
                                      ↓
                                   JSON response
```

**Sau (đúng):**
```
User → index.php → Router → ContactController::submit()
                                      ↓
                         ValidationService.validate()
                                      ↓
                              ContactsModel.create()
                                      ↓
                         EmailNotificationService.send()
                                      ↓
                              JSON response
```
