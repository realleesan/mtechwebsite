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