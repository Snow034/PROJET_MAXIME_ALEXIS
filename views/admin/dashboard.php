<h1 class="h3 mb-4 text-gray-800">Tableau de bord</h1>
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-primary border-4 shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Articles Publiés</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= $articlesCount ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-newspaper fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-success border-4 shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Utilisateurs Inscrits</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= $usersCount ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Derniers Articles</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Auteur</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($latestArticles as $article): ?>
                        <tr>
                            <td>
                                <?= htmlspecialchars($article['title']) ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($article['author']) ?>
                            </td>
                            <td>
                                <?= date('d/m/Y', strtotime($article['created_at'])) ?>
                            </td>
                            <td>
                                <a href="/admin/article/edit?id=<?= $article['id'] ?>" class="btn btn-sm btn-info"><i
                                        class="fas fa-edit"></i></a>
                                <a href="/article?id=<?= $article['id'] ?>" target="_blank"
                                    class="btn btn-sm btn-secondary"><i class="fas fa-eye"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>