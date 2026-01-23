<?php
?>
<div class="row fade-in-up">
    <div class="col-lg-8 mx-auto">
        <div class="card shadow-lg border-0 rounded-3 overflow-hidden">
            <div class="card-header bg-primary text-white p-5 text-center"
                style="background-image: url('https://images.unsplash.com/photo-1519791883288-dc8bd696e667?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80'); background-size: cover; background-position: center;">
                <div
                    style="background: rgba(26, 74, 59, 0.85); position: absolute; top: 0; left: 0; width: 100%; height: 100%;">
                </div>
                <div style="position: relative; z-index: 1;">
                    <h1 class="display-4 fw-bold font-heading mb-2">Contactez-nous</h1>
                    <p class="lead opacity-75">Une question, une suggestion ou simplement envie d'échanger ?</p>
                </div>
            </div>
            <div class="card-body p-5 bg-white">
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success border-start border-success border-4 shadow-sm" role="alert">
                        <i class="fas fa-check-circle me-2"></i> Votre message a bien été envoyé. Nous vous répondrons dans
                        les plus brefs délais.
                    </div>
                <?php endif; ?>
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger border-start border-danger border-4 shadow-sm" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i> Veuillez remplir tous les champs du formulaire.
                    </div>
                <?php endif; ?>
                <form action="/contact" method="POST">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control bg-light border-0" id="name" name="name"
                                    placeholder="Votre Nom" required>
                                <label for="name" class="text-muted">Votre Nom</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="email" class="form-control bg-light border-0" id="email" name="email"
                                    placeholder="name@example.com" required>
                                <label for="email" class="text-muted">Votre Email</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating">
                                <input type="text" class="form-control bg-light border-0" id="subject" name="subject"
                                    placeholder="Sujet" required>
                                <label for="subject" class="text-muted">Sujet</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating">
                                <textarea class="form-control bg-light border-0" placeholder="Laissez votre message ici"
                                    id="message" name="message" style="height: 150px" required></textarea>
                                <label for="message" class="text-muted">Message</label>
                            </div>
                        </div>
                        <div class="col-12 text-center mt-4">
                            <button class="btn btn-primary btn-lg px-5 py-3 rounded-pill shadow-sm hover-scale"
                                type="submit">
                                <i class="fas fa-paper-plane me-2"></i> Envoyer le Message
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<style>
    .form-control:focus {
        background-color: #fff !important;
        box-shadow: 0 0 0 0.25rem rgba(26, 74, 59, 0.15);
        border: 1px solid var(--primary-color) !important;
    }
    .hover-scale {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .hover-scale:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }
</style>