<?php
namespace App\Model;
class Like extends Model
{
    public function add(int $userId, int $articleId)
    {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO likes (user_id, article_id) VALUES (:user_id, :article_id)");
            return $stmt->execute([
                'user_id' => $userId,
                'article_id' => $articleId
            ]);
        } catch (\PDOException $e) {
            return false;
        }
    }
    public function remove(int $userId, int $articleId)
    {
        $stmt = $this->pdo->prepare("DELETE FROM likes WHERE user_id = :user_id AND article_id = :article_id");
        return $stmt->execute([
            'user_id' => $userId,
            'article_id' => $articleId
        ]);
    }
    public function hasLiked(int $userId, int $articleId)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM likes WHERE user_id = :user_id AND article_id = :article_id");
        $stmt->execute([
            'user_id' => $userId,
            'article_id' => $articleId
        ]);
        return $stmt->fetchColumn() > 0;
    }
    public function countByArticle(int $articleId)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM likes WHERE article_id = :article_id");
        $stmt->execute(['article_id' => $articleId]);
        return $stmt->fetchColumn();
    }
    public function countTotal()
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM likes");
        return $stmt->fetchColumn();
    }
    public function getAllDates()
    {
        $stmt = $this->pdo->query("SELECT created_at FROM likes ORDER BY created_at ASC");
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
}