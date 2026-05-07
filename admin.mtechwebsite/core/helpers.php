<?php
/**
 * helpers.php - Helper functions dùng chung cho toàn bộ ứng dụng
 */

/**
 * Chuyển đổi ngày tháng từ tiếng Anh sang tiếng Việt
 * Ví dụ: "28 April, 2026" -> "28 Tháng 4, 2026"
 * 
 * @param string $dateStr Chuỗi ngày tháng tiếng Anh
 * @return string Chuỗi ngày tháng tiếng Việt
 */
function format_date_vietnamese($dateStr) {
    $months = [
        'January'   => 'Tháng 1',
        'February'  => 'Tháng 2',
        'March'     => 'Tháng 3',
        'April'     => 'Tháng 4',
        'May'       => 'Tháng 5',
        'June'      => 'Tháng 6',
        'July'      => 'Tháng 7',
        'August'    => 'Tháng 8',
        'September' => 'Tháng 9',
        'October'   => 'Tháng 10',
        'November'  => 'Tháng 11',
        'December'  => 'Tháng 12'
    ];
    
    return str_replace(array_keys($months), array_values($months), $dateStr);
}

/**
 * Tạo URL đẹp cho blog dựa trên category
 * 
 * @param array $blog Thông tin blog
 * @return string URL đẹp
 */
function get_blog_url($blog) {
    $isHiring = ($blog['category_id'] == 7);
    return $isHiring
        ? '/chi-tiet-' . urlencode($blog['slug'])
        : '/chi-tiet-tin-tuc-' . urlencode($blog['slug']);
}

/**
 * Tạo excerpt từ content
 * 
 * @param string $content Nội dung đầy đủ
 * @param int $length Độ dài tối đa
 * @return string Excerpt
 */
function create_excerpt($content, $length = 220) {
    if (empty($content)) return '';
    
    // Loại bỏ HTML tags
    $content = strip_tags($content);
    
    if (mb_strlen($content) <= $length) {
        return $content;
    }
    
    return mb_substr($content, 0, $length) . '...';
}

/**
 * Tạo URL cho trang blog với pagination
 * Chuyển từ blogs.php để tái sử dụng
 * 
 * @param int $page Số trang
 * @param string $category Category slug (optional)
 * @param string $search Search term (optional)
 * @return string URL
 */
function blogs_page_url($page, $category = '', $search = '') {
    $params = [];
    
    if ($page > 1) {
        $params['page'] = $page;
    }
    
    if (!empty($category)) {
        $params['category'] = $category;
    }
    
    if (!empty($search)) {
        $params['search'] = $search;
    }
    
    $queryString = !empty($params) ? '?' . http_build_query($params) : '';
    
    return '/tin-tuc' . $queryString;
}

/**
 * Tạo URL cho chi tiết blog dựa trên slug và category
 * 
 * @param string $slug Blog slug
 * @param int $categoryId Category ID để xác định loại URL
 * @return string URL
 */
function get_blog_detail_url($slug, $categoryId = null) {
    // Category ID 7 là tuyển dụng
    $isHiring = ($categoryId == 7);
    
    return $isHiring 
        ? '/chi-tiet-' . urlencode($slug)
        : '/chi-tiet-tin-tuc-' . urlencode($slug);
}

/**
 * Tạo URL cho trang category
 * 
 * @param string $slug Category slug
 * @return string URL
 */
function get_category_url($slug) {
    return '/chi-tiet-dich-vu-' . urlencode($slug);
}

/**
 * Tạo URL cho trang project detail
 * 
 * @param string $slug Project slug
 * @return string URL
 */
function get_project_url($slug) {
    return '/chi-tiet-du-an-' . urlencode($slug);
}

/**
 * Sanitize output để tránh XSS
 * 
 * @param string $string
 * @return string
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Truncate text với từ hoàn chỉnh
 * 
 * @param string $text
 * @param int $limit
 * @param string $end
 * @return string
 */
function str_limit($text, $limit = 100, $end = '...') {
    if (mb_strlen($text) <= $limit) {
        return $text;
    }
    
    return rtrim(mb_substr($text, 0, $limit)) . $end;
}