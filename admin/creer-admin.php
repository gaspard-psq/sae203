<?php
/**
 * Script à exécuter UNE SEULE FOIS pour créer le compte administrateur.
 * ⚠️  SUPPRIMER CE FICHIER DU SERVEUR IMMÉDIATEMENT APRÈS UTILISATION !
 */
require_once '../php/config.php';

// --- Personnalisez ces deux valeurs avant d'exécuter ---
$login    = 'admin';
$password = 'ellusion2026';
// -------------------------------------------------------

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $db->prepare("
    INSERT INTO admin (login, password_hash)
    VALUES (?, ?)
    ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash)
");
$stmt->execute([$login, $hash]);

echo '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8">
<title>Création admin</title>
<style>body{font-family:monospace;max-width:600px;margin:60px auto;padding:20px}
.ok{background:#e8f5e9;border-left:4px solid #4caf50;padding:20px;border-radius:6px}
.warn{background:#fff3e0;border-left:4px solid #ff9800;padding:16px;margin-top:16px;border-radius:6px}
</style></head><body>';

echo '<div class="ok">';
echo '<strong>✅ Compte admin créé avec succès.</strong><br><br>';
echo 'Login &nbsp;&nbsp;&nbsp; : <strong>' . htmlspecialchars($login) . '</strong><br>';
echo 'Mot de passe : <strong>' . htmlspecialchars($password) . '</strong>';
echo '</div>';

echo '<div class="warn">';
echo '<strong>⚠️ ACTION REQUISE</strong><br>';
echo 'Supprimez ce fichier <code>admin/creer-admin.php</code> du serveur maintenant.';
echo '</div>';

echo '</body></html>';
