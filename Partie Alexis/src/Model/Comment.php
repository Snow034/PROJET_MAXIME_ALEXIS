<?php
namespace App\Model;
class Comment extends Model
{
    public function getByArticleId(int $articleId)
    {
        $stmt = $this->pdo->prepare("
            SELECT c.*, u.username
            FROM comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.article_id = :article_id
            ORDER BY c.created_at DESC
        ");
        $stmt->execute(['article_id' => $articleId]);
        return $stmt->fetchAll();
    }
    public function create(int $articleId, int $userId, string $content)
    {
        $stmt = $this->pdo->prepare("INSERT INTO comments (article_id, user_id, content) VALUES (:article_id, :user_id, :content)");
        return $stmt->execute([
            'article_id' => $articleId,
            'user_id' => $userId,
            'content' => $content
        ]);
    }
    public function countTotal()
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM comments");
        return $stmt->fetchColumn();
    }
    public function getAllDates()
    {
        $stmt = $this->pdo->query("SELECT created_at FROM comments ORDER BY created_at ASC");
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
}