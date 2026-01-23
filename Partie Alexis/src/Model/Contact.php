<?php
namespace App\Model;
class Contact extends Model
{
    public function create(string $name, string $email, string $subject, string $message, ?int $userId = null)
    {
        $stmt = $this->pdo->prepare("INSERT INTO contacts (name, email, subject, message, user_id) VALUES (:name, :email, :subject, :message, :user_id)");
        return $stmt->execute([
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message,
            'user_id' => $userId
        ]);
    }
    public function getAll(?string $status = null, ?string $search = null)
    {
        $sql = "SELECT contacts.*, users.username FROM contacts LEFT JOIN users ON contacts.user_id = users.id WHERE 1=1";
        $params = [];
        if ($status) {
            $sql .= " AND contacts.status = :status";
            $params['status'] = $status;
        }
        if ($search) {
            $sql .= " AND (contacts.name LIKE :search OR contacts.email LIKE :search OR contacts.subject LIKE :search OR contacts.message LIKE :search)";
            $params['search'] = "%$search%";
        }
$sql .= " ORDER BY contacts.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    public function getByUserId(int $userId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM contacts WHERE user_id = :user_id ORDER BY created_at DESC");
        $stmt->execute(['user_id' => $userId]);
        $contacts = $stmt->fetchAll();
        foreach ($contacts as &$contact) {
            $contact['replies'] = $this->getReplies($contact['id']);
        }
        return $contacts;
    }
    public function getReplies(int $contactId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM contact_replies WHERE contact_id = :contact_id ORDER BY created_at ASC");
        $stmt->execute(['contact_id' => $contactId]);
        return $stmt->fetchAll();
    }
    public function addReply(int $contactId, string $senderType, string $message)
    {
        $stmt = $this->pdo->prepare("INSERT INTO contact_replies (contact_id, sender_type, message) VALUES (:contact_id, :sender_type, :message)");
        return $stmt->execute([
            'contact_id' => $contactId,
            'sender_type' => $senderType,
            'message' => $message
        ]);
    }
    public function updateStatus(int $id, string $status)
    {
        $stmt = $this->pdo->prepare("UPDATE contacts SET status = :status WHERE id = :id");
        return $stmt->execute(['id' => $id, 'status' => $status]);
    }
    public function getUnreadCount()
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM contacts WHERE status = 'new'");
        return $stmt->fetchColumn();
    }
    public function delete(int $id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM contacts WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}