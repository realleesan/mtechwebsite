# Hướng Dẫn Pull Code Từ Hosting Về Local + Merge

## ⚠️ Lưu ý Quan Trọng
Khi pull từ hosting về, có thể có conflicts vì:
- Local: Bạn đã thêm Projects page
- Hosting: Thành viên khác đã thêm Categories page
- Cả 2 đều sửa `master.php`, `index.php`

## 📋 Quy Trình 3 Bước

### Bước 1: Backup Local Changes
```bash
# 1. Commit hoặc stash thay đổi local hiện tại
git add -A
git commit -m "Backup: local changes before pulling from hosting"

# Hoặc tạo branch backup
git checkout -b backup-local-$(date +%Y%m%d)
git checkout master
```

### Bước 2: Tạo Script Pull Từ Hosting

**Cách 1: Dùng FTP Download (Khuyên dùng)**
```bash
# Tạo folder temp để chứa file từ hosting
mkdir -p /c/temp/hosting-pull
cd /c/temp/hosting-pull

# Dùng FileZilla hoặc WinSCP để download tất cả files từ:
# Remote: /public_html/
# → Local: C:\temp\hosting-pull\
```

**Cách 2: Dùng cPanel Backup**
```bash
# 1. Đăng nhập cPanel
# 2. File Manager → Select All → Compress (zip)
# 3. Download zip về
# 4. Giải nén vào C:\temp\hosting-pull\
```

### Bước 3: So Sánh và Merge

```bash
# Vào folder project
cd d:\xampp\htdocs\mtechwebsite

# So sánh từng file quan trọng
diff -u app/views/_layout/master.php /c/temp/hosting-pull/app/views/_layout/master.php
diff -u index.php /c/temp/hosting-pull/index.php

# Hoặc dùng VS Code để compare
# Right-click file → "Select for Compare"
```

## 🔍 Files Cần Chú Ý (Dễ Conflict)

| File | Local Change | Hosting Change |
|------|-------------|----------------|
| `master.php` | + case 'projects' CSS/JS | + case 'categories' CSS/JS |
| `index.php` | + route 'projects' | + route 'categories' |
| `breadcrumb.php` | Đã tạo mới | Có thể hosting có version khác |

## ✅ Chiến Lược Merge An Toàn

### Phương án A: Merge từng file (Khuyên dùng)

```bash
# 1. Giữ nguyên file trên hosting cho các file không quan trọng
cp /c/temp/hosting-pull/app/views/categories/categories.php app/views/categories/

# 2. Merge thủ công các file quan trọng (dùng VS Code)
# - Mở cả 2 version
# - Copy paste phần cần giữ từ cả 2

# 3. Đặc biệt chú ý master.php:
# - Giữ cả 'projects' và 'categories' cases
# - CSS section và JS section đều phải có cả 2
```

### Phương án B: Dùng git để merge

```bash
# 1. Tạo commit từ hosting files
cd /c/temp/hosting-pull
git init
git add -A
git commit -m "Hosting version"

# 2. Add làm remote cho local repo
cd d:\xampp\htdocs\mtechwebsite
git remote add hosting /c/temp/hosting-pull/.git

# 3. Fetch và merge
git fetch hosting
git merge hosting/master --allow-unrelated-histories

# 4. Resolve conflicts nếu có
```

## 🛠️ Công Cụ Hỗ Trợ

### 1. VS Code Compare
```
1. Mở file local (master.php)
2. Ctrl+Shift+P → "File: Compare Active File With..."
3. Chọn file từ hosting folder
4. Xem diff và merge
```

### 2. WinMerge (Miễn phí)
```bash
# Tải WinMerge
# File → Open → Chọn 2 file/folder để so sánh
```

### 3. Command line diff
```bash
# So sánh 2 file
diff file1.php file2.php

# So sánh 2 folder
diff -r folder1/ folder2/
```

## 🎯 Checklist Sau Merge

- [ ] `master.php` có cả case 'projects' và 'categories'?
- [ ] `index.php` có cả route 'projects' và 'categories'?
- [ ] `categories.php` đã đầy đủ nội dung (không 0 bytes)?
- [ ] Test `?page=projects` hoạt động
- [ ] Test `?page=categories` hoạt động
- [ ] Commit merge result
- [ ] Push lên GitHub
- [ ] Upload lại lên hosting nếu cần

## 🆘 Nếu Bị Lỗi Sau Merge

### Lỗi: Conflicts chưa resolved
```bash
git status
# Xem file nào còn conflict
# Edit file → Tìm <<<<< ===== >>>>> và sửa
# Sau đó:
git add <file>
git commit
```

### Lỗi: Mất code sau merge
```bash
# Khôi phục từ backup branch
git checkout backup-local-[ngày tháng] -- <file cần khôi phục>
```

### Lỗi: Hosting không đồng bộ với local
```bash
# Re-upload tất cả files đã merge
# Hoặc dùng rsync nếu có SSH access
```
