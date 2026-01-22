<?php
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/functions.php";

checkAdmin();

$log_file = __DIR__ . "/../logs/app.log";
$logs_content = '';

// Vider les logs
if (isset($_POST['clear_logs'])) {
    if (file_exists($log_file) && is_writable($log_file)) {
        file_put_contents($log_file, '');
        log_msg("Logs vidés par " . $_SESSION['username']);
        $logs_content = "Logs vidés avec succès.";
    }
}

// Lire les logs
if (file_exists($log_file)) {
    $logs_array = file($log_file);
    $logs_array = array_reverse($logs_array);
    $logs_array = array_slice($logs_array, 0, 100); // Dernières 100 lignes
    $logs_content = implode('', $logs_array);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs Système</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #f0f4f0;
            padding: 2rem;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        h1 {
            font-size: 2rem;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .subtitle {
            color: #a0aec0;
            margin-bottom: 2rem;
        }

        .nav-links {
            margin-bottom: 2rem;
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .nav-links a {
            color: #22c55e;
            text-decoration: none;
            font-weight: 600;
            padding: 0.5rem 1rem;
            background: white;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .nav-links a:hover {
            background: #22c55e;
            color: white;
        }

        .controls {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            align-items: center;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            font-size: 0.9rem;
        }

        .btn-primary {
            background: #22c55e;
            color: white;
        }

        .btn-primary:hover {
            background: #16a34a;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #4a5568;
        }

        .btn-secondary:hover {
            background: #cbd5e1;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .checkbox-group input {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .checkbox-group label {
            cursor: pointer;
            color: #2d3748;
            font-weight: 600;
        }

        .logs-container {
            background: #1a1a1a;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            max-height: 600px;
            overflow-y: auto;
        }

        .logs-content {
            font-family: 'Courier New', monospace;
            font-size: 0.85rem;
            color: #10b981;
            line-height: 1.6;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .logs-container::-webkit-scrollbar {
            width: 10px;
        }

        .logs-container::-webkit-scrollbar-track {
            background: #2d2d2d;
            border-radius: 10px;
        }

        .logs-container::-webkit-scrollbar-thumb {
            background: #22c55e;
            border-radius: 10px;
        }

        .logs-container::-webkit-scrollbar-thumb:hover {
            background: #16a34a;
        }

        .empty-logs {
            text-align: center;
            color: #a0aec0;
            padding: 3rem;
            font-size: 1.1rem;
        }

        .status-indicator {
            display: inline-block;
            width: 10px;
            height: 10px;
            background: #22c55e;
            border-radius: 50%;
            margin-right: 0.5rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .info-badge {
            background: #d1fae5;
            color: #065f46;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Logs Système</h1>
        <p class="subtitle">Surveillance en temps réel de l'activité</p>

        <div class="nav-links">
            <a href="/admin/dashboard.php">← Retour au dashboard</a>
            <a href="/index.php">Voir le site</a>
        </div>

        <div class="controls">
            <div class="checkbox-group">
                <input type="checkbox" id="auto-refresh" checked>
                <label for="auto-refresh">
                    <span class="status-indicator"></span>
                    Auto-refresh (3s)
                </label>
            </div>

            <button onclick="refreshLogs()" class="btn btn-primary">Rafraîchir maintenant</button>

            <form method="post" style="display: inline;" onsubmit="return confirm('Vider tous les logs ?');">
                <button type="submit" name="clear_logs" class="btn btn-danger">Vider les logs</button>
            </form>

            <button onclick="toggleLogs()" class="btn btn-secondary" id="toggle-btn">Masquer les logs</button>

            <div class="info-badge">
                100 dernières entrées
            </div>
        </div>

        <div class="logs-container" id="logs-container">
            <?php if (empty($logs_content)): ?>
                <div class="empty-logs">Aucun log disponible</div>
            <?php else: ?>
                <div class="logs-content" id="logs-content"><?php echo htmlspecialchars($logs_content); ?></div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        let autoRefreshEnabled = true;
        let logsVisible = true;

        // Toggle auto-refresh
        document.getElementById('auto-refresh').addEventListener('change', function() {
            autoRefreshEnabled = this.checked;
            if (autoRefreshEnabled) {
                refreshLogs();
            }
        });

        // Rafraîchir les logs via AJAX
        function refreshLogs() {
            if (!logsVisible) return;

            fetch(window.location.href)
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContent = doc.getElementById('logs-content');
                    
                    if (newContent) {
                        document.getElementById('logs-content').innerHTML = newContent.innerHTML;
                    }
                })
                .catch(error => console.error('Erreur refresh:', error));
        }

        // Auto-refresh toutes les 3 secondes
        setInterval(() => {
            if (autoRefreshEnabled && logsVisible) {
                refreshLogs();
            }
        }, 3000);

        // Toggle affichage logs
        function toggleLogs() {
            const container = document.getElementById('logs-container');
            const btn = document.getElementById('toggle-btn');
            
            if (logsVisible) {
                container.style.display = 'none';
                btn.textContent = 'Afficher les logs';
                logsVisible = false;
            } else {
                container.style.display = 'block';
                btn.textContent = 'Masquer les logs';
                logsVisible = true;
                refreshLogs();
            }
        }
    </script>
</body>
</html>