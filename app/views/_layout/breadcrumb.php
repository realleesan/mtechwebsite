<?php
/**
 * Breadcrumb Configuration
 * 
 * Tập trung toàn bộ cấu hình breadcrumb cho tất cả các trang.
 * Mỗi key tương ứng với giá trị $_GET['page'].
 * 
 * Cấu trúc mỗi item:
 *   ['title' => string, 'url' => string|null]
 *   - url = null  → item cuối (active, không có link)
 *   - url = string → item có link
 * 
 * Sử dụng trong index.php:
 *   require_once 'app/views/breadcrumb.php';
 *   $breadcrumbs = get_breadcrumbs($page);
 */

/**
 * Trả về mảng breadcrumb cho trang được chỉ định.
 * Item đầu tiên (Home) được tự động thêm bởi pageheader.php,
 * nên chỉ cần khai báo từ item thứ 2 trở đi.
 *
 * @param  string      $page   Giá trị $_GET['page']
 * @param  array       $params Tham số bổ sung (id, slug, title động, ...)
 * @return array|null          Mảng breadcrumb hoặc null nếu không cần
 */
function get_breadcrumbs(string $page, array $params = []): ?array
{
    $map = [

        // ── Trang chủ ─────────────────────────────────────────────
        'home' => null,

        // ── About ─────────────────────────────────────────────────
        'about' => [
            ['title' => 'About Us', 'url' => null],
        ],

        'company.history' => [
            ['title' => 'About',           'url' => '?page=about'],
            ['title' => 'Company History', 'url' => null],
        ],

        'teams' => [
            ['title' => 'About',    'url' => '?page=about'],
            ['title' => 'Our Team', 'url' => null],
        ],

        // ── Services ──────────────────────────────────────────────
        'services' => [
            ['title' => 'Services', 'url' => null],
        ],

        // ── Projects ──────────────────────────────────────────────
        'projects' => [
            ['title' => 'Projects', 'url' => null],
        ],

        'project-details' => [
            ['title' => 'Projects',       'url' => '?page=projects'],
            ['title' => 'Project Details', 'url' => null],
        ],

        // ── Blog ──────────────────────────────────────────────────
        'blogs' => [
            ['title' => 'Blog', 'url' => null],
        ],

        'blog-details' => [
            ['title' => 'Blog',         'url' => '?page=blogs'],
            ['title' => 'Blog Details', 'url' => null],
        ],

        // ── Categories ────────────────────────────────────────────
        'categories' => [
            ['title' => 'Categories', 'url' => null],
        ],

        'categories-details' => [
            ['title' => 'Categories',        'url' => '?page=categories'],
            ['title' => 'Category Details',  'url' => null],
        ],

        // ── Search ────────────────────────────────────────────────
        'search' => [
            ['title' => 'Search Results', 'url' => null],
        ],

        // ── Contact ───────────────────────────────────────────────
        'contact' => [
            ['title' => 'Contact Us', 'url' => null],
        ],

        // ── Auth ──────────────────────────────────────────────────
        'login' => [
            ['title' => 'Login', 'url' => null],
        ],

        'register' => [
            ['title' => 'Register', 'url' => null],
        ],

        'forgot' => [
            ['title' => 'Forgot Password', 'url' => null],
        ],

        // ── User Dashboard ────────────────────────────────────────
        'users' => [
            ['title' => 'My Account', 'url' => null],
        ],

        // ── Checkout / Payment ────────────────────────────────────
        'checkout' => [
            ['title' => 'Checkout', 'url' => null],
        ],

        'payment' => [
            ['title' => 'Checkout', 'url' => '?page=checkout'],
            ['title' => 'Payment',  'url' => null],
        ],

        'payment_success' => [
            ['title' => 'Checkout',          'url' => '?page=checkout'],
            ['title' => 'Payment Success',   'url' => null],
        ],

        // ── Products ──────────────────────────────────────────────
        'products' => [
            ['title' => 'Products', 'url' => null],
        ],

        'details' => [
            ['title' => 'Products',       'url' => '?page=products'],
            ['title' => 'Product Details', 'url' => null],
        ],

        // ── News ──────────────────────────────────────────────────
        'news' => [
            ['title' => 'News', 'url' => null],
        ],

        'news-details' => [
            ['title' => 'News',         'url' => '?page=news'],
            ['title' => 'News Details', 'url' => null],
        ],

    ];

    $crumbs = $map[$page] ?? null;

    // Ghi đè title động nếu được truyền vào (vd: tên bài viết, tên dự án)
    if ($crumbs && isset($params['last_title'])) {
        $last = count($crumbs) - 1;
        $crumbs[$last]['title'] = $params['last_title'];
    }

    return $crumbs;
}
