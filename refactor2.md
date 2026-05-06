# KẾ HOẠCH REFACTOR MVC SERVICE - GIAI ĐOẠN 2

## 📋 MỤC TIÊU
- Nâng cấp kiến trúc từ MVC cơ bản lên MVC Service hoàn chỉnh
- Tách bạch business logic khỏi Views và Index.php
- Đạt được separation of concerns 95%
- Code maintainable và scalable

## 🎯 PHẠM VI REFACTOR

### Vấn đề hiện tại:
1. **Views** chứa business logic (require_once Model, new Model())
2. **Index.php** chứa quá nhiều logic (AJAX handling, maintenance)
3. **Master Layout** chứa URL calculation logic
4. **Thiếu Services** cho các business logic phức tạp

### Mục tiêu:
- Tạo 8 Service mới
- Di chuyển logic từ Views → Services
- Di chuyển logic từ Index.php → Router/Controllers
- Implement Dependency Injection pattern

---

## 📁 CẤU TRÚC FOLDER MỚI

```
app/
├── controllers/          # (Không đổi)
├── models/             # (Không đổi)
├── services/           # ✅ Mở rộng
│   ├── ValidationService.php      # ✅ Có sẵn
│   ├── EmailNotificationService.php # ✅ Có sẵn
│   ├── UrlService.php            # ❌ Cần tạo
│   ├── HeaderService.php         # ❌ Cần tạo
│   ├── FooterService.php         # ❌ Cần tạo
│   ├── ContactService.php        # ❌ Cần tạo
│   ├── BlogService.php           # ❌ Cần tạo
│   ├── JobApplicationService.php  # ❌ Cần tạo
│   ├── MenuService.php           # ❌ Cần tạo
│   └── MaintenanceService.php    # ❌ Cần tạo
├── views/              # ✅ Clean up
│   └── _layout/
│       ├── master.php    # ✅ Clean logic
│       ├── header.php    # ✅ Clean logic
│       └── footer.php    # ✅ Clean logic
└── middleware/         # ✅ Mở rộng
    └── MaintenanceMiddleware.php # ❌ Cần tạo

core/
├── BaseController.php  # ✅ Cập nhật
├── router.php         # ✅ Cập nhật
└── ServiceContainer.php # ❌ Cần tạo
```

---

## 🚀 KẾ HOẠCH CÁC TASK

### **PHASE 1: FOUNDATION (Thứ tự ưu tiên cao nhất)**

#### Task 1.1: Tạo ServiceContainer
- **File:** `core/ServiceContainer.php`
- **Mục đích:** Quản lý dependency injection
- **Nội dung:**
  ```php
  class ServiceContainer {
      private static $services = [];
      
      public static function register($name, $callback) {
          self::$services[$name] = $callback;
      }
      
      public static function get($name) {
          if (!isset(self::$services[$name])) {
              throw new Exception("Service {$name} not found");
          }
          return self::$services[$name]();
      }
  }
  ```

#### Task 1.2: Tạo UrlService
- **File:** `app/services/UrlService.php`
- **Mục đích:** Xử lý URL generation và base URL calculation
- **Di chuyển logic từ:** `master.php:29-36`
- **Methods:**
  ```php
  public static function getBaseUrl()
  public static function asset($path)
  public static function route($name, $params = [])
  public static function getCurrentUrl()
  ```

#### Task 1.3: Cập nhật BaseController
- **File:** `core/BaseController.php`
- **Mục đích:** Thêm dependency injection support
- **Methods mới:**
  ```php
  protected function getService($name)
  protected function view($viewPath, $data = [])
  ```

---

### **PHASE 2: LAYER SEPARATION (Tách logic khỏi Views)**

#### Task 2.1: Tạo HeaderService
- **File:** `app/services/HeaderService.php`
- **Mục đích:** Xử lý toàn bộ logic header
- **Di chuyển logic từ:** `header.php:12-29`
- **Methods:**
  ```php
  public function getHeaderData()
  public function getMenuProjects()
  public function getMenuServices()
  public function getMenuBlogCategories()
  ```

#### Task 2.2: Tạo FooterService
- **File:** `app/services/FooterService.php`
- **Mục đích:** Xử lý toàn bộ logic footer
- **Di chuyển logic từ:** `footer.php:8-21`
- **Methods:**
  ```php
  public function getFooterData()
  public function getUsefulLinks()
  public function getSocialLinks()
  public function getCompanyInfo()
  ```

#### Task 2.3: Tạo MenuService
- **File:** `app/services/MenuService.php`
- **Mục đích:** Xử lý logic xây dựng menu động
- **Di chuyển logic từ:** HeaderService và FooterService
- **Methods:**
  ```php
  public function buildMainMenu()
  public function buildFooterMenu()
  public function getActiveMenuItems()
  ```

---

### **PHASE 3: BUSINESS LOGIC SERVICES**

#### Task 3.1: Tạo ContactService
- **File:** `app/services/ContactService.php`
- **Mục đích:** Xử lý contact form logic
- **Di chuyển logic từ:** `HomeController::contactSubmit()`
- **Methods:**
  ```php
  public function submitContact($data)
  public function validateContactData($data)
  public function saveContact($data)
  public function sendContactNotifications($data)
  ```

#### Task 3.2: Tạo BlogService
- **File:** `app/services/BlogService.php`
- **Mục đích:** Xử lý business logic của blogs
- **Di chuyển logic từ:** `BlogsModel` methods
- **Methods:**
  ```php
  public function isExpired($blog)
  public function getDaysRemaining($blog)
  public function isHiringOpen($blog)
  public function processBlogFilters($filters)
  public function generateBlogUrl($blog)
  ```

#### Task 3.3: Tạo JobApplicationService
- **File:** `app/services/JobApplicationService.php`
- **Mục đích:** Xử lý logic ứng tuyển công việc
- **Di chuyển logic từ:** `JobApplicationModel` file operations
- **Methods:**
  ```php
  public function submitApplication($data, $file)
  public function validateCV($file)
  public function uploadCV($file)
  public function processApplication($data)
  ```

---

### **PHASE 4: MAINTENANCE & MIDDLEWARE**

#### Task 4.1: Tạo MaintenanceService
- **File:** `app/services/MaintenanceService.php`
- **Mục đích:** Xử lý logic maintenance/coming soon
- **Di chuyển logic từ:** `index.php:121-149`
- **Methods:**
  ```php
  public function isMaintenanceMode()
  public function shouldRedirectToMaintenance($uri)
  public function getMaintenancePage()
  ```

#### Task 4.2: Tạo MaintenanceMiddleware
- **File:** `app/middleware/MaintenanceMiddleware.php`
- **Mục đích:** Middleware kiểm tra maintenance mode
- **Usage:** Đăng ký trong Router
- **Methods:**
  ```php
  public function handle($request, $next)
  public function isExcludedRoute($uri)
  ```

---

### **PHASE 5: CLEAN UP VIEWS**

#### Task 5.1: Clean master.php
- **File:** `app/views/_layout/master.php`
- **Mục đích:** Xóa business logic, chỉ còn rendering
- **Xóa:** Lines 29-36 (URL calculation)
- **Thay thế:** `UrlService::getBaseUrl()`

#### Task 5.2: Clean header.php
- **File:** `app/views/_layout/header.php`
- **Mục đích:** Xóa model instantiation, chỉ còn HTML
- **Xóa:** Lines 12-29 (Model loading)
- **Thay thế:** `$this->getService('HeaderService')->getHeaderData()`

#### Task 5.3: Clean footer.php
- **File:** `app/views/_layout/footer.php`
- **Mục đích:** Xóa model instantiation, chỉ còn HTML
- **Xóa:** Lines 8-21 (Model loading)
- **Thay thế:** `$this->getService('FooterService')->getFooterData()`

---

### **PHASE 6: UPDATE INDEX.PHP & ROUTER**

#### Task 6.1: Clean index.php
- **File:** `index.php`
- **Mục đích:** Chỉ còn bootstrapping
- **Xóa:** Lines 218-241 (AJAX handling)
- **Xóa:** Lines 121-149 (Maintenance logic)
- **Thay thế:** Router và Middleware

#### Task 6.2: Update Router
- **File:** `core/router.php`
- **Mục đích:** Thêm middleware support
- **Methods mới:**
  ```php
  public function addMiddleware($middleware)
  public function executeMiddlewares($request)
  ```

---

## 📅 LỊCH TRÌNH THỰC HIỆN

### **Tuần 1: Foundation**
- [ ] Day 1-2: Task 1.1, 1.2 (ServiceContainer, UrlService)
- [ ] Day 3-4: Task 1.3 (BaseController update)
- [ ] Day 5: Test foundation

### **Tuần 2: Layer Separation**
- [ ] Day 1-2: Task 2.1 (HeaderService)
- [ ] Day 3-4: Task 2.2 (FooterService)
- [ ] Day 5: Task 2.3 (MenuService)

### **Tuần 3: Business Logic**
- [ ] Day 1-2: Task 3.1 (ContactService)
- [ ] Day 3-4: Task 3.2 (BlogService)
- [ ] Day 5: Task 3.3 (JobApplicationService)

### **Tuần 4: Maintenance & Cleanup**
- [ ] Day 1-2: Task 4.1, 4.2 (Maintenance)
- [ ] Day 3-4: Task 5.1, 5.2, 5.3 (Views cleanup)
- [ ] Day 5: Task 6.1, 6.2 (Index & Router)

---

## ✅ KIỂM TRA HOÀN THÀNH

### **Criteria Checklist:**
- [ ] Không còn `require_once Model` trong Views
- [ ] Không còn `new Model()` trong Views
- [ ] Index.php chỉ còn bootstrap code
- [ ] Tất cả business logic nằm trong Services
- [ ] Controllers chỉ gọi Services, không gọi trực tiếp Models
- [ ] Dependency Injection hoạt động
- [ ] Middleware hoạt động
- [ ] Tests passed

### **Performance Metrics:**
- [ ] Page load time không tăng
- [ ] Memory usage giảm 10%
- [ ] Code maintainability score > 90%

---

## 🚨 RỦI RO & GIẢI PHÁP

### **Rủi ro 1: Downtime**
- **Giải pháp:** Implement trên branch, test kỹ trước merge

### **Rủi ro 2: Performance**
- **Giải pháp:** Profile mỗi service, optimize queries

### **Rủi ro 3: Complexity**
- **Giải pháp:** Document rõ ràng, code review

---

## 📚 TÀI LIỆU THAM KHẢO

### **Design Patterns:**
- Service Container Pattern
- Dependency Injection
- Middleware Pattern
- Repository Pattern (optional)

### **Best Practices:**
- SOLID Principles
- Clean Architecture
- PSR-12 Coding Standard

---

## 🎯 KẾT QUẢ DỰ KIẾN

### **Sau refactor:**
- **Separation of Concerns:** 95%
- **Code Reusability:** 90%
- **Maintainability:** 90%
- **Testability:** 85%
- **Performance:** Tốt hơn hoặc không đổi

### **Benefits:**
- Dễ maintain và extend
- Dễ unit test
- Code sạch và readable
- Architecture scalable

---

**Status:** Ready to implement
**Last Updated:** 2026-05-06
**Author:** Cascade AI Assistant
