<?php
class ErrorController extends BaseController
{
    public function notFound()
    {
        http_response_code(404);
        include __DIR__ . '/../../errors/404.php';
    }

    public function serverError()
    {
        http_response_code(500);
        include __DIR__ . '/../../errors/500.php';
    }
}
