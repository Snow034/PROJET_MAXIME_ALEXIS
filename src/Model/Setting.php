<?php
namespace App\Model;
class Setting extends Model
{
    public function getAll()
    {
        $stmt = $this->pdo->query("SELECT * FROM settings");
        return $stmt->fetchAll();
    }
    public function update(string $key, string $value)
    {
        $stmt = $this->pdo->prepare("UPDATE settings SET setting_value = :value WHERE setting_key = :key");
        return $stmt->execute(['value' => $value, 'key' => $key]);
    }
    public function get(string $key)
    {
        $stmt = $this->pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = :key");
        $stmt->execute(['key' => $key]);
        $result = $stmt->fetch();
        return $result ? $result['setting_value'] : null;
    }
    public function set(string $key, ?string $value)
    {
        $stmt = $this->pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (:key, :value) ON DUPLICATE KEY UPDATE setting_value = :value");
        return $stmt->execute(['key' => $key, 'value' => $value]);
    }
}