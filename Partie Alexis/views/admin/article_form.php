<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <?= $title ?>
    </h1>
    <a href="/admin/articles" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
</div>
<div class="card shadow mb-4">
    <div class="card-body">
        <form action="<?= isset($article) ? '/admin/article/edit' : '/admin/article/new' ?>" method="post"
            enctype="multipart/form-data">
            <?php if (isset($article)): ?>
                <input type="hidden" name="id" value="<?= $article['id'] ?>">
                <input type="hidden" name="current_image" value="<?= htmlspecialchars($article['image_url'] ?? '') ?>">
            <?php endif; ?>
            <div class="mb-3">
                <label for="title" class="form-label">Titre de l'article</label>
                <input type="text" class="form-control" id="title" name="title"
                    value="<?= isset($article) ? htmlspecialchars($article['title']) : '' ?>" required>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Image de couverture</label>
                <?php if (isset($article) && !empty($article['image_url'])): ?>
                    <div class="mb-2">
                        <img src="<?= htmlspecialchars($article['image_url']) ?>" alt="Image actuelle" class="img-thumbnail"
                            style="max-height: 200px;">
                    </div>
                <?php endif; ?>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                <small class="text-muted">Formats acceptés : JPG, PNG, WEBP. Laissez vide pour conserver l'image
                    actuelle.</small>
            </div>
            <div class="mb-3">
                <label for="summernote" class="form-label">Contenu</label>
                <textarea id="summernote" name="content"><?= isset($article) ? $article['content'] : '' ?></textarea>
            </div>
            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Enregistrer</button>
        </form>
    </div>
</div>