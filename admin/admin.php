<?php
session_start();
include('../php/config.php');

if (empty($_SESSION['admin_connecte'])) {
    header('Location: admin-login.php');
    exit;
}

$message = '';
$msgType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($_POST['action'] === 'supprimer') {
        $id_inscription = (int)$_POST['id_inscription'];
        try {
            $db->beginTransaction();
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

    if ($_POST['action'] === 'modifier') {
        $id_inscription = (int)$_POST['id_inscription'];
        $nom            = trim($_POST['nom'] ?? '');
        $prenom         = trim($_POST['prenom'] ?? '');
        $email          = trim($_POST['email'] ?? '');
        $nb_personnes   = max(1, (int)($_POST['nb_personnes'] ?? 1));
        $buffet         = isset($_POST['buffet']) ? 1 : 0;
        $new_id_creneau = (int)$_POST['id_creneau'];
        $id_categorie   = (int)$_POST['id_categorie'];

        try {
            $db->beginTransaction();
            $stmt = $db->prepare("SELECT id_creneau, nb_personnes FROM inscription WHERE id_inscription = ?");
            $stmt->execute([$id_inscription]);
            $ancien = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($ancien) {
                $ancien_nb      = (int)$ancien['nb_personnes'];
                $ancien_creneau = (int)$ancien['id_creneau'];
                $ok = true;

                if ($new_id_creneau !== $ancien_creneau) {
                    // Restituer les places à l'ancien créneau
                    $db->prepare("UPDATE creneau SET places_restantes = places_restantes + ? WHERE id_creneau = ?")
                       ->execute([$ancien_nb, $ancien_creneau]);
                    // Vérifier la disponibilité du nouveau créneau
                    $stmt2 = $db->prepare("SELECT places_restantes FROM creneau WHERE id_creneau = ?");
                    $stmt2->execute([$new_id_creneau]);
                    $new_cren = $stmt2->fetch(PDO::FETCH_ASSOC);
                    if (!$new_cren || $new_cren['places_restantes'] < $nb_personnes) {
                        $db->rollBack();
                        $message = 'Pas assez de places dans ce créneau (' . ($new_cren['places_restantes'] ?? 0) . ' disponible(s)).';
                        $msgType = 'danger';
                        $ok = false;
                    } else {
                        $db->prepare("UPDATE creneau SET places_restantes = places_restantes - ? WHERE id_creneau = ?")
                           ->execute([$nb_personnes, $new_id_creneau]);
                    }
                } else {
                    // Même créneau : ajuster la différence de personnes
                    $diff = $nb_personnes - $ancien_nb;
                    if ($diff > 0) {
                        $stmt2 = $db->prepare("SELECT places_restantes FROM creneau WHERE id_creneau = ?");
                        $stmt2->execute([$new_id_creneau]);
                        $cren = $stmt2->fetch(PDO::FETCH_ASSOC);
                        if (!$cren || $cren['places_restantes'] < $diff) {
                            $db->rollBack();
                            $message = 'Pas assez de places disponibles dans ce créneau.';
                            $msgType = 'danger';
                            $ok = false;
                        } else {
                            $db->prepare("UPDATE creneau SET places_restantes = places_restantes - ? WHERE id_creneau = ?")
                               ->execute([$diff, $new_id_creneau]);
                        }
                    } elseif ($diff < 0) {
                        $db->prepare("UPDATE creneau SET places_restantes = places_restantes + ? WHERE id_creneau = ?")
                           ->execute([-$diff, $new_id_creneau]);
                    }
                }

                if ($ok) {
                    $db->prepare("
                        UPDATE inscription
                        SET nom = ?, prenom = ?, email = ?, nb_personnes = ?, buffet = ?, id_creneau = ?, id_categorie = ?
                        WHERE id_inscription = ?
                    ")->execute([$nom, $prenom, $email, $nb_personnes, $buffet, $new_id_creneau, $id_categorie, $id_inscription]);
                    $db->commit();
                    $message = 'Réservation mise à jour.';
                    $msgType = 'success';
                }
            } else {
                $db->rollBack();
                $message = 'Réservation introuvable.';
                $msgType = 'danger';
            }
        } catch (Exception $e) {
            if ($db->inTransaction()) $db->rollBack();
            $message = 'Erreur lors de la modification.';
            $msgType = 'danger';
        }
    }
}

$salles      = $db->query("SELECT id_salle, nom FROM salle ORDER BY id_salle")->fetchAll(PDO::FETCH_ASSOC);
$creneaux_raw = $db->query("SELECT id_creneau, id_salle, date, heure_debut, places_restantes FROM creneau ORDER BY id_salle, date, heure_debut")->fetchAll(PDO::FETCH_ASSOC);
$categories  = $db->query("SELECT id_categorie, libelle FROM categorie_visite ORDER BY id_categorie")->fetchAll(PDO::FETCH_ASSOC);

$creneaux_js = [];
foreach ($creneaux_raw as $c) {
    $creneaux_js[(int)$c['id_salle']][] = [
        'id'     => (int)$c['id_creneau'],
        'label'  => date('d/m H\hi', strtotime($c['date'] . ' ' . $c['heure_debut'])),
        'places' => (int)$c['places_restantes'],
    ];
}

$reservations = $db->query("
    SELECT i.*, c.date, c.heure_debut, s.nom AS salle_nom, s.id_salle, cat.libelle AS categorie
    FROM inscription i
    JOIN creneau          c   ON i.id_creneau  = c.id_creneau
    JOIN salle            s   ON c.id_salle    = s.id_salle
    JOIN categorie_visite cat ON i.id_categorie = cat.id_categorie
    ORDER BY c.date, c.heure_debut, s.nom
")->fetchAll(PDO::FETCH_ASSOC);

$total_resa   = count($reservations);
$total_pers   = array_sum(array_column($reservations, 'nb_personnes'));
$total_buffet = count(array_filter($reservations, fn($r) => $r['buffet']));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration — E-LLUSION</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/style-reservation.css">
    <link rel="stylesheet" href="../css/style-admin.css">
</head>
<body>
<main>

<div class="container">
    <section class="intro-section">
        <h1 class="pixel-title">Administration<span class="red-dot">.</span></h1>
        <p>Connecté en tant que <strong><?= htmlspecialchars($_SESSION['admin_login']) ?></strong> —
           <a href="admin-logout.php" class="admin-logout-link">Se déconnecter</a></p>
    </section>

    <?php if ($message): ?>
    <div class="admin-alert admin-alert-<?= $msgType ?>"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="admin-stats">
        <div class="admin-stat-card">
            <span class="admin-stat-nb"><?= $total_resa ?></span>
            <span class="admin-stat-label">Réservations</span>
        </div>
        <div class="admin-stat-card">
            <span class="admin-stat-nb"><?= $total_pers ?></span>
            <span class="admin-stat-label">Personnes</span>
        </div>
        <div class="admin-stat-card">
            <span class="admin-stat-nb"><?= $total_buffet ?></span>
            <span class="admin-stat-label">Buffet</span>
        </div>
    </div>

    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>E-mail</th>
                    <th>Salle</th>
                    <th>Créneau</th>
                    <th>Profil</th>
                    <th>Pers.</th>
                    <th>Buffet</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reservations)): ?>
                <tr><td colspan="10" style="text-align:center;padding:30px;color:#999">Aucune réservation pour le moment.</td></tr>
                <?php else: ?>
                    <?php foreach ($reservations as $r): ?>
                    <tr>
                        <form method="POST" action="admin.php">
                            <input type="hidden" name="action" value="modifier">
                            <input type="hidden" name="id_inscription" value="<?= $r['id_inscription'] ?>">

                            <td><?= $r['id_inscription'] ?></td>
                            <td><input type="text"  name="nom"    value="<?= htmlspecialchars($r['nom']) ?>" class="admin-input"></td>
                            <td><input type="text"  name="prenom" value="<?= htmlspecialchars($r['prenom']) ?>" class="admin-input"></td>
                            <td><input type="email" name="email"  value="<?= htmlspecialchars($r['email']) ?>" class="admin-input admin-input-wide"></td>
                            <td>
                                <select class="admin-input salle-select" data-current-creneau="<?= $r['id_creneau'] ?>">
                                    <?php foreach ($salles as $s): ?>
                                    <option value="<?= $s['id_salle'] ?>" <?= $s['id_salle'] == $r['id_salle'] ? 'selected' : '' ?>><?= htmlspecialchars($s['nom']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <select name="id_creneau" class="admin-input admin-input-wide creneau-select"></select>
                            </td>
                            <td>
                                <select name="id_categorie" class="admin-input">
                                    <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id_categorie'] ?>" <?= $cat['id_categorie'] == $r['id_categorie'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['libelle']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="number" name="nb_personnes" value="<?= $r['nb_personnes'] ?>" min="1" max="12" class="admin-input admin-input-nb"></td>
                            <td style="text-align:center">
                                <input type="checkbox" name="buffet" value="1" <?= $r['buffet'] ? 'checked' : '' ?>>
                            </td>
                            <td class="admin-actions">
                                <button type="submit" class="admin-btn admin-btn-save" title="Enregistrer">💾</button>
                        </form>
                        <form method="POST" action="admin.php" onsubmit="return confirm('Supprimer cette réservation ?');" style="display:inline">
                            <input type="hidden" name="action" value="supprimer">
                            <input type="hidden" name="id_inscription" value="<?= $r['id_inscription'] ?>">
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

<script>
const creneauxBySalle = <?= json_encode($creneaux_js, JSON_UNESCAPED_UNICODE) ?>;

function populateCreneaux(salleSelect, selectedId) {
    const idSalle = parseInt(salleSelect.value);
    const creneauSelect = salleSelect.closest('tr').querySelector('[name="id_creneau"]');
    creneauSelect.innerHTML = '';
    (creneauxBySalle[idSalle] || []).forEach(s => {
        const opt = new Option(s.label + ' (' + s.places + ' pl.)', s.id, false, s.id === selectedId);
        creneauSelect.appendChild(opt);
    });
}

document.querySelectorAll('.salle-select').forEach(sel => {
    populateCreneaux(sel, parseInt(sel.dataset.currentCreneau));
    sel.addEventListener('change', () => populateCreneaux(sel, null));
});
</script>

</main>
</body>
</html>
