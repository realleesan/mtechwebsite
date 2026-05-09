<?php
/**
 * BaseController - Class cha cho tất cả Admin Controllers
 */

class BaseController
{
    protected $cssFiles = [];
    protected $jsFiles = [];

    /**
     * Render view với admin master layout
     * @param string $viewPath  Đường dẫn từ app/views/ (vd: 'dashboard/index')
     * @param array  $data      Dữ liệu truyền vào view
     */
    protected function view($viewPath, $data = [])
    {
        extract($data);

        $viewFile = __DIR__ . '/../app/views/' . $viewPath . '.php';

        if (!file_exists($viewFile)) {
            throw new Exception("View not found: {$viewPath}");
        }

        // $content được dùng trong master layout để include view cụ thể
        $content = $viewFile;

        include __DIR__ . '/../app/views/_layout/master.php';
    }

    /**
     * Register CSS file
     */
    public function registerCSS($path)
    {
        if (!in_array($path, $this->cssFiles)) {
            $this->cssFiles[] = $path;
        }
    }

    /**
     * Register JS file
     */
    public function registerJS($path)
    {
        if (!in_array($path, $this->jsFiles)) {
            $this->jsFiles[] = $path;
        }
    }

    /**
     * Get registered CSS files
     */
    public function getCSSFiles()
    {
        return $this->cssFiles;
    }

    /**
     * Get registered JS files
     */
    public function getJSFiles()
    {
        return $this->jsFiles;
    }

    /**
     * Trả JSON response
     */
    protected function json($data, $httpCode = 200)
    {
        header('Content-Type: application/json');
        http_response_code($httpCode);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Redirect
     */
    protected function redirect($url, $httpCode = 302)
    {
        header("Location: {$url}", true, $httpCode);
        exit;
    }

    /**
     * Lấy flash message và xóa khỏi session
     */
    protected function getFlash($key)
    {
        $value = $_SESSION[$key] ?? null;
        unset($_SESSION[$key]);
        return $value;
    }

    /**
     * Set flash message
     */
    protected function setFlash($key, $message)
    {
        $_SESSION[$key] = $message;
    }

    /**
     * Lấy IP client
     */
    protected function getClientIP()
    {
        foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'] as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = trim(explode(',', $_SERVER[$key])[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        return null;
    }

    /**
     * Validate input đơn giản
     * @param array $data   Dữ liệu cần validate (thường là $_POST)
     * @param array $rules  Rules: ['field' => 'required|min:3|max:255']
     * @return array ['passes' => bool, 'errors' => array]
     */
    protected function validate($data, $rules)
    {
        $errors = [];

        foreach ($rules as $field => $rule) {
            $parts = explode('|', $rule);
            $value = trim($data[$field] ?? '');

            foreach ($parts as $r) {
                if ($r === 'required' && empty($value)) {
                    $errors[$field] = "Trường này là bắt buộc";
                    break;
                }
                if ($r === 'email' && !empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = "Email không hợp lệ";
                    break;
                }
                if (str_starts_with($r, 'min:') && !empty($value)) {
                    $min = (int) substr($r, 4);
                    if (strlen($value) < $min) {
                        $errors[$field] = "Tối thiểu {$min} ký tự";
                        break;
                    }
                }
                if (str_starts_with($r, 'max:') && !empty($value)) {
                    $max = (int) substr($r, 4);
                    if (strlen($value) > $max) {
                        $errors[$field] = "Tối đa {$max} ký tự";
                        break;
                    }
                }
            }
        }

        return ['passes' => empty($errors), 'errors' => $errors];
    }
}
