<?php
namespace App\Controller;
use App\Core\Controller;
use App\Model\Article;
use App\Model\Like;
use App\Model\Comment;
class ArticleController extends Controller
{
    public function show()
    {
        $id = $_GET['id'] ?? null;
        if (!$id)
            $this->redirect('/');
        $articleModel = new Article();
        $article = $articleModel->getById((int) $id);
        if (!$article) {
            http_response_code(404);
            echo "Article non trouvé.";
            return;
        }
$articleModel->incrementViews((int) $id);
        $commentModel = new Comment();
        $likeModel = new Like();
        if ($this->isPost()) {
            if (isset($_POST['content']) && isset($_SESSION['user'])) {
                $content = $_POST['content'];
                if (!empty($content)) {
                    $commentModel->create($id, $_SESSION['user']['id'], $content);
                }
            }
            if (isset($_POST['like']) && isset($_SESSION['user'])) {
                $userId = $_SESSION['user']['id'];
                if ($likeModel->hasLiked($userId, $id)) {
                    $likeModel->remove($userId, $id);
                } else {
                    $likeModel->add($userId, $id);
                }
            }
            $this->redirect("/article?id=$id");
        }
        $comments = $commentModel->getByArticleId((int) $id);
        $likesCount = $likeModel->countByArticle((int) $id);
        $userLiked = false;
        if (isset($_SESSION['user'])) {
            $userLiked = $likeModel->hasLiked($_SESSION['user']['id'], (int) $id);
        }
        $this->render('article/show', [
            'title' => $article['title'],
            'article' => $article,
            'comments' => $comments,
            'likesCount' => $likesCount,
            'userLiked' => $userLiked
        ]);
    }
}