# 🎯 CÁC BƯỚC TIẾP THEO

## ✅ Đã hoàn thành:
- Header hiển thị đúng trên `test_header.html`
- CSS và JS đã hoạt động

## 📋 Bước tiếp theo:

### BƯỚC 1: Kiểm tra trang chính (index.php)

**Truy cập:**
```
http://your-domain.com/
```

**Kết quả mong đợi:**
- Header hiển thị giống `test_header.html`
- Logo hiển thị
- Menu ngang với styling đẹp
- Hover → chữ vàng
- Dropdown hoạt động

**Nếu KHÔNG hiển thị đúng:**
→ Xem file `CHECK_MAIN_PAGE.md` để debug

---

### BƯỚC 2: Tạo các trang còn thiếu

Hiện tại header có menu:
- Home ✅ (đã có)
- About ❌ (chưa có)
- Services ❌ (chưa có)
- Projects ❌ (chưa có)
- Blog ❌ (chưa có)
- Contact ❌ (chưa có)

**Cần tạo các file view:**

#### 1. About Page
```
app/views/about/about.php
```

#### 2. Services Page
```
app/views/services/services.php
```

#### 3. Projects Page
```
app/views/projects/projects.php
app/views/projects/details.php
```

#### 4. Blog Page
```
app/views/blogs/blogs.php
app/views/blogs/details.php
```

#### 5. Contact Page
```
app/views/contact/contact.php
```

---

### BƯỚC 3: Tùy chỉnh Header (nếu cần)

#### A. Thay đổi Logo
1. Chuẩn bị logo của bạn (PNG, SVG, JPG)
2. Upload vào: `docs/template/_layout/screen/`
3. Sửa file `header.php`:
```php
<img src="docs/template/_layout/screen/your-logo.png" alt="Your Logo">
```

#### B. Thay đổi Menu Items
Sửa file `app/views/_layout/header.php`:
- Thêm/xóa menu items
- Thay đổi tên menu
- Thay đổi URL

#### C. Thay đổi màu sắc
Sửa file `assets/css/header.css`:
```css
/* Màu chữ gốc */
.menu > .nav-item > .nav-link {
    color: #fff; /* Thay đổi màu này */
}

/* Màu hover */
.menu > .nav-item:hover > .nav-link {
    color: #ffb600; /* Thay đổi màu này */
}
```

#### D. Thêm số điện thoại
Trong `header.php`, thêm sau `</ul>`:
```html
<a href="tel:0123456789" class="phone-number">
    <i class="fa fa-phone"></i> 0123 456 789
</a>
```

Thêm CSS trong `header.css`:
```css
.phone-number {
    color: #fff;
    text-decoration: none;
    margin-left: 20px;
    font-weight: 500;
}
.phone-number:hover {
    color: #ffb600;
}
```

#### E. Thêm Search Icon
Trong `header.php`, thêm vào menu:
```html
<li class="nav-item search">
    <a class="nav-link" href="#search">
        <i class="fa fa-search"></i>
    </a>
</li>
```

---

### BƯỚC 4: Test Responsive

#### Desktop (> 992px)
- [ ] Menu hiển thị ngang
- [ ] Dropdown hoạt động khi hover
- [ ] Sticky header khi scroll

#### Tablet (768px - 991px)
- [ ] Menu vẫn hiển thị ngang
- [ ] Padding điều chỉnh

#### Mobile (< 768px)
- [ ] Hamburger icon hiển thị
- [ ] Click hamburger → sidebar menu
- [ ] Dropdown toggle khi click
- [ ] Close menu khi click outside

---

### BƯỚC 5: Tối ưu hóa

#### A. Minify CSS/JS (tùy chọn)
- Minify `header.css` → `header.min.css`
- Minify `header.js` → `header.min.js`

#### B. Lazy Load (tùy chọn)
- Lazy load logo
- Lazy load dropdown menu

#### C. Cache (tùy chọn)
Thêm cache headers trong `.htaccess`:
```apache
<FilesMatch "\.(css|js|png|jpg|jpeg|gif|svg)$">
    Header set Cache-Control "max-age=31536000, public"
</FilesMatch>
```

---

## 🎨 Tùy chỉnh nâng cao

### 1. Mega Menu (nếu cần)
Thay dropdown đơn giản bằng mega menu với nhiều cột.

### 2. Sticky Header với Animation
Thêm animation khi header chuyển từ transparent sang solid.

### 3. Multi-level Dropdown
Thêm dropdown con trong dropdown.

### 4. Language Switcher
Thêm nút chuyển ngôn ngữ.

### 5. Dark Mode Toggle
Thêm nút chuyển dark/light mode.

---

## 📚 Tài liệu tham khảo

- `HEADER_IMPLEMENTATION.md` - Tài liệu chi tiết về header
- `DEBUG_HEADER.md` - Hướng dẫn debug
- `CHECK_MAIN_PAGE.md` - Kiểm tra trang chính
- `QUICK_FIX.md` - Quick fixes

---

## 🆘 Cần hỗ trợ?

### Vấn đề thường gặp:

**1. Header không hiển thị trên trang chính**
→ Xem `CHECK_MAIN_PAGE.md`

**2. Dropdown không hoạt động**
→ Kiểm tra JavaScript có load không
→ Kiểm tra Console có lỗi không

**3. Responsive không hoạt động**
→ Kiểm tra viewport meta tag
→ Kiểm tra CSS media queries

**4. Logo không hiển thị**
→ Kiểm tra đường dẫn logo
→ Kiểm tra file có tồn tại không

**5. Màu sắc không đúng**
→ Kiểm tra CSS có load không
→ Kiểm tra CSS có bị override không

---

## ✅ Checklist hoàn thành

- [x] Header hiển thị trên test_header.html
- [ ] Header hiển thị trên trang chính
- [ ] Tạo các trang còn thiếu (About, Services, Projects, Blog, Contact)
- [ ] Thay logo thật
- [ ] Tùy chỉnh menu items
- [ ] Test responsive
- [ ] Test trên các browsers khác nhau
- [ ] Tối ưu hóa performance

---

**Bước tiếp theo ngay bây giờ:**
1. Truy cập `http://your-domain.com/`
2. Kiểm tra header có hiển thị đúng không
3. Nếu đúng → Tạo các trang còn thiếu
4. Nếu sai → Debug theo `CHECK_MAIN_PAGE.md`
