# MTech Website Project

Dự án website tổng thể cho công ty MTech, bao gồm cả frontend (user) và backend (admin panel).

## 🏗️ Cấu trúc Project

```
mtechwebsite/
├── admin.mtechwebsite/     # Admin Panel (Backend)
│   ├── app/               # Controllers, Models, Views
│   ├── assets/            # CSS, JS, Images cho admin
│   ├── core/              # Core system files
│   ├── .env.example       # Environment config template
│   └── README.md          # Admin documentation
├── user.metchwebsite/      # User Frontend
│   ├── app/               # Controllers, Models, Views
│   ├── assets/            # CSS, JS, Images cho frontend
│   ├── core/              # Core system files
│   ├── .env.example       # Environment config template
│   └── docs/              # Documentation
└── README.md              # This file
```

## 🚀 Tính năng

### 👥 User Frontend (user.metchwebsite)
- **Trang chủ**: Giới thiệu công ty, dịch vụ
- **Dịch vụ**: Chi tiết các dịch vụ tư vấn
- **Dự án**: Portfolio các dự án đã thực hiện
- **Tin tức**: Blog/tin tức công ty
- **Liên hệ**: Form liên hệ, thông tin công ty
- **Tuyển dụng**: Đăng tin tuyển dụng, nhận CV

### 🔧 Admin Panel (admin.mtechwebsite)
- **Dashboard**: Thống kê tổng quan
- **Quản lý Liên hệ**: Xem, trả lời liên hệ từ website
- **Quản lý Đơn ứng tuyển**: Xem, duyệt CV ứng viên
- **Quản lý Nội dung**: CRUD cho blogs, dự án, dịch vụ
- **Quản lý Đội ngũ**: Thông tin team members
- **Cài đặt**: Cấu hình website

## ⚙️ Yêu cầu hệ thống

- **PHP**: >= 7.4
- **MySQL**: >= 5.7 hoặc MariaDB >= 10.2
- **Apache**: với mod_rewrite enabled
- **Extensions**: PDO, PDO_MySQL, mbstring, json, curl

## 🛠️ Cài đặt

### 1. Clone Repository
```bash
git clone https://github.com/yourusername/mtechwebsite.git
cd mtechwebsite
```

### 2. Cấu hình Database
- Tạo database MySQL
- Import schema từ `database/` folder

### 3. Cấu hình Environment

#### Admin Panel:
```bash
cd admin.mtechwebsite
cp .env.example .env
# Chỉnh sửa .env với thông tin database và email
```

#### User Frontend:
```bash
cd user.metchwebsite
cp .env.example .env
# Chỉnh sửa .env với thông tin database và services
```

### 4. Cấu hình Web Server
- Point domain chính đến `user.metchwebsite/`
- Point subdomain admin đến `admin.mtechwebsite/`

## 🌐 Deployment

### Production URLs:
- **Frontend**: `https://truongvinalogistics.com.vn/`
- **Admin Panel**: `https://admin.truongvinalogistics.com.vn/`

### Hosting: InfinityFree
- Database: `if0_41698410_mtech`
- Host: `sql213.infinityfree.com`

## 🔐 Bảo mật

- ✅ Environment variables cho sensitive data
- ✅ SQL injection protection (PDO prepared statements)
- ✅ XSS protection
- ✅ CSRF protection
- ✅ Input validation & sanitization
- ✅ Secure password hashing

## 📁 Cấu trúc Database

### Bảng chính:
- `admins`: Tài khoản quản trị
- `contacts`: Liên hệ từ website
- `job_applications`: Đơn ứng tuyển
- `blogs`: Tin tức/bài viết
- `projects`: Dự án portfolio
- `categories`: Dịch vụ/danh mục
- `teams`: Thành viên đội ngũ
- `awards`: Giải thưởng/chứng chỉ
- `client_logos`: Logo đối tác

## 🔧 Development

### Git Workflow:
```bash
# Tạo feature branch
git checkout -b feature/new-feature

# Commit changes
git add .
git commit -m "Add new feature"

# Push to GitHub
git push origin feature/new-feature

# Create Pull Request
```

### Coding Standards:
- **PHP**: PSR-4 autoloading, PSR-12 coding style
- **HTML**: Semantic markup, accessibility compliant
- **CSS**: BEM methodology, responsive design
- **JavaScript**: ES6+, modular approach

## 📚 Documentation

- [Admin Panel Documentation](admin.mtechwebsite/README.md)
- [User Frontend Documentation](user.metchwebsite/docs/)
- [API Documentation](docs/api.md)
- [Deployment Guide](docs/deployment.md)

## 🤝 Contributing

1. Fork the project
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is proprietary software. All rights reserved.

## 📞 Support

- **Email**: baominhkpkp@gmail.com
- **Website**: https://truongvinalogistics.com.vn/
- **Admin Panel**: https://admin.truongvinalogistics.com.vn/

---

**Developed with ❤️ for MTech Company**