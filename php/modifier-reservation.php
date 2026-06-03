<?php
session_start();
include('config.php');

$token = trim($_GET['token'] ?? $_POST['token'] ?? '');
if (empty($token)) {
    header('Location: reservation.php');
    exit;
}

function formaterDateFr($date) {
    $jours = ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'];
    $mois  = ['','janvier','fevrier','mars','avril','mai','juin','juillet','aout','septembre','octobre','novembre','decembre'];
    $ts = strtotime($date);
    return $jours[date('w',$ts)] . ' ' . date('j',$ts) . ' ' . $mois[(int)date('n',$ts)] . ' ' . date('Y',$ts);
}

$modifie = false;
$erreur  = '';

$stmtInsc = $db->prepare("
    SELECT i.*, c.date, c.heure_debut, c.id_salle, c.places_restantes AS places_creneau,
           s.nom AS salle_nom, s.capacite_max,
           cat.libelle AS categorie
    FROM inscription i
    JOIN creneau          c   ON i.id_creneau  = c.id_creneau
    JOIN salle            s   ON c.id_salle    = s.id_salle
    JOIN categorie_visite cat ON i.id_categorie = cat.id_categorie
    WHERE i.token = ?
");
$stmtInsc->execute([$token]);
$inscription = $stmtInsc->fetch(PDO::FETCH_ASSOC);

if (!$inscription) {
    $page_styles = ['style-reservation.css', 'style-confirmation.css'];
    include('header.php');
    ?>
    <div class="container">
        <div class="confirmation-wrapper">
            <div class="error-block">
                <div class="error-icon">✕</div>
                <h2>Lien invalide</h2>
                <p>Ce lien de modification est invalide ou a déjà été utilisé.</p>
                <a href="reservation.php" class="btn-submit-large">Faire une nouvelle réservation</a>
            </div>
        </div>
    </div>
    <?php
    include('footer.php');
    exit;
}

// Traitement de la modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nouveau_id = (int)($_POST['nouveau_id_creneau'] ?? 0);
    $nouveau_nb = (int)($_POST['nb_personnes'] ?? 1);
    $ancien_id  = (int)$inscription['id_creneau'];
    $ancien_nb  = (int)$inscription['nb_personnes'];

    if ($nouveau_id > 0 && $nouveau_nb >= 1) {
        $stmtCheck = $db->prepare("SELECT places_restantes, id_salle FROM creneau WHERE id_creneau = ?");
        $stmtCheck->execute([$nouveau_id]);
        $nouveau_creneau = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($nouveau_creneau && (int)$nouveau_creneau['id_salle'] === (int)$inscription['id_salle']) {
            $dispo = ($nouveau_id === $ancien_id)
                ? $nouveau_creneau['places_restantes'] + $ancien_nb
                : $nouveau_creneau['places_restantes'];

            if ($dispo >= $nouveau_nb) {
                try {
                    $db->beginTransaction();

                    if ($nouveau_id !== $ancien_id) {
                        $db->prepare("UPDATE creneau SET places_restantes = places_restantes + ? WHERE id_creneau = ?")
                           ->execute([$ancien_nb, $ancien_id]);
                        $db->prepare("UPDATE creneau SET places_restantes = places_restantes - ? WHERE id_creneau = ?")
                           ->execute([$nouveau_nb, $nouveau_id]);
                    } else {
                        $diff = $nouveau_nb - $ancien_nb;
                        if ($diff !== 0) {
                            $db->prepare("UPDATE creneau SET places_restantes = places_restantes - ? WHERE id_creneau = ?")
                               ->execute([$diff, $ancien_id]);
                        }
                    }

                    $db->prepare("UPDATE inscription SET id_creneau = ?, nb_personnes = ? WHERE token = ?")
                       ->execute([$nouveau_id, $nouveau_nb, $token]);

                    $db->commit();
                    $stmtInsc->execute([$token]);
                    $inscription = $stmtInsc->fetch(PDO::FETCH_ASSOC);
                    $modifie = true;
                } catch (Exception $e) {
                    $db->rollBack();
                    $erreur = 'Une erreur est survenue. Veuillez réessayer.';
                }
            } else {
                $erreur = "Ce créneau n'a plus assez de places disponibles ({$dispo} place(s) restante(s)).";
            }
        } else {
            $erreur = 'Créneau invalide.';
        }
    }
}

// Récupérer tous les créneaux de cette salle
$stmtCreneaux = $db->prepare("SELECT * FROM creneau WHERE id_salle = ? ORDER BY date, heure_debut");
$stmtCreneaux->execute([$inscription['id_salle']]);
$creneaux_salle = $stmtCreneaux->fetchAll(PDO::FETCH_ASSOC);

// Ajuster l'affichage des places (remettre les places du créneau actuel)
foreach ($creneaux_salle as &$c) {
    $c['places_display'] = ($c['id_creneau'] == $inscription['id_creneau'])
        ? $c['places_restantes'] + (int)$inscription['nb_personnes']
        : $c['places_restantes'];
}
unset($c);

$page_styles = ['style-reservation.css', 'style-confirmation.css'];
include('header.php');
?>

<div class="container">
    <section class="intro-section">
        <h1 class="pixel-title">Modifier<span class="red-dot">.</span></h1>
        <p>Modifiez votre créneau pour la salle <strong><?php echo htmlspecialchars($inscription['salle_nom']); ?></strong>.</p>
    </section>

    <div class="form-wrapper-linear">

        <?php if ($modifie): ?>
        <div class="confirmation-success-banner" style="margin-bottom:24px">
            <div class="check-icon">✓</div>
            <h2>Modification enregistrée !</h2>
            <p>Votre créneau a bien été mis à jour.</p>
        </div>
        <?php endif; ?>

        <?php if ($erreur): ?>
        <div class="error-banner"><?php echo htmlspecialchars($erreur); ?></div>
        <?php endif; ?>

        <div class="form-section-linear">
            <h3><span class="section-icon">01</span> Créneau actuel</h3>
            <div class="creneau-actuel-block">
                <div class="creneau-actuel-heure">
                    <?php echo date('H\hi', strtotime($inscription['heure_debut'])); ?>
                    <span class="sep">–</span>
                    <?php echo date('H\hi', strtotime($inscription['heure_debut'] . ' +30 minutes')); ?>
                </div>
                <div class="creneau-actuel-details">
                    <strong><?php echo htmlspecialchars($inscription['salle_nom']); ?></strong>
                    <span><?php echo formaterDateFr($inscription['date']); ?></span>
                    <span><?php echo $inscription['nb_personnes']; ?> personne(s)</span>
                </div>
            </div>
        </div>

        <form method="POST" action="modifier-reservation.php?token=<?php echo urlencode($token); ?>">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <input type="hidden" name="nouveau_id_creneau" id="nouveau_id_creneau" value="<?php echo $inscription['id_creneau']; ?>">

            <div class="form-section-linear">
                <h3><span class="section-icon">02</span> Choisir un autre créneau</h3>
                <p style="margin-bottom:18px;color:#555">Sélectionnez le créneau souhaité :</p>
                <div class="horaires-badges-grid">
                    <?php foreach ($creneaux_salle as $c):
                        $heure   = date('H\hi', strtotime($c['heure_debut']));
                        $places  = $c['places_display'];
                        $actuel  = ($c['id_creneau'] == $inscription['id_creneau']);
                        $plein   = ($places <= 0 && !$actuel);
                        $classes = 'horaire-badge-item modifier-badge';
                        if ($actuel) $classes .= ' selected';
                        if ($plein)  $classes .= ' is-disabled';
                    ?>
                    <label class="<?php echo $classes; ?>"
                           data-id-creneau="<?php echo $c['id_creneau']; ?>"
                           data-places="<?php echo $places; ?>">
                        <span class="badge-content">
                            <span class="badge-time"><?php echo $heure; ?></span>
                            <span class="badge-places"><?php echo $places; ?> pl. rest.</span>
                            <?php if ($actuel): ?>
                            <span class="badge-actuel-tag">actuel</span>
                            <?php endif; ?>
                        </span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-section-linear">
                <h3><span class="section-icon">03</span> Nombre de personnes</h3>
                <div class="input-group" style="max-width:260px">
                    <label for="nb_personnes">Nombre de personnes <span class="red-dot">*</span></label>
                    <input type="number" id="nb_personnes" name="nb_personnes"
                           min="1" max="<?php echo $inscription['capacite_max']; ?>"
                           value="<?php echo $inscription['nb_personnes']; ?>">
                    <small id="jauge-modifier" class="jauge-info-txt"></small>
                </div>
            </div>

            <div class="form-footer-linear" style="display:flex;gap:16px;justify-content:center;flex-wrap:wrap">
                <a href="reservation.php" class="btn-secondary">Nouvelle réservation</a>
                <button type="submit" class="btn-submit-large">Enregistrer les modifications</button>
            </div>
        </form>
    </div>
</div>

<script>
var nbInput  = document.getElementById('nb_personnes');
var hiddenId = document.getElementById('nouveau_id_creneau');

document.querySelectorAll('.modifier-badge:not(.is-disabled)').forEach(function(badge) {
    badge.addEventListener('click', function() {
        document.querySelectorAll('.modifier-badge').forEach(function(b) { b.classList.remove('selected'); });
        this.classList.add('selected');
        hiddenId.value = this.dataset.idCreneau;
        var places = parseInt(this.dataset.places);
        nbInput.max = places;
        if (parseInt(nbInput.value) > places) nbInput.value = places;
        document.getElementById('jauge-modifier').textContent = places + ' place(s) disponible(s) sur ce creneau.';
    });
});
</script>

<?php include('footer.php'); ?>