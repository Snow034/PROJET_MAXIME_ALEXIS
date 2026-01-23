<?php
namespace App\Controller;
use App\Core\Controller;
use App\Model\Article;
use App\Model\User;
use App\Model\Log;
use App\Model\Setting;
use App\Model\Comment;
use App\Model\Like;
class AdminController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            $this->redirect('/login');
        }
    }
    public function dashboard()
    {
        $articleModel = new Article();
        $userModel = new User();
        $logModel = new Log();
        $articles = $articleModel->getAll(5);
        $users = $userModel->getAll();
        $logs = $logModel->getAll(5);
        $this->render('admin/dashboard', [
            'title' => 'Tableau de bord',
            'articlesCount' => count($articleModel->getAll(1000)),
            'usersCount' => count($users),
            'latestArticles' => $articles,
            'latestLogs' => $logs
        ], 'admin');
    }
    public function articles()
    {
        $articleModel = new Article();
        $articles = $articleModel->getAll(50);
        $this->render('admin/articles', [
            'title' => 'Gestion des Articles',
            'articles' => $articles
        ], 'admin');
    }
    public function createArticle()
    {
        $this->render('admin/article_form', ['title' => 'Nouvel Article'], 'admin');
    }
    private function handleImageUpload()
    {
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            $fileType = mime_content_type($_FILES['image']['tmp_name']);
            if (in_array($fileType, $allowedTypes)) {
                $uploadDir = __DIR__ . '/../../public/uploads/articles/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = uniqid('article_') . '.' . $extension;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
                    return '/public/uploads/articles/' . $filename;
                }
            }
        }
        return null;
    }
    public function storeArticle()
    {
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        $imageUrl = $this->handleImageUpload();
        if ($imageUrl === null) {
            $imageUrl = '/assets/img/default-article.jpg';
        }
if (!$imageUrl)
            $imageUrl = '';
        $articleModel = new Article();
        $articleModel->create($_SESSION['user']['id'], $title, $content, $imageUrl);
        $logModel = new Log();
        $logModel->log($_SESSION['user']['id'], 'Création article', "Titre: $title");
        $this->redirect('/admin/articles');
    }
    public function editArticle()
    {
        $id = $_GET['id'] ?? null;
        if (!$id)
            $this->redirect('/admin/articles');
        $articleModel = new Article();
        $article = $articleModel->getById((int) $id);
        if (!$article)
            $this->redirect('/admin/articles');
        $this->render('admin/article_form', [
            'title' => 'Modifier Article',
            'article' => $article
        ], 'admin');
    }
    public function updateArticle()
    {
        $id = $_POST['id'] ?? null;
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        $currentImage = $_POST['current_image'] ?? '';
        $newImage = $this->handleImageUpload();
        $imageUrl = $newImage ? $newImage : $currentImage;
        if ($id) {
            $articleModel = new Article();
            $articleModel->update((int) $id, $title, $content, $imageUrl);
            $logModel = new Log();
            $logModel->log($_SESSION['user']['id'], 'Modification article', "ID: $id");
        }
        $this->redirect('/admin/articles');
    }
    public function deleteArticle()
    {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $articleModel = new Article();
            $articleModel->delete((int) $id);
            $logModel = new Log();
            $logModel->log($_SESSION['user']['id'], 'Suppression article', "ID: $id");
        }
        $this->redirect('/admin/articles');
    }
    public function users()
    {
        $userModel = new User();
        $users = $userModel->getAll();
        $this->render('admin/users', [
            'title' => 'Gestion des Utilisateurs',
            'users' => $users
        ], 'admin');
    }
    public function banUser()
    {
        $id = $_GET['id'] ?? null;
        if ($id && $id != $_SESSION['user']['id']) {
            $userModel = new User();
            $userModel->delete((int) $id);
            $logModel = new Log();
            $logModel->log($_SESSION['user']['id'], 'Bannissement utilisateur', "ID: $id");
        }
        $this->redirect('/admin/users');
    }
    public function updateUserRole()
    {
        $id = $_POST['id'] ?? null;
        $role = $_POST['role'] ?? null;
        if ($id && $role && in_array($role, ['admin', 'user'])) {
            if ($id == $_SESSION['user']['id']) {
                $this->redirect('/admin/users?error=self_update');
                exit;
            }
            $userModel = new User();
            $userModel->updateRole((int) $id, $role);
            $logModel = new Log();
            $logModel->log($_SESSION['user']['id'], 'Modification rôle', "ID: $id -> $role");
        }
        $this->redirect('/admin/users');
    }
    public function logs()
    {
        $logModel = new Log();
        $logs = $logModel->getAll(100);
        $this->render('admin/logs', [
            'title' => 'Historique des Logs',
            'logs' => $logs
        ], 'admin');
    }
    public function stats()
    {
        $userModel = new User();
        $articleModel = new Article();
        $likeModel = new Like();
        $commentModel = new Comment();
$users = $userModel->getAll();
        $articles = $articleModel->getAll(1000);
$totalArticles = count($articles);
        $totalUsers = count($users);
        $totalViews = $articleModel->getTotalViews();
        $totalLikes = $likeModel->countTotal();
        $totalComments = $commentModel->countTotal();
$detailedStats = $articleModel->getAllWithStats();
$articlesByMonth = [];
        foreach ($articles as $article) {
            $month = date('Y-m', strtotime($article['created_at']));
            if (!isset($articlesByMonth[$month]))
                $articlesByMonth[$month] = 0;
            $articlesByMonth[$month]++;
        }
        ksort($articlesByMonth);
$usersByMonth = [];
        foreach ($users as $user) {
            $month = date('Y-m', strtotime($user['created_at']));
            if (!isset($usersByMonth[$month]))
                $usersByMonth[$month] = 0;
            $usersByMonth[$month]++;
        }
        ksort($usersByMonth);
$likeDates = $likeModel->getAllDates();
        $commentDates = $commentModel->getAllDates();
        $likesByMonth = [];
        foreach ($likeDates as $date) {
            $month = date('Y-m', strtotime($date));
            if (!isset($likesByMonth[$month]))
                $likesByMonth[$month] = 0;
            $likesByMonth[$month]++;
        }
        ksort($likesByMonth);
        $commentsByMonth = [];
        foreach ($commentDates as $date) {
            $month = date('Y-m', strtotime($date));
            if (!isset($commentsByMonth[$month]))
                $commentsByMonth[$month] = 0;
            $commentsByMonth[$month]++;
        }
        ksort($commentsByMonth);
$topViews = $detailedStats;
        usort($topViews, function ($a, $b) {
            return $b['views'] <=> $a['views'];
        });
        $topViews = array_slice($topViews, 0, 5);
        $topLikes = $detailedStats;
        usort($topLikes, function ($a, $b) {
            return $b['likes_count'] <=> $a['likes_count'];
        });
        $topLikes = array_slice($topLikes, 0, 5);
$usersByRole = ['admin' => 0, 'user' => 0];
        foreach ($users as $user) {
            $role = $user['role'] ?? 'user';
            if (!isset($usersByRole[$role]))
                $usersByRole[$role] = 0;
            $usersByRole[$role]++;
        }
$articlesByAuthor = [];
        foreach ($articles as $article) {
            $author = $article['author'] ?? 'Inconnu';
            if (!isset($articlesByAuthor[$author]))
                $articlesByAuthor[$author] = 0;
            $articlesByAuthor[$author]++;
        }
        arsort($articlesByAuthor);
        $this->render('admin/stats', [
            'title' => 'Statistiques',
            'articlesByMonth' => $articlesByMonth,
            'usersByMonth' => $usersByMonth,
            'likesByMonth' => $likesByMonth,
            'commentsByMonth' => $commentsByMonth,
            'topViews' => $topViews,
            'topLikes' => $topLikes,
            'usersByRole' => $usersByRole,
            'articlesByAuthor' => $articlesByAuthor,
            'totalArticles' => $totalArticles,
            'totalUsers' => $totalUsers,
            'totalViews' => $totalViews,
            'totalLikes' => $totalLikes,
            'totalComments' => $totalComments,
            'detailedStats' => $detailedStats
        ], 'admin');
    }
    public function settings()
    {
        $settingModel = new Setting();
        $settings = $settingModel->getAll();
$formattedSettings = [];
        foreach ($settings as $s) {
            $formattedSettings[$s['setting_key']] = $s['setting_value'];
        }
        $this->render('admin/settings', [
            'title' => 'Paramètres du Site',
            'settings' => $formattedSettings
        ], 'admin');
    }
    public function updateSettings()
    {
        $settingModel = new \App\Model\Setting();
        $keysToUpdate = [
            'site_title',
            'site_description',
            'hero_title',
            'hero_subtitle'
        ];
        foreach ($keysToUpdate as $key) {
            if (isset($_POST[$key])) {
                $settingModel->set($key, $_POST[$key]);
            }
        }
$maintenanceMode = isset($_POST['maintenance_mode']) ? '1' : '0';
        $settingModel->set('maintenance_mode', $maintenanceMode);
if (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            $fileType = mime_content_type($_FILES['hero_image']['tmp_name']);
            if (in_array($fileType, $allowedTypes)) {
                $uploadDir = __DIR__ . '/../../public/uploads/settings/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $extension = pathinfo($_FILES['hero_image']['name'], PATHINFO_EXTENSION);
                $filename = 'hero_' . uniqid() . '.' . $extension;
                if (move_uploaded_file($_FILES['hero_image']['tmp_name'], $uploadDir . $filename)) {
                    $settingModel->set('hero_image_url', '/public/uploads/settings/' . $filename);
                }
            }
        }
        $logModel = new Log();
        $logModel->log($_SESSION['user']['id'], 'Mise à jour paramètres');
        $this->redirect('/admin/settings');
    }
    public function contacts()
    {
        $status = $_GET['status'] ?? null;
        $search = $_GET['q'] ?? null;
        $contactModel = new \App\Model\Contact();
        $contacts = $contactModel->getAll($status, $search);
foreach ($contacts as &$contact) {
            $contact['replies'] = $contactModel->getReplies($contact['id']);
        }
        $this->render('admin/contacts', [
            'title' => 'Messagerie',
            'contacts' => $contacts,
            'currentStatus' => $status,
            'currentSearch' => $search
        ], 'admin');
    }
    public function updateContactStatus()
    {
        $id = $_POST['id'] ?? null;
        $status = $_POST['status'] ?? null;
        if ($id && $status) {
            $contactModel = new \App\Model\Contact();
            $contactModel->updateStatus((int) $id, $status);
        }
        $this->redirect('/admin/contacts');
    }
    public function replyContact()
    {
        $id = $_POST['id'] ?? null;
        $reply = $_POST['reply'] ?? '';
        if ($id && !empty($reply)) {
            $contactModel = new \App\Model\Contact();
            $contactModel->addReply((int) $id, 'admin', $reply);
            $contactModel->updateStatus((int) $id, 'read');
$logModel = new Log();
            $logModel->log($_SESSION['user']['id'], 'Réponse message', "ID msg: $id");
        }
        $this->redirect('/admin/contacts');
    }
    public function deleteContact()
    {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $contactModel = new \App\Model\Contact();
            $contactModel->delete((int) $id);
            $logModel = new Log();
            $logModel->log($_SESSION['user']['id'], 'Suppression message', "ID: $id");
        }
        $this->redirect('/admin/contacts');
    }
}