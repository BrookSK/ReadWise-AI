<?php
namespace Core;

class Controller
{
    protected function view(string $view, array $data = [])
    {
        extract($data);
        $viewFile = __DIR__ . '/../app/views/' . $view . '.php';
        $layout = __DIR__ . '/../app/views/layout.php';
        ob_start();
        require $viewFile;
        $content = ob_get_clean();
        require $layout;
    }
}
