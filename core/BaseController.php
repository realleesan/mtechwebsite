<?php
/**
 * BaseController - Class cha cho tất cả controllers
 * Cung cấp helper methods chung để render views, trả JSON, redirect, validate
 */

class BaseController
{
    /**
     * Render view với data
     * @param string $viewPath Đường dẫn đến view file (từ app/views/)
     * @param array $data Dữ liệu truyền vào view
     */
    protected function view($viewPath, $data = [])
    {
        // Chuyển các key trong $data thành biến riêng lẻ
        extract($data);
        
        // Đường dẫn đầy đủ đến view file
        $viewFile = __DIR__ . '/../app/views/' . $viewPath;
        
        // Kiểm tra file tồn tại
        if (!file_exists($viewFile)) {
            throw new Exception("View file not found: {$viewPath}");
        }
        
        // Include view file
        include $viewFile;
    }
    
    /**
     * Trả JSON response
     * @param mixed $data Dữ liệu cần trả về
     * @param int $httpCode HTTP status code
     */
    protected function json($data, $httpCode = 200)
    {
        header('Content-Type: application/json');
        http_response_code($httpCode);
        echo json_encode($data);
        exit;
    }
    
    /**
     * Redirect đến URL khác
     * @param string $url URL cần redirect
     * @param int $httpCode HTTP status code (302 mặc định)
     */
    protected function redirect($url, $httpCode = 302)
    {
        header("Location: {$url}", true, $httpCode);
        exit;
    }
    
    /**
     * Validate input data với rules
     * @param array $data Dữ liệu cần validate (thường là $_POST)
     * @param array $rules Rules validation
     * @return array ['passes' => bool, 'errors' => array]
     */
    protected function validate($data, $rules)
    {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $ruleParts = explode('|', $rule);
            $value = isset($data[$field]) ? trim($data[$field]) : '';
            
            foreach ($ruleParts as $singleRule) {
                // Required rule
                if ($singleRule === 'required') {
                    if (empty($value)) {
                        $errors[$field] = 'Vui lòng nhập ' . $this->getFieldName($field);
                        break;
                    }
                }
                
                // Email rule
                elseif ($singleRule === 'email') {
                    if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $errors[$field] = 'Email không hợp lệ';
                        break;
                    }
                }
                
                // Min length rule (format: min:10)
                elseif (strpos($singleRule, 'min:') === 0) {
                    $minLength = (int) substr($singleRule, 4);
                    if (!empty($value) && strlen($value) < $minLength) {
                        $errors[$field] = 'Phải có ít nhất ' . $minLength . ' ký tự';
                        break;
                    }
                }
                
                // Max length rule (format: max:255)
                elseif (strpos($singleRule, 'max:') === 0) {
                    $maxLength = (int) substr($singleRule, 4);
                    if (!empty($value) && strlen($value) > $maxLength) {
                        $errors[$field] = 'Không được vượt quá ' . $maxLength . ' ký tự';
                        break;
                    }
                }
            }
        }
        
        return [
            'passes' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Lấy tên field hiển thị thân thiện
     * @param string $field
     * @return string
     */
    private function getFieldName($field)
    {
        $fieldNames = [
            'name' => 'họ tên',
            'email' => 'email',
            'phone' => 'số điện thoại',
            'subject' => 'tiêu đề',
            'message' => 'nội dung',
            'question' => 'câu hỏi'
        ];
        
        return isset($fieldNames[$field]) ? $fieldNames[$field] : $field;
    }
    
    /**
     * Lấy IP address của client
     * @return string|null
     */
    protected function getClientIP()
    {
        $ipKeys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                $ips = explode(',', $_SERVER[$key]);
                $ip = trim($ips[0]);
                
                // Validate IP
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return null;
    }
    
    /**
     * Lấy User Agent của client
     * @return string|null
     */
    protected function getUserAgent()
    {
        return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
    }
}