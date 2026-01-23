<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Paramètres du Site</h1>
</div>
<div class="card shadow mb-4">
    <div class="card-body">
        <form action="/admin/settings" method="post" enctype="multipart/form-data">
            <ul class="nav nav-tabs mb-4" id="settingsTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="hero-tab" data-bs-toggle="tab" data-bs-target="#hero" type="button"
                        role="tab" aria-controls="hero" aria-selected="true">Page d'Accueil (Hero)</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link text-danger" id="maintenance-tab" data-bs-toggle="tab"
                        data-bs-target="#maintenance" type="button" role="tab" aria-controls="maintenance"
                        aria-selected="false"><i class="fas fa-tools"></i> Maintenance</button>
                </li>
            </ul>
            <div class="tab-content" id="settingsTabContent">
<div class="tab-pane fade show active" id="hero" role="tabpanel" aria-labelledby="hero-tab">
                    <div class="alert alert-info">
                        Personnalisez la bannière (Hero) de la page d'accueil.
                    </div>
                    <div class="mb-3">
                        <label for="hero_title" class="form-label">Grand Titre</label>
                        <input type="text" class="form-control" id="hero_title" name="hero_title"
                            value="<?= htmlspecialchars($settings['hero_title'] ?? '') ?>">
                        <div class="form-text">Ce titre apparaît en très grand.</div>
                    </div>
                    <div class="mb-3">
                        <label for="hero_subtitle" class="form-label">Sous-titre / Description</label>
                        <textarea class="form-control" id="hero_subtitle" name="hero_subtitle"
                            rows="3"><?= htmlspecialchars($settings['hero_subtitle'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="hero_image" class="form-label">Image du Portrait (Gauche)</label>
                        <?php if (!empty($settings['hero_image_url'])): ?>
                            <div class="mb-2">
                                <img src="<?= htmlspecialchars($settings['hero_image_url']) ?>" alt="Hero Current"
                                    class="img-thumbnail" style="max-height: 150px;">
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="hero_image" name="hero_image" accept="image/*">
                        <div class="form-text">Laissez vide pour conserver l'image actuelle.</div>
                    </div>
                </div>
<div class="tab-pane fade" id="maintenance" role="tabpanel" aria-labelledby="maintenance-tab">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <i class="fas fa-exclamation-triangle"></i> Zone de Danger
                        </div>
                        <div class="card-body">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="maintenance_mode"
                                    name="maintenance_mode" value="1" <?= ($settings['maintenance_mode'] ?? '0') === '1' ? 'checked' : '' ?> onchange="confirmMaintenance(this)">
                                <label class="form-check-label fw-bold" for="maintenance_mode">Activer le Mode
                                    Maintenance</label>
                            </div>
                            <p class="text-muted small">
                                Si vous activez cette option, le site sera inaccessible pour les visiteurs (ils verront
                                une page "En Maintenance").
                                Seuls les administrateurs connectés pourront accéder au site.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="mt-4">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer les
                paramètres</button>
        </form>
        <script>
            function confirmMaintenance(checkbox) {
                if (checkbox.checked) {
                    if (!confirm("ATTENTION : Si vous activez l'option de maintenance, les utilisateurs ne pourront plus accéder au site tant que vous ne l'aurez pas désactivée. Êtes-vous sûr ?")) {
                        checkbox.checked = false;
                    }
                }
            }
        </script>
    </div>
</div>