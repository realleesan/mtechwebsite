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
        $this->get('/api/access-stats',     'DashboardController@getAccessStats');

        // ----------------------------------------

        // Blog Categories (đặt trước blogs để tránh conflict)
        // ----------------------------------------
        $this->get('/blogs/categories',             'BlogCategoriesController@index');
        $this->get('/blogs/categories/create',      'BlogCategoriesController@create');
        $this->post('/blogs/categories/store',      'BlogCategoriesController@store');
        $this->get('/blogs/categories/edit/{id}',   'BlogCategoriesController@edit');
        $this->post('/blogs/categories/update/{id}','BlogCategoriesController@update');
        $this->post('/blogs/categories/delete/{id}','BlogCategoriesController@delete');
        
        // AJAX API endpoints for categories
        $this->get('/api/blogs/categories',         'BlogCategoriesController@getCategories');
        $this->post('/api/blogs/categories/store',  'BlogCategoriesController@storeAjax');
        $this->post('/api/blogs/categories/update/{id}','BlogCategoriesController@updateAjax');
        $this->post('/api/blogs/categories/delete/{id}','BlogCategoriesController@deleteAjax');

        // ----------------------------------------

        // Blogs
        // ----------------------------------------
        $this->get('/blogs',                'BlogsController@index');
        $this->get('/blogs/create',         'BlogsController@create');
        $this->post('/blogs/store',         'BlogsController@store');
        $this->get('/blogs/view/{id}',      'BlogsController@viewBlog');
        $this->get('/blogs/edit/{id}',      'BlogsController@edit');
        $this->post('/blogs/update/{id}',   'BlogsController@update');
        $this->post('/blogs/delete/{id}',   'BlogsController@delete');
        $this->get('/blogs/trash',          'BlogsController@trash');
        $this->post('/blogs/restore/{id}',  'BlogsController@restore');
        $this->post('/blogs/hard-delete/{id}','BlogsController@hardDelete');

        // ----------------------------------------

        // Blog Categories
        // ----------------------------------------
        $this->get('/blogs/categories',             'BlogCategoriesController@index');
        $this->get('/blogs/categories/create',      'BlogCategoriesController@create');
        $this->post('/blogs/categories/store',      'BlogCategoriesController@store');
        $this->get('/blogs/categories/edit/{id}',   'BlogCategoriesController@edit');
        $this->post('/blogs/categories/update/{id}','BlogCategoriesController@update');
        $this->post('/blogs/categories/delete/{id}','BlogCategoriesController@delete');

        // ----------------------------------------

        // Projects
        // ----------------------------------------
        $this->get('/projects',                 'ProjectsController@index');
        $this->get('/projects/create',          'ProjectsController@create');
        $this->post('/projects/store',          'ProjectsController@store');
        $this->get('/projects/edit/{id}',       'ProjectsController@edit');
        $this->post('/projects/update/{id}',    'ProjectsController@update');
        $this->post('/projects/delete/{id}',     'ProjectsController@delete');
        $this->get('/projects/trash',           'ProjectsController@trash');
        $this->post('/projects/restore/{id}',    'ProjectsController@restore');
        $this->post('/projects/hard-delete/{id}','ProjectsController@hardDelete');

        // ----------------------------------------
        // Categories (Lĩnh vực)
        // ----------------------------------------
        $this->get('/categories',                   'CategoriesController@index');
        $this->get('/categories/create',            'CategoriesController@create');
        $this->post('/categories/store',            'CategoriesController@store');
        $this->get('/categories/edit/{id}',         'CategoriesController@edit');
        $this->post('/categories/update/{id}',      'CategoriesController@update');
        $this->post('/categories/delete/{id}',      'CategoriesController@delete');
        $this->get('/categories/trash',             'CategoriesController@trash');
        $this->post('/categories/restore/{id}',     'CategoriesController@restore');
        $this->post('/categories/hard-delete/{id}', 'CategoriesController@hardDelete');

        // ----------------------------------------
        // Contacts
        // ----------------------------------------
        $this->get('/contacts',                     'ContactsController@index');
        $this->get('/contacts/trash',               'ContactsController@trash');
        $this->get('/contacts/view/{id}',           'ContactsController@show');
        $this->get('/contacts/edit/{id}',           'ContactsController@edit');
        $this->post('/contacts/update/{id}',        'ContactsController@update');
        $this->post('/contacts/delete/{id}',        'ContactsController@delete');
        $this->post('/contacts/restore/{id}',       'ContactsController@restore');
        $this->post('/contacts/hard-delete/{id}',   'ContactsController@hardDelete');

        // ----------------------------------------
        // Teams
        // ----------------------------------------
        $this->get('/teams',                    'TeamsController@index');
        $this->get('/teams/create',             'TeamsController@create');
        $this->post('/teams/store',             'TeamsController@store');
        $this->get('/teams/edit/{id}',          'TeamsController@edit');
        $this->post('/teams/update/{id}',       'TeamsController@update');
        $this->post('/teams/delete/{id}',       'TeamsController@delete');
        $this->get('/teams/trash',              'TeamsController@trash');
        $this->post('/teams/restore/{id}',      'TeamsController@restore');
        $this->post('/teams/hard-delete/{id}',  'TeamsController@hardDelete');

        // ----------------------------------------
        // Capacity Fields (Chứng chỉ năng lực)
        // ----------------------------------------
        $this->get('/capacity-fields',                              'CapacityFieldsController@index');
        $this->get('/capacity-fields/create',                       'CapacityFieldsController@createField');
        $this->post('/capacity-fields/store',                       'CapacityFieldsController@storeField');
        $this->get('/capacity-fields/edit/{id}',                    'CapacityFieldsController@editField');
        $this->post('/capacity-fields/update/{id}',                 'CapacityFieldsController@updateField');
        $this->post('/capacity-fields/delete/{id}',                 'CapacityFieldsController@deleteField');
        $this->get('/capacity-fields/{fieldId}/items/create',       'CapacityFieldsController@createItem');
        $this->post('/capacity-fields/{fieldId}/items/store',       'CapacityFieldsController@storeItem');
        $this->get('/capacity-fields/items/edit/{itemId}',          'CapacityFieldsController@editItem');
        $this->post('/capacity-fields/items/update/{itemId}',       'CapacityFieldsController@updateItem');
        $this->post('/capacity-fields/items/delete/{itemId}',       'CapacityFieldsController@deleteItem');

        // ----------------------------------------
        // Awards → Chứng chỉ năng lực hoạt động xây dựng
        // ----------------------------------------
        $this->get('/awards',                           'AwardsController@index');
        $this->get('/awards/create',                    'AwardsController@create');
        $this->post('/awards/store',                    'AwardsController@store');
        $this->get('/awards/edit/{id}',                 'AwardsController@edit');
        $this->post('/awards/update/{id}',              'AwardsController@update');
        $this->post('/awards/delete/{id}',              'AwardsController@delete');
        // Items (mục con)
        $this->get('/awards/{fieldId}/items/create',    'AwardsController@createItem');
        $this->post('/awards/{fieldId}/items/store',    'AwardsController@storeItem');
        $this->get('/awards/items/edit/{itemId}',       'AwardsController@editItem');
        $this->post('/awards/items/update/{itemId}',    'AwardsController@updateItem');
        $this->post('/awards/items/delete/{itemId}',    'AwardsController@deleteItem');
        // Giữ lại để không lỗi 404 nếu URL cũ còn được gọi
        $this->get('/awards/trash',                     'AwardsController@trash');
        $this->post('/awards/restore/{id}',             'AwardsController@restore');
        $this->post('/awards/hard-delete/{id}',         'AwardsController@hardDelete');

        // ----------------------------------------
        // Client Logos
        // ----------------------------------------
        $this->get('/client-logos',                     'ClientLogosController@index');
        $this->get('/client-logos/create',              'ClientLogosController@create');
        $this->post('/client-logos/store',              'ClientLogosController@store');
        $this->get('/client-logos/edit/{id}',           'ClientLogosController@edit');
        $this->post('/client-logos/update/{id}',        'ClientLogosController@update');
        $this->post('/client-logos/delete/{id}',        'ClientLogosController@delete');
        $this->get('/client-logos/trash',               'ClientLogosController@trash');
        $this->post('/client-logos/restore/{id}',       'ClientLogosController@restore');
        $this->post('/client-logos/hard-delete/{id}',   'ClientLogosController@hardDelete');

        // ----------------------------------------
        // Job Applications
        // ----------------------------------------
        $this->get('/job-applications',                       'JobApplicationsController@index');
        $this->get('/job-applications/trash',                 'JobApplicationsController@trash');
        $this->get('/job-applications/view/{id}',             'JobApplicationsController@show');
        $this->get('/job-applications/edit/{id}',             'JobApplicationsController@edit');
        $this->get('/job-applications/download-cv/{id}',      'JobApplicationsController@downloadCv');
        $this->post('/job-applications/update/{id}',          'JobApplicationsController@update');
        $this->post('/job-applications/delete/{id}',          'JobApplicationsController@delete');
        $this->post('/job-applications/restore/{id}',         'JobApplicationsController@restore');
        $this->post('/job-applications/hard-delete/{id}',     'JobApplicationsController@hardDelete');

        // ----------------------------------------
        // Header & Footer content
        // ----------------------------------------
        $this->get('/header',               'HeaderController@index');
        $this->get('/header/settings',      'HeaderController@settings');
        $this->post('/header/settings/update', 'HeaderController@updateSettings');
        $this->get('/header/profile',       'HeaderController@profile');
        $this->post('/header/profile/update', 'HeaderController@updateProfile');
        $this->post('/header/update',       'HeaderController@update');
        
        // Footer Management
        $this->get('/footer',               'FooterController@index');
        $this->get('/footer/add',           'FooterController@add');
        $this->post('/footer/store',        'FooterController@store');
        $this->get('/footer/edit/{id}',     'FooterController@edit');
        $this->post('/footer/update/{id}',  'FooterController@update');
        $this->post('/footer/delete/{id}',   'FooterController@delete');
        $this->get('/footer/trash',          'FooterController@trash');
        $this->post('/footer/restore/{id}',   'FooterController@restore');
        $this->post('/footer/hard-delete/{id}', 'FooterController@hardDelete');
        
        // Social Links Management
        $this->get('/footer/social',                'FooterController@social');
        $this->get('/footer/social/{platform}',     'FooterController@editSocial');
        $this->post('/footer/social/update',        'FooterController@updateSocial');
        $this->post('/footer/social/bulk-toggle',   'FooterController@bulkToggleSocial');
        $this->post('/footer/social/clear-urls',    'FooterController@clearSocialUrls');
        
        // Footer Settings
        $this->get('/footer/settings',              'FooterController@settings');
        $this->post('/footer/settings/update',      'FooterController@updateSettings');

        // ----------------------------------------
        // Home Sliders (Hero Slider)
        // ----------------------------------------
        $this->get('/home-sliders',             'HomeSlidersController@index');
        $this->get('/home-sliders/create',      'HomeSlidersController@create');
        $this->post('/home-sliders/store',      'HomeSlidersController@store');
        $this->get('/home-sliders/edit/{id}',   'HomeSlidersController@edit');
        $this->post('/home-sliders/update/{id}', 'HomeSlidersController@update');
        $this->post('/home-sliders/delete/{id}', 'HomeSlidersController@delete');
        $this->get('/home-sliders/trash',       'HomeSlidersController@trash');
        $this->post('/home-sliders/restore/{id}',  'HomeSlidersController@restore');
        $this->post('/home-sliders/hard-delete/{id}', 'HomeSlidersController@hardDelete');

        // ----------------------------------------
        // Filter Config (Mega Menu & Filters)
        // ----------------------------------------
        $this->get('/filter-config',        'FilterConfigController@index');
        $this->post('/filter-config/save',  'FilterConfigController@save');

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
