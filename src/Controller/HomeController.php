<?php
namespace App\Controller;
use App\Core\Controller;
use App\Model\Article;
class HomeController extends Controller
{
    public function index()
    {
        $articleModel = new Article();
        $articles = $articleModel->getAll(6);
        $settingModel = new \App\Model\Setting();
        $settingsRaw = $settingModel->getAll();
        $settings = [];
        foreach ($settingsRaw as $s) {
            $settings[$s['setting_key']] = $s['setting_value'];
        }
        $this->render('home/index', [
            'title' => ($settings['site_title'] ?? 'Accueil') . ' - S. A. Chopplet',
            'articles' => $articles,
            'settings' => $settings
        ]);
    }
}