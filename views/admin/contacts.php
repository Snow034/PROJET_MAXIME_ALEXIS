<h1 class="h3 mb-4 text-gray-800">Messagerie</h1>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
            <h6 class="m-0 font-weight-bold text-primary">Messages Reçus</h6>
            <div class="d-flex align-items-center gap-2">
                <form action="/admin/contacts" method="GET" class="d-flex">
                    <?php if ($currentStatus): ?><input type="hidden" name="status"
                            value="<?= $currentStatus ?>"><?php endif; ?>
                    <div class="input-group input-group-sm">
                        <input type="text" name="q" class="form-control border-primary" placeholder="Rechercher..."
                            value="<?= htmlspecialchars($currentSearch ?? '') ?>">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                        <?php if ($currentSearch): ?>
                            <a href="/admin/contacts<?= $currentStatus ? '?status=' . $currentStatus : '' ?>"
                                class="btn btn-outline-secondary" title="Effacer"><i class="fas fa-times"></i></a>
                        <?php endif; ?>
                    </div>
                </form>
                <div class="btn-group btn-group-sm" role="group">
                    <a href="/admin/contacts<?= $currentSearch ? '?q=' . $currentSearch : '' ?>"
                        class="btn btn-outline-custom <?= !$currentStatus ? 'active' : '' ?>">Tous</a>
                    <a href="/admin/contacts?status=new<?= $currentSearch ? '&q=' . $currentSearch : '' ?>"
                        class="btn btn-outline-custom <?= $currentStatus === 'new' ? 'active' : '' ?>">Nouveaux</a>
                    <a href="/admin/contacts?status=read<?= $currentSearch ? '&q=' . $currentSearch : '' ?>"
                        class="btn btn-outline-custom <?= $currentStatus === 'read' ? 'active' : '' ?>">Lus</a>
                    <a href="/admin/contacts?status=closed<?= $currentSearch ? '&q=' . $currentSearch : '' ?>"
                        class="btn btn-outline-custom <?= $currentStatus === 'closed' ? 'active' : '' ?>">Traités</a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <?php if (empty($contacts)): ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-inbox fa-3x mb-3"></i>
                <p>Aucun message pour le moment.</p>
            </div>
        <?php else: ?>
            <div class="accordion" id="accordionContacts">
                <?php foreach ($contacts as $index => $contact): ?>
                    <?php
                    $isNew = $contact['status'] === 'new';
                    $statusBadge = match ($contact['status']) {
                        'new' => '<span class="badge bg-danger">Nouveau</span>',
                        'read' => '<span class="badge bg-warning text-dark">Lu</span>',
                        'closed' => '<span class="badge bg-secondary">Traité</span>',
                        default => '<span class="badge bg-light text-dark">Inconnu</span>'
                    };
                    $rowClass = $isNew ? 'border-left-danger' : '';
                    ?>
                    <div class="accordion-item mb-2 border rounded shadow-sm <?= $isNew ? 'border-primary border-2' : '' ?>">
                        <h2 class="accordion-header" id="heading<?= $contact['id'] ?>">
                            <button class="accordion-button <?= $index !== 0 ? 'collapsed' : '' ?>" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapse<?= $contact['id'] ?>"
                                aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>"
                                aria-controls="collapse<?= $contact['id'] ?>">
                                <div class="d-flex w-100 justify-content-between align-items-center pe-3">
                                    <div>
                                        <?= $statusBadge ?>
                                        <span class="fw-bold ms-2 text-dark">
                                            <?= htmlspecialchars($contact['subject']) ?>
                                        </span>
                                        <span class="text-muted small ms-2">-
                                            <?= htmlspecialchars($contact['name']) ?>
                                        </span>
                                    </div>
                                    <small class="text-muted">
                                        <?= date('d/m/Y H:i', strtotime($contact['created_at'])) ?>
                                    </small>
                                </div>
                            </button>
                        </h2>
                        <div id="collapse<?= $contact['id'] ?>"
                            class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>"
                            aria-labelledby="heading<?= $contact['id'] ?>" data-bs-parent="#accordionContacts">
                            <div class="accordion-body bg-light">
                                <div class="mb-3 p-3 bg-white rounded border">
                                    <h6 class="text-primary fw-bold mb-2">Message :</h6>
                                    <p class="mb-0 text-dark" style="white-space: pre-wrap;">
                                        <?= nl2br(htmlspecialchars($contact['message'])) ?>
                                    </p>
                                </div>
                                <div class="d-flex flex-column mt-3">
                                    <div class="d-flex flex-column mt-3">
                                        <?php if (!empty($contact['replies'])): ?>
                                            <h6 class="text-primary fw-bold mb-2 ps-2 border-start border-4 border-primary">
                                                Historique :</h6>
                                            <div class="mb-3">
                                                <?php foreach ($contact['replies'] as $reply): ?>
                                                    <div
                                                        class="p-3 mb-2 rounded border <?= $reply['sender_type'] === 'admin' ? 'bg-white ms-4 border-success' : 'bg-light me-4' ?>">
                                                        <div class="d-flex justify-content-between mb-1">
                                                            <strong
                                                                class="<?= $reply['sender_type'] === 'admin' ? 'text-success' : 'text-dark' ?>">
                                                                <?= $reply['sender_type'] === 'admin' ? 'Vous' : ($contact['username'] ?? $contact['name']) ?>
                                                            </strong>
                                                            <small
                                                                class="text-muted"><?= date('d/m/Y H:i', strtotime($reply['created_at'])) ?></small>
                                                        </div>
                                                        <p class="mb-0 text-dark"><?= nl2br(htmlspecialchars($reply['message'])) ?></p>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
<form action="/admin/contacts/reply" method="POST" class="mb-3 mt-2">
                                            <input type="hidden" name="id" value="<?= $contact['id'] ?>">
                                            <div class="mb-2">
                                                <label class="form-label small text-muted">Ajouter une réponse :</label>
                                                <textarea name="reply" class="form-control" rows="3"
                                                    placeholder="Écrivez votre réponse ici..."></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="fas fa-paper-plane me-1"></i> Envoyer la réponse
                                            </button>
                                        </form>
                                        <div class="d-flex justify-content-between align-items-center border-top pt-3">
                                            <div>
                                                <a href="mailto:<?= htmlspecialchars($contact['email']) ?>?subject=RE: <?= urlencode($contact['subject']) ?>"
                                                    class="btn btn-outline-secondary btn-sm">
                                                    <i class="fas fa-reply me-1"></i> Email
                                                </a>
                                                <span class="text-muted small ms-2"><i class="fas fa-envelope me-1"></i>
                                                    <?= htmlspecialchars($contact['email']) ?></span>
                                            </div>
                                            <div class="d-flex">
                                                <form action="/admin/contacts/update" method="POST" class="d-inline me-2">
                                                    <input type="hidden" name="id" value="<?= $contact['id'] ?>">
                                                    <?php if ($contact['status'] === 'new'): ?>
                                                        <button type="submit" name="status" value="read"
                                                            class="btn btn-warning btn-sm text-white" title="Marquer comme lu">
                                                            <i class="fas fa-check-double"></i> Lu
                                                        </button>
                                                    <?php endif; ?>
                                                    <?php if ($contact['status'] === 'closed'): ?>
                                                        <button type="submit" name="status" value="read"
                                                            class="btn btn-outline-secondary btn-sm" title="Réouvrir">
                                                            <i class="fas fa-box-open"></i> Réouvrir
                                                        </button>
                                                    <?php endif; ?>
                                                    <?php if ($contact['status'] !== 'closed'): ?>
                                                        <button type="submit" name="status" value="closed"
                                                            class="btn btn-secondary btn-sm" title="Archiver">
                                                            <i class="fas fa-archive"></i> Archiver
                                                        </button>
                                                    <?php endif; ?>
                                                </form>
                                                <a href="/admin/contacts/delete?id=<?= $contact['id'] ?>"
                                                    class="btn btn-outline-danger btn-sm"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce message ?')"
                                                    title="Supprimer">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
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
        .accordion-button:not(.collapsed) {
            color: var(--primary-color);
            background-color: rgba(26, 74, 59, 0.05);
        }
        .accordion-button:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.25rem rgba(197, 160, 101, 0.25);
        }
        .btn-outline-custom {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
        .btn-outline-custom:hover,
        .btn-outline-custom.active {
            background-color: var(--primary-color);
            color: #fff;
        }
    </style>