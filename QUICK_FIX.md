# 🚀 QUICK FIX - Header không hiển thị

## ⚠️ Vấn đề: File CSS lỗi 404

### ✅ Giải pháp: Upload files lên hosting

## 📦 3 FILES QUAN TRỌNG NHẤT:

```
1. assets/css/header.css
2. assets/js/header.js  
3. app/views/_layout/header.php
```

## 🔥 CÁCH UPLOAD NHANH NHẤT:

### Qua cPanel File Manager:

1. **Đăng nhập cPanel** → Mở **File Manager**

2. **Upload file CSS:**
   - Navigate đến: `public_html/assets/css/`
   - Click **Upload**
   - Chọn file: `C:\xampp\htdocs\mtechwebsite\assets\css\header.css`
   - Upload

3. **Upload file JS:**
   - Navigate đến: `public_html/assets/js/`
   - Click **Upload**
   - Chọn file: `C:\xampp\htdocs\mtechwebsite\assets\js\header.js`
   - Upload

4. **Upload file header.php:**
   - Navigate đến: `public_html/app/views/_layout/`
   - Click **Upload**
   - Chọn file: `C:\xampp\htdocs\mtechwebsite\app\views\_layout\header.php`
   - Upload

5. **Upload các files còn lại:**
   - `app/views/_layout/master.php`
   - `app/views/home/home.php`
   - `index.php`
   - `test_header.html`
   - `docs/template/_layout/screen/logo_header.svg`

## ✅ KIỂM TRA:

### Bước 1: Test CSS
Mở trình duyệt:
```
http://your-domain.com/assets/css/header.css
```
**Phải thấy:** Nội dung CSS (KHÔNG phải 404)

### Bước 2: Test Header
```
http://your-domain.com/test_header.html
```
**Phải thấy:** Header đẹp với styling

### Bước 3: Test Trang chính
```
http://your-domain.com/
```
**Phải thấy:** Header giống test_header.html

## 🔄 Clear Cache:

**Browser:** Ctrl+Shift+Delete → Clear cache

**Hoặc thêm ?v=123:**
```
http://your-domain.com/?v=123
```

## 🆘 Vẫn không được?

### Plan B: Sử dụng CSS đơn giản

1. Upload thêm: `assets/css/header_simple.css`

2. Sửa file `master.php`, thay:
```html
<link rel="stylesheet" href="assets/css/header.css">
```
Bằng:
```html
<link rel="stylesheet" href="assets/css/header_simple.css">
```

3. Upload lại `master.php`

### Plan C: Đường dẫn tuyệt đối

Trong `master.php`, thay:
```html
<link rel="stylesheet" href="assets/css/header.css">
```
Bằng:
```html
<link rel="stylesheet" href="/assets/css/header.css">
```

## 📞 Cần hỗ trợ?

Cung cấp:
1. URL website
2. Screenshot khi truy cập: `http://your-domain.com/assets/css/header.css`
3. Screenshot File Manager (cấu trúc thư mục)

---

**TIP:** Upload file `test_header.html` trước để test nhanh!
