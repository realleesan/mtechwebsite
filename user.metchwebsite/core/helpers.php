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
 * @param int $categoryId Category ID (optional)
 * @param string $tag Tag slug (optional)
 * @param string $search Search term (optional)
 * @return string URL
 */
function blogs_page_url($page, $categoryId = 0, $tag = '', $search = '') {
    $params = [];
    
    if ($page > 1) {
        $params['p'] = $page;
    }
    
    if (!empty($categoryId) && $categoryId > 0) {
        $params['cat'] = $categoryId;
    }
    
    if (!empty($tag)) {
        $params['tag'] = $tag;
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
    return '/chi-tiet-linh-vuc-' . urlencode($slug);
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
 * Chuyển chuỗi tiếng Việt (có dấu) thành slug không dấu
 * Ví dụ: "tuyển dụng" → "tuyen-dung"
 *
 * @param string $str
 * @return string
 */
function slugify($str) {
    // Bảng chuyển đổi ký tự tiếng Việt → không dấu
    $from = [
        'à','á','ả','ã','ạ','ă','ắ','ằ','ẳ','ẵ','ặ','â','ấ','ầ','ẩ','ẫ','ậ',
        'è','é','ẻ','ẽ','ẹ','ê','ế','ề','ể','ễ','ệ',
        'ì','í','ỉ','ĩ','ị',
        'ò','ó','ỏ','õ','ọ','ô','ố','ồ','ổ','ỗ','ộ','ơ','ớ','ờ','ở','ỡ','ợ',
        'ù','ú','ủ','ũ','ụ','ư','ứ','ừ','ử','ữ','ự',
        'ỳ','ý','ỷ','ỹ','ỵ',
        'đ',
        'À','Á','Ả','Ã','Ạ','Ă','Ắ','Ằ','Ẳ','Ẵ','Ặ','Â','Ấ','Ầ','Ẩ','Ẫ','Ậ',
        'È','É','Ẻ','Ẽ','Ẹ','Ê','Ế','Ề','Ể','Ễ','Ệ',
        'Ì','Í','Ỉ','Ĩ','Ị',
        'Ò','Ó','Ỏ','Õ','Ọ','Ô','Ố','Ồ','Ổ','Ỗ','Ộ','Ơ','Ớ','Ờ','Ở','Ỡ','Ợ',
        'Ù','Ú','Ủ','Ũ','Ụ','Ư','Ứ','Ừ','Ử','Ữ','Ự',
        'Ỳ','Ý','Ỷ','Ỹ','Ỵ',
        'Đ',
    ];
    $to = [
        'a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a',
        'e','e','e','e','e','e','e','e','e','e','e',
        'i','i','i','i','i',
        'o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o',
        'u','u','u','u','u','u','u','u','u','u','u',
        'y','y','y','y','y',
        'd',
        'A','A','A','A','A','A','A','A','A','A','A','A','A','A','A','A','A',
        'E','E','E','E','E','E','E','E','E','E','E',
        'I','I','I','I','I',
        'O','O','O','O','O','O','O','O','O','O','O','O','O','O','O','O','O',
        'U','U','U','U','U','U','U','U','U','U','U',
        'Y','Y','Y','Y','Y',
        'D',
    ];
    $str = str_replace($from, $to, $str);
    // Chuyển về chữ thường
    $str = strtolower($str);
    // Thay khoảng trắng và ký tự đặc biệt bằng dấu gạch ngang
    $str = preg_replace('/[^a-z0-9]+/', '-', $str);
    // Bỏ dấu gạch ngang ở đầu/cuối
    $str = trim($str, '-');
    return $str;
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