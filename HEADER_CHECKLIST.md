# Header Implementation Checklist

## ✅ Checklist kiểm tra Header

### 1. Files đã tạo
- [ ] `app/views/_layout/header.php` - File header PHP
- [ ] `assets/css/header.css` - File CSS chính
- [ ] `assets/css/header_simple.css` - File CSS đơn giản (backup)
- [ ] `assets/js/header.js` - File JavaScript
- [ ] `docs/template/_layout/screen/logo_header.svg` - Logo
- [ ] `test_header.html` - File test độc lập

### 2. Files đã cập nhật
- [ ] `app/views/_layout/master.php` - Đã include header.php và CSS/JS
- [ ] `index.php` - Đã thêm routes cho blogs, projects, services
- [ ] `app/views/home/home.php` - Trang home demo

### 3. Kiểm tra trên hosting

#### A. Upload files
```bash
# Đảm bảo các file sau đã được upload:
assets/css/header.css
assets/css/header_simple.css
assets/js/header.js
app/views/_layout/header.php
docs/template/_layout/screen/logo_header.svg
test_header.html
```

#### B. Kiểm tra quyền files
```bash
# Trên Linux hosting
chmod 644 assets/css/header.css
chmod 644 assets/js/header.js
chmod 644 app/views/_layout/header.php
```

#### C. Test từng bước

**Bước 1: Test file CSS có load được không**
```
Mở: http://your-domain.com/assets/css/header.css
Kết quả mong đợi: Hiển thị nội dung CSS
```

**Bước 2: Test file test_header.html**
```
Mở: http://your-domain.com/test_header.html
Kết quả mong đợi: Header hiển thị đúng với styling
```

**Bước 3: Test trang chính**
```
Mở: http://your-domain.com/
hoặc: http://your-domain.com/index.php
Kết quả mong đợi: Header hiển thị giống test_header.html
```

### 4. Debug với Browser DevTools

#### Mở DevTools (F12)

**Tab Network:**
- [ ] File `header.css` có status 200 (OK)
- [ ] File `header.js` có status 200 (OK)
- [ ] File `logo_header.svg` có status 200 (OK)

**Tab Console:**
- [ ] Không có lỗi JavaScript
- [ ] Không có lỗi CSS

**Tab Elements:**
- [ ] Element `<header class="main_menu_area">` tồn tại
- [ ] Element `<nav class="menu_absolute">` tồn tại
- [ ] Element `<ul class="navbar-nav menu">` tồn tại
- [ ] Các class CSS được apply đúng

### 5. Test chức năng

#### Desktop (> 992px)
- [ ] Header hiển thị ngang
- [ ] Logo hiển thị
- [ ] Menu items hiển thị ngang
- [ ] Chữ màu trắng (#fff)
- [ ] Hover vào menu → chữ chuyển vàng (#ffb600)
- [ ] Hover vào Services/Projects/Blog → dropdown hiển thị
- [ ] Dropdown có nền đen, border vàng ở top
- [ ] Hover vào dropdown item → chữ vàng, background nhạt
- [ ] Scroll xuống → header chuyển nền đen
- [ ] Header sticky (fixed) khi scroll

#### Mobile (< 992px)
- [ ] Hamburger icon hiển thị
- [ ] Click hamburger → sidebar menu slide in từ phải
- [ ] Menu items hiển thị dọc
- [ ] Click menu item → dropdown toggle
- [ ] Click outside → menu đóng

### 6. Nếu header không hiển thị đúng

#### Thử 1: Sử dụng CSS đơn giản
Trong `master.php`, thay:
```html
<link rel="stylesheet" href="assets/css/header.css">
```
Bằng:
```html
<link rel="stylesheet" href="assets/css/header_simple.css">
```

#### Thử 2: Sử dụng đường dẫn tuyệt đối
```html
<link rel="stylesheet" href="/assets/css/header.css">
```

#### Thử 3: Thêm cache buster
```html
<link rel="stylesheet" href="assets/css/header.css?v=<?php echo time(); ?>">
```

#### Thử 4: Inline CSS
Thêm vào `<head>` của master.php:
```html
<style>
    body { padding-top: 80px; }
    .menu_absolute { 
        position: fixed; 
        top: 0; 
        left: 0; 
        width: 100%; 
        background: transparent; 
        z-index: 9999; 
        transition: all 0.3s; 
    }
    .menu_absolute.sticky { background: #000; }
    .menu > .nav-item > .nav-link { 
        color: #fff !important; 
        font-family: 'Poppins', sans-serif; 
    }
    .menu > .nav-item:hover > .nav-link { color: #ffb600 !important; }
</style>
```

### 7. Thông tin cần cung cấp nếu vẫn lỗi

- [ ] URL của website
- [ ] Screenshot của trang hiện tại
- [ ] Screenshot của DevTools → Network tab
- [ ] Screenshot của DevTools → Console tab
- [ ] Screenshot của DevTools → Elements tab (inspect header)
- [ ] Thông tin hosting:
  - [ ] Loại server (Apache/Nginx)
  - [ ] PHP version
  - [ ] Có sử dụng .htaccess không
  - [ ] Có sử dụng cache không

### 8. Kết quả mong đợi

Header phải giống như trong `docs/template/_layout/screen/header.png`:
- ✓ Nền trong suốt ở top
- ✓ Nền đen khi scroll
- ✓ Chữ trắng, hover vàng
- ✓ Dropdown menu đẹp
- ✓ Responsive mobile

## Quick Fix Commands

```bash
# 1. Upload lại tất cả files
# Sử dụng FTP/SFTP client để upload:
# - assets/css/header.css
# - assets/js/header.js
# - app/views/_layout/header.php

# 2. Clear cache
# Trong browser: Ctrl+Shift+Delete

# 3. Restart server (nếu có quyền)
sudo service apache2 restart
# hoặc
sudo service nginx restart

# 4. Check file permissions
chmod -R 755 assets/
chmod 644 assets/css/*.css
chmod 644 assets/js/*.js
```

## Liên hệ hỗ trợ

Nếu làm theo checklist mà vẫn không được, cung cấp:
1. Kết quả của từng bước trong checklist
2. Screenshots của DevTools
3. URL website để kiểm tra trực tiếp
