<?php
session_start();
include('config.php');

$tokens = $_SESSION['reservation_tokens'] ?? [];
if (empty($tokens)) {
    header('Location: reservation.php');
    exit;
}
unset($_SESSION['reservation_tokens']);

function formaterDateFr($date) {
    $jours = ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'];
    $mois  = ['','janvier','fevrier','mars','avril','mai','juin','juillet','aout','septembre','octobre','novembre','decembre'];
    $ts = strtotime($date);
    return $jours[date('w',$ts)] . ' ' . date('j',$ts) . ' ' . $mois[(int)date('n',$ts)] . ' ' . date('Y',$ts);
}

$ph   = implode(',', array_fill(0, count($tokens), '?'));
$stmt = $db->prepare("
    SELECT i.token, i.nom, i.prenom, i.email, i.nb_personnes, i.buffet,
           c.date, c.heure_debut,
           s.nom AS salle_nom,
           cat.libelle AS categorie
    FROM inscription i
    JOIN creneau          c   ON i.id_creneau  = c.id_creneau
    JOIN salle            s   ON c.id_salle    = s.id_salle
    JOIN categorie_visite cat ON i.id_categorie = cat.id_categorie
    WHERE i.token IN ($ph)
    ORDER BY c.date, c.heure_debut
");
$stmt->execute($tokens);
$inscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($inscriptions)) {
    header('Location: reservation.php');
    exit;
}

$visiteur    = $inscriptions[0];
$page_styles = ['style-reservation.css', 'style-confirmation.css'];
include('header.php');
?>

<div class="container">
    <section class="intro-section">
        <h1 class="pixel-title">Confirmation<span class="red-dot">.</span></h1>
    </section>

    <div class="confirmation-wrapper">

        <div class="confirmation-success-banner">
            <div class="check-icon">✓</div>
            <h2>Réservation confirmée !</h2>
            <p>Un e-mail de confirmation a été envoyé à <strong><?php echo htmlspecialchars($visiteur['email']); ?></strong></p>
        </div>

        <div class="confirmation-card">
            <h3 class="confirmation-card-title">
                <span class="section-icon">01</span> Vos informations
            </h3>
            <div class="confirmation-info-grid">
                <div class="info-item">
                    <span class="info-label">Nom complet</span>
                    <span class="info-value"><?php echo htmlspecialchars($visiteur['prenom'] . ' ' . $visiteur['nom']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">E-mail</span>
                    <span class="info-value"><?php echo htmlspecialchars($visiteur['email']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Profil</span>
                    <span class="info-value"><?php echo htmlspecialchars($visiteur['categorie']); ?></span>
                </div>
                <?php if ($visiteur['buffet']): ?>
                <div class="info-item">
                    <span class="info-label">Buffet de clôture</span>
                    <span class="info-value info-tag-green">Participation confirmée</span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="confirmation-card">
            <h3 class="confirmation-card-title">
                <span class="section-icon">02</span> Vos créneaux réservés
            </h3>
            <div class="creneaux-confirm-list">
                <?php foreach ($inscriptions as $insc):
                    $date_fr     = formaterDateFr($insc['date']);
                    $heure_debut = date('H\hi', strtotime($insc['heure_debut']));
                    $heure_fin   = date('H\hi', strtotime($insc['heure_debut'] . ' +30 minutes'));
                ?>
                <div class="creneau-confirm-item">
                    <div class="creneau-confirm-left">
                        <div class="creneau-confirm-heure"><?php echo $heure_debut; ?></div>
                        <div class="creneau-confirm-sep">–</div>
                        <div class="creneau-confirm-heure fin"><?php echo $heure_fin; ?></div>
                    </div>
                    <div class="creneau-confirm-info">
                        <h4><?php echo htmlspecialchars($insc['salle_nom']); ?></h4>
                        <p><?php echo $date_fr; ?></p>
                        <p class="creneau-confirm-nb"><?php echo $insc['nb_personnes']; ?> personne(s)</p>
                    </div>
                    <a href="modifier-reservation.php?token=<?php echo urlencode($insc['token']); ?>" class="btn-modifier">
                        Modifier
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="confirmation-footer">
            <a href="index.php" class="btn-submit-large">Retour à l'accueil</a>
        </div>

    </div>
</div>

<?php include('footer.php'); ?>