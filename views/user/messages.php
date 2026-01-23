<?php
?>
<div class="row fade-in-up">
    <div class="col-lg-10 mx-auto">
        <h1 class="serif-font mb-4 text-center">Ma Messagerie</h1>
        <?php if (empty($messages)): ?>
            <div class="text-center py-5 bg-white shadow-sm rounded">
                <i class="fas fa-paper-plane fa-3x text-muted mb-3 opacity-50"></i>
                <p class="lead text-muted">Vous n'avez envoyé aucun message pour le moment.</p>
                <a href="/contact" class="btn btn-primary mt-3">Écrire un message</a>
            </div>
        <?php else: ?>
            <div class="list-group shadow border-0">
                <?php foreach ($messages as $message): ?>
                    <div class="list-group-item list-group-item-action border-0 mb-4 rounded shadow-sm p-4">
                        <div class="d-flex w-100 justify-content-between align-items-center mb-3 border-bottom pb-2">
                            <h5 class="mb-0 serif-font fw-bold" style="color: #1a4a3b;">
                                <?= htmlspecialchars($message['subject']) ?></h5>
                            <small class="text-muted"><?= date('d/m/Y H:i', strtotime($message['created_at'])) ?></small>
                        </div>
<div class="mb-3">
                            <div class="p-3 rounded mb-2" style="background-color: #f8f9fa;">
                                <strong class="d-block text-dark mb-1">Mo:</strong>
                                <p class="mb-0 text-dark"><?= nl2br(htmlspecialchars($message['message'])) ?></p>
                            </div>
                            <?php if (!empty($message['replies'])): ?>
                                <?php foreach ($message['replies'] as $reply): ?>
                                    <div class="p-3 rounded mb-2 position-relative <?= $reply['sender_type'] === 'admin' ? 'ms-4' : 'me-4' ?>"
                                        style="background-color: <?= $reply['sender_type'] === 'admin' ? 'rgba(26, 74, 59, 0.1)' : '#f8f9fa' ?>; border-left: <?= $reply['sender_type'] === 'admin' ? '3px solid #1a4a3b' : 'none' ?>;">
                                        <strong class="d-block mb-1"
                                            style="color: <?= $reply['sender_type'] === 'admin' ? '#1a4a3b' : '#333' ?>;">
                                            <?= $reply['sender_type'] === 'admin' ? 'Admin' : 'Moi' ?>
                                        </strong>
                                        <p class="mb-0 text-dark"><?= nl2br(htmlspecialchars($reply['message'])) ?></p>
                                        <small class="text-muted d-block text-end mt-1" style="font-size: 0.75rem;">
                                            <?= date('d/m/Y H:i', strtotime($reply['created_at'])) ?>
                                        </small>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
<?php if ($message['status'] !== 'closed'): ?>
                            <form action="/mes-messages/reply" method="POST" class="mt-3">
                                <input type="hidden" name="contact_id" value="<?= $message['id'] ?>">
                                <div class="input-group">
                                    <input type="text" name="message" class="form-control" placeholder="Répondre..." required>
                                    <button class="btn text-white" type="submit"
                                        style="background-color: #1a4a3b; border-color: #1a4a3b;">Envoyer</button>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-secondary py-2 mt-3 mb-0 text-center small">
                                <i class="fas fa-lock me-1"></i> Cette conversation est fermée.
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>