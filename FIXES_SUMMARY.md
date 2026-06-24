# Khắc phục lỗi Blogs - Tóm tắt

## Ngày: 2026-06-25

### ✅ Vấn đề 1: Lỗi 500 khi xem chi tiết bài viết

**Triệu chứng:**
- Khi bấm vào chi tiết bài viết (`/blogs/view/{id}`), trang hiển thị lỗi 500

**Nguyên nhân:**
1. Hàm `viewBlog()` không có proper exception handling
2. Hàm `getBlogDetails()` có thể trả về `false` hoặc null mà không được xử lý
3. View `view.php` truy cập nhiều fields của `$blog` mà có thể undefined

**Sửa chữa:**
- ✅ Thêm try-catch trong `BlogsController::viewBlog()` (line 407-431)
- ✅ Cải thiện `getBlogDetails()` với exception handling cho cả PDOException và Throwable (line 690-701)
- ✅ Thêm `LIMIT 1` vào SQL query
- ✅ Thêm default values cho tất cả fields trong `view.php` (line 9-30)

**Files sửa:**
- `admin.mtechwebsite/app/controllers/BlogsController.php` (viewBlog, getBlogDetails)
- `admin.mtechwebsite/app/views/blogs/view.php` (initialization)

---

### ✅ Vấn đề 2: Filter danh mục bài viết bị mất

**Triệu chứng:**
- Trang danh sách blogs không hiển thị dropdown filter theo danh mục
- Phần filter danh mục như bị xoá

**Nguyên nhân:**
1. Hàm `index()` trong `BlogsController` gọi hai lần các hàm database:
   - Lần 1: `getBlogs()` (dành cho user page) + `getAllBlogCategories()`
   - Lần 2: `getAdminBlogs()` (dành cho admin) + `getAdminBlogCategories()`
2. Điều này làm ghi đè biến `$categories` và dữ liệu filter bị mất
3. Hơn nữa, `getBlogs()` không phù hợp cho admin interface vì nó filter `status = 1`

**Sửa chữa:**
- ✅ Loại bỏ lệnh gọi `getBlogs()` thừa
- ✅ Giữ lại chỉ một lệnh gọi `getAdminBlogs()` để lấy blogs
- ✅ Gọi `getAdminBlogCategories()` một lần duy nhất
- ✅ Gộp các debug logs lại một chỗ

**Files sửa:**
- `admin.mtechwebsite/app/controllers/BlogsController.php` (index method, line 36-65)

---

### ✅ Vấn đề 3: Undefined variables trong views

**Triệu chứng:**
- Có thể gặp PHP notices về undefined variables
- Một số fields hiển thị lỗi nếu dữ liệu không đầy đủ

**Sửa chữa:**
- ✅ Thêm default values initialization trong `blogs/index.php` (line 1-9)
- ✅ Thêm default values initialization trong `blogs/edit.php` (line 1-29)
- ✅ Thêm default values initialization trong `blogs/create.php` (line 1-5)
- ✅ Thêm comprehensive default values trong `blogs/view.php` (line 9-30)

**Files sửa:**
- `admin.mtechwebsite/app/views/blogs/index.php`
- `admin.mtechwebsite/app/views/blogs/edit.php`
- `admin.mtechwebsite/app/views/blogs/create.php`
- `admin.mtechwebsite/app/views/blogs/view.php`

---

## Kiểm tra và verify

✅ Controller `index()` - Loại bỏ duplicate calls, filter danh mục hoạt động
✅ Controller `viewBlog()` - Thêm exception handling toàn diện
✅ Model `getAdminBlogs()` - Logic filter category chính xác
✅ All views - Có default values cho tất cả fields

---

## Testing steps

1. **Danh sách blogs:**
   - Kiểm tra `/blogs` có hiển thị filter danh mục dropdown không
   - Chọn một danh mục rồi submit form - phải lọc đúng bài viết
   - Kiểm tra search functionality

2. **Xem chi tiết bài viết:**
   - Click vào icon "eye" để xem chi tiết bài viết
   - Trang phải load đúng không gặp lỗi 500
   - Kiểm tra tất cả thông tin hiển thị đúng (title, content, tags, meta info)

3. **Edit blog:**
   - Click vào icon "pencil" để chỉnh sửa bài viết
   - Form phải load đầy đủ dữ liệu cũ

---

## Notes

- Đã bảo tồn toàn bộ business logic
- Không làm thay đổi database schema
- Các thay đổi chỉ là cải thiện error handling và data validation
- Soft delete functionality vẫn giữ nguyên
- Recruitment fields handling vẫn giữ nguyên

---

## Phần còn có thể cải thiện thêm (không bắt buộc)

1. Thêm logging vào view files để track rendering issues
2. Tạo utility function cho việc set default values (DRY principle)
3. Thêm unit tests cho các getter/setter functions
4. Optimize SQL queries (có thể dùng eager loading cho tags)
