<div class="container mt-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent p-0">
            <li class="breadcrumb-item"><a href="/" class="text-decoration-none theme-link fw-bold">Accueil</a></li>
            <li class="breadcrumb-item active text-muted" aria-current="page"><?= htmlspecialchars($article['title']) ?>
            </li>
        </ol>
    </nav>
    <article class="mb-5 bg-white p-4 p-md-5 rounded shadow-sm border-top border-5 theme-border-top">
        <h1 class="display-5 fw-bold mb-4 serif-font theme-text"><?= htmlspecialchars($article['title']) ?></h1>
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <div class="text-muted small text-uppercase spacing-1">
                Par <strong class="text-dark"><?= htmlspecialchars($article['author']) ?></strong> &bull;
                <?= date('d/m/Y', strtotime($article['created_at'])) ?>
            </div>
            <div>
                <?php if (isset($_SESSION['user'])): ?>
                    <form method="post" class="d-inline">
                        <input type="hidden" name="like" value="1">
                        <button type="submit" class="btn btn-outline-theme btn-sm rounded-pill px-3">
                            <i class="<?= ($likesCount ?? 0) > 0 ? 'fas' : 'far' ?> fa-heart text-danger"></i>
                            <span class="ms-1 fw-bold"><?= $likesCount ?? 0 ?></span> J'aime
                        </button>
                    </form>
                <?php else: ?>
                    <span class="badge bg-light text-dark border p-2 rounded-pill">
                        <i class="far fa-heart text-danger"></i> <?= $likesCount ?? 0 ?> J'aime
                    </span>
                <?php endif; ?>
            </div>
        </div>
        <?php if ($article['image_url']): ?>
            <div class="mb-4 overflow-hidden rounded shadow-sm">
                <img src="<?= htmlspecialchars($article['image_url']) ?>" class="img-fluid w-100 article-image"
                    alt="<?= htmlspecialchars($article['title']) ?>">
            </div>
        <?php endif; ?>
        <div class="article-content fs-5 lh-lg text-dark">
            <?= $article['content'] ?>
        </div>
    </article>
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <section class="mb-5">
                <div class="d-flex align-items-center mb-4">
                    <h3 class="serif-font theme-text mb-0 me-3">Commentaires</h3>
                    <span class="badge bg-secondary rounded-pill"><?= count($comments) ?></span>
                </div>
                <?php if (isset($_SESSION['user'])): ?>
                    <div class="card border-0 shadow-sm mb-5 bg-light">
                        <div class="card-body p-4">
                            <h5 class="card-title text-muted mb-3 fs-6 text-uppercase fw-bold">Laisser un commentaire</h5>
                            <form method="post">
                                <div class="mb-3">
                                    <textarea class="form-control border-0 shadow-sm" id="content" name="content" rows="3"
                                        placeholder="Partagez votre avis..." required style="resize: none;"></textarea>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-theme px-4 rounded-pill fw-bold">
                                        <i class="fas fa-paper-plane me-2"></i>Publier
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-light border shadow-sm text-center py-4 mb-5">
                        <p class="mb-0 text-muted">
                            <a href="/login" class="fw-bold theme-link text-decoration-none">Connectez-vous</a> pour
                            rejoindre la discussion.
                        </p>
                    </div>
                <?php endif; ?>
                <div class="comment-list">
                    <?php if (empty($comments)): ?>
                        <div class="text-center text-muted py-5">
                            <i class="far fa-comments fa-3x mb-3 opacity-25"></i>
                            <p>Soyez le premier à commenter cet article !</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($comments as $comment): ?>
                            <div class="card border-0 shadow-sm mb-3">
                                <div class="card-body p-3">
                                    <div class="d-flex w-100 justify-content-between align-items-center mb-2">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-placeholder rounded-circle bg-theme text-white d-flex align-items-center justify-content-center me-3"
                                                style="width: 40px; height: 40px; font-size: 1.2rem;">
                                                <?= strtoupper(substr($comment['username'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold text-dark"><?= htmlspecialchars($comment['username']) ?>
                                                </h6>
                                                <small class="text-muted"
                                                    style="font-size: 0.8rem;"><?= date('d F Y à H:i', strtotime($comment['created_at'])) ?></small>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="mb-0 mt-2 text-secondary ps-5 ms-1">
                                        <?= nl2br(htmlspecialchars($comment['content'])) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>
</div>
<style>
    :root {
        --theme-color: #1a4a3b;
        --theme-hover: #143a2d;
        --theme-gold: #c5a065;
    }
    .theme-text {
        color: var(--theme-color);
    }
    .theme-link {
        color: var(--theme-color);
        transition: color 0.2s;
    }
    .theme-link:hover {
        color: var(--theme-gold);
    }
    .theme-border-top {
        border-top-color: var(--theme-color) !important;
    }
    .btn-theme {
        background-color: var(--theme-color);
        color: white;
        border: none;
        transition: all 0.3s ease;
    }
    .btn-theme:hover {
        background-color: var(--theme-hover);
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .btn-outline-theme {
        color: var(--theme-color);
        border-color: var(--theme-color);
    }
    .btn-outline-theme:hover {
        background-color: var(--theme-color);
        color: white;
    }
    .bg-theme {
        background-color: var(--theme-color);
    }
    .article-image {
        transition: transform 0.5s ease;
    }
    .article-image:hover {
        transform: scale(1.02);
    }
    .spacing-1 {
        letter-spacing: 1px;
    }
    /* Breadcrumb divider override */
    .breadcrumb-item+.breadcrumb-item::before {
        color: var(--theme-gold);
    }
</style>