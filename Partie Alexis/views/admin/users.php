<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Gestion des Utilisateurs</h1>
</div>
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom d'utilisateur</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Date d'inscription</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2"
                                        style="width: 32px; height: 32px;">
                                        <?= strtoupper(substr($user['username'], 0, 1)) ?>
                                    </div>
                                    <?= htmlspecialchars($user['username']) ?>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <?php if ($user['role'] === 'admin'): ?>
                                    <span class="badge bg-danger"><i class="fas fa-shield-alt me-1"></i> Admin</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Utilisateur</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <?php if ($user['role'] !== 'admin' || $user['id'] !== $_SESSION['user']['id']): ?>
                                        <button type="button" class="btn btn-info text-white" data-bs-toggle="modal"
                                            data-bs-target="#editRoleModal<?= $user['id'] ?>" title="Modifier le rôle">
                                            <i class="fas fa-user-tag"></i>
                                        </button>
                                    <?php endif; ?>
                                    <?php if ($user['id'] !== $_SESSION['user']['id']): ?>
                                        <a href="/admin/user/ban?id=<?= $user['id'] ?>" class="btn btn-danger"
                                            onclick="return confirm('Êtes-vous sûr de vouloir bannir cet utilisateur ?');"
                                            title="Bannir">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
<div class="modal fade" id="editRoleModal<?= $user['id'] ?>" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Modifier le rôle -
                                                    <?= htmlspecialchars($user['username']) ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form action="/admin/user/role" method="POST">
                                                <div class="modal-body">
                                                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                                    <div class="mb-3">
                                                        <label class="form-label">Sélectionner un nouveau rôle :</label>
                                                        <select name="role" class="form-select">
                                                            <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>Utilisateur</option>
                                                            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Administrateur</option>
                                                        </select>
                                                        <div class="form-text text-muted mt-2">
                                                            <i class="fas fa-exclamation-triangle text-warning me-1"></i>
                                                            Attention : Un administrateur a accès à tout le back-office.
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Annuler</button>
                                                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>