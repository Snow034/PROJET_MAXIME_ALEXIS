<?php
namespace App\Core;
abstract class Controller
{
    protected function render(string $view, array $data = [], string $layout = 'main')
    {
        extract($data);
ob_start();
        $viewPath = __DIR__ . '/../../views/' . $view . '.php';
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            echo "Vue introuvable : $view";
        }
        $content = ob_get_clean();
$layoutPath = __DIR__ . '/../../views/layout/' . $layout . '.php';
        if (file_exists($layoutPath)) {
            require $layoutPath;
        } else {
            echo $content;
        }
    }
    protected function redirect(string $url)
    {
        header("Location: $url");
        exit;
    }
    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
}