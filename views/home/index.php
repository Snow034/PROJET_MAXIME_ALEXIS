<div class="hero-section position-relative mb-5" style="background-color: #1a4a3b; color: #ffffff; padding: 100px 0;">
    <div class="container position-relative z-2">
        <div class="row align-items-center">
            <div class="col-lg-5 mb-5 mb-lg-0 text-center">
                <div class="position-relative d-inline-block">
                    <div
                        style="position: absolute; top: 20px; left: 20px; width: 100%; height: 100%; border: 2px solid var(--accent-color); z-index: 0; opacity: 0.6;">
                    </div>
                    <img src="<?= !empty($settings['hero_image_url']) ? htmlspecialchars($settings['hero_image_url']) : '/public/assets/img/portrait.jpg' ?>"
                        alt="Portrait" class="img-fluid position-relative z-1 shadow"
                        style="max-height: 500px; object-fit: cover; width: 100%; background-color: #eee;">
                </div>
            </div>
<div class="col-lg-7 text-center text-lg-start ps-lg-5">
                <span class="script-font fs-3 mb-2 d-block" style="color: var(--accent-color) !important;">Conférences
                    de</span>
                <h1 class="display-3 fw-bold mb-4"
                    style="line-height: 1.1; font-family: 'Playfair Display', serif; color: #ffffff;">
                    <?= nl2br($settings['hero_title'] ?? "Solange Anastasia\nChopplet") ?>
                </h1>
                <p class="lead mb-5 opacity-75 mx-auto mx-lg-0"
                    style="max-width: 700px; font-weight: 300; font-size: 1.25rem; color: #e0e0e0;">
                    <?= nl2br($settings['hero_subtitle'] ?? "Analyses littéraires, critiques théâtrales et conférences sur l'histoire de l'art. Un espace de réflexion et de partage.") ?>
                </p>
                <div class="d-flex justify-content-center justify-content-lg-start">
                    <a href="#articles" class="btn btn-primary px-5 py-3 shadow-lg">
                        Lire les travaux <i class="fas fa-book-reader ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
<div
        style="position: absolute; bottom: 0; right: 10%; width: 1px; height: 100px; background: var(--accent-color); opacity: 0.3;">
    </div>
</div>
<div class="container mb-5">
    <div class="text-center position-relative">
        <hr style="border-color: var(--accent-color); opacity: 0.3;">
        <span class="position-absolute top-50 start-50 translate-middle bg-body px-3 text-muted"
            style="font-family: 'Playfair Display', serif; font-style: italic; background: var(--bg-color) !important;">Dernières
            publications</span>
    </div>
</div>
<div class="container" id="articles">
    <div class="row g-5 justify-content-center">
        <?php if (empty($articles)): ?>
            <div class="col-12 text-center py-5">
                <div class="p-5 border rounded-3 bg-white shadow-sm opacity-50">
                    <i class="fas fa-book-reader fa-3x mb-3" style="color: var(--primary-color);"></i>
                    <h3 class="h5 serif-font">La bibliothèque est vide pour le moment</h3>
                    <p class="text-muted">Les écrits arriveront très prochainement.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="col-lg-10">
                <?php foreach ($articles as $article): ?>
                    <article class="card-horizontal mb-5">
                        <div class="img-wrapper">
                            <a href="/article?id=<?= $article['id'] ?>">
                                <?php if ($article['image_url']): ?>
                                    <img src="<?= htmlspecialchars($article['image_url']) ?>"
                                        alt="<?= htmlspecialchars($article['title']) ?>">
                                <?php else: ?>
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light">
                                        <i class="fas fa-image fa-2x text-muted opacity-25"></i>
                                    </div>
                                <?php endif; ?>
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <span class="text-uppercase small fw-bold"
                                    style="color: var(--accent-color); letter-spacing: 1px;">
                                    Article
                                </span>
                                <span class="mx-2 text-muted">•</span>
                                <span class="small text-muted"><?= date('d F Y', strtotime($article['created_at'])) ?></span>
                            </div>
                            <h2 class="h3 fw-bold mb-3 serif-font">
                                <a href="/article?id=<?= $article['id'] ?>" class="text-decoration-none"
                                    style="color: var(--primary-color);">
                                    <?= htmlspecialchars($article['title']) ?>
                                </a>
                            </h2>
                            <p class="text-muted mb-4"
                                style="line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                <?= substr(strip_tags($article['content']), 0, 200) ?>...
                            </p>
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="small fst-italic text-muted">Par <?= htmlspecialchars($article['author']) ?></span>
                                <a href="/article?id=<?= $article['id'] ?>"
                                    class="btn btn-outline-primary btn-sm px-3 rounded-0">
                                    Lire la suite
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>