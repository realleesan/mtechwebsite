# Kiểm tra Trang Chính

## ✅ Test header đã thành công!

Header hiển thị đúng trên `test_header.html` → CSS và JS đã hoạt động.

## 🔍 Bước tiếp theo: Kiểm tra trang chính

### 1. Truy cập trang chính
```
http://your-domain.com/
hoặc
http://your-domain.com/index.php
```

### 2. Các trường hợp có thể xảy ra:

#### ✅ Trường hợp 1: Header hiển thị đúng
→ **HOÀN THÀNH!** Không cần làm gì thêm.

#### ❌ Trường hợp 2: Trang trắng / Lỗi PHP
→ Kiểm tra lỗi PHP

**Cách kiểm tra:**
1. Mở DevTools (F12) → Tab Console
2. Xem có lỗi JavaScript không
3. Kiểm tra PHP error log

**Giải pháp:**
- Đảm bảo đã upload `index.php` mới
- Đảm bảo đã upload `master.php` mới
- Đảm bảo đã upload `header.php`

#### ❌ Trường hợp 3: Header không có styling (chỉ text)
→ CSS chưa được load

**Cách kiểm tra:**
1. View Page Source (Ctrl+U)
2. Tìm dòng: `<link rel="stylesheet" href="assets/css/header.css">`
3. Click vào link CSS, xem có load được không

**Giải pháp:**
- Clear browser cache (Ctrl+Shift+Delete)
- Thêm cache buster: `?v=123`
- Kiểm tra đường dẫn CSS

#### ❌ Trường hợp 4: Header hiển thị nhưng không giống test_header.html
→ Có thể do CSS bị conflict

**Giải pháp:**
- Kiểm tra có CSS khác đang override không
- Kiểm tra thứ tự load CSS

## 🛠️ Debug Steps

### Bước 1: View Page Source
```
Ctrl+U hoặc Right-click → View Page Source
```

Kiểm tra xem có các dòng sau không:
```html
<link rel="stylesheet" href="assets/css/header.css">
<script src="assets/js/header.js"></script>
```

### Bước 2: Check DevTools Network
```
F12 → Tab Network → Reload (Ctrl+R)
```

Tìm file `header.css`:
- Status: 200 (OK) ✅
- Status: 404 (Not Found) ❌
- Status: 304 (Not Modified) ✅

### Bước 3: Check Console
```
F12 → Tab Console
```

Xem có lỗi JavaScript không.

### Bước 4: Inspect Header Element
```
F12 → Tab Elements → Inspect header
```

Kiểm tra:
- Element `<header class="main_menu_area">` có tồn tại không
- CSS classes có được apply không
- Computed styles có đúng không

## 🔧 Quick Fixes

### Fix 1: Clear Cache
```
Ctrl+Shift+Delete → Clear cache
```

### Fix 2: Hard Reload
```
Ctrl+Shift+R (Chrome)
Cmd+Shift+R (Mac)
```

### Fix 3: Thêm Cache Buster
Trong `master.php`, thay:
```html
<link rel="stylesheet" href="assets/css/header.css">
```
Bằng:
```html
<link rel="stylesheet" href="assets/css/header.css?v=<?php echo time(); ?>">
```

### Fix 4: Kiểm tra đường dẫn
Thử đường dẫn tuyệt đối:
```html
<link rel="stylesheet" href="/assets/css/header.css">
```

## 📋 Checklist

- [ ] Truy cập `http://your-domain.com/`
- [ ] Header có hiển thị không?
- [ ] Header có styling không?
- [ ] Logo có hiển thị không?
- [ ] Menu items có hiển thị ngang không?
- [ ] Hover vào menu → chữ chuyển vàng?
- [ ] Hover vào Services/Projects/Blog → dropdown hiển thị?
- [ ] Scroll xuống → header chuyển nền đen?
- [ ] Resize browser → hamburger menu xuất hiện?
- [ ] Click hamburger → sidebar menu slide in?

## 🎯 Kết quả mong đợi

Trang chính phải hiển thị giống hệt `test_header.html`:
- ✓ Nền trong suốt ở top
- ✓ Nền đen khi scroll
- ✓ Chữ trắng, hover vàng #ffb600
- ✓ Dropdown menu đẹp
- ✓ Responsive mobile

## 📞 Nếu vẫn có vấn đề

Cung cấp:
1. URL trang chính
2. Screenshot trang chính
3. Screenshot DevTools → Network tab
4. Screenshot DevTools → Console tab
5. View Page Source (Ctrl+U) → Copy phần `<head>`
