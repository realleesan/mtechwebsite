<?php
/**
 * AdminRouter - Xử lý routing cho Admin Panel
 * Tất cả routes (trừ auth) đều yêu cầu đăng nhập
 */

class AdminRouter
{
    private $routes = [];

    public function __construct()
    {
        $this->loadRoutes();
    }

    public function get($path, $handler)
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post($path, $handler)
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function any($path, $handler)
    {
        $this->addRoute('GET', $path, $handler);
        $this->addRoute('POST', $path, $handler);
    }

    private function addRoute($method, $path, $handler)
    {
        $path = rtrim($path, '/');
        if (empty($path)) $path = '/';
        $this->routes[$method][$path] = $handler;
    }

    private function loadRoutes()
    {
        // ----------------------------------------
        // Auth routes - không cần đăng nhập
        // ----------------------------------------
        $this->get('/login',                'AuthController@showLogin');
        $this->post('/login',               'AuthController@login');
        $this->get('/logout',               'AuthController@logout');
        $this->get('/forgot-password',      'AuthController@showForgot');
        $this->post('/forgot-password',     'AuthController@sendResetLink');
        $this->get('/reset-password',       'AuthController@showReset');
        $this->post('/reset-password',      'AuthController@resetPassword');

        // ----------------------------------------
        // Dashboard
        // ----------------------------------------
        $this->get('/',                     'DashboardController@index');
        $this->get('/dashboard',            'DashboardController@index');

        // ----------------------------------------
        // Blogs
        // ----------------------------------------
        $this->get('/blogs',                'BlogsController@index');
        $this->get('/blogs/create',         'BlogsController@create');
        $this->post('/blogs/store',         'BlogsController@store');
        $this->get('/blogs/edit/{id}',      'BlogsController@edit');
        $this->post('/blogs/update/{id}',   'BlogsController@update');
        $this->post('/blogs/delete/{id}',   'BlogsController@delete');

        // ----------------------------------------
        // Projects
        // ----------------------------------------
        $this->get('/projects',             'ProjectsController@index');
        $this->get('/projects/create',      'ProjectsController@create');
        $this->post('/projects/store',      'ProjectsController@store');
        $this->get('/projects/edit/{id}',   'ProjectsController@edit');
        $this->post('/projects/update/{id}','ProjectsController@update');
        $this->post('/projects/delete/{id}','ProjectsController@delete');

        // ----------------------------------------
        // Categories (Dịch vụ)
        // ----------------------------------------
        $this->get('/categories',               'CategoriesController@index');
        $this->get('/categories/create',        'CategoriesController@create');
        $this->post('/categories/store',        'CategoriesController@store');
        $this->get('/categories/edit/{id}',     'CategoriesController@edit');
        $this->post('/categories/update/{id}',  'CategoriesController@update');
        $this->post('/categories/delete/{id}',  'CategoriesController@delete');

        // ----------------------------------------
        // Contacts (chỉ xem)
        // ----------------------------------------
        $this->get('/contacts',             'ContactsController@index');
        $this->get('/contacts/view/{id}',   'ContactsController@show');
        $this->post('/contacts/delete/{id}','ContactsController@delete');

        // ----------------------------------------
        // Teams
        // ----------------------------------------
        $this->get('/teams',                'TeamsController@index');
        $this->get('/teams/create',         'TeamsController@create');
        $this->post('/teams/store',         'TeamsController@store');
        $this->get('/teams/edit/{id}',      'TeamsController@edit');
        $this->post('/teams/update/{id}',   'TeamsController@update');
        $this->post('/teams/delete/{id}',   'TeamsController@delete');

        // ----------------------------------------
        // Awards
        // ----------------------------------------
        $this->get('/awards',               'AwardsController@index');
        $this->get('/awards/create',        'AwardsController@create');
        $this->post('/awards/store',        'AwardsController@store');
        $this->get('/awards/edit/{id}',     'AwardsController@edit');
        $this->post('/awards/update/{id}',  'AwardsController@update');
        $this->post('/awards/delete/{id}',  'AwardsController@delete');

        // ----------------------------------------
        // Client Logos
        // ----------------------------------------
        $this->get('/client-logos',                 'ClientLogosController@index');
        $this->get('/client-logos/create',          'ClientLogosController@create');
        $this->post('/client-logos/store',          'ClientLogosController@store');
        $this->get('/client-logos/edit/{id}',       'ClientLogosController@edit');
        $this->post('/client-logos/update/{id}',    'ClientLogosController@update');
        $this->post('/client-logos/delete/{id}',    'ClientLogosController@delete');

        // ----------------------------------------
        // Job Applications (chỉ xem)
        // ----------------------------------------
        $this->get('/job-applications',             'JobApplicationsController@index');
        $this->get('/job-applications/view/{id}',   'JobApplicationsController@show');
        $this->post('/job-applications/update-status/{id}', 'JobApplicationsController@updateStatus');

        // ----------------------------------------
        // Header & Footer content
        // ----------------------------------------
        $this->get('/header',               'HeaderController@edit');
        $this->post('/header/update',       'HeaderController@update');
        $this->get('/footer',               'FooterController@edit');
        $this->post('/footer/update',       'FooterController@update');

        // ----------------------------------------
        // Settings
        // ----------------------------------------
        $this->get('/settings',             'SettingsController@index');
        $this->post('/settings/update',     'SettingsController@update');

        // ----------------------------------------
        // Fallback 404
        // ----------------------------------------
        $this->any('{path}',                'ErrorController@notFound');
    }

    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri    = $this->getRequestUri();

        // Tìm route khớp
        $match = $this->findRoute($method, $uri)
               ?? $this->findRouteWithParams($method, $uri);

        if (!$match) {
            $this->handleNotFound();
            return;
        }

        // Kiểm tra auth (trừ auth routes)
        $handler = is_array($match) ? $match['handler'] : $match;
        if (!$this->isAuthRoute($handler)) {
            $this->requireAuth();
        }

        $this->executeHandler($match);
    }

    // ----------------------------------------
    // Auth check
    // ----------------------------------------

    private $authRoutes = [
        'AuthController@showLogin',
        'AuthController@login',
        'AuthController@logout',
        'AuthController@showForgot',
        'AuthController@sendResetLink',
        'AuthController@showReset',
        'AuthController@resetPassword',
    ];

    private function isAuthRoute($handler): bool
    {
        return in_array($handler, $this->authRoutes, true);
    }

    private function requireAuth(): void
    {
        if (empty($_SESSION['admin_id'])) {
            header('Location: /login');
            exit;
        }
    }

    // ----------------------------------------
    // URI helpers
    // ----------------------------------------

    private function getRequestUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'];
        $uri = explode('?', $uri)[0];
        $uri = rtrim($uri, '/');
        return empty($uri) ? '/' : $uri;
    }

    private function findRoute($method, $uri)
    {
        return $this->routes[$method][$uri] ?? null;
    }

    private function findRouteWithParams($method, $uri)
    {
        if (!isset($this->routes[$method])) return null;

        foreach ($this->routes[$method] as $route => $handler) {
            $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $route);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                return [
                    'handler' => $handler,
                    'params'  => array_slice($matches, 1),
                ];
            }
        }

        return null;
    }

    // ----------------------------------------
    // Execute handler
    // ----------------------------------------

    private function executeHandler($match): void
    {
        try {
            if (is_array($match)) {
                $controllerMethod = $match['handler'];
                $params           = $match['params'] ?? [];
            } else {
                $controllerMethod = $match;
                $params           = [];
            }

            [$controllerName, $method] = explode('@', $controllerMethod);

            // Load BaseController trước
            require_once __DIR__ . '/BaseController.php';

            $controllerFile = __DIR__ . '/../app/controllers/' . $controllerName . '.php';
            if (!file_exists($controllerFile)) {
                throw new Exception("Controller not found: {$controllerName}");
            }

            require_once $controllerFile;

            if (!class_exists($controllerName)) {
                throw new Exception("Controller class not found: {$controllerName}");
            }

            $controller = new $controllerName();

            if (!method_exists($controller, $method)) {
                throw new Exception("Method not found: {$controllerName}::{$method}");
            }

            call_user_func_array([$controller, $method], $params);

        } catch (Exception $e) {
            error_log('AdminRouter error: ' . $e->getMessage());
            $this->handleServerError();
        } catch (Error $e) {
            error_log('AdminRouter fatal error: ' . $e->getMessage());
            $this->handleServerError();
        }
    }

    private function handleNotFound(): void
    {
        http_response_code(404);
        include __DIR__ . '/../errors/404.php';
    }

    private function handleServerError(): void
    {
        http_response_code(500);
        include __DIR__ . '/../errors/500.php';
    }
}
