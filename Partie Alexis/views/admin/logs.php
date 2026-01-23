<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h1 class="h3 mb-1 serif-font theme-text fw-bold">Historique d'Activité</h1>
        <p class="mb-0 text-muted small">Suivi des actions et de la sécurité du site.</p>
    </div>
    <div class="d-none d-md-block">
        <span class="badge bg-white text-secondary shadow-sm p-2 border">
            <i class="fas fa-history me-1"></i> 100 derniers logs
        </span>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <?php if (empty($logs)): ?>
            <div class="text-center py-5">
                <div class="mb-3 text-muted opacity-25">
                    <i class="fas fa-clipboard-list fa-4x"></i>
                </div>
                <h4 class="text-muted fw-light">Aucune activité enregistrée pour le moment.</h4>
            </div>
        <?php else: ?>
            <div class="timeline">
                <?php foreach ($logs as $index => $log): ?>
                    <?php
$icon = 'fa-info';
                    $borderColor = 'var(--primary-color)';
                    $textColor = 'var(--primary-color)';
                    $bgColor = 'rgba(26, 74, 59, 0.04)';
                    $actionLower = strtolower($log['action']);
                    if (strpos($actionLower, 'login') !== false || strpos($actionLower, 'connexion') !== false) {
                        $icon = 'fa-key';
                        $borderColor = 'var(--primary-color)';
                        $textColor = 'var(--primary-color)';
                        $bgColor = 'rgba(26, 74, 59, 0.05)';
                    } elseif (strpos($actionLower, 'logout') !== false || strpos($actionLower, 'déconnexion') !== false) {
                        $icon = 'fa-sign-out-alt';
                        $borderColor = '#8c8c8c';
                        $textColor = '#6c757d';
                        $bgColor = '#f8f9fa';
                    } elseif (strpos($actionLower, 'create') !== false || strpos($actionLower, 'ajout') !== false) {
                        $icon = 'fa-plus-circle';
                        $borderColor = 'var(--primary-color)';
                        $textColor = 'var(--primary-color)';
                        $bgColor = 'rgba(26, 74, 59, 0.05)';
                    } elseif (strpos($actionLower, 'update') !== false || strpos($actionLower, 'modif') !== false) {
                        $icon = 'fa-pen-nib';
                        $borderColor = 'var(--accent-color)';
                        $textColor = '#b08d55';
                        $bgColor = 'rgba(197, 160, 101, 0.08)';
                    } elseif (strpos($actionLower, 'delete') !== false || strpos($actionLower, 'suppress') !== false || strpos($actionLower, 'ban') !== false) {
                        $icon = 'fa-trash-alt';
                        $borderColor = '#a33b3b';
                        $textColor = '#a33b3b';
                        $bgColor = 'rgba(163, 59, 59, 0.05)';
                    } elseif (strpos($actionLower, 'upload') !== false) {
                        $icon = 'fa-image';
                        $borderColor = 'var(--primary-color)';
                        $textColor = 'var(--primary-color)';
                        $bgColor = 'rgba(26, 74, 59, 0.05)';
                    }
$date = new DateTime($log['created_at']);
                    $now = new DateTime();
                    $interval = $now->diff($date);
                    if ($interval->d == 0 && $interval->h == 0 && $interval->i < 5) {
                        $timeText = "À l'instant";
                    } elseif ($interval->d == 0) {
                        $timeText = "Aujourd'hui à " . $date->format('H:i');
                    } elseif ($interval->d == 1) {
                        $timeText = "Hier à " . $date->format('H:i');
                    } else {
                        $timeText = $date->format('d/m/Y à H:i');
                    }
                    ?>
                    <div class="card border-0 shadow-sm mb-3 log-card hover-lift"
                        style="border-left: 4px solid <?= $borderColor ?> !important;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 45px; height: 45px; background-color: <?= $bgColor ?>; color: <?= $textColor ?>; border: 1px solid <?= $textColor ?>25;">
                                        <i class="fas <?= $icon ?>"></i>
                                    </div>
                                </div>
<div class="flex-grow-1 min-w-0">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <h6 class="mb-0 fw-bold text-dark text-truncate"
                                            style="font-family: 'Playfair Display', serif;">
                                            <?= htmlspecialchars($log['action']) ?>
                                        </h6>
                                        <small class="text-muted ms-2 text-nowrap" title="<?= $log['created_at'] ?>">
                                            <i class="far fa-clock me-1"></i> <?= $timeText ?>
                                        </small>
                                    </div>
                                    <p class="mb-1 text-muted small">
                                        <?= htmlspecialchars($log['details']) ?>
                                    </p>
                                    <div class="d-flex align-items-center mt-2">
                                        <div class="d-flex align-items-center me-3">
                                            <div class="avatar-xs rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2"
                                                style="width: 20px; height: 20px; font-size: 10px;">
                                                <?= strtoupper(substr($log['username'] ?? 'S', 0, 1)) ?>
                                            </div>
                                            <span
                                                class="small fw-semibold text-secondary"><?= htmlspecialchars($log['username'] ?? 'Système') ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<style>
    :root {
        --primary-color: #1a4a3b;
        --accent-color: #c5a065;
        --theme-text: #2c3e50;
    }
    .hover-lift {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hover-lift:hover {
        transform: translateY(-2px);
        box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .08) !important;
    }
</style>