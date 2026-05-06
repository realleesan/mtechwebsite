<?php
/**
 * Router - Xử lý routing thay thế switch-case trong index.php
 * Map URL -> Controller::method
 */

class Router
{
    private $routes = [];
    private $notFoundHandler = null;
    
    public function __construct()
    {
        $this->loadRoutes();
    }
    
    /**
     * Định nghĩa route GET
     */
    public function get($path, $handler)
    {
        $this->addRoute('GET', $path, $handler);
    }
    
    /**
     * Định nghĩa route POST
     */
    public function post($path, $handler)
    {
        $this->addRoute('POST', $path, $handler);
    }
    
    /**
     * Định nghĩa route cho cả GET và POST
     */
    public function any($path, $handler)
    {
        $this->addRoute('GET', $path, $handler);
        $this->addRoute('POST', $path, $handler);
    }
    
    /**
     * Thêm route vào danh sách
     */
    private function addRoute($method, $path, $handler)
    {
        // Normalize path: bỏ dấu / ở cuối
        $path = rtrim($path, '/');
        
        // Nếu path rỗng, set là /
        if (empty($path)) {
            $path = '/';
        }
        
        $this->routes[$method][$path] = $handler;
    }
    
    /**
     * Load tất cả routes từ cấu hình
     */
    private function loadRoutes()
    {
        // Trang chủ
        $this->get('/', 'HomeController@index');
        $this->get('/home', 'HomeController@index');
        $this->get('/trang-chu', 'HomeController@index');
        
        // Contact - GET hiển thị form, POST xử lý submit
        $this->get('/contact', 'ContactController@index');
        $this->get('/lien-he', 'ContactController@index');
        $this->post('/contact/submit', 'ContactController@submit');
        $this->post('/lien-he/submit', 'ContactController@submit');
        
        // About
        $this->get('/about', 'AboutController@index');
        $this->get('/gioi-thieu', 'AboutController@index');
        $this->get('/teams', 'TeamsController@index');
        $this->get('/doi-ngu', 'TeamsController@index');
        $this->get('/company.history', 'AboutController@companyHistory');
        $this->get('/lich-su-cong-ty', 'AboutController@companyHistory');
        
        // Blogs
        $this->get('/blogs', 'BlogsController@index');
        $this->get('/tin-tuc', 'BlogsController@index');
        $this->get('/blog-details', 'BlogController@details');
        $this->get('/chi-tiet-tin-tuc', 'BlogController@details');
        
        // Projects
        $this->get('/projects', 'ProjectsController@index');
        $this->get('/du-an', 'ProjectsController@index');
        $this->get('/project-details', 'ProjectsController@details');
        $this->get('/chi-tiet-du-an', 'ProjectsController@details');
        
        // Categories/Dịch vụ
        $this->get('/categories', 'CategoriesController@index');
        $this->get('/dich-vu', 'CategoriesController@index');
        $this->get('/categories-details', 'CategoriesController@details');
        $this->get('/chi-tiet-dich-vu', 'CategoriesController@details');
        
        // Search
        $this->get('/search', 'SearchController@index');
        $this->get('/tim-kiem', 'SearchController@index');
        
        // Awards
        $this->get('/awards', 'AwardsController@index');
        $this->get('/giai-thuong', 'AwardsController@index');
        
        // Coming Soon
        $this->get('/comingsoon', 'ComingSoonController@index');
        $this->post('/comingsoon/subscribe', 'ComingSoonController@subscribe');
        
        // AJAX routes - form submissions
        $this->post('/home/contact-submit', 'HomeController@contactSubmit');
        $this->post('/teams/submit-question', 'TeamsController@submitQuestion');
        
        // Job application
        $this->post('/job-application-submit', 'BlogController@jobApplicationSubmit');
        
        // 404 page
        $this->get('/404', 'ErrorController@notFound');
        $this->get('/500', 'ErrorController@serverError');
        
        // Default route cho mọi thứ khác
        $this->any('{path}', 'ErrorController@notFound');
    }
    
    /**
     * Dispatch request đến controller phù hợp
     */
    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $this->getRequestUri();
        
        // Tìm route khớp
        $handler = $this->findRoute($method, $uri);
        
        if (!$handler) {
            // Thử route với parameter
            $handler = $this->findRouteWithParams($method, $uri);
        }
        
        if (!$handler) {
            // 404 - Không tìm thấy route
            $this->handleNotFound();
            return;
        }
        
        // Execute handler
        $this->executeHandler($handler);
    }
    
    /**
     * Lấy URI từ request
     */
    private function getRequestUri()
    {
        $uri = $_SERVER['REQUEST_URI'];
        
        // Xóa query string
        $uri = explode('?', $uri)[0];
        
        // Normalize: bỏ dấu / ở cuối
        $uri = rtrim($uri, '/');
        
        // Nếu uri rỗng, set là /
        if (empty($uri)) {
            $uri = '/';
        }
        
        return $uri;
    }
    
    /**
     * Tìm route chính xác
     */
    private function findRoute($method, $uri)
    {
        if (isset($this->routes[$method][$uri])) {
            return $this->routes[$method][$uri];
        }
        
        return null;
    }
    
    /**
     * Tìm route với parameter
     */
    private function findRouteWithParams($method, $uri)
    {
        if (!isset($this->routes[$method])) {
            return null;
        }
        
        foreach ($this->routes[$method] as $route => $handler) {
            // Chuyển {param} thành regex
            $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $route);
            $pattern = '#^' . $pattern . '$#';
            
            if (preg_match($pattern, $uri, $matches)) {
                // Lấy parameters (bỏ match đầu tiên là full string)
                $params = array_slice($matches, 1);
                
                return [
                    'handler' => $handler,
                    'params' => $params
                ];
            }
        }
        
        return null;
    }
    
    /**
     * Execute handler
     */
    private function executeHandler($handler)
    {
        try {
            if (is_array($handler)) {
                // Handler có parameters
                $controllerMethod = $handler['handler'];
                $params = $handler['params'] ?? [];
            } else {
                // Handler đơn giản
                $controllerMethod = $handler;
                $params = [];
            }
            
            // Parse Controller@method
            $parts = explode('@', $controllerMethod);
            if (count($parts) !== 2) {
                throw new Exception("Invalid handler format: {$controllerMethod}");
            }
            
            $controllerName = $parts[0];
            $method = $parts[1];
            
            // Load controller file
            $controllerFile = __DIR__ . '/../app/controllers/' . $controllerName . '.php';
            if (!file_exists($controllerFile)) {
                throw new Exception("Controller not found: {$controllerName}");
            }
            
            require_once $controllerFile;
            
            // Kiểm tra class tồn tại
            if (!class_exists($controllerName)) {
                throw new Exception("Controller class not found: {$controllerName}");
            }
            
            // Tạo instance
            $controller = new $controllerName();
            
            // Kiểm tra method tồn tại
            if (!method_exists($controller, $method)) {
                throw new Exception("Method not found: {$controllerName}::{$method}");
            }
            
            // Gọi method với parameters
            call_user_func_array([$controller, $method], $params);
            
        } catch (Exception $e) {
            error_log('Router error: ' . $e->getMessage());
            $this->handleServerError();
        }
    }
    
    /**
     * Xử lý 404
     */
    private function handleNotFound()
    {
        http_response_code(404);
        
        // Set các biến cho layout
        $page = '404';
        $title = 'Không tìm thấy trang - MTECHJSC';
        $content = 'errors/404.php';
        $showPageHeader = false;
        $showCTA = false;
        $showBreadcrumb = false;
        $hideHeader = true;
        
        // Include layout
        include_once __DIR__ . '/../app/views/_layout/master.php';
    }
    
    /**
     * Xử lý 500
     */
    private function handleServerError()
    {
        http_response_code(500);
        
        // Set các biến cho layout
        $page = '500';
        $title = 'Lỗi máy chủ - MTECHJSC';
        $content = 'errors/500.php';
        $showPageHeader = false;
        $showCTA = false;
        $showBreadcrumb = false;
        $hideHeader = true;
        
        // Include layout
        include_once __DIR__ . '/../app/views/_layout/master.php';
    }
    
    /**
     * Set custom 404 handler
     */
    public function setNotFoundHandler($handler)
    {
        $this->notFoundHandler = $handler;
    }
}