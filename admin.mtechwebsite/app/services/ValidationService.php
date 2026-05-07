<?php
/**
 * ValidationService - Xử lý validation input data
 * Tách validation logic khỏi View và Controller
 */

class ValidationService
{
    private $data;
    private $rules;
    private $errors = [];
    
    /**
     * Tạo validation mới
     * @param array $data Dữ liệu cần validate
     * @param array $rules Rules validation
     */
    public function make($data, $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->errors = [];
        
        return $this;
    }
    
    /**
     * Kiểm tra tất cả rules
     * @return bool True nếu pass hết, false nếu có lỗi
     */
    public function passes()
    {
        foreach ($this->rules as $field => $rule) {
            $ruleParts = explode('|', $rule);
            $value = isset($this->data[$field]) ? trim($this->data[$field]) : '';
            
            foreach ($ruleParts as $singleRule) {
                $this->validateRule($field, $value, $singleRule);
                
                // Nếu có lỗi required, break các rules khác
                if (isset($this->errors[$field]) && $singleRule === 'required') {
                    break;
                }
            }
        }
        
        return empty($this->errors);
    }
    
    /**
     * Kiểm tra fails (ngược với passes)
     * @return bool True nếu có lỗi, false nếu pass hết
     */
    public function fails()
    {
        return !$this->passes();
    }
    
    /**
     * Lấy tất cả errors (flatten để tương thích với controller)
     * @return array
     */
    public function errors()
    {
        $flatErrors = [];
        foreach ($this->errors as $field => $messages) {
            $flatErrors[$field] = is_array($messages) ? $messages[0] : $messages;
        }
        return $flatErrors;
    }
    
    /**
     * Lấy lỗi đầu tiên
     * @return string|null
     */
    public function first()
    {
        if (empty($this->errors)) {
            return null;
        }
        
        return reset($this->errors)[0] ?? null;
    }
    
    /**
     * Lấy lỗi của field cụ thể
     * @param string $field
     * @return array|null
     */
    public function getError($field)
    {
        return isset($this->errors[$field]) ? $this->errors[$field] : null;
    }
    
    /**
     * Kiểm tra field có lỗi không
     * @param string $field
     * @return bool
     */
    public function hasError($field)
    {
        return isset($this->errors[$field]);
    }
    
    /**
     * Validate một rule cụ thể
     * @param string $field
     * @param mixed $value
     * @param string $rule
     */
    private function validateRule($field, $value, $rule)
    {
        // Required rule
        if ($rule === 'required') {
            if (empty($value)) {
                $this->addError($field, 'Vui lòng nhập ' . $this->getFieldName($field));
            }
            return;
        }
        
        // Nếu value rỗng và không required, bỏ qua các rules khác
        if (empty($value)) {
            return;
        }
        
        // Email rule - validation chặt chẽ hơn
        if ($rule === 'email') {
            // Kiểm tra format cơ bản
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $this->addError($field, 'Email không hợp lệ');
                return;
            }
            
            // Kiểm tra thêm: domain phải có ít nhất 2 ký tự sau dấu chấm cuối
            $parts = explode('@', $value);
            if (count($parts) === 2) {
                $domain = $parts[1];
                $domainParts = explode('.', $domain);
                if (count($domainParts) < 2 || strlen(end($domainParts)) < 2) {
                    $this->addError($field, 'Email không hợp lệ');
                    return;
                }
            }
            return;
        }
        
        // Numeric rule
        if ($rule === 'numeric') {
            if (!is_numeric($value)) {
                $this->addError($field, $this->getFieldName($field) . ' phải là số');
            }
            return;
        }
        
        // Phone rule (đơn giản)
        if ($rule === 'phone') {
            if (!preg_match('/^[0-9\-\+\(\)\s]+$/', $value)) {
                $this->addError($field, 'Số điện thoại không hợp lệ');
            }
            return;
        }
        
        // Min length rule (format: min:10)
        if (strpos($rule, 'min:') === 0) {
            $minLength = (int) substr($rule, 4);
            if (strlen($value) < $minLength) {
                $this->addError($field, $this->getFieldName($field) . ' phải có ít nhất ' . $minLength . ' ký tự');
            }
            return;
        }
        
        // Max length rule (format: max:255)
        if (strpos($rule, 'max:') === 0) {
            $maxLength = (int) substr($rule, 4);
            if (strlen($value) > $maxLength) {
                $this->addError($field, $this->getFieldName($field) . ' không được vượt quá ' . $maxLength . ' ký tự');
            }
            return;
        }
        
        // Between rule (format: between:5,10)
        if (strpos($rule, 'between:') === 0) {
            $range = substr($rule, 8);
            list($min, $max) = explode(',', $range);
            $length = strlen($value);
            if ($length < (int)$min || $length > (int)$max) {
                $this->addError($field, $this->getFieldName($field) . ' phải có độ dài từ ' . $min . ' đến ' . $max . ' ký tự');
            }
            return;
        }
        
        // Regex rule (format: regex:/pattern/)
        if (strpos($rule, 'regex:') === 0) {
            $pattern = substr($rule, 6);
            if (!preg_match($pattern, $value)) {
                $this->addError($field, $this->getFieldName($field) . ' không hợp lệ');
            }
            return;
        }
        
        // URL rule
        if ($rule === 'url') {
            if (!filter_var($value, FILTER_VALIDATE_URL)) {
                $this->addError($field, 'URL không hợp lệ');
            }
            return;
        }
        
        // Alpha rule (chỉ chữ cái)
        if ($rule === 'alpha') {
            if (!preg_match('/^[a-zA-ZÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỊỎỐỒỔỖỘỚỜỞỠỢỤỦŨỪỲỴÝỶỸửữỳỵýỷỹ\s]+$/', $value)) {
                $this->addError($field, $this->getFieldName($field) . ' chỉ được chứa chữ cái');
            }
            return;
        }
        
        // Alpha numeric rule
        if ($rule === 'alpha_num') {
            if (!preg_match('/^[a-zA-Z0-9ÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỊỎỐỒỔỖỘỚỜỞỠỢỤỦŨỪỲỴÝỶỸửữỳỵýỷỹ\s]+$/', $value)) {
                $this->addError($field, $this->getFieldName($field) . ' chỉ được chứa chữ cái và số');
            }
            return;
        }
    }
    
    /**
     * Thêm lỗi vào danh sách
     * @param string $field
     * @param string $message
     */
    private function addError($field, $message)
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        
        $this->errors[$field][] = $message;
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
            'Name' => 'họ tên', // Home form field
            'email' => 'email',
            'phone' => 'số điện thoại',
            'subject' => 'tiêu đề',
            'message' => 'nội dung',
            'Message' => 'nội dung tin nhắn', // Home form field
            'question' => 'câu hỏi',
            'full_name' => 'họ tên',
            'position' => 'vị trí ứng tuyển',
            'cv' => 'CV',
            'address' => 'địa chỉ',
            'company' => 'công ty',
            'website' => 'website',
            'password' => 'mật khẩu',
            'password_confirm' => 'xác nhận mật khẩu',
            'comment' => 'bình luận',
            'title' => 'tiêu đề',
            'content' => 'nội dung',
            'description' => 'mô tả',
            'category' => 'danh mục',
            'tag' => 'thẻ',
            'search' => 'từ khóa tìm kiếm'
        ];
        
        return isset($fieldNames[$field]) ? $fieldNames[$field] : $field;
    }
    
    /**
     * Static method để validate nhanh
     * @param array $data
     * @param array $rules
     * @return ValidationService
     */
    public static function validate($data, $rules)
    {
        $validator = new self();
        return $validator->make($data, $rules);
    }
    
    /**
     * Static method để validate và trả về kết quả
     * @param array $data
     * @param array $rules
     * @return array ['passes' => bool, 'errors' => array]
     */
    public static function quickValidate($data, $rules)
    {
        $validator = self::validate($data, $rules);
        
        return [
            'passes' => $validator->passes(),
            'errors' => $validator->errors()
        ];
    }
}