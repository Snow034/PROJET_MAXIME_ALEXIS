<?php
namespace App\Model;
use PDO;
abstract class Model
{
    protected PDO $pdo;
    public function __construct()
    {
        $connection = require __DIR__ . '/../../config/database.php';
        $this->pdo = $connection();
    }
}