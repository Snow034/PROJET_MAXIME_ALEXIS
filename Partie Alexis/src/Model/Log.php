<?php
namespace App\Model;
class Log extends Model
{
    public function log(int $userId, string $action, ?string $details = null)
    {
$stmt = $this->pdo->prepare("INSERT INTO logs (user_id, action, details) VALUES (:user_id, :action, :details)");
        return $stmt->execute([
            'user_id' => $userId,
            'action' => $action,
            'details' => $details
        ]);
    }
    public function getAll(int $limit = 50)
    {
        $stmt = $this->pdo->prepare("
            SELECT l.*, u.username
            FROM logs l
            LEFT JOIN users u ON l.user_id = u.id
            ORDER BY l.created_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}