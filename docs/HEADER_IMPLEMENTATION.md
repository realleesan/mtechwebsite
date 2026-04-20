# Header Implementation - MTech Website

## Tổng quan
Header đã được xây dựng hoàn chỉnh theo mẫu thiết kế từ `docs/template/_layout/code/header.html` và `docs/template/_layout/screen/header.png`.

## Các file đã tạo/cập nhật

### 1. Header PHP
- **File**: `app/views/_layout/header.php`
- **Chức năng**: 
  - Navigation menu với 6 mục chính: Home, About, Services, Projects, Blog, Contact
  - Dropdown menu cho Services, Projects, Blog
  - Active state tự động theo trang hiện tại
  - Responsive hamburger menu cho mobile

### 2. Header CSS
- **File**: `assets/css/header.css`
- **Màu sắc chính xác**:
  - Màu chữ gốc: `#fff` (trắng)
  - Màu hover/active: `#ffb600` (vàng)
  - Font: `Poppins, sans-serif` (500, 16px)
- **Responsive**:
  - Desktop: Menu ngang
  - Tablet (< 1280px): Padding điều chỉnh
  - Mobile (< 991px): Hamburger menu, sidebar slide-in

### 3. Header JavaScript
- **File**: `assets/js/header.js`
- **Chức năng**:
  - Toggle hamburger menu
  - Sticky header khi scroll
  - Dropdown toggle cho mobile
  - Close menu khi click outside
  - ESC key để đóng menu
  - Active menu highlight

### 4. Logo
- **File**: `docs/template/_layout/screen/logo_header.svg`
- **Ghi chú**: Logo placeholder, có thể thay thế bằng logo thực tế

### 5. Master Layout
- **File**: `app/views/_layout/master.php`
- **Cập nhật**:
  - Include header.php
  - Link header.css
  - Link header.js
  - Google Font Poppins

### 6. Index Router
- **File**: `index.php`
- **Cập nhật**:
  - Thêm routes cho blogs, projects, services
  - Thêm routes cho blog-details, project-details

## Cấu trúc Menu

```
Home (/)
About (?page=about)
Services (?page=services)
  ├─ All Services
  ├─ Web Development
  ├─ Mobile App
  ├─ Software Consulting
  └─ Cloud Solutions
Projects (?page=projects)
  ├─ All Projects
  ├─ Web Projects
  ├─ Mobile Projects
  └─ Project Details
Blog (?page=blogs)
  ├─ All Blogs
  ├─ Technology
  ├─ Business
  └─ Blog Details
Contact (?page=contact)
```

## Cách sử dụng

### Thay đổi Logo
Thay thế file `docs/template/_layout/screen/logo_header.svg` bằng logo thực tế của bạn.

### Thêm/Sửa Menu Items
Chỉnh sửa file `app/views/_layout/header.php`, tìm phần `<ul class="navbar-nav menu ml-auto">` và thêm/sửa các `<li>` items.

### Thay đổi màu sắc
Chỉnh sửa file `assets/css/header.css`:
- Màu chữ: `.menu > .nav-item > .nav-link { color: #fff; }`
- Màu hover: `.menu > .nav-item:hover > .nav-link { color: #ffb600; }`

### Tùy chỉnh Dropdown
Trong `app/views/_layout/header.php`, thêm/sửa các `<ul class="dropdown-menu">` bên trong `<li class="nav-item dropdown">`.

## Testing

1. Mở trình duyệt và truy cập: `http://localhost/mtechwebsite/`
2. Kiểm tra:
   - Menu hiển thị đúng
   - Hover vào menu items → màu vàng
   - Click vào dropdown → hiển thị submenu
   - Resize browser → hamburger menu xuất hiện
   - Click hamburger → sidebar menu slide in
   - Scroll trang → header sticky

## Responsive Breakpoints

- **Desktop**: >= 992px - Menu ngang đầy đủ
- **Tablet**: 768px - 991px - Menu ngang, padding nhỏ hơn
- **Mobile**: < 768px - Hamburger menu, sidebar

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Notes

- Header sử dụng `position: absolute` với class `menu_absolute`
- Khi scroll, thêm class `sticky` để fixed header
- Dropdown menu hiển thị khi hover (desktop) hoặc click (mobile)
- Mobile menu có overlay background khi mở
- Body scroll bị khóa khi mobile menu mở

## Tùy chỉnh nâng cao

### Thêm Search Icon
Thêm vào cuối `<ul class="navbar-nav menu ml-auto">`:
```html
<li class="nav-item search">
    <a href="#search" class="nav-link">
        <i class="fa fa-search"></i>
    </a>
</li>
```

### Thêm Phone Number
Thêm sau `</ul>` trong navbar-collapse:
```html
<a href="tel:1800685432" class="phone">1800 685 432</a>
```

Sau đó thêm CSS cho `.phone` trong `header.css`.

## Liên hệ

Nếu có vấn đề hoặc cần hỗ trợ, vui lòng liên hệ team phát triển.
