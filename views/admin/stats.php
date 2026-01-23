<h1 class="h3 mb-4 text-gray-800">Statistiques Détaillées</h1>
<div class="row mb-4">
    <div class="col-xl-2 col-md-6 mb-4">
        <div class="card border-start border-primary border-4 shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Articles</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalArticles ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-newspaper fa-2x text-primary"></i></div>
                </div>
            </div>
        </div>
    </div>
<div class="col-xl-2 col-md-6 mb-4">
        <div class="card border-start border-success border-4 shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Utilisateurs</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalUsers ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-users fa-2x text-success"></i></div>
                </div>
            </div>
        </div>
    </div>
<div class="col-xl-2 col-md-6 mb-4">
        <div class="card border-start border-info border-4 shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Vues Totales</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalViews ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-eye fa-2x text-info"></i></div>
                </div>
            </div>
        </div>
    </div>
<div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-warning border-4 shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">J'aime (Likes)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalLikes ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-heart fa-2x text-warning"></i></div>
                </div>
            </div>
        </div>
    </div>
<div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-secondary border-4 shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Commentaires</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalComments ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-comments fa-2x text-secondary"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Publications par Mois</h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="articlesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
<div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Répartition Utilisateurs</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="usersPieChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Articles par Auteur</h6>
            </div>
            <div class="card-body">
                <canvas id="authorsChart"></canvas>
            </div>
        </div>
    </div>
<div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Inscriptions par Mois</h6>
            </div>
            <div class="card-body">
                <canvas id="usersChart"></canvas>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Évolution des Interactions (Likes & Commentaires)</h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="interactionsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Top 5 Articles les plus Vus</h6>
            </div>
            <div class="card-body">
                <canvas id="topViewsChart"></canvas>
            </div>
        </div>
    </div>
<div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Top 5 Articles les plus Aimés</h6>
            </div>
            <div class="card-body">
                <canvas id="topLikesChart"></canvas>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Configuration globale des couleurs
    const themePrimary = '#1a4a3b';
    const themeSuccess = '#2d5a4e';
    const themeAccent = '#c5a065';
    const themeBg = '#fcfbf8';
    // Données PHP vers JS
    const articlesData = <?= json_encode($articlesByMonth) ?>;
    const usersData = <?= json_encode($usersByMonth) ?>;
    const rolesData = <?= json_encode($usersByRole) ?>;
    const authorsData = <?= json_encode($articlesByAuthor) ?>;
    // Nouvelles Données
    const likesData = <?= json_encode($likesByMonth) ?>;
    const commentsData = <?= json_encode($commentsByMonth) ?>;
    // Top 5 Data
    const topViewsRaw = <?= json_encode($topViews) ?>;
    const topLikesRaw = <?= json_encode($topLikes) ?>;
    const topViewsLabels = topViewsRaw.map(a => a.title.length > 20 ? a.title.substring(0, 20) + '...' : a.title);
    const topViewsValues = topViewsRaw.map(a => a.views);
    const topLikesLabels = topLikesRaw.map(a => a.title.length > 20 ? a.title.substring(0, 20) + '...' : a.title);
    const topLikesValues = topLikesRaw.map(a => a.likes_count);
</script>
<script>
    // --- Graphique Interactions (Ligne Double) ---
    const ctxInteractions = document.getElementById('interactionsChart').getContext('2d');
    // Fusionner les labels (mois)
    const allMonths = [...new Set([...Object.keys(likesData), ...Object.keys(commentsData)])].sort();
    const likesDataset = allMonths.map(m => likesData[m] || 0);
    const commentsDataset = allMonths.map(m => commentsData[m] || 0);
    new Chart(ctxInteractions, {
        type: 'line',
        data: {
            labels: allMonths,
            datasets: [
                {
                    label: 'J\'aime',
                    data: likesDataset,
                    borderColor: themeAccent,
                    backgroundColor: 'rgba(197, 160, 101, 0.1)',
                    borderWidth: 2,
                    pointRadius: 3,
                    tension: 0.3,
                    fill: false
                },
                {
                    label: 'Commentaires',
                    data: commentsDataset,
                    borderColor: '#6c757d', // Secondary
                    backgroundColor: 'rgba(108, 117, 125, 0.1)',
                    borderWidth: 2,
                    pointRadius: 3,
                    tension: 0.3,
                    fill: false
                }
            ]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
    // --- Top 5 Vues ---
    new Chart(document.getElementById('topViewsChart'), {
        type: 'bar',
        data: {
            labels: topViewsLabels,
            datasets: [{
                label: 'Vues',
                data: topViewsValues,
                backgroundColor: 'rgba(78, 115, 223, 0.8)', // Info Color equiv (Blue/Greenish) -> Let's use Info/Accent
                backgroundColor: themeAccent,
                borderRadius: 5
            }]
        },
        options: {
            indexAxis: 'y',
            scales: { x: { beginAtZero: true } }
        }
    });
    // --- Top 5 Likes ---
    new Chart(document.getElementById('topLikesChart'), {
        type: 'bar',
        data: {
            labels: topLikesLabels,
            datasets: [{
                label: 'Likes',
                data: topLikesValues,
                backgroundColor: themePrimary,
                borderRadius: 5
            }]
        },
        options: {
            indexAxis: 'y',
            scales: { x: { beginAtZero: true } }
        }
    });
// --- Graphique Articles par Mois ---
    const ctxArticles = document.getElementById('articlesChart').getContext('2d');
    new Chart(ctxArticles, {
        type: 'line',
        data: {
            labels: Object.keys(articlesData),
            datasets: [{
                label: 'Articles Publiés',
                data: Object.values(articlesData),
                borderColor: themePrimary,
                backgroundColor: 'rgba(26, 74, 59, 0.1)',
                pointRadius: 4,
                pointBackgroundColor: themeAccent,
                pointBorderColor: themePrimary,
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, grid: { color: "#e3e6f0" } },
                x: { grid: { display: false } }
            },
            plugins: { legend: { display: false } }
        }
    });
    // --- Graphique Utilisateurs (Pie) ---
    const ctxPie = document.getElementById('usersPieChart').getContext('2d');
    new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: Object.keys(rolesData),
            datasets: [{
                data: Object.values(rolesData),
                backgroundColor: [themePrimary, themeAccent],
                hoverBackgroundColor: [themeSuccess, '#d6b67e'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: { position: 'bottom' }
            }
        },
    });
    // --- Graphique Auteurs (Bar) ---
    const ctxAuthors = document.getElementById('authorsChart').getContext('2d');
    new Chart(ctxAuthors, {
        type: 'bar',
        data: {
            labels: Object.keys(authorsData),
            datasets: [{
                label: 'Articles',
                data: Object.values(authorsData),
                backgroundColor: themeAccent,
                borderColor: themePrimary,
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
    // --- Graphique Inscriptions par Mois ---
    const ctxUsers = document.getElementById('usersChart').getContext('2d');
    new Chart(ctxUsers, {
        type: 'line',
        data: {
            labels: Object.keys(usersData),
            datasets: [{
                label: 'Nouveaux Inscrits',
                data: Object.values(usersData),
                borderColor: themeSuccess,
                backgroundColor: 'rgba(45, 90, 78, 0.1)',
                pointRadius: 4,
                pointBackgroundColor: themePrimary,
                pointBorderColor: themeSuccess,
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>