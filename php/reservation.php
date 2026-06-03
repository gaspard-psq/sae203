<?php
session_start();
include('config.php');

function envoyerEmailConfirmation($email, $nom, $prenom, $inscriptions, $base_url) {
    $sujet = '=?UTF-8?B?' . base64_encode('Confirmation de reservation - E-LLUSION') . '?=';

    $lignes = '';
    foreach ($inscriptions as $i) {
        $heure   = date('H\hi', strtotime($i['heure_debut']));
        $heure_f = date('H\hi', strtotime($i['heure_debut'] . ' +30 minutes'));
        $ts      = strtotime($i['date']);
        $jours   = ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'];
        $mois    = ['','janvier','fevrier','mars','avril','mai','juin','juillet','aout','septembre','octobre','novembre','decembre'];
        $date_fr = $jours[date('w',$ts)] . ' ' . date('j',$ts) . ' ' . $mois[(int)date('n',$ts)] . ' ' . date('Y',$ts);
        $lien    = $base_url . '/modifier-reservation.php?token=' . $i['token'];
        $lignes .= "<tr>
            <td style='padding:10px 16px;border-bottom:1px solid #e0e0e0'><strong>" . htmlspecialchars($i['salle_nom']) . "</strong></td>
            <td style='padding:10px 16px;border-bottom:1px solid #e0e0e0'>{$date_fr} a {$heure}</td>
            <td style='padding:10px 16px;border-bottom:1px solid #e0e0e0'>{$i['nb_personnes']} pers.</td>
            <td style='padding:10px 16px;border-bottom:1px solid #e0e0e0'><a href='{$lien}' style='color:#00bbaa;font-weight:bold;text-decoration:none'>Modifier</a></td>
        </tr>";
    }

    $corps = "<!DOCTYPE html><html lang='fr'><body style='margin:0;padding:0;background:#f5f5f5;font-family:Arial,sans-serif'>
<table width='100%' cellpadding='0' cellspacing='0' style='background:#f5f5f5;padding:40px 20px'>
<tr><td align='center'>
  <table width='600' cellpadding='0' cellspacing='0' style='background:#ffffff;border-radius:12px;overflow:hidden'>
    <tr><td style='background:#00bbaa;padding:32px 40px;text-align:center'>
      <h1 style='margin:0;color:#ffffff;font-size:28px;font-weight:900;letter-spacing:3px'>E-LLUSION</h1>
      <p style='margin:8px 0 0;color:rgba(255,255,255,0.85);font-size:14px'>Exposition MMI - Chambery</p>
    </td></tr>
    <tr><td style='padding:40px'>
      <h2 style='margin:0 0 8px;color:#000;font-size:22px'>Reservation confirmee</h2>
      <p style='color:#444;line-height:1.7'>Bonjour <strong>{$prenom} {$nom}</strong>,<br>
      Votre reservation pour l'exposition <strong>E-LLUSION</strong> a bien ete enregistree.</p>
      <table width='100%' cellpadding='0' cellspacing='0' style='margin:24px 0;border:1px solid #e0e0e0;border-radius:8px;overflow:hidden'>
        <thead><tr style='background:#c5f9f2'>
          <th style='padding:10px 16px;text-align:left;font-size:13px'>Salle</th>
          <th style='padding:10px 16px;text-align:left;font-size:13px'>Horaire</th>
          <th style='padding:10px 16px;text-align:left;font-size:13px'>Personnes</th>
          <th style='padding:10px 16px;text-align:left;font-size:13px'>Action</th>
        </tr></thead>
        <tbody>{$lignes}</tbody>
      </table>
      <hr style='border:none;border-top:1px solid #e0e0e0;margin:32px 0'>
      <p style='color:#999;font-size:12px;text-align:center;margin:0'>E-LLUSION - MMI Chambery - Universite Savoie Mont Blanc</p>
    </td></tr>
  </table>
</td></tr></table>
</body></html>";

    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: E-LLUSION <no-reply@e-llusion.fr>\r\n";
    @mail($email, $sujet, $corps, $headers);
}

// 1. TRAITEMENT DE L'INSCRIPTION
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_inscription'])) {
    $nom              = trim($_POST['nom'] ?? '');
    $prenom           = trim($_POST['prenom'] ?? '');
    $email            = trim($_POST['email'] ?? '');
    $id_categorie     = (int)($_POST['id_categorie'] ?? 0);
    $buffet           = isset($_POST['buffet']) ? 1 : 0;
    $creneaux_post    = $_POST['creneaux'] ?? [];
    $nb_personnes_arr = $_POST['nb_personnes'] ?? [];

    // Compatible PHP 7.2+ (pas de fn())
    $creneaux_valides = array_filter($creneaux_post, function($v) { return (int)$v > 0; });

    if (!empty($nom) && !empty($prenom) && !empty($email) && $id_categorie > 0 && !empty($creneaux_valides)) {
        try {
            $db->beginTransaction();
            $stmtCheck  = $db->prepare("SELECT places_restantes FROM creneau WHERE id_creneau = ?");
            $stmtInsert = $db->prepare("
                INSERT INTO inscription (id_creneau, id_categorie, nom, prenom, email, nb_personnes, buffet, token)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmtUpdate   = $db->prepare("UPDATE creneau SET places_restantes = places_restantes - ? WHERE id_creneau = ?");
            $tokens_crees = [];

            foreach ($creneaux_valides as $id_salle => $id_creneau) {
                $id_creneau   = (int)$id_creneau;
                $nb_personnes = (int)($nb_personnes_arr[$id_salle] ?? 1);

                $stmtCheck->execute([$id_creneau]);
                $creneau = $stmtCheck->fetch(PDO::FETCH_ASSOC);

                if ($creneau && $creneau['places_restantes'] >= $nb_personnes) {
                    $token = bin2hex(random_bytes(16));
                    $stmtInsert->execute([$id_creneau, $id_categorie, $nom, $prenom, $email, $nb_personnes, $buffet, $token]);
                    $stmtUpdate->execute([$nb_personnes, $id_creneau]);
                    $tokens_crees[] = $token;
                }
            }
            $db->commit();

            if (!empty($tokens_crees)) {
                $ph = implode(',', array_fill(0, count($tokens_crees), '?'));
                $stmtDetails = $db->prepare("
                    SELECT i.token, i.nb_personnes, c.date, c.heure_debut, s.nom AS salle_nom
                    FROM inscription i
                    JOIN creneau c ON i.id_creneau = c.id_creneau
                    JOIN salle   s ON c.id_salle   = s.id_salle
                    WHERE i.token IN ($ph)
                    ORDER BY c.date, c.heure_debut
                ");
                $stmtDetails->execute($tokens_crees);
                $details = $stmtDetails->fetchAll(PDO::FETCH_ASSOC);

                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                $base_url = $protocol . '://' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
                envoyerEmailConfirmation($email, $nom, $prenom, $details, $base_url);

                $_SESSION['reservation_tokens'] = $tokens_crees;
                header('Location: confirmation.php');
                exit;
            }
        } catch (Exception $e) {
            $db->rollBack();
        }
    }
}

// 2. RÉCUPÉRATION DES DONNÉES
$categories    = $db->query("SELECT * FROM categorie_visite ORDER BY id_categorie ASC")->fetchAll(PDO::FETCH_ASSOC);
$tous_creneaux = $db->query("SELECT * FROM creneau ORDER BY date, heure_debut")->fetchAll(PDO::FETCH_ASSOC);
$salles_db     = $db->query("SELECT * FROM salle ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);

$jauge_index = [];
foreach ($tous_creneaux as $c) {
    $date_key  = (date('Y-m-d', strtotime($c['date'])) <= '2026-06-18') ? 'jeudi' : 'vendredi';
    $heure_key = date('H:i', strtotime($c['heure_debut']));
    $jauge_index[$c['id_salle']][$date_key][$heure_key] = [
        'id_creneau'       => $c['id_creneau'],
        'places_restantes' => (int)$c['places_restantes'],
    ];
}

$jauge_json  = json_encode($jauge_index);
$page_styles = ['style-reservation.css'];
include('header.php');
?>

<div class="container">
    <section class="intro-section">
        <h1 class="pixel-title">Réservation<span class="red-dot">.</span></h1>
        <p>Planifiez votre visite de l'exposition E-llusion en remplissant les étapes ci-dessous.</p>
    </section>

    <div class="form-wrapper-linear">
        <form action="reservation.php" method="POST" id="formReservation">
            <input type="hidden" name="action_inscription" value="1">

            <div class="form-section-linear">
                <h3><span class="section-icon">01</span> Vos coordonnées</h3>
                <div class="input-group">
                    <label for="nom">Nom <span class="red-dot">*</span></label>
                    <input type="text" id="nom" name="nom" placeholder="Ex: Dupont" required>
                </div>
                <div class="input-group">
                    <label for="prenom">Prénom <span class="red-dot">*</span></label>
                    <input type="text" id="prenom" name="prenom" placeholder="Ex: Lucas" required>
                </div>
                <div class="input-group">
                    <label for="email">Adresse e-mail <span class="red-dot">*</span></label>
                    <input type="email" id="email" name="email" placeholder="lucas.dupont@gmail.com" required>
                </div>
            </div>

            <div class="form-section-linear">
                <h3><span class="section-icon">02</span> Votre profil</h3>
                <div class="radio-group-horizontal">
                    <?php foreach ($categories as $index => $cat): ?>
                        <label class="custom-radio">
                            <input type="radio" name="id_categorie" value="<?php echo $cat['id_categorie']; ?>" <?php echo $index === 0 ? 'checked' : ''; ?>>
                            <span class="radio-mark"></span> <?php echo htmlspecialchars($cat['libelle']); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-section-linear">
                <h3><span class="section-icon">03</span> Choix de la salle et de l'horaire</h3>
                <p style="margin-bottom: 20px; color: #555;">Sélectionnez la salle que vous souhaitez visiter :</p>
                <div class="salles-accordion-list">
                    <?php foreach ($salles_db as $salle):
                        $id_salle = $salle['id_salle'];
                    ?>
                    <details class="salle-accordion" data-salle-id="<?php echo $id_salle; ?>">
                        <summary class="salle-summary">
                            <div class="salle-summary-text">
                                <h4><?php echo htmlspecialchars($salle['nom']); ?></h4>
                                <small>Capacité maximale : <?php echo $salle['capacite_max']; ?> personnes</small>
                            </div>
                            <span class="accordion-arrow">▼</span>
                        </summary>
                        <div class="horaires-dropdown-content">
                            <input type="hidden" name="creneaux[<?php echo $id_salle; ?>]" id="hidden_id_creneau_<?php echo $id_salle; ?>" value="">
                            <div class="creneaux-zone" id="creneaux_salle_<?php echo $id_salle; ?>">
                                <p class="creneaux-hint">Ouvrez cette salle pour voir les créneaux disponibles.</p>
                            </div>
                            <div class="nb-personnes-zone" id="nb_zone_<?php echo $id_salle; ?>" style="display:none; margin-top: 20px;">
                                <div class="input-group" style="max-width: 250px;">
                                    <label for="nb_personnes_<?php echo $id_salle; ?>">Nombre de personnes <span class="red-dot">*</span></label>
                                    <input type="number" id="nb_personnes_<?php echo $id_salle; ?>" name="nb_personnes[<?php echo $id_salle; ?>]" min="1" max="12" value="1">
                                    <small id="jauge-info-<?php echo $id_salle; ?>" class="jauge-info-txt"></small>
                                </div>
                            </div>
                        </div>
                    </details>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-section-linear">
                <h3><span class="section-icon">04</span> Informations de visite</h3>
                <div class="checkbox-group">
                    <label class="custom-checkbox">
                        <input type="checkbox" name="buffet" value="1">
                        <span class="checkmark"></span>
                        <span class="checkbox-text">Je souhaite participer au buffet de clôture</span>
                    </label>
                </div>
            </div>

            <div class="form-footer-linear">
                <button type="submit" class="btn-submit-large">Confirmer ma réservation</button>
            </div>
        </form>
    </div>
</div>

<script>
const JAUGE_INDEX = <?php echo $jauge_json; ?>;
const CRENEAUX_DATA = {
    jeudi: { label: 'Jeudi 18 juin', slots: ['15:00','15:30','16:00','16:30','17:00','17:30','18:00','19:00','19:30','20:00'] }
};
const selectedBySalle = {};

function renderCreneaux(salleId, jourKey) {
    const zone        = document.getElementById('creneaux_salle_' + salleId);
    const jourData    = CRENEAUX_DATA[jourKey];
    const jaugesSalle = (JAUGE_INDEX[salleId] && JAUGE_INDEX[salleId][jourKey]) ? JAUGE_INDEX[salleId][jourKey] : {};

    let html = '<div class="horaires-badges-grid">';
    jourData.slots.forEach(function(slot) {
        const jaugeInfo  = jaugesSalle[slot] || null;
        const idCreneau  = jaugeInfo ? jaugeInfo.id_creneau : null;
        const placesRest = jaugeInfo ? jaugeInfo.places_restantes : 12;
        html += '<label class="horaire-badge-item" data-id-creneau="' + idCreneau + '" data-places="' + placesRest + '" data-salle="' + salleId + '" data-jour="' + jourKey + '" data-slot="' + slot + '">'
             + '<span class="badge-content">'
             + '<span class="badge-time">' + slot.replace(':','h') + '</span>'
             + '<span class="badge-places">' + placesRest + ' places rest.</span>'
             + '</span></label>';
    });
    html += '</div>';
    zone.innerHTML = html;

    const prev = selectedBySalle[salleId];
    if (prev) {
        zone.querySelectorAll('.horaire-badge-item').forEach(function(b) {
            if (b.dataset.slot === prev.slot) b.classList.add('selected');
        });
    }

    zone.querySelectorAll('.horaire-badge-item').forEach(function(badge) {
        badge.addEventListener('click', function() {
            const thisSalleId = this.dataset.salle;
            const thisZone    = document.getElementById('creneaux_salle_' + thisSalleId);
            thisZone.querySelectorAll('.horaire-badge-item').forEach(function(b) { b.classList.remove('selected'); });
            this.classList.add('selected');

            const placesMax = parseInt(this.dataset.places);
            selectedBySalle[thisSalleId] = { slot: this.dataset.slot };
            document.getElementById('hidden_id_creneau_' + thisSalleId).value = this.dataset.idCreneau || '';

            const nbZone  = document.getElementById('nb_zone_' + thisSalleId);
            nbZone.style.display = 'block';
            const nbInput = document.getElementById('nb_personnes_' + thisSalleId);
            nbInput.max = placesMax;
            if (parseInt(nbInput.value) > placesMax) nbInput.value = placesMax;
            document.getElementById('jauge-info-' + thisSalleId).textContent = placesMax + ' place(s) disponible(s) sur ce creneau.';
        });
    });
}

document.querySelectorAll('.salle-accordion').forEach(function(details) {
    details.addEventListener('toggle', function() {
        if (this.open) {
            document.querySelectorAll('.salle-accordion').forEach(function(other) {
                if (other !== details) other.removeAttribute('open');
            });
            renderCreneaux(details.dataset.salleId, 'jeudi');
        }
    });
});

document.getElementById('formReservation').addEventListener('submit', function(e) {
    const inputs  = document.querySelectorAll('input[name^="creneaux["]');
    const valide  = Array.from(inputs).some(function(input) { return input.value !== ''; });
    if (!valide) {
        e.preventDefault();
        alert('Veuillez selectionner au moins un creneau horaire avant de confirmer.');
    }
});
</script>

<?php include('footer.php'); ?>