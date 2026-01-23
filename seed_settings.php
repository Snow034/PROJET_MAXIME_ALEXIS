<?php
require __DIR__ . '/index.php';
use App\Model\Setting;
$settingModel = new Setting();
$defaults = [
    'maintenance_mode' => '0',
    'hero_title' => 'Solange Anastasia Chopplet',
    'hero_subtitle' => 'Analyses littéraires, critiques théâtrales et conférences sur l\'histoire de l\'art. Un espace de réflexion et de partage.',
    'hero_image_url' => '/public/assets/img/portrait.jpg'
];
foreach ($defaults as $key => $value) {
    $existing = $settingModel->get($key);
    if ($existing === null) {
        $settingModel->set($key, $value);
        echo "Set default for $key\n";
    } else {
        echo "Skipped $key (already exists)\n";
    }
}
echo "Seeding completed.";
exit;