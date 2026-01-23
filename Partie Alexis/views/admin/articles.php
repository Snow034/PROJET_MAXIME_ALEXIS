<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Gestion des Articles</h1>
    <a href="/admin/article/new" class="btn btn-primary"><i class="fas fa-plus"></i> Nouvel Article</a>
</div>
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th style="width: 50px;">ID</th>
                        <th>Titre</th>
                        <th>Auteur</th>
                        <th>Date de création</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($articles)): ?>
                        <tr>
                            <td colspan="5" class="text-center">Aucun article trouvé.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($articles as $article): ?>
                            <tr>
                                <td>
                                    <?= $article['id'] ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($article['title']) ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($article['author']) ?>
                                </td>
                                <td>
                                    <?= date('d/m/Y H:i', strtotime($article['created_at'])) ?>
                                </td>
                                <td>
                                    <a href="/admin/article/edit?id=<?= $article['id'] ?>" class="btn btn-sm btn-info"
                                        title="Modifier"><i class="fas fa-edit"></i></a>
                                    <a href="/admin/article/delete?id=<?= $article['id'] ?>" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?');"
                                        title="Supprimer"><i class="fas fa-trash"></i></a>
                                    <a href="/article?id=<?= $article['id'] ?>" target="_blank" class="btn btn-sm btn-secondary"
                                        title="Voir"><i class="fas fa-eye"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>