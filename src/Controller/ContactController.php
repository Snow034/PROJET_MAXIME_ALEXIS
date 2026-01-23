<?php
namespace App\Controller;
use App\Core\Controller;
use App\Model\Contact;
class ContactController extends Controller
{
    public function index()
    {
        $user = $_SESSION['user'] ?? null;
        $name = $user ? $user['username'] : '';
        $email = $user ? $user['email'] : '';
        $this->render('contact/index', [
            'title' => 'Contact',
            'name' => $name,
            'email' => $email
        ], 'main');
    }
    public function messages()
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('/connexion');
        }
        $contactModel = new Contact();
        $messages = $contactModel->getByUserId($_SESSION['user']['id']);
        $this->render('user/messages', [
            'title' => 'Ma Messagerie',
            'messages' => $messages
        ], 'main');
    }
    public function send()
    {
        if ($this->isPost()) {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $subject = $_POST['subject'] ?? '';
            $message = $_POST['message'] ?? '';
            $userId = isset($_SESSION['user']) ? $_SESSION['user']['id'] : null;
            if (empty($name) || empty($email) || empty($message)) {
                $this->redirect('/contact?error=missing_fields');
                return;
            }
            $contactModel = new Contact();
            $contactModel->create($name, $email, $subject, $message, $userId);
            $this->redirect('/contact?success=1');
        }
    }
    public function reply()
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('/connexion');
        }
        if ($this->isPost()) {
            $contactId = $_POST['contact_id'] ?? null;
            $message = $_POST['message'] ?? '';
            if ($contactId && $message) {
                $contactModel = new Contact();
                $contactModel->addReply((int) $contactId, 'user', $message);
                $contactModel->updateStatus((int) $contactId, 'new');
            }
        }
        $this->redirect('/mes-messages');
    }
}