<?php
/**
 * ImageHelper.php - Helper tối ưu hóa hiển thị hình ảnh (WebP & Lazy Loading)
 */

class ImageHelper
{
    /**
     * Lấy đường dẫn ảnh tối ưu (ưu tiên .webp nếu file tồn tại)
     * 
     * @param string $path Đường dẫn tương đối của ảnh (ví dụ: assets/images/logo_mtech.png)
     * @return string Đường dẫn ảnh WebP hoặc đường dẫn gốc
     */
    public static function getUrl(string $path): string
    {
        if (empty($path)) {
            return 'assets/images/placeholder.jpg';
        }

        // Nếu đã là URL tuyệt đối external (http:// hoặc https://)
        if (preg_match('/^https?:\/\//i', $path)) {
            return $path;
        }

        $hasLeadingSlash = (strpos($path, '/') === 0);
        $cleanPath = ltrim($path, '/');
        $ext = pathinfo($cleanPath, PATHINFO_EXTENSION);
        $prefix = $hasLeadingSlash ? '/' : '';

        if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png'])) {
            $webpRelativePath = pathinfo($cleanPath, PATHINFO_DIRNAME) . '/' . pathinfo($cleanPath, PATHINFO_FILENAME) . '.webp';
            $fullWebpPath = __DIR__ . '/../' . $webpRelativePath;

            try {
                if (@file_exists($fullWebpPath)) {
                    return $prefix . $webpRelativePath;
                }
            } catch (Throwable $e) {
                // Bỏ qua lỗi truy cập filesystem
            }
        }

        return $path;
    }

    /**
     * Render thẻ <img> chuẩn tối ưu với WebP fallback và Lazy loading
     * 
     * @param string $src Đường dẫn ảnh
     * @param string $alt Thẻ alt mô tả
     * @param string $class Class CSS
     * @param bool $isLazy Thẻ có dùng lazy load hay không (mặc định true)
     * @param array $attributes Thuộc tính bổ sung (id, style...)
     * @return string Chuỗi HTML
     */
    public static function renderImg(string $src, string $alt = '', string $class = '', bool $isLazy = true, array $attributes = []): string
    {
        $optimizedSrc = htmlspecialchars(self::getUrl($src));
        $altText = htmlspecialchars($alt);
        $classText = !empty($class) ? ' class="' . htmlspecialchars($class) . '"' : '';
        
        $loadingAttr = $isLazy ? ' loading="lazy" decoding="async"' : ' fetchpriority="high" decoding="async"';

        $extraAttr = '';
        foreach ($attributes as $key => $val) {
            $extraAttr .= ' ' . htmlspecialchars($key) . '="' . htmlspecialchars($val) . '"';
        }

        return sprintf('<img src="%s" alt="%s"%s%s%s>', $optimizedSrc, $altText, $classText, $loadingAttr, $extraAttr);
    }
}
