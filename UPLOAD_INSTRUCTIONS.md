# Hướng dẫn Upload Files lên Hosting

## ⚠️ Vấn đề hiện tại
File CSS không tồn tại trên hosting → Lỗi 404

## 📦 Danh sách files cần upload

### 1. Files CSS (Quan trọng nhất)
```
assets/css/header.css (9,086 bytes)
assets/css/header_simple.css (4,525 bytes)
```

### 2. Files JavaScript
```
assets/js/header.js
```

### 3. Files PHP
```
app/views/_layout/header.php
app/views/_layout/master.php (đã cập nhật)
app/views/home/home.php
index.php (đã cập nhật)
```

### 4. Files khác
```
docs/template/_layout/screen/logo_header.svg
test_header.html
```

## 🚀 Cách upload lên hosting

### Phương pháp 1: Sử dụng FTP Client (FileZilla, WinSCP)

#### Bước 1: Kết nối FTP
- Host: ftp.your-domain.com (hoặc IP hosting)
- Username: your-ftp-username
- Password: your-ftp-password
- Port: 21 (hoặc 22 cho SFTP)

#### Bước 2: Upload files
1. Mở FileZilla/WinSCP
2. Kết nối đến hosting
3. Navigate đến thư mục website (thường là `public_html` hoặc `www`)
4. Upload các files theo đúng cấu trúc:

```
public_html/
├── assets/
│   ├── css/
│   │   ├── header.css          ← Upload file này
│   │   └── header_simple.css   ← Upload file này
│   └── js/
│       └── header.js            ← Upload file này
├── app/
│   └── views/
│       ├── _layout/
│       │   ├── header.php       ← Upload file này
│       │   └── master.php       ← Upload file này (đã cập nhật)
│       └── home/
│           └── home.php         ← Upload file này
├── docs/
│   └── template/
│       └── _layout/
│           └── screen/
│               └── logo_header.svg  ← Upload file này
├── index.php                    ← Upload file này (đã cập nhật)
└── test_header.html             ← Upload file này
```

### Phương pháp 2: Sử dụng cPanel File Manager

#### Bước 1: Đăng nhập cPanel
- URL: https://your-domain.com:2083
- Hoặc: https://your-domain.com/cpanel

#### Bước 2: Mở File Manager
- Click vào "File Manager" trong cPanel
- Navigate đến thư mục website

#### Bước 3: Upload files
1. Click nút "Upload" ở góc trên
2. Chọn files từ máy tính:
   - `C:\xampp\htdocs\mtechwebsite\assets\css\header.css`
   - `C:\xampp\htdocs\mtechwebsite\assets\css\header_simple.css`
   - `C:\xampp\htdocs\mtechwebsite\assets\js\header.js`
   - Các files khác...
3. Upload vào đúng thư mục tương ứng

### Phương pháp 3: Sử dụng Git (nếu có)

```bash
# Commit changes
git add .
git commit -m "Add header implementation"

# Push to hosting
git push origin main
```

## ✅ Kiểm tra sau khi upload

### 1. Kiểm tra file CSS
Mở trình duyệt và truy cập:
```
http://your-domain.com/assets/css/header.css
```
**Kết quả mong đợi:** Hiển thị nội dung CSS (không phải 404)

### 2. Kiểm tra file JS
```
http://your-domain.com/assets/js/header.js
```
**Kết quả mong đợi:** Hiển thị nội dung JavaScript

### 3. Kiểm tra logo
```
http://your-domain.com/docs/template/_layout/screen/logo_header.svg
```
**Kết quả mong đợi:** Hiển thị logo SVG

### 4. Kiểm tra test page
```
http://your-domain.com/test_header.html
```
**Kết quả mong đợi:** Header hiển thị đẹp với styling

### 5. Kiểm tra trang chính
```
http://your-domain.com/
```
**Kết quả mong đợi:** Header hiển thị giống test page

## 🔧 Kiểm tra quyền files (nếu cần)

Sau khi upload, đảm bảo files có quyền đúng:

### Qua cPanel File Manager:
1. Right-click vào file
2. Chọn "Change Permissions"
3. Set permissions:
   - Files CSS/JS/PHP: 644 (rw-r--r--)
   - Folders: 755 (rwxr-xr-x)

### Qua SSH/Terminal:
```bash
# Set permissions cho files
chmod 644 assets/css/header.css
chmod 644 assets/css/header_simple.css
chmod 644 assets/js/header.js
chmod 644 app/views/_layout/header.php

# Set permissions cho folders
chmod 755 assets/css/
chmod 755 assets/js/
```

## 📝 Checklist Upload

- [ ] Upload `assets/css/header.css`
- [ ] Upload `assets/css/header_simple.css`
- [ ] Upload `assets/js/header.js`
- [ ] Upload `app/views/_layout/header.php`
- [ ] Upload `app/views/_layout/master.php`
- [ ] Upload `app/views/home/home.php`
- [ ] Upload `index.php`
- [ ] Upload `docs/template/_layout/screen/logo_header.svg`
- [ ] Upload `test_header.html`
- [ ] Kiểm tra URL: `http://your-domain.com/assets/css/header.css`
- [ ] Kiểm tra URL: `http://your-domain.com/test_header.html`
- [ ] Kiểm tra URL: `http://your-domain.com/`
- [ ] Clear browser cache (Ctrl+Shift+Delete)
- [ ] Test header trên desktop
- [ ] Test header trên mobile

## 🆘 Nếu vẫn lỗi 404 sau khi upload

### 1. Kiểm tra đường dẫn
Đảm bảo cấu trúc thư mục đúng:
```
public_html/assets/css/header.css
```
KHÔNG phải:
```
public_html/mtechwebsite/assets/css/header.css
```

### 2. Kiểm tra .htaccess
Đảm bảo file `.htaccess` không block CSS:
```apache
# Allow CSS files
<FilesMatch "\.(css|js|png|jpg|jpeg|gif|svg)$">
    Order allow,deny
    Allow from all
</FilesMatch>
```

### 3. Kiểm tra case-sensitive
Trên Linux hosting, tên file phân biệt hoa thường:
- `header.css` ≠ `Header.css`
- `assets` ≠ `Assets`

### 4. Clear server cache
Nếu hosting có cache, clear cache:
- cPanel: "Cache Manager" → Clear All
- Hoặc đợi 5-10 phút để cache tự clear

## 📞 Thông tin cần cung cấp nếu vẫn lỗi

1. URL của website
2. Screenshot của File Manager (cấu trúc thư mục)
3. Screenshot khi truy cập: `http://your-domain.com/assets/css/header.css`
4. Loại hosting (shared/VPS/dedicated)
5. Control panel (cPanel/Plesk/DirectAdmin)

## 💡 Tip: Upload nhanh qua ZIP

1. Nén các files cần upload thành ZIP:
   ```
   mtechwebsite_update.zip
   ├── assets/
   ├── app/
   ├── docs/
   ├── index.php
   └── test_header.html
   ```

2. Upload file ZIP lên hosting

3. Extract ZIP trên hosting (qua File Manager)

4. Kiểm tra lại cấu trúc thư mục

---

**Lưu ý quan trọng:** Sau khi upload xong, nhớ clear browser cache (Ctrl+Shift+Delete) trước khi test!
