# MTech Admin Panel

Admin panel quản lý website MTech được xây dựng bằng PHP thuần, sử dụng kiến trúc MVC.

## 🚀 Tính năng

### ✅ Đã hoàn thành
- **Dashboard**: Thống kê tổng quan, dữ liệu mới nhất
- **Quản lý Liên hệ**: Xem, đánh dấu đã đọc, xóa
- **Quản lý Đơn ứng tuyển**: Xem, cập nhật trạng thái
- **Quản lý Đội ngũ**: CRUD đầy đủ cho team members
- **Authentication**: Đăng nhập, đăng xuất, quên mật khẩu
- **Routing**: Hệ thống routing linh hoạt với parameters
- **Database**: Kết nối PDO với error handling
- **Security**: Middleware authentication, input validation

### 🔄 Đang phát triển
- **Quản lý Blogs**: CRUD cho tin tức/bài viết
- **Quản lý Dự án**: CRUD cho portfolio projects  
- **Quản lý Dịch vụ**: CRUD cho categories/services
- **Quản lý Giải thưởng**: CRUD cho awards & certificates
- **Quản lý Logo đối tác**: CRUD cho client logos
- **Cài đặt Header/Footer**: Chỉnh sửa nội dung
- **Cài đặt chung**: Thông tin website

## 📁 Cấu trúc thư mục

```
admin.mtechwebsite/
├── app/
│   ├── controllers/          # Controllers xử lý logic
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── ContactsController.php
│   │   ├── JobApplicationsController.php
│   │   ├── TeamsController.php
│   │   └── ...
│   ├── middleware/           # Middleware xử lý request
│   │   └── AuthMiddleware.php
│   ├── models/              # Models xử lý database
│   │   ├── AuthModel.php
│   │   ├── ContactsModel.php
│   │   ├── TeamsModel.php
│   │   └── ...
│   ├── services/            # Services xử lý business logic
│   │   ├── EmailNotificationService.php
│   │   └── ValidationService.php
│   └── views/               # Views hiển thị giao diện
│       ├── _layout/
│       ├── auth/
│       ├── dashboard/
│       └── ...
├── core/                    # Core system files
│   ├── BaseController.php   # Base controller class
│   ├── database.php         # Database connection
│   └── router.php           # Routing system
├── public/                  # Public assets
│   ├── css/
│   ├── js/
│   └── images/
├── .env                     # Environment configuration
├── .htaccess               # Apache rewrite rules
├── index.php               # Application entry point
└── README.md               # This file
```

## 🛠️ Yêu cầu hệ thống

- **PHP**: >= 7.4
- **MySQL**: >= 5.7 hoặc MariaDB >= 10.2
- **Apache**: với mod_rewrite enabled
- **Extensions**: PDO, PDO_MySQL, mbstring, json

## ⚙️ Cài đặt local

1. **Clone/Copy project**
   ```bash
   cp -r admin.mtechwebsite /path/to/xampp/htdocs/
   ```

2. **Cấu hình database**
   - Tạo database MySQL
   - Cập nhật thông tin trong `.env`
   - Import schema từ `DEPLOYMENT_GUIDE.md`

3. **Truy cập**
   ```
   http://localhost/admin.mtechwebsite/
   ```

4. **Đăng nhập**
   - Username: `admin`
   - Password: `admin123`

## 🔧 Cấu hình

### Database (.env)
```env
DB_HOST=localhost
DB_NAME=mtech
DB_USER=root
DB_PASSWORD=
```

### Email SMTP (.env)
```env
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=your-email@gmail.com
SMTP_PASSWORD=your-app-password
```

## 🏗️ Kiến trúc

### MVC Pattern
- **Models**: Xử lý dữ liệu, database queries
- **Views**: Hiển thị giao diện người dùng
- **Controllers**: Xử lý logic, điều phối giữa Model và View

### Routing System
```php
// Định nghĩa routes trong core/router.php
$this->get('/teams', 'TeamsController@index');
$this->post('/teams/store', 'TeamsController@store');
$this->get('/teams/edit/{id}', 'TeamsController@edit');
```

### Authentication Middleware
```php
// Tự động kiểm tra đăng nhập cho tất cả routes (trừ auth)
AuthMiddleware::requireLogin();
```

## 📊 Database Schema

### Bảng chính
- `admins`: Tài khoản quản trị
- `contacts`: Liên hệ từ website
- `job_applications`: Đơn ứng tuyển
- `teams`: Thành viên đội ngũ
- `blogs`: Tin tức/bài viết
- `projects`: Dự án portfolio
- `categories`: Dịch vụ/danh mục
- `awards`: Giải thưởng/chứng chỉ
- `client_logos`: Logo đối tác

## 🔒 Bảo mật

- **Password hashing**: bcrypt
- **SQL injection**: Prepared statements
- **XSS protection**: Input sanitization
- **CSRF protection**: Session tokens
- **File access**: .htaccess restrictions
- **Authentication**: Session-based

## 🚀 Deployment

Xem chi tiết trong `DEPLOYMENT_GUIDE.md`

## 🐛 Debugging

### Error Logs
```php
// Bật error reporting trong index.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Database Debug
```php
// Trong models, errors được log tự động
error_log('Model error: ' . $e->getMessage());
```

## 📝 TODO

- [ ] Hoàn thiện CRUD cho tất cả modules
- [ ] Upload file/image handling
- [ ] Rich text editor cho content
- [ ] Image resizing/optimization
- [ ] Backup/restore database
- [ ] Activity logs
- [ ] Multi-language support
- [ ] API endpoints
- [ ] Unit tests

## 🤝 Đóng góp

1. Fork project
2. Tạo feature branch
3. Commit changes
4. Push to branch
5. Create Pull Request

## 📄 License

Private project - All rights reserved.