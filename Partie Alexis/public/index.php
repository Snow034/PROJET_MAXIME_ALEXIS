<?php
spl_autoload_register(function ($class) {
    if (strpos($class, 'App\\') === 0) {
        $className = substr($class, 4);
        $file = __DIR__ . '/../src/' . str_replace('\\', '/', $className) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }
});
session_start();
use App\Core\Router;
use App\Core\Env;
use App\Controller\HomeController;
use App\Controller\ArticleController;
use App\Controller\AuthController;
use App\Controller\AdminController;
Env::load(__DIR__ . '/../.env');
$router = new Router();
$router->get('/', [HomeController::class, 'index']);
$router->get('/article', [ArticleController::class, 'show']);
$router->post('/article', [ArticleController::class, 'show']);
$router->get('/login', [AuthController::class, 'login']);
$router->post('/login', [AuthController::class, 'loginPost']);
$router->get('/register', [AuthController::class, 'register']);
$router->post('/register', [AuthController::class, 'registerPost']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->get('/admin', [AdminController::class, 'dashboard']);
$router->get('/admin/articles', [AdminController::class, 'articles']);
$router->get('/admin/article/new', [AdminController::class, 'createArticle']);
$router->post('/admin/article/new', [AdminController::class, 'storeArticle']);
$router->get('/admin/article/edit', [AdminController::class, 'editArticle']);
$router->post('/admin/article/edit', [AdminController::class, 'updateArticle']);
$router->get('/admin/article/delete', [AdminController::class, 'deleteArticle']);
$router->get('/admin/users', [AdminController::class, 'users']);
$router->get('/admin/user/ban', [AdminController::class, 'banUser']);
$router->get('/admin/logs', [AdminController::class, 'logs']);
$router->get('/admin/settings', [AdminController::class, 'settings']);
$router->post('/admin/settings', [AdminController::class, 'updateSettings']);
$router->dispatch();