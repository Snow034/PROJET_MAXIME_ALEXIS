<?php
namespace App\Model;
class Article extends Model
{
    public function getAll(int $limit = 10, int $offset = 0)
    {
        $stmt = $this->pdo->prepare("
            SELECT a.*, u.username as author
            FROM articles a
            JOIN users u ON a.user_id = u.id
            ORDER BY a.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function getById(int $id)
    {
        $stmt = $this->pdo->prepare("
            SELECT a.*, u.username as author
            FROM articles a
            JOIN users u ON a.user_id = u.id
            WHERE a.id = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    public function create(int $userId, string $title, string $content, ?string $imageUrl = null)
    {
        $stmt = $this->pdo->prepare("INSERT INTO articles (user_id, title, content, image_url) VALUES (:user_id, :title, :content, :image_url)");
        return $stmt->execute([
            'user_id' => $userId,
            'title' => $title,
            'content' => $content,
            'image_url' => $imageUrl
        ]);
    }
    public function update(int $id, string $title, string $content, ?string $imageUrl = null)
    {
        $sql = "UPDATE articles SET title = :title, content = :content";
        $params = [
            'id' => $id,
            'title' => $title,
            'content' => $content
        ];
        if ($imageUrl !== null) {
            $sql .= ", image_url = :image_url";
            $params['image_url'] = $imageUrl;
        }
        $sql .= " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }
    public function incrementViews(int $id)
    {
        $stmt = $this->pdo->prepare("UPDATE articles SET views = views + 1 WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
    public function getTotalViews()
    {
        $stmt = $this->pdo->query("SELECT SUM(views) FROM articles");
        return $stmt->fetchColumn() ?: 0;
    }
public function getAllWithStats()
    {
        $sql = "
            SELECT a.*,
                   u.username as author,
                   (SELECT COUNT(*) FROM comments c WHERE c.article_id = a.id) as comments_count,
                   (SELECT COUNT(*) FROM likes l WHERE l.article_id = a.id) as likes_count
            FROM articles a
            JOIN users u ON a.user_id = u.id
            ORDER BY a.created_at DESC
        ";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }
    public function delete(int $id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM articles WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}