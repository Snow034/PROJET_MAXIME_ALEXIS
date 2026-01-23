<?php
namespace App\Controller;
use App\Core\Controller;
use App\Model\User;
class AuthController extends Controller
{
    public function login()
    {
        $this->render('auth/login', [
            'title' => 'Connexion'
        ]);
    }
    public function loginPost()
    {
        if (!$this->isPost()) {
            $this->redirect('/connexion');
        }
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        if (empty($email) || empty($password)) {
            return $this->render('auth/login', ['error' => 'Tous les champs sont requis', 'title' => 'Connexion']);
        }
        $userModel = new User();
        $user = $userModel->findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role']
            ];
            $this->redirect('/');
        } else {
            return $this->render('auth/login', ['error' => 'Email ou mot de passe incorrect', 'title' => 'Connexion']);
        }
    }
    public function register()
    {
        $this->render('auth/register', [
            'title' => 'Inscription'
        ]);
    }
    public function registerPost()
    {
        if (!$this->isPost()) {
            $this->redirect('/inscription');
        }
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
            return $this->render('auth/register', ['error' => 'Tous les champs sont requis', 'title' => 'Inscription']);
        }
        if ($password !== $confirmPassword) {
            return $this->render('auth/register', ['error' => 'Les mots de passe ne correspondent pas', 'title' => 'Inscription']);
        }
        $userModel = new User();
        if ($userModel->findByEmail($email)) {
            return $this->render('auth/register', ['error' => 'Cet email est déjà utilisé', 'title' => 'Inscription']);
        }
        if ($userModel->create($username, $email, $password)) {
            $this->redirect('/connexion');
        } else {
            return $this->render('auth/register', ['error' => 'Erreur lors de l\'inscription', 'title' => 'Inscription']);
        }
    }
    public function logout()
    {
        session_destroy();
        $this->redirect('/');
    }
}