<?php
session_start();
include('../php/config.php');

// Sécurité : accès réservé aux admins connectés
if (empty($_SESSION['admin_connecte'])) {
    header('Location: admin-login.php');
    exit;
}

$message = '';
$msgType = '';

// ============================================================
// TRAITEMENT DES ACTIONS (modification / suppression)
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- SUPPRESSION ---
    if (isset($_POST['action']) && $_POST['action'] === 'supprimer') {
        $id_inscription = (int)$_POST['id_inscription'];
        try {
            $db->beginTransaction();
            // On récupère le créneau et le nb pour rendre les places
            $stmt = $db->prepare("SELECT id_creneau, nb_personnes FROM inscription WHERE id_inscription = ?");
            $stmt->execute([$id_inscription]);
            $insc = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($insc) {
                $db->prepare("UPDATE creneau SET places_restantes = places_restantes + ? WHERE id_creneau = ?")
                   ->execute([$insc['nb_personnes'], $insc['id_creneau']]);
                $db->prepare("DELETE FROM inscription WHERE id_inscription = ?")
                   ->execute([$id_inscription]);
            }
            $db->commit();
            $message = 'Réservation supprimée.';
            $msgType = 'success';
        } catch (Exception $e) {
            $db->rollBack();
            $message = 'Erreur lors de la suppression.';
            $msgType = 'danger';
        }
    }

    // --- MODIFICATION ---
    if (isset($_POST['action']) && $_POST['action'] === 'modifier') {
        $id_inscription = (int)$_POST['id_inscription'];
        $nom            = trim($_POST['nom'] ?? '');
        $prenom         = trim($_POST['prenom'] ?? '');
        $email          = trim($_POST['email'] ?? '');
        $nb_personnes   = (int)($_POST['nb_personnes'] ?? 1);
        $buffet         = (int)($_POST['buffet'] ?? 0);
        $id_creneau     = (int)$_POST['id_creneau'];

        try {
            $db->beginTransaction();
            // Ancien nb pour ajuster les places du créneau
            $stmt = $db->prepare("SELECT nb_personnes FROM inscription WHERE id_inscription = ?");
            $stmt->execute([$id_inscription]);
            $ancien = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($ancien) {
                $diff = $nb_personnes - (int)$ancien['nb_personnes'];
                if ($diff !== 0) {
                    $db->prepare("UPDATE creneau SET places_restantes = places_restantes - ? WHERE id_creneau = ?")
                       ->execute([$diff, $id_creneau]);
                }
                $db->prepare("
                    UPDATE inscription
                    SET nom = ?, prenom = ?, email = ?, nb_personnes = ?, buffet = ?
                    WHERE id_inscription = ?
                ")->execute([$nom, $prenom, $email, $nb_personnes, $buffet, $id_inscription]);
            }
            $db->commit();
            $message = 'Réservation mise à jour.';
            $msgType = 'success';
        } catch (Exception $e) {
            $db->rollBack();
            $message = 'Erreur lors de la modification.';
            $msgType = 'danger';
        }
    }
}

// ============================================================
// RÉCUPÉRATION DE TOUTES LES RÉSERVATIONS
// ============================================================
$reservations = $db->query("
    SELECT i.*, c.date, c.heure_debut, s.nom AS salle_nom, cat.libelle AS categorie
    FROM inscription i
    JOIN creneau          c   ON i.id_creneau  = c.id_creneau
    JOIN salle            s   ON c.id_salle    = s.id_salle
    JOIN categorie_visite cat ON i.id_categorie = cat.id_categorie
    ORDER BY c.date, c.heure_debut, s.nom
")->fetchAll(PDO::FETCH_ASSOC);

// Statistiques rapides
$total_resa  = count($reservations);
$total_pers  = array_sum(array_column($reservations, 'nb_personnes'));
$total_buffet = 0;
foreach ($reservations as $r) { if ($r['buffet']) $total_buffet++; }

$page_styles = ['style-reservation.css', 'style-admin.css'];
include('../php/header.php');
?>

<div class="container">
    <section class="intro-section">
        <h1 class="pixel-title">Administration<span class="red-dot">.</span></h1>
        <p>Connecté en tant que <strong><?php echo htmlspecialchars($_SESSION['admin_login']); ?></strong> —
           <a href="admin-logout.php" class="admin-logout-link">Se déconnecter</a></p>
    </section>

    <?php if ($message): ?>
    <div class="admin-alert admin-alert-<?php echo $msgType; ?>"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <!-- Statistiques -->
    <div class="admin-stats">
        <div class="admin-stat-card">
            <span class="admin-stat-nb"><?php echo $total_resa; ?></span>
            <span class="admin-stat-label">Réservations</span>
        </div>
        <div class="admin-stat-card">
            <span class="admin-stat-nb"><?php echo $total_pers; ?></span>
            <span class="admin-stat-label">Personnes</span>
        </div>
        <div class="admin-stat-card">
            <span class="admin-stat-nb"><?php echo $total_buffet; ?></span>
            <span class="admin-stat-label">Buffet</span>
        </div>
    </div>

    <!-- Tableau des réservations -->
    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>E-mail</th>
                    <th>Salle</th>
                    <th>Date</th>
                    <th>Heure</th>
                    <th>Profil</th>
                    <th>Pers.</th>
                    <th>Buffet</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reservations)): ?>
                <tr><td colspan="11" style="text-align:center;padding:30px;color:#999">Aucune réservation pour le moment.</td></tr>
                <?php else: ?>
                    <?php foreach ($reservations as $r):
                        $heure = date('H\hi', strtotime($r['heure_debut']));
                    ?>
                    <tr>
                        <form method="POST" action="admin.php">
                            <input type="hidden" name="action" value="modifier">
                            <input type="hidden" name="id_inscription" value="<?php echo $r['id_inscription']; ?>">
                            <input type="hidden" name="id_creneau" value="<?php echo $r['id_creneau']; ?>">

                            <td><?php echo $r['id_inscription']; ?></td>
                            <td><input type="text"  name="nom"    value="<?php echo htmlspecialchars($r['nom']); ?>" class="admin-input"></td>
                            <td><input type="text"  name="prenom" value="<?php echo htmlspecialchars($r['prenom']); ?>" class="admin-input"></td>
                            <td><input type="email" name="email"  value="<?php echo htmlspecialchars($r['email']); ?>" class="admin-input admin-input-wide"></td>
                            <td><?php echo htmlspecialchars($r['salle_nom']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($r['date'])); ?></td>
                            <td><?php echo $heure; ?></td>
                            <td><?php echo htmlspecialchars($r['categorie']); ?></td>
                            <td><input type="number" name="nb_personnes" value="<?php echo $r['nb_personnes']; ?>" min="1" class="admin-input admin-input-nb"></td>
                            <td style="text-align:center">
                                <input type="checkbox" name="buffet" value="1" <?php echo $r['buffet'] ? 'checked' : ''; ?>>
                            </td>
                            <td class="admin-actions">
                                <button type="submit" class="admin-btn admin-btn-save" title="Enregistrer">💾</button>
                        </form>
                        <form method="POST" action="admin.php" onsubmit="return confirm('Supprimer cette réservation ?');" style="display:inline">
                            <input type="hidden" name="action" value="supprimer">
                            <input type="hidden" name="id_inscription" value="<?php echo $r['id_inscription']; ?>">
                            <button type="submit" class="admin-btn admin-btn-delete" title="Supprimer">🗑️</button>
                        </form>
                            </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include('../php/footer.php'); ?>
