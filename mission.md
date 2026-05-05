# PHÂN CÔNG NHIỆM VỤ REFACTOR MVC

> Thành viên: 3 người  
> Tổng số task: 27 (chia đều 9 task/người)  
> Thời gian dự kiến: 2-3 ngày  
> Reference: `refactor.md`

---

## THÀNH VIÊN A - INFRASTRUCTURE LEAD

> Nhiệm vụ: Xây dựng nền tảng core + controllers có AJAX phức tạp

### Task List (9 nhiệm vụ)

| # | File | Vị trí | Chi tiết | Độ phức tạp |
|---|------|--------|----------|-------------|
| 1 | `core/BaseController.php` | `core/BaseController.php` | Class cha với `view()`, `json()`, `redirect()`, `validate()` | Cao |
| 2 | `core/Router.php` | `core/Router.php` | Thay thế switch-case 660 dòng | Cao |
| 3 | `app/services/ValidationService.php` | `app/services/ValidationService.php` | Tách validation từ các View | Trung bình |
| 4 | `app/controllers/ContactController.php` | `app/controllers/ContactController.php` | `index()`, `submit()` - AJAX form | Cao |
| 5 | `app/controllers/TeamsController.php` | `app/controllers/TeamsController.php` | `index()`, `submitQuestion()` - AJAX form | Cao |
| 6 | `app/controllers/HomeController.php` | `app/controllers/HomeController.php` | `index()`, `contactSubmit()` - AJAX form | Cao |
| 7 | `index.php` modify | `index.php` | Bỏ switch-case, thêm `Router::dispatch()` | Cao |
| 8 | View cleanup | `app/views/contact/contact.php` | Xóa dòng 7-145 (logic) | Thấp |
| 9 | View cleanup | `app/views/about/teams.php` | Xóa dòng 7-105 (logic) | Thấp |

### Instructions chi tiết

**Task 1-2 (Core):**
- Tạo `BaseController` trước, đảm bảo các helper methods hoạt động
- Tạo `Router` sau, test với 1-2 route đơn giản trước
- **Blocking:** Các thành viên khác chờ file này xong mới bắt đầu controllers

**Task 3 (Service):**
- Tách validation từ `contact.php:46-67`, `home.php:39-51`, `teams.php:31-50`
- Methods: `make()`, `required()`, `email()`, `min()`, `passes()`, `errors()`

**Task 4-6 (Controllers - AJAX):**
- Kế thừa `BaseController`
- Chuyển toàn bộ logic POST handling từ View sang đây
- Giữ nguyên flow: validate → model → service → json response

**Task 7 (Entry Point):**
- Giữ phần error handler, session start
- Xóa switch-case 256-916
- Thay bằng: `require 'core/Router.php'; $router = new Router(); $router->dispatch();`
- **Coordination:** Làm sau khi Task 1-2 xong, trước khi B và C merge

**Task 8-9 (Cleanup):**
- Xóa PHP logic, chỉ giữ HTML
- Đảm bảo form action trỏ đúng route mới

---

## THÀNH VIÊN B - CONTENT CONTROLLERS LEAD

> Nhiệm vụ: Controllers cho content pages + cleanup views

### Task List (9 nhiệm vụ)

| # | File | Vị trí | Chi tiết | Độ phức tạp |
|---|------|--------|----------|-------------|
| 1 | `app/controllers/BlogsController.php` | `app/controllers/BlogsController.php` | `index()` - list, filter, pagination | Trung bình |
| 2 | `app/controllers/BlogController.php` | `app/controllers/BlogController.php` | `details($slug)` - chi tiết blog | Trung bình |
| 3 | `app/controllers/ProjectsController.php` | `app/controllers/ProjectsController.php` | `index()`, `details()` | Trung bình |
| 4 | `app/controllers/CategoriesController.php` | `app/controllers/CategoriesController.php` | `index()`, `details($slug)` | Trung bình |
| 5 | `app/controllers/SearchController.php` | `app/controllers/SearchController.php` | `index()` - tìm kiếm | Trung bình |
| 6 | `app/controllers/AwardsController.php` | `app/controllers/AwardsController.php` | `index()` - đơn giản | Thấp |
| 7 | `app/controllers/AboutController.php` | `app/controllers/AboutController.php` | `index()`, `companyHistory()` | Thấp |
| 8 | `app/controllers/ComingSoonController.php` | `app/controllers/ComingSoonController.php` | `index()`, `subscribe()` AJAX | Trung bình |
| 9 | View cleanup | `app/views/home/home.php`, `blogs.php`, `projects.details.php`, `comingsoon.php` | Xóa business logic | Thấp |

### Instructions chi tiết

**Task 1-7 (Controllers):**
- Chờ **Thành viên A** hoàn thành `BaseController.php` và `Router.php`
- Kế thừa `BaseController`
- Copy logic từ `index.php` switch-case sang
- Đơn giản: chỉ load model, lấy data, gọi view

**Task 8 (ComingSoonController):**
- Có AJAX nhẹ: `subscribe()` method
- Tách từ `comingsoon.php:17-23`

**Task 9 (Cleanup):**
- `home.php`: Xóa dòng 19-108 (AJAX handling)
- `blogs.php`: Xóa dòng 23-50 (helper functions → chuyển sang `core/helpers.php` nếu A chưa làm)
- `projects.details.php`: Xóa dòng 12-58 (model instantiation)
- `comingsoon.php`: Xóa dòng 12-14, 17-23

---

## THÀNH VIÊN C - SERVICES & HELPERS LEAD

> Nhiệm vụ: Service implementations + helpers + model fix + cleanup

### Task List (6 nhiệm vụ)

| # | File | Vị trí | Chi tiết | Độ phức tạp |
|---|------|--------|----------|-------------|
| 1 | `core/helpers.php` modify | `core/helpers.php` | Thêm `format_date_vietnamese()`, `blogs_page_url()` | Thấp |
| 2 | `app/models/ContactsModel.php` modify | `app/models/ContactsModel.php` | Dòng 55-56: bỏ `$_SERVER`, nhận từ parameter | Thấp |
| 3 | View cleanup | `app/views/blogs/blogs.php` | Xóa helper functions | Thấp |
| 4 | View cleanup | `app/views/projects/projects.details.php` | Hỗ trợ B xóa logic | Thấp |
| 5 | Testing | - | Test các form AJAX sau khi A hoàn thành | - |
| 6 | Code review | - | Review code A và B trước khi merge | - |

### Phase 2 (Sau này - không làm ngay)
- AuthController, UserController - chỉ khi cần login
- AuthService, UserService, AdminService - chỉ khi cần auth

### Instructions chi tiết

**Task 1 (Helpers):**
- Chuyển từ `blogs.php:23-50`
- `format_date_vietnamese()` - dùng cho hiển thị ngày
- `blogs_page_url()` - build URL pagination

**Task 2 (Model fix):**
- Dòng 55-56: Thay `$_SERVER['REMOTE_ADDR']` → `$data['ip_address']`
- Controller sẽ truyền `ip_address` vào

**Task 3-4 (Cleanup):**
- Xóa các function trong view, gọi helper thay thế
- Hỗ trợ B dọn dẹp `projects.details.php`

**Task 5-6 (Testing & Review):**
- Test các form contact, home, teams sau khi A hoàn thành
- Review code của A và B trước khi merge vào master

---

## TIMELINE & MILESTONES

### Ngày 1
| Giờ | Hoạt động | Người phụ trách |
|-----|-----------|-----------------|
| Buổi sáng | **A** tạo BaseController + Router (Task 1-2) | A |
| Buổi sáng | **B** chuẩn bị cấu trúc controllers (research) | B |
| Buổi sáng | **C** làm helpers + model fix (Task 1-2) | C |
| Buổi chiều | A hoàn thành ValidationService + ContactController | A |
| Buổi chiều | B bắt đầu BlogsController, ProjectsController | B |
| Buổi chiều | C hỗ trợ test + review code | C |

**Milestone Day 1:** A có BaseController + Router chạy được

### Ngày 2
| Giờ | Hoạt động | Người phụ trách |
|-----|-----------|-----------------|
| Buổi sáng | **Merge A vào master** - B và C pull về | All |
| Buổi sáng | A hoàn thành TeamsController + HomeController | A |
| Buổi sáng | B tiếp tục controllers còn lại | B |
| Buổi sáng | C cleanup views + test | C |
| Buổi chiều | A sửa index.php để dùng Router | A |
| Buổi chiều | B hoàn thành tất cả controllers | B |
| Buổi chiều | C final review + test | C |

**Milestone Day 2:** Tất cả controllers và services hoàn thành

### Ngày 3
| Giờ | Hoạt động | Người phụ trách |
|-----|-----------|-----------------|
| Buổi sáng | **Integration testing** - chạy thử từng trang | All |
| Buổi sáng | Fix bugs phát sinh | All |
| Buổi chiều | **Final review** - đảm bảo không còn logic trong View | All |
| Buổi chiều | Merge toàn bộ vào master | A |

**Milestone Day 3:** Project chạy ổn định với MVC mới

---

## GIT WORKFLOW

### Branching Strategy
```
master (stable)
  ├── refactor-2026 (integration branch)
        ├── member-a/core           (Thành viên A)
        ├── member-b/controllers    (Thành viên B)
        └── member-c/auth-services  (Thành viên C)
```

### Quy tắc
1. **A tạo branch đầu tiên:** `member-a/core` từ `master`
2. **A merge vào refactor-2026** khi BaseController + Router xong
3. **B và C pull từ refactor-2026** để có BaseController
4. **Mỗi ngày merge vào refactor-2026** để tránh conflict
5. **Không push lên master** cho đến Day 3 khi test xong

### Commit message format
```
[A] Add BaseController with view() and json() helpers
[B] Add BlogsController with filter and pagination
[C] Implement AuthService login and logout methods
```

---

## CHECKLIST TRƯỚC KHI MERGE

### Mỗi thành viên tự check
- [ ] Code chạy không lỗi syntax
- [ ] Không còn `new Model()` trong View
- [ ] Không còn `$_POST` handling trong View
- [ ] Controllers kế thừa BaseController
- [ ] Services gọi đúng chỗ (từ Controller, không từ View)

### Integration check (cả team)
- [ ] Trang chủ load được
- [ ] Form contact submit AJAX hoạt động
- [ ] Trang blog list + detail hoạt động
- [ ] Trang đội ngũ + form question hoạt động
- [ ] Không còn business logic trong bất kỳ View nào

---

## RỦI RO & GIẢI PHÁP

| Rủi ro | Khả năng | Giải pháp |
|--------|----------|-----------|
| A chậm BaseController | Cao | B và C có thể tạo controller skeleton không kế thừa, refactor sau |
| Conflict khi merge | Trung bình | Mỗi ngày merge 1 lần, tránh để quá lâu |
| AJAX form không chạy | Trung bình | Test kỹ các form sau khi chuyển sang Controller |
| Router miss route | Thấp | Định nghĩa rõ GET vs POST, test từng route |

---

## LIÊN LẠC & HỖ TRỢ

- **Daily standup:** 9:00 AM mỗi ngày, 15 phút
- **Communication:** Slack/Discord group
- **Code review:** Pull request trên GitHub, ít nhất 1 người review trước khi merge
- **Blocker:** Nếu stuck > 30 phút, hỏi team ngay

---

## SUMMARY

| Thành viên | Focus | Số task | Độ khó trung bình |
|------------|-------|---------|-------------------|
| **A** | Infrastructure + AJAX Controllers | 9 | Cao |
| **B** | Content Controllers | 9 | Trung bình |
| **C** | Helpers + Testing + Review | 6 | Thấp |

**Key principle:** A là blocker cho toàn team → ưu tiên hoàn thành BaseController + Router trước end of Day 1.

---

*Generated: May 5, 2026*  
*Reference: refactor.md*
