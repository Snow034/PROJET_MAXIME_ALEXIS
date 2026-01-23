<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
spl_autoload_register(function ($class) {
    if (strpos($class, 'App\\') === 0) {
        $className = substr($class, 4);
        $file = __DIR__ . '/src/' . str_replace('\\', '/', $className) . '.php';
        if (file_exists($file)) {
            require $file;
        } else {
        }
    }
});
@session_start();
use App\Core\Router;
use App\Core\Env;
use App\Controller\HomeController;
use App\Controller\ArticleController;
use App\Controller\AuthController;
use App\Controller\AdminController;
try {
    echo "<!-- DEBUG: Start -->";
echo "<!-- DEBUG: Loading Env -->";
    if (file_exists(__DIR__ . '/.env')) {
        Env::load(__DIR__ . '/.env');
    } else {
        echo "WARNING: .env not found<br>";
    }
    echo "<!-- DEBUG: Instantiating Router -->";
    $router = new Router();
try {
        $settingCheck = new \App\Model\Setting();
        $isMaintenance = $settingCheck->get('maintenance_mode');
        if ($isMaintenance === '1') {
            $isAdmin = isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
            $isLoginRoute = $_SERVER['REQUEST_URI'] === '/connexion' || $_SERVER['REQUEST_URI'] === '/login';
if (!$isAdmin && !$isLoginRoute && strpos($_SERVER['REQUEST_URI'], '/admin') !== 0) {
                require __DIR__ . '/views/maintenance.php';
                exit;
            }
        }
    } catch (\Throwable $t) {
}
    echo "<!-- DEBUG: Defining Routes -->";
    $router->get('/', [HomeController::class, 'index']);
    $router->get('/article', [ArticleController::class, 'show']);
    $router->post('/article', [ArticleController::class, 'show']);
    $router->get('/connexion', [AuthController::class, 'login']);
    $router->post('/connexion', [AuthController::class, 'loginPost']);
    $router->get('/inscription', [AuthController::class, 'register']);
    $router->post('/inscription', [AuthController::class, 'registerPost']);
    $router->get('/logout', [AuthController::class, 'logout']);
$router->get('/admin', [AdminController::class, 'dashboard']);
    $router->get('/admin/articles', [AdminController::class, 'articles']);
    $router->get('/admin/article/new', [AdminController::class, 'createArticle']);
    $router->post('/admin/article/new', [AdminController::class, 'storeArticle']);
    $router->get('/admin/article/edit', [AdminController::class, 'editArticle']);
    $router->post('/admin/article/edit', [AdminController::class, 'updateArticle']);
    $router->get('/admin/article/delete', [AdminController::class, 'deleteArticle']);
    $router->get('/admin/stats', [AdminController::class, 'stats']);
    $router->get('/admin/users', [AdminController::class, 'users']);
    $router->get('/admin/user/ban', [AdminController::class, 'banUser']);
    $router->post('/admin/user/role', [AdminController::class, 'updateUserRole']);
    $router->get('/admin/logs', [AdminController::class, 'logs']);
    $router->get('/admin/settings', [AdminController::class, 'settings']);
    $router->post('/admin/settings', [AdminController::class, 'updateSettings']);
    $router->get('/admin/contacts', [AdminController::class, 'contacts']);
    $router->post('/admin/contacts/update', [AdminController::class, 'updateContactStatus']);
    $router->post('/admin/contacts/reply', [AdminController::class, 'replyContact']);
    $router->get('/admin/contacts/delete', [AdminController::class, 'deleteContact']);
$router->get('/contact', [App\Controller\ContactController::class, 'index']);
    $router->post('/contact', [App\Controller\ContactController::class, 'send']);
    $router->get('/mes-messages', [App\Controller\ContactController::class, 'messages']);
    $router->post('/mes-messages/reply', [App\Controller\ContactController::class, 'reply']);
    echo "<!-- DEBUG: Dispatching -->";
    $router->dispatch();
} catch (\Throwable $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 20px; border: 1px solid #f5c6cb; margin: 20px; font-family: sans-serif;'>";
    echo "<h3>Une erreur est survenue (Debug)</h3>";
    echo "<strong>Message :</strong> " . htmlspecialchars($e->getMessage()) . "<br>";
    echo "<strong>Fichier :</strong> " . htmlspecialchars($e->getFile()) . "<br>";
    echo "<strong>Ligne :</strong> " . $e->getLine() . "<br>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}